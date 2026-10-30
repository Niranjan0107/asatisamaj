<?php
/** Temporary registration data; accounts are created at the final paid/free step. */
if ( ! defined( 'ABSPATH' ) ) { exit; }

function samanvay_registration_draft_key( $token ) {
	return 'sam_registration_' . hash_hmac( 'sha256', $token, wp_salt( 'auth' ) );
}

function samanvay_get_registration_draft( $token ) {
	if ( ! is_string( $token ) || ! preg_match( '/^[a-zA-Z0-9]{48}$/', $token ) ) { return false; }
	$data = get_transient( samanvay_registration_draft_key( $token ) );
	if ( ! is_array( $data ) || empty( $data['email'] ) ) { return false; }
	return (object) array_merge( array_fill_keys( array( 'gender', 'date_of_birth', 'father_name', 'marital_status', 'employment_sector', 'nominee_one_name', 'nominee_one_relationship', 'nominee_two_name', 'nominee_two_relationship' ), '' ), $data );
}

function samanvay_complete_registration_draft( $token, $transaction_id, $attachment_id, $free_registration = false ) {
	global $wpdb;
	$draft = samanvay_get_registration_draft( $token );
	if ( ! $draft || ! samanvay_member_has_family_details( $draft ) ) { return new WP_Error( 'invalid_session' ); }
	// Serialize final submissions for the same email, including separate draft tabs.
	$lock = 'sam_reg_' . md5( strtolower( $draft->email ) );
	if ( '1' !== (string) $wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK(%s, 10)', $lock ) ) ) { return new WP_Error( 'create_failed' ); }
	$user_id = 0;
	$member_id = 0;
	$payment_id = 0;
	$complete = false;
	try {
		$draft = samanvay_get_registration_draft( $token );
		if ( ! $draft || ! samanvay_member_has_family_details( $draft ) ) { return new WP_Error( 'invalid_session' ); }
		$is_lifetime = samanvay_member_is_lifetime( $draft );
		if ( $is_lifetime !== $free_registration ) { return new WP_Error( 'invalid_session' ); }
		if ( email_exists( $draft->email ) ) { return new WP_Error( 'exists' ); }
		if ( ! $is_lifetime && '' === $transaction_id && ! $attachment_id ) { return new WP_Error( 'payment_proof_required' ); }
		if ( '' !== $transaction_id && $wpdb->get_var( $wpdb->prepare( 'SELECT id FROM ' . samanvay_member_payments_table() . ' WHERE transaction_id = %s LIMIT 1', $transaction_id ) ) ) { return new WP_Error( 'duplicate_transaction' ); }
		$dob = $draft->date_of_birth;
		$created = wp_insert_user( array( 'user_login' => samanvay_unique_member_login( $draft->email ), 'user_email' => $draft->email, 'user_pass' => substr( $dob, 8, 2 ) . substr( $dob, 5, 2 ) . substr( $dob, 0, 4 ), 'display_name' => $draft->full_name, 'first_name' => $draft->full_name, 'role' => 'samanvay_member' ) );
		if ( is_wp_error( $created ) ) { return new WP_Error( 'create_failed' ); }
		$user_id = $created;
		$fields = array();
		foreach ( array( 'full_name', 'email', 'mobile', 'state', 'district', 'tehsil', 'address', 'pin_code', 'gender', 'date_of_birth', 'father_name', 'marital_status', 'employment_sector', 'nominee_one_name', 'nominee_one_relationship', 'nominee_two_name', 'nominee_two_relationship' ) as $key ) { $fields[ $key ] = $draft->$key; }
		$now = current_time( 'mysql', true );
		$status = $is_lifetime ? 'active' : 'payment_review';
		$fields = array_merge( $fields, array( 'user_id' => $user_id, 'status' => $status, 'is_lifetime_free' => (int) $is_lifetime, 'membership_start' => $is_lifetime ? $now : null, 'membership_expires' => null, 'created_at' => $now, 'updated_at' => $now ) );
		if ( ! $wpdb->insert( samanvay_members_table(), $fields ) ) { return new WP_Error( 'create_failed' ); }
		$member_id = (int) $wpdb->insert_id;
		if ( ! $is_lifetime ) {
			$amount = absint( samanvay_get_theme_setting( 'registration_fee' ) ) + absint( samanvay_get_theme_setting( 'membership_fee' ) );
			if ( ! $wpdb->insert( samanvay_member_payments_table(), array( 'member_id' => $member_id, 'user_id' => $user_id, 'attachment_id' => $attachment_id, 'transaction_id' => $transaction_id, 'payment_type' => 'registration_first', 'amount' => $amount, 'status' => 'pending', 'submitted_at' => $now ) ) ) { return new WP_Error( 'create_failed' ); }
			$payment_id = (int) $wpdb->insert_id;
		}
		if ( ! samanvay_assign_member_identifiers( $member_id ) ) { return new WP_Error( 'create_failed' ); }
		update_user_meta( $user_id, 'samanvay_member_status', $status );
		$complete = true;
		delete_transient( samanvay_registration_draft_key( $token ) );
		return $member_id;
	} finally {
		if ( ! $complete && $user_id ) {
			if ( $payment_id ) { $wpdb->delete( samanvay_member_payments_table(), array( 'id' => $payment_id ), array( '%d' ) ); }
			if ( $member_id ) { $wpdb->delete( samanvay_members_table(), array( 'id' => $member_id ), array( '%d' ) ); }
			require_once ABSPATH . 'wp-admin/includes/user.php';
			wp_delete_user( $user_id );
		}
		$wpdb->get_var( $wpdb->prepare( 'SELECT RELEASE_LOCK(%s)', $lock ) );
	}
}
