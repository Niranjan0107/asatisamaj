<?php
/** Reversible member removal and protected permanent deletion. @package Samanvay */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function samanvay_trash_member( $member ) {
	if ( ! samanvay_can_delete_community_member( $member ) || 'trashed' === $member->status ) { return false; }
	global $wpdb;
	$now = current_time( 'mysql', true );
	$result = $wpdb->update( samanvay_members_table(), array( 'status' => 'trashed', 'pre_trash_status' => $member->status, 'trashed_at' => $now, 'updated_at' => $now, 'payment_token_hash' => null, 'payment_token_expires' => null ), array( 'id' => $member->id, 'status' => $member->status ) );
	if ( ! $result ) { return false; }
	update_user_meta( $member->user_id, 'samanvay_member_status', 'trashed' );
	WP_Session_Tokens::get_instance( $member->user_id )->destroy_all();
	return true;
}

add_filter( 'wp_authenticate_user', function ( $user ) {
	if ( is_wp_error( $user ) ) { return $user; }
	global $wpdb;
	$status = $wpdb->get_var( $wpdb->prepare( 'SELECT status FROM ' . samanvay_members_table() . ' WHERE user_id = %d', $user->ID ) );
	return 'trashed' === $status ? new WP_Error( 'member_trashed', __( 'This member account is in Trash. Please contact the administrator.', 'samanvay' ) ) : $user;
}, 99 );

add_action( 'admin_menu', function () {
	add_users_page( __( 'Trashed Members', 'samanvay' ), __( 'Trashed Members', 'samanvay' ), 'manage_options', 'samanvay-member-trash', 'samanvay_render_member_trash' );
} );

function samanvay_handle_member_trash_action() {
	if ( 'POST' !== $_SERVER['REQUEST_METHOD'] || ! current_user_can( 'manage_options' ) || ! current_user_can( 'delete_users' ) ) {
		wp_die( esc_html__( 'Action not permitted.', 'samanvay' ), '', array( 'response' => 403 ) );
	}
	$id = isset( $_POST['member_id'] ) && is_scalar( $_POST['member_id'] ) ? absint( $_POST['member_id'] ) : 0;
	check_admin_referer( 'samanvay_trash_action_' . $id );
	$decision = $_POST['decision'] ?? '';
	global $wpdb;
	$member = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . samanvay_members_table() . ' WHERE id = %d', $id ) );
	if ( ! $member || 'trashed' !== $member->status || ! samanvay_can_delete_community_member( $member ) || ! in_array( $decision, array( 'restore', 'delete' ), true ) ) {
		wp_die( esc_html__( 'Only eligible members currently in Trash can be restored or permanently deleted. Check the member deletion setting.', 'samanvay' ), '', array( 'response' => 403 ) );
	}
	if ( 'restore' === $decision ) {
		if ( ! get_userdata( $member->user_id ) ) { wp_die( esc_html__( 'The associated login account is missing and cannot be restored.', 'samanvay' ) ); }
		$status = in_array( $member->pre_trash_status, array( 'active', 'expired', 'pending_payment', 'payment_review', 'payment_rejected' ), true ) ? $member->pre_trash_status : 'pending_payment';
		if ( 'active' === $status && ( ! $member->membership_expires || strtotime( $member->membership_expires . ' UTC' ) <= time() ) ) { $status = 'expired'; }
		if ( ! $wpdb->update( samanvay_members_table(), array( 'status' => $status, 'pre_trash_status' => null, 'trashed_at' => null, 'updated_at' => current_time( 'mysql', true ) ), array( 'id' => $id, 'status' => 'trashed' ) ) ) { wp_die( esc_html__( 'Restore failed. Please retry.', 'samanvay' ) ); }
		update_user_meta( $member->user_id, 'samanvay_member_status', $status );
	} else {
		if ( ( $_POST['confirm_delete'] ?? '' ) !== '1' ) { wp_die( esc_html__( 'Confirm permanent deletion first.', 'samanvay' ) ); }
		require_once ABSPATH . 'wp-admin/includes/user.php';
		if ( get_userdata( $member->user_id ) && ! wp_delete_user( $member->user_id, get_current_user_id() ) ) { wp_die( esc_html__( 'Account deletion failed.', 'samanvay' ) ); }
		if ( false === $wpdb->delete( samanvay_member_payments_table(), array( 'member_id' => $id ), array( '%d' ) ) || false === $wpdb->delete( samanvay_members_table(), array( 'id' => $id, 'status' => 'trashed' ), array( '%d', '%s' ) ) ) {
			wp_die( esc_html__( 'Account removed, but record cleanup failed. Retry permanent deletion from Trash.', 'samanvay' ) );
		}
	}
	wp_safe_redirect( add_query_arg( array( 'page' => 'samanvay-member-trash', 'completed' => $decision ), admin_url( 'users.php' ) ) );
	exit;
}
add_action( 'admin_post_samanvay_member_trash_action', 'samanvay_handle_member_trash_action' );

function samanvay_render_member_trash() {
	if ( ! current_user_can( 'manage_options' ) ) { return; }
	global $wpdb;
	$members = $wpdb->get_results( 'SELECT * FROM ' . samanvay_members_table() . " WHERE status = 'trashed' ORDER BY trashed_at DESC, id DESC" );
	?>
	<div class="wrap"><h1><?php esc_html_e( 'Trashed Members', 'samanvay' ); ?></h1>
	<?php if ( isset( $_GET['completed'] ) && in_array( $_GET['completed'], array( 'restore', 'delete' ), true ) ) : ?><div class="notice notice-success is-dismissible"><p><?php echo 'restore' === $_GET['completed'] ? esc_html__( 'Member restored with the same IDs and payment history. Membership expiry is unchanged.', 'samanvay' ) : esc_html__( 'Member account and payment records permanently deleted. Media retained in the Media Library.', 'samanvay' ); ?></p></div><?php endif; ?>
	<p><a href="<?php echo esc_url( admin_url( 'users.php?page=samanvay-members' ) ); ?>"><?php esc_html_e( 'Back to Community Members', 'samanvay' ); ?></a></p>
	<p><?php esc_html_e( 'Trash retains member details, IDs and payments. Trashed members cannot log in and are not publicly listed. Permanent deletion cannot be undone.', 'samanvay' ); ?></p>
	<table class="widefat striped"><thead><tr><th>Member</th><th>Member ID</th><th>Trashed on</th><th>Previous status</th><th>Actions</th></tr></thead><tbody>
	<?php foreach ( $members as $member ) : ?><tr><td><?php echo esc_html( $member->full_name ); ?><br><?php echo esc_html( $member->email ); ?></td><td><?php echo esc_html( $member->member_code ); ?></td><td><?php echo esc_html( get_date_from_gmt( $member->trashed_at, get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) ) ); ?></td><td><?php echo esc_html( $member->pre_trash_status ); ?></td><td>
	<?php if ( samanvay_can_delete_community_member( $member ) ) : ?>
	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><?php wp_nonce_field( 'samanvay_trash_action_' . $member->id ); ?><input type="hidden" name="action" value="samanvay_member_trash_action"><input type="hidden" name="member_id" value="<?php echo esc_attr( $member->id ); ?>"><button class="button" name="decision" value="restore">Restore</button></form>
	<details><summary>Permanently delete</summary><form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><?php wp_nonce_field( 'samanvay_trash_action_' . $member->id ); ?><input type="hidden" name="action" value="samanvay_member_trash_action"><input type="hidden" name="member_id" value="<?php echo esc_attr( $member->id ); ?>"><p><label><input type="checkbox" name="confirm_delete" value="1" required> Permanently delete this account and all member/payment records. This cannot be undone.</label></p><button class="button" name="decision" value="delete">Delete permanently</button></form></details>
	<?php else : ?>Deletion disabled or account protected.<?php endif; ?></td></tr><?php endforeach; ?>
	<?php if ( ! $members ) : ?><tr><td colspan="5">Trash is empty.</td></tr><?php endif; ?>
	</tbody></table></div>
	<?php
}
