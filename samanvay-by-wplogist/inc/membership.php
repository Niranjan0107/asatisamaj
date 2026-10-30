<?php
/**
 * Member registration, payments, expiry and administration.
 *
 * @package Samanvay
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SAMANVAY_MEMBERSHIP_DB_VERSION', '2.1.0' );

/** Allocate a durable sequence number; deleted members' numbers are never reused. */
function samanvay_assign_member_identifiers( $member_id ) {
	global $wpdb;
	$lock = 'sam_ids_' . md5( $wpdb->prefix );
	if ( '1' !== (string) $wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK(%s, 10)', $lock ) ) ) { return false; }
	try {
		return samanvay_assign_member_identifiers_locked( $member_id );
	} finally {
		$wpdb->get_var( $wpdb->prepare( 'SELECT RELEASE_LOCK(%s)', $lock ) );
	}
}

function samanvay_assign_member_identifiers_locked( $member_id ) {
	global $wpdb;
	$sequence = $wpdb->prefix . 'samanvay_member_sequence';
	$number = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$sequence} WHERE member_id = %d", $member_id ) );
	if ( $wpdb->last_error ) { return false; }
	if ( ! $number ) {
		if ( false === $wpdb->insert( $sequence, array( 'member_id' => $member_id ), array( '%d' ) ) ) { return false; }
		$number = $wpdb->insert_id;
	}
	if ( ! $number ) { return false; }
	$suffix = str_pad( (string) $number, 2, '0', STR_PAD_LEFT );
	$table = samanvay_members_table();
	return false !== $wpdb->query( $wpdb->prepare( "UPDATE {$table} SET member_code = COALESCE(NULLIF(member_code, ''), %s), family_code = COALESCE(NULLIF(family_code, ''), %s) WHERE id = %d", 'ASATI' . $suffix, 'ASATI_FAMILY' . $suffix, $member_id ) );
}

/** Hide the frontend toolbar for members without changing administrator preferences. */
function samanvay_member_admin_bar_visibility( $show ) {
	if ( is_user_logged_in() && ! current_user_can( 'manage_options' ) ) {
		$user = wp_get_current_user();
		if ( in_array( 'samanvay_member', (array) $user->roles, true ) ) {
			return false;
		}
	}
	return $show;
}
add_filter( 'show_admin_bar', 'samanvay_member_admin_bar_visibility', 100 );

function samanvay_members_table() {
	global $wpdb;
	return $wpdb->prefix . 'samanvay_members';
}

function samanvay_member_payments_table() {
	global $wpdb;
	return $wpdb->prefix . 'samanvay_member_payments';
}

function samanvay_install_membership_tables() {
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$charset = $wpdb->get_charset_collate();
	$members = samanvay_members_table();
	$payments = samanvay_member_payments_table();
	dbDelta( "CREATE TABLE {$members} (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		user_id bigint(20) unsigned NOT NULL,
		member_code varchar(40) NULL,
		family_code varchar(40) NULL,
		full_name varchar(190) NOT NULL,
		email varchar(190) NOT NULL,
		mobile varchar(20) NOT NULL,
		state varchar(120) NOT NULL,
		district varchar(120) NOT NULL,
		tehsil varchar(120) NOT NULL,
		address text NOT NULL,
		pin_code varchar(10) NOT NULL,
		gender varchar(20) NOT NULL DEFAULT '',
		date_of_birth date NULL,
		father_name varchar(190) NOT NULL DEFAULT '',
		marital_status varchar(30) NOT NULL DEFAULT '',
		employment_sector varchar(20) NOT NULL DEFAULT '',
		nominee_one_name varchar(190) NOT NULL DEFAULT '',
		nominee_one_relationship varchar(120) NOT NULL DEFAULT '',
		nominee_two_name varchar(190) NOT NULL DEFAULT '',
		nominee_two_relationship varchar(120) NOT NULL DEFAULT '',
		profile_picture_id bigint(20) unsigned NOT NULL DEFAULT 0,
		status varchar(30) NOT NULL DEFAULT 'pending_payment',
		pre_trash_status varchar(30) NULL,
		trashed_at datetime NULL,
		membership_start datetime NULL,
		membership_expires datetime NULL,
		payment_token_hash char(64) NULL,
		payment_token_expires datetime NULL,
		created_at datetime NOT NULL,
		updated_at datetime NOT NULL,
		PRIMARY KEY  (id),
		UNIQUE KEY user_id (user_id),
		UNIQUE KEY member_code (member_code),
		KEY family_code (family_code),
		KEY email (email),
		KEY status (status),
		KEY membership_expires (membership_expires)
	) {$charset};" );
	dbDelta( "CREATE TABLE {$payments} (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		member_id bigint(20) unsigned NOT NULL,
		user_id bigint(20) unsigned NOT NULL,
		attachment_id bigint(20) unsigned NOT NULL DEFAULT 0,
		transaction_id varchar(100) NULL,
		payment_type varchar(20) NOT NULL DEFAULT 'membership',
		amount decimal(10,2) NULL,
		status varchar(20) NOT NULL DEFAULT 'pending',
		submitted_at datetime NOT NULL,
		reviewed_at datetime NULL,
		reviewed_by bigint(20) unsigned NULL,
		review_note text NULL,
		PRIMARY KEY  (id),
		KEY member_id (member_id),
		KEY status (status)
	) {$charset};" );
	$sequence = $wpdb->prefix . 'samanvay_member_sequence';
	dbDelta( "CREATE TABLE {$sequence} (
		id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
		member_id bigint(20) unsigned NOT NULL,
		PRIMARY KEY  (id),
		UNIQUE KEY member_id (member_id)
	) {$charset};" );
	// Existing members receive their identifiers in creation order.
	$missing_ids = $wpdb->get_col( "SELECT id FROM {$members} WHERE member_code IS NULL OR member_code = '' OR family_code IS NULL OR family_code = '' ORDER BY id ASC" );
	if ( $wpdb->last_error ) { return; }
	foreach ( $missing_ids as $missing_id ) {
		if ( ! samanvay_assign_member_identifiers( $missing_id ) ) { return; }
	}
	$department_column = $wpdb->get_var( $wpdb->prepare( "SHOW COLUMNS FROM {$members} LIKE %s", 'department_name' ) );
	if ( $department_column ) {
		$wpdb->query( "ALTER TABLE {$members} DROP COLUMN department_name" );
	}
	add_role( 'samanvay_member', __( 'Community Member', 'samanvay' ), array( 'read' => true ) );
	update_option( 'samanvay_membership_db_version', SAMANVAY_MEMBERSHIP_DB_VERSION );
}

function samanvay_maybe_install_membership() {
	if ( SAMANVAY_MEMBERSHIP_DB_VERSION !== get_option( 'samanvay_membership_db_version' ) ) {
		samanvay_install_membership_tables();
	}
	if ( ! wp_next_scheduled( 'samanvay_expire_memberships' ) ) {
		wp_schedule_event( time() + HOUR_IN_SECONDS, 'daily', 'samanvay_expire_memberships' );
	}
}
add_action( 'init', 'samanvay_maybe_install_membership', 5 );

function samanvay_ensure_membership_pages() {
	$pages = array(
		'member-registration' => array( __( 'Member Registration', 'samanvay' ), 'page-member-registration.php' ),
		'member-login'        => array( __( 'Member Login', 'samanvay' ), 'page-member-login.php' ),
		'member-dashboard'    => array( __( 'Member Dashboard', 'samanvay' ), 'page-member-dashboard.php' ),
		'member-forgot-password' => array( __( 'Forgot Password', 'samanvay' ), 'page-member-forgot-password.php' ),
		'member-reset-password'  => array( __( 'Reset Password', 'samanvay' ), 'page-member-reset-password.php' ),
	);
	foreach ( $pages as $slug => $details ) {
		$page = get_page_by_path( $slug );
		if ( $page ) {
			if ( $details[1] !== get_page_template_slug( $page->ID ) ) {
				update_post_meta( $page->ID, '_wp_page_template', $details[1] );
			}
			continue;
		}
		$page_id = wp_insert_post( array(
			'post_title'  => $details[0],
			'post_name'   => $slug,
			'post_status' => 'publish',
			'post_type'   => 'page',
		) );
		if ( ! is_wp_error( $page_id ) ) {
			update_post_meta( $page_id, '_wp_page_template', $details[1] );
		}
	}
}
add_action( 'after_switch_theme', 'samanvay_ensure_membership_pages' );

function samanvay_maybe_ensure_registration_page() {
	if ( '3.0.0' !== get_option( 'samanvay_registration_page_version' ) ) {
		samanvay_ensure_membership_pages();
		update_option( 'samanvay_registration_page_version', '3.0.0' );
	}
}
add_action( 'init', 'samanvay_maybe_ensure_registration_page', 20 );

function samanvay_get_registration_page_url() {
	$page = get_page_by_path( 'member-registration' );
	return $page ? get_permalink( $page ) : home_url( '/member-registration/' );
}

function samanvay_get_member_login_url() {
	$page = get_page_by_path( 'member-login' );
	return $page ? get_permalink( $page ) : home_url( '/member-login/' );
}

function samanvay_get_member_dashboard_url() {
	$page = get_page_by_path( 'member-dashboard' );
	return $page ? get_permalink( $page ) : home_url( '/member-dashboard/' );
}

function samanvay_get_member_forgot_password_url() {
	$page = get_page_by_path( 'member-forgot-password' );
	return $page ? get_permalink( $page ) : home_url( '/member-forgot-password/' );
}

function samanvay_get_member_reset_password_url() {
	$page = get_page_by_path( 'member-reset-password' );
	return $page ? get_permalink( $page ) : home_url( '/member-reset-password/' );
}

function samanvay_custom_lostpassword_url( $lostpassword_url, $redirect ) {
	$url = samanvay_get_member_forgot_password_url();
	return $redirect ? add_query_arg( 'redirect_to', $redirect, $url ) : $url;
}
add_filter( 'lostpassword_url', 'samanvay_custom_lostpassword_url', 10, 2 );

function samanvay_dashboard_redirect( $args = array() ) {
	wp_safe_redirect( add_query_arg( $args, samanvay_get_member_dashboard_url() ) );
	exit;
}

function samanvay_membership_redirect( $args = array() ) {
	wp_safe_redirect( add_query_arg( $args, samanvay_get_registration_page_url() ) );
	exit;
}

function samanvay_membership_error_message( $code ) {
	$password_errors = array(
		'current_password' => __( 'वर्तमान पासवर्ड सही नहीं है।', 'samanvay' ),
		'new_password' => __( 'नया पासवर्ड कम से कम 8 अक्षरों का होना चाहिए और पुष्टि पासवर्ड से मेल खाना चाहिए।', 'samanvay' ),
		'password_unchanged' => __( 'नया पासवर्ड वर्तमान पासवर्ड से अलग होना चाहिए।', 'samanvay' ),
		'password_update_failed' => __( 'पासवर्ड अपडेट नहीं हो सका। कृपया फिर प्रयास करें।', 'samanvay' ),
	);
	if ( isset( $password_errors[ $code ] ) ) { return $password_errors[ $code ]; }
	if ( 'terms_required' === $code ) {
		return __( 'कृपया पंजीकरण से पहले नियमावली से सहमति दें।', 'samanvay' );
	}
	$messages = array(
		'required'          => __( 'Please complete all required fields.', 'samanvay' ),
		'invalid_email'     => __( 'Please enter a valid email address.', 'samanvay' ),
		'invalid_mobile'    => __( 'Please enter a valid 10-digit mobile number.', 'samanvay' ),
		'invalid_pin'       => __( 'Please enter a valid 6-digit PIN code.', 'samanvay' ),
		'password'          => __( 'Passwords must match and contain at least 8 characters.', 'samanvay' ),
		'exists'            => __( 'An account already exists with this email address.', 'samanvay' ),
		'create_failed'     => __( 'Your account could not be created. Please try again.', 'samanvay' ),
		'invalid_session'   => __( 'This payment session is invalid or has expired. Please start again.', 'samanvay' ),
		'invalid_file'      => __( 'Upload a JPG, PNG or WebP payment screenshot up to 5 MB.', 'samanvay' ),
		'invalid_transaction' => __( 'Please enter a valid Transaction ID.', 'samanvay' ),
		'duplicate_transaction' => __( 'This Transaction ID has already been submitted.', 'samanvay' ),
		'payment_proof_required' => __( 'Please enter a Transaction ID or upload a payment screenshot.', 'samanvay' ),
		'upload_failed'     => __( 'The payment screenshot could not be uploaded. Please try again.', 'samanvay' ),
		'invalid_login'     => __( 'The renewal email or password is incorrect.', 'samanvay' ),
		'not_member'        => __( 'No membership record was found for this account.', 'samanvay' ),
		'profile_invalid'   => __( 'Please check all profile fields and select a valid location.', 'samanvay' ),
		'invalid_profile_picture' => __( 'Upload a JPG, PNG or WebP profile picture up to 3 MB.', 'samanvay' ),
	);
	return isset( $messages[ $code ] ) ? $messages[ $code ] : '';
}

function samanvay_unique_member_login( $email ) {
	$base = sanitize_user( strtok( $email, '@' ), true );
	$base = $base ? $base : 'member';
	$login = $base;
	$i = 1;
	while ( username_exists( $login ) ) {
		$login = $base . $i;
		$i++;
	}
	return $login;
}

function samanvay_is_valid_member_location( $state, $district, $tehsil ) {
	$path = get_template_directory() . '/assets/data-india-locations.json';
	if ( ! is_readable( $path ) ) {
		return false;
	}
	$data = json_decode( file_get_contents( $path ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	foreach ( (array) $data as $state_item ) {
		if ( ! isset( $state_item['state'] ) || $state !== $state_item['state'] ) {
			continue;
		}
		foreach ( (array) $state_item['districts'] as $district_item ) {
			if ( isset( $district_item['name'], $district_item['sub_districts'] ) && $district === $district_item['name'] && in_array( $tehsil, $district_item['sub_districts'], true ) ) {
				return true;
			}
		}
	}
	return false;
}

function samanvay_get_member_by_user( $user_id ) {
	global $wpdb;
	return $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . samanvay_members_table() . " WHERE user_id = %d AND status <> 'trashed'", absint( $user_id ) ) );
}

function samanvay_handle_member_login() {
	if ( ! isset( $_POST['samanvay_login_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['samanvay_login_nonce'] ) ), 'samanvay_member_login' ) ) {
		wp_die( esc_html__( 'Security check failed.', 'samanvay' ), 403 );
	}
	$identity = isset( $_POST['member_identity'] ) ? sanitize_text_field( wp_unslash( $_POST['member_identity'] ) ) : '';
	$password = isset( $_POST['member_password'] ) ? (string) wp_unslash( $_POST['member_password'] ) : '';
	$user = is_email( $identity ) ? get_user_by( 'email', $identity ) : get_user_by( 'login', $identity );
	if ( ! $user ) {
		wp_safe_redirect( add_query_arg( 'login_error', 'invalid_login', samanvay_get_member_login_url() ) );
		exit;
	}
	$credentials = array( 'user_login' => $user->user_login, 'user_password' => $password, 'remember' => ! empty( $_POST['remember'] ) );
	$signed_in = wp_signon( $credentials, is_ssl() );
	if ( is_wp_error( $signed_in ) || ! samanvay_get_member_by_user( $signed_in->ID ) ) {
		if ( ! is_wp_error( $signed_in ) ) {
			wp_logout();
		}
		wp_safe_redirect( add_query_arg( 'login_error', 'invalid_login', samanvay_get_member_login_url() ) );
		exit;
	}
	wp_safe_redirect( samanvay_get_member_dashboard_url() );
	exit;
}
add_action( 'admin_post_nopriv_samanvay_member_login', 'samanvay_handle_member_login' );

function samanvay_handle_member_forgot_password() {
	if ( ! isset( $_POST['samanvay_forgot_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['samanvay_forgot_nonce'] ) ), 'samanvay_member_forgot_password' ) ) {
		wp_die( esc_html__( 'Security check failed.', 'samanvay' ), 403 );
	}
	$identity = isset( $_POST['member_identity'] ) ? sanitize_text_field( wp_unslash( $_POST['member_identity'] ) ) : '';
	$rate_key = 'samanvay_reset_' . md5( strtolower( $identity ) . '|' . ( isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '' ) );
	$mail_failed = false;
	if ( ! get_transient( $rate_key ) ) {
		$user = is_email( $identity ) ? get_user_by( 'email', $identity ) : get_user_by( 'login', $identity );
		if ( $user && samanvay_get_member_by_user( $user->ID ) ) {
			try {
				$key = get_password_reset_key( $user );
				if ( is_wp_error( $key ) ) {
					$mail_failed = true;
				} else {
					$reset_url = add_query_arg( array( 'key' => $key, 'login' => $user->user_login ), samanvay_get_member_reset_password_url() );
					$site_name = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
					$subject = sprintf( __( '[%s] Member password reset', 'samanvay' ), $site_name );
					$message = sprintf( __( "Hello %1$s,\n\nA password reset was requested for your member account.\n\nSet a new password using this secure link:\n%2$s\n\nIf you did not request this, you can ignore this email.\n\n%3$s", 'samanvay' ), $user->display_name, $reset_url, $site_name );
					$mail_failed = ! wp_mail( $user->user_email, $subject, $message );
				}
			} catch ( Throwable $error ) {
				$mail_failed = true;
				error_log( 'Samanvay member password email failed: ' . $error->getMessage() ); // phpcs:ignore WordPress.PHP.DevelopmentFunctions.error_log_error_log
			}
		}
		if ( ! $mail_failed ) {
			set_transient( $rate_key, 1, 10 * MINUTE_IN_SECONDS );
		}
	}
	if ( $mail_failed ) {
		wp_safe_redirect( add_query_arg( 'email_error', 1, samanvay_get_member_forgot_password_url() ) );
		exit;
	}
	wp_safe_redirect( add_query_arg( 'reset_requested', 1, samanvay_get_member_forgot_password_url() ) );
	exit;
}
add_action( 'admin_post_nopriv_samanvay_member_forgot_password', 'samanvay_handle_member_forgot_password' );

function samanvay_handle_member_reset_password() {
	if ( ! isset( $_POST['samanvay_reset_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['samanvay_reset_nonce'] ) ), 'samanvay_member_reset_password' ) ) {
		wp_die( esc_html__( 'Security check failed.', 'samanvay' ), 403 );
	}
	$login = isset( $_POST['reset_login'] ) ? sanitize_text_field( wp_unslash( $_POST['reset_login'] ) ) : '';
	$key = isset( $_POST['reset_key'] ) ? sanitize_text_field( wp_unslash( $_POST['reset_key'] ) ) : '';
	$password = isset( $_POST['new_password'] ) ? (string) wp_unslash( $_POST['new_password'] ) : '';
	$confirm = isset( $_POST['confirm_password'] ) ? (string) wp_unslash( $_POST['confirm_password'] ) : '';
	$user = check_password_reset_key( $key, $login );
	if ( is_wp_error( $user ) || ! samanvay_get_member_by_user( $user->ID ) ) {
		wp_safe_redirect( add_query_arg( 'reset_error', 'invalid_key', samanvay_get_member_forgot_password_url() ) );
		exit;
	}
	if ( strlen( $password ) < 8 || ! hash_equals( $password, $confirm ) ) {
		wp_safe_redirect( add_query_arg( array( 'key' => $key, 'login' => $login, 'reset_error' => 'password' ), samanvay_get_member_reset_password_url() ) );
		exit;
	}
	reset_password( $user, $password );
	wp_safe_redirect( add_query_arg( 'password_reset', 1, samanvay_get_member_login_url() ) );
	exit;
}
add_action( 'admin_post_nopriv_samanvay_member_reset_password', 'samanvay_handle_member_reset_password' );

function samanvay_handle_member_profile_update() {
	if ( ! is_user_logged_in() ) {
		wp_safe_redirect( samanvay_get_member_login_url() );
		exit;
	}
	check_admin_referer( 'samanvay_update_member_profile', 'samanvay_profile_nonce' );
	$member = samanvay_get_member_by_user( get_current_user_id() );
	if ( ! $member ) {
		wp_die( esc_html__( 'Member record not found.', 'samanvay' ), 403 );
	}
	$fields = array();
	// Only editable profile fields are accepted; identity and nominees are immutable here.
	foreach ( array( 'state', 'district', 'tehsil', 'address', 'pin_code', 'gender', 'date_of_birth', 'father_name', 'marital_status', 'employment_sector' ) as $key ) {
		$raw = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : '';
		$fields[ $key ] = in_array( $key, array( 'gender', 'marital_status', 'employment_sector', 'nominee_one_relationship', 'nominee_two_relationship' ), true ) ? sanitize_key( $raw ) : ( 'address' === $key ? sanitize_textarea_field( $raw ) : sanitize_text_field( $raw ) );
	}
	$valid_profile = ! in_array( '', $fields, true )
		&& preg_match( '/^[1-9][0-9]{5}$/', $fields['pin_code'] )
		&& samanvay_is_valid_member_location( $fields['state'], $fields['district'], $fields['tehsil'] )
		&& in_array( $fields['gender'], array( 'male', 'female', 'other' ), true )
		&& samanvay_is_valid_birth_date( $fields['date_of_birth'] )
		&& in_array( $fields['marital_status'], array( 'unmarried', 'married', 'widowed', 'divorced' ), true )
		&& in_array( $fields['employment_sector'], array( 'government', 'non_government', 'business', 'housewife' ), true )
		&& ( 'housewife' !== $fields['employment_sector'] || 'female' === $fields['gender'] );
	if ( ! $valid_profile ) {
		samanvay_dashboard_redirect( array( 'dashboard_error' => 'profile_invalid' ) );
	}
	global $wpdb;
	if ( ! empty( $_FILES['profile_picture']['name'] ) ) {
		if ( UPLOAD_ERR_OK !== (int) $_FILES['profile_picture']['error'] || (int) $_FILES['profile_picture']['size'] > 3 * MB_IN_BYTES ) {
			samanvay_dashboard_redirect( array( 'dashboard_error' => 'invalid_profile_picture' ) );
		}
		$filetype = wp_check_filetype_and_ext( $_FILES['profile_picture']['tmp_name'], $_FILES['profile_picture']['name'] );
		if ( ! in_array( $filetype['type'], array( 'image/jpeg', 'image/png', 'image/webp' ), true ) ) {
			samanvay_dashboard_redirect( array( 'dashboard_error' => 'invalid_profile_picture' ) );
		}
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';
		$profile_picture_id = media_handle_upload( 'profile_picture', 0, array( 'post_title' => $member->full_name . ' profile picture' ), array( 'test_form' => false ) );
		if ( is_wp_error( $profile_picture_id ) ) {
			samanvay_dashboard_redirect( array( 'dashboard_error' => 'invalid_profile_picture' ) );
		}
		$fields['profile_picture_id'] = $profile_picture_id;
	}
	$fields['updated_at'] = current_time( 'mysql', true );
	$formats = array_fill( 0, 10, '%s' );
	if ( isset( $fields['profile_picture_id'] ) ) {
		$formats[] = '%d';
	}
	$formats[] = '%s';
	$wpdb->update( samanvay_members_table(), $fields, array( 'id' => $member->id ), $formats, array( '%d' ) );
	samanvay_dashboard_redirect( array( 'profile_updated' => 1 ) );
}
add_action( 'admin_post_samanvay_update_member_profile', 'samanvay_handle_member_profile_update' );

function samanvay_handle_member_picture_update() {
	if ( ! is_user_logged_in() ) {
		wp_safe_redirect( samanvay_get_member_login_url() );
		exit;
	}
	check_admin_referer( 'samanvay_update_member_picture', 'samanvay_picture_nonce' );
	$member = samanvay_get_member_by_user( get_current_user_id() );
	if ( ! $member || empty( $_FILES['profile_picture']['name'] ) || UPLOAD_ERR_OK !== (int) $_FILES['profile_picture']['error'] || (int) $_FILES['profile_picture']['size'] > 3 * MB_IN_BYTES ) {
		samanvay_dashboard_redirect( array( 'dashboard_error' => 'invalid_profile_picture' ) );
	}
	$filetype = wp_check_filetype_and_ext( $_FILES['profile_picture']['tmp_name'], $_FILES['profile_picture']['name'] );
	if ( ! in_array( $filetype['type'], array( 'image/jpeg', 'image/png', 'image/webp' ), true ) ) {
		samanvay_dashboard_redirect( array( 'dashboard_error' => 'invalid_profile_picture' ) );
	}
	require_once ABSPATH . 'wp-admin/includes/file.php';
	require_once ABSPATH . 'wp-admin/includes/media.php';
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$profile_picture_id = media_handle_upload( 'profile_picture', 0, array( 'post_title' => $member->full_name . ' profile picture' ), array( 'test_form' => false ) );
	if ( is_wp_error( $profile_picture_id ) ) {
		samanvay_dashboard_redirect( array( 'dashboard_error' => 'invalid_profile_picture' ) );
	}
	global $wpdb;
	$wpdb->update( samanvay_members_table(), array( 'profile_picture_id' => $profile_picture_id, 'updated_at' => current_time( 'mysql', true ) ), array( 'id' => $member->id ), array( '%d', '%s' ), array( '%d' ) );
	samanvay_dashboard_redirect( array( 'picture_updated' => 1 ) );
}
add_action( 'admin_post_samanvay_update_member_picture', 'samanvay_handle_member_picture_update' );

function samanvay_issue_payment_token( $member_id ) {
	global $wpdb;
	$token = wp_generate_password( 48, false, false );
	$hash = hash_hmac( 'sha256', $token, wp_salt( 'auth' ) );
	$expires = gmdate( 'Y-m-d H:i:s', time() + DAY_IN_SECONDS );
	$wpdb->update( samanvay_members_table(), array( 'payment_token_hash' => $hash, 'payment_token_expires' => $expires, 'updated_at' => current_time( 'mysql', true ) ), array( 'id' => $member_id ), array( '%s', '%s', '%s' ), array( '%d' ) );
	return $token;
}

function samanvay_verify_payment_token( $member_id, $token ) {
	global $wpdb;
	$member = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . samanvay_members_table() . ' WHERE id = %d', $member_id ) );
	if ( ! $member || 'trashed' === $member->status || ! $token || ! $member->payment_token_hash || strtotime( $member->payment_token_expires . ' UTC' ) < time() ) {
		return false;
	}
	$hash = hash_hmac( 'sha256', $token, wp_salt( 'auth' ) );
	return hash_equals( $member->payment_token_hash, $hash ) ? $member : false;
}

function samanvay_get_nominee_relationships() {
	return array(
		'father'      => __( 'पिता (Father)', 'samanvay' ),
		'mother'      => __( 'माता (Mother)', 'samanvay' ),
		'brother'     => __( 'भाई (Brother)', 'samanvay' ),
		'sister'      => __( 'बहन (Sister)', 'samanvay' ),
		'husband'     => __( 'पति (Husband)', 'samanvay' ),
		'wife'        => __( 'पत्नी (Wife)', 'samanvay' ),
		'son'         => __( 'पुत्र (Son)', 'samanvay' ),
		'daughter'    => __( 'पुत्री (Daughter)', 'samanvay' ),
		'grandfather' => __( 'दादा (Grandfather)', 'samanvay' ),
		'grandmother' => __( 'दादी (Grandmother)', 'samanvay' ),
		'other'       => __( 'अन्य (Other)', 'samanvay' ),
	);
}

function samanvay_member_has_family_details( $member ) {
	if ( ! $member ) {
		return false;
	}
	return in_array( $member->gender, array( 'male', 'female', 'other' ), true )
		&& samanvay_is_valid_birth_date( $member->date_of_birth )
		&& ! empty( $member->father_name )
		&& in_array( $member->marital_status, array( 'unmarried', 'married', 'widowed', 'divorced' ), true )
		&& in_array( $member->employment_sector, array( 'government', 'non_government', 'business', 'housewife' ), true )
		&& ( 'housewife' !== $member->employment_sector || 'female' === $member->gender )
		&& ! empty( $member->nominee_one_name )
		&& array_key_exists( $member->nominee_one_relationship, samanvay_get_nominee_relationships() )
		&& ! empty( $member->nominee_two_name )
		&& array_key_exists( $member->nominee_two_relationship, samanvay_get_nominee_relationships() );
}

function samanvay_is_valid_birth_date( $date ) {
	if ( ! is_string( $date ) || ! preg_match( '/^(\d{4})-(\d{2})-(\d{2})$/', $date, $parts ) ) {
		return false;
	}
	return checkdate( (int) $parts[2], (int) $parts[3], (int) $parts[1] ) && $date <= current_time( 'Y-m-d' );
}

function samanvay_handle_member_registration() {
	if ( ! isset( $_POST['samanvay_register_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['samanvay_register_nonce'] ) ), 'samanvay_register_member' ) ) {
		wp_die( esc_html__( 'Security check failed.', 'samanvay' ), 403 );
	}
	if ( ! empty( $_POST['company_name'] ) ) {
		samanvay_membership_redirect( array( 'registration_error' => 'create_failed' ) );
	}
	if ( ! isset( $_POST['accept_terms'] ) || '1' !== $_POST['accept_terms'] ) {
		samanvay_membership_redirect( array( 'registration_error' => 'terms_required' ) );
	}
	$fields = array();
	foreach ( array( 'full_name', 'email', 'mobile', 'state', 'district', 'tehsil', 'address', 'pin_code' ) as $key ) {
		$raw = isset( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : '';
		$fields[ $key ] = 'address' === $key ? sanitize_textarea_field( $raw ) : sanitize_text_field( $raw );
	}
	$fields['email'] = sanitize_email( $fields['email'] );
	$fields['mobile'] = preg_replace( '/\D+/', '', $fields['mobile'] );
	if ( in_array( '', $fields, true ) ) {
		samanvay_membership_redirect( array( 'registration_error' => 'required' ) );
	}
	if ( ! is_email( $fields['email'] ) ) {
		samanvay_membership_redirect( array( 'registration_error' => 'invalid_email' ) );
	}
	if ( ! preg_match( '/^[6-9][0-9]{9}$/', $fields['mobile'] ) ) {
		samanvay_membership_redirect( array( 'registration_error' => 'invalid_mobile' ) );
	}
	if ( ! preg_match( '/^[1-9][0-9]{5}$/', $fields['pin_code'] ) ) {
		samanvay_membership_redirect( array( 'registration_error' => 'invalid_pin' ) );
	}
	if ( ! samanvay_is_valid_member_location( $fields['state'], $fields['district'], $fields['tehsil'] ) ) {
		samanvay_membership_redirect( array( 'registration_error' => 'required' ) );
	}
	if ( email_exists( $fields['email'] ) ) {
		samanvay_membership_redirect( array( 'registration_error' => 'exists' ) );
	}
	$user_id = wp_insert_user( array(
		'user_login'   => samanvay_unique_member_login( $fields['email'] ),
		'user_email'   => $fields['email'],
		'user_pass'    => wp_generate_password( 32, true, true ),
		'display_name' => $fields['full_name'],
		'first_name'   => $fields['full_name'],
		'role'         => 'samanvay_member',
	) );
	if ( is_wp_error( $user_id ) ) {
		samanvay_membership_redirect( array( 'registration_error' => 'create_failed' ) );
	}
	global $wpdb;
	$now = current_time( 'mysql', true );
	$inserted = $wpdb->insert( samanvay_members_table(), array_merge( $fields, array( 'user_id' => $user_id, 'status' => 'pending_payment', 'created_at' => $now, 'updated_at' => $now ) ), array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s' ) );
	if ( ! $inserted ) {
		require_once ABSPATH . 'wp-admin/includes/user.php';
		wp_delete_user( $user_id );
		samanvay_membership_redirect( array( 'registration_error' => 'create_failed' ) );
	}
	$member_id = (int) $wpdb->insert_id;
	if ( ! samanvay_assign_member_identifiers( $member_id ) ) {
		$wpdb->delete( samanvay_members_table(), array( 'id' => $member_id ), array( '%d' ) );
		require_once ABSPATH . 'wp-admin/includes/user.php';
		wp_delete_user( $user_id );
		samanvay_membership_redirect( array( 'registration_error' => 'create_failed' ) );
	}
	update_user_meta( $user_id, 'samanvay_initial_password_pending', 1 );
	update_user_meta( $user_id, 'samanvay_member_status', 'pending_payment' );
	$token = samanvay_issue_payment_token( $member_id );
	samanvay_membership_redirect( array( 'membership_step' => 'family', 'member_id' => $member_id, 'member_token' => $token ) );
}
add_action( 'admin_post_nopriv_samanvay_register_member', 'samanvay_handle_member_registration' );
add_action( 'admin_post_samanvay_register_member', 'samanvay_handle_member_registration' );

function samanvay_handle_member_family_details() {
	if ( ! isset( $_POST['samanvay_family_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['samanvay_family_nonce'] ) ), 'samanvay_save_family_details' ) ) {
		wp_die( esc_html__( 'Security check failed.', 'samanvay' ), 403 );
	}
	$member_id     = isset( $_POST['member_id'] ) ? absint( $_POST['member_id'] ) : 0;
	$token         = isset( $_POST['member_token'] ) ? sanitize_text_field( wp_unslash( $_POST['member_token'] ) ) : '';
	$member        = samanvay_verify_payment_token( $member_id, $token );
	$gender        = isset( $_POST['gender'] ) ? sanitize_key( wp_unslash( $_POST['gender'] ) ) : '';
	$date_of_birth = isset( $_POST['date_of_birth'] ) ? sanitize_text_field( wp_unslash( $_POST['date_of_birth'] ) ) : '';
	$father_name   = isset( $_POST['father_name'] ) ? sanitize_text_field( wp_unslash( $_POST['father_name'] ) ) : '';
	$marital_status = isset( $_POST['marital_status'] ) ? sanitize_key( wp_unslash( $_POST['marital_status'] ) ) : '';
	$employment_sector = isset( $_POST['employment_sector'] ) ? sanitize_key( wp_unslash( $_POST['employment_sector'] ) ) : '';
	$nominee_one_name = isset( $_POST['nominee_one_name'] ) ? sanitize_text_field( wp_unslash( $_POST['nominee_one_name'] ) ) : '';
	$nominee_one_relationship = isset( $_POST['nominee_one_relationship'] ) ? sanitize_key( wp_unslash( $_POST['nominee_one_relationship'] ) ) : '';
	$nominee_two_name = isset( $_POST['nominee_two_name'] ) ? sanitize_text_field( wp_unslash( $_POST['nominee_two_name'] ) ) : '';
	$nominee_two_relationship = isset( $_POST['nominee_two_relationship'] ) ? sanitize_key( wp_unslash( $_POST['nominee_two_relationship'] ) ) : '';

	if ( ! $member ) {
		samanvay_membership_redirect( array( 'registration_error' => 'invalid_session' ) );
	}
	if ( ! in_array( $gender, array( 'male', 'female', 'other' ), true ) || ! samanvay_is_valid_birth_date( $date_of_birth ) || '' === $father_name || ! in_array( $marital_status, array( 'unmarried', 'married', 'widowed', 'divorced' ), true ) || ! in_array( $employment_sector, array( 'government', 'non_government', 'business', 'housewife' ), true ) || ( 'housewife' === $employment_sector && 'female' !== $gender ) || '' === $nominee_one_name || ! array_key_exists( $nominee_one_relationship, samanvay_get_nominee_relationships() ) || '' === $nominee_two_name || ! array_key_exists( $nominee_two_relationship, samanvay_get_nominee_relationships() ) ) {
		samanvay_membership_redirect( array( 'membership_step' => 'family', 'member_id' => $member_id, 'member_token' => $token, 'registration_error' => 'required' ) );
	}

	global $wpdb;
	$updated = $wpdb->update(
		samanvay_members_table(),
		array(
			'gender'         => $gender,
			'date_of_birth'  => $date_of_birth,
			'father_name'    => $father_name,
			'marital_status' => $marital_status,
			'employment_sector' => $employment_sector,
			'nominee_one_name' => $nominee_one_name,
			'nominee_one_relationship' => $nominee_one_relationship,
			'nominee_two_name' => $nominee_two_name,
			'nominee_two_relationship' => $nominee_two_relationship,
			'updated_at'     => current_time( 'mysql', true ),
		),
		array( 'id' => $member_id ),
		array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' ),
		array( '%d' )
	);
	if ( false === $updated ) {
		samanvay_membership_redirect( array( 'membership_step' => 'family', 'member_id' => $member_id, 'member_token' => $token, 'registration_error' => 'create_failed' ) );
	}

	// Set the initial password only once, after validated step-two details save.
	// Later profile edits or repeated submissions must not reset the password.
	if ( get_user_meta( $member->user_id, 'samanvay_initial_password_pending', true ) ) {
		$birth_password = substr( $date_of_birth, 8, 2 ) . substr( $date_of_birth, 5, 2 ) . substr( $date_of_birth, 0, 4 );
		wp_set_password( $birth_password, $member->user_id );
		delete_user_meta( $member->user_id, 'samanvay_initial_password_pending' );
	}
	samanvay_membership_redirect( array( 'membership_step' => 'payment', 'member_id' => $member_id, 'member_token' => $token ) );
}
add_action( 'admin_post_nopriv_samanvay_save_member_family_details', 'samanvay_handle_member_family_details' );
add_action( 'admin_post_samanvay_save_member_family_details', 'samanvay_handle_member_family_details' );

function samanvay_handle_member_payment() {
	if ( ! isset( $_POST['samanvay_payment_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['samanvay_payment_nonce'] ) ), 'samanvay_submit_payment' ) ) {
		wp_die( esc_html__( 'Security check failed.', 'samanvay' ), 403 );
	}
	$member_id = isset( $_POST['member_id'] ) ? absint( $_POST['member_id'] ) : 0;
	$token = isset( $_POST['member_token'] ) ? sanitize_text_field( wp_unslash( $_POST['member_token'] ) ) : '';
	$transaction_id = isset( $_POST['transaction_id'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['transaction_id'] ) ) ) : '';
	$from_dashboard = is_user_logged_in();
	$member = $from_dashboard ? samanvay_get_member_by_user( get_current_user_id() ) : samanvay_verify_payment_token( $member_id, $token );
	if ( $from_dashboard && ( ! $member || (int) $member->id !== $member_id ) ) {
		$member = false;
	}
	if ( ! $member ) {
		$from_dashboard ? samanvay_dashboard_redirect( array( 'dashboard_error' => 'invalid_session' ) ) : samanvay_membership_redirect( array( 'registration_error' => 'invalid_session' ) );
	}
	if ( ! $from_dashboard && ! samanvay_member_has_family_details( $member ) ) {
		samanvay_membership_redirect( array( 'membership_step' => 'family', 'member_id' => $member_id, 'member_token' => $token, 'registration_error' => 'required' ) );
	}
	$has_screenshot = ! empty( $_FILES['payment_screenshot']['name'] );
	if ( '' === $transaction_id && ! $has_screenshot ) {
		$from_dashboard ? samanvay_dashboard_redirect( array( 'dashboard_error' => 'payment_proof_required' ) ) : samanvay_membership_redirect( array( 'membership_step' => 'payment', 'member_id' => $member_id, 'member_token' => $token, 'payment_error' => 'payment_proof_required' ) );
	}
	if ( '' !== $transaction_id && strlen( $transaction_id ) > 100 ) {
		$from_dashboard ? samanvay_dashboard_redirect( array( 'dashboard_error' => 'invalid_transaction' ) ) : samanvay_membership_redirect( array( 'membership_step' => 'payment', 'member_id' => $member_id, 'member_token' => $token, 'payment_error' => 'invalid_transaction' ) );
	}
	global $wpdb;
	if ( '' !== $transaction_id && $wpdb->get_var( $wpdb->prepare( 'SELECT id FROM ' . samanvay_member_payments_table() . ' WHERE transaction_id = %s LIMIT 1', $transaction_id ) ) ) {
		$from_dashboard ? samanvay_dashboard_redirect( array( 'dashboard_error' => 'duplicate_transaction' ) ) : samanvay_membership_redirect( array( 'membership_step' => 'payment', 'member_id' => $member_id, 'member_token' => $token, 'payment_error' => 'duplicate_transaction' ) );
	}
	$attachment_id = 0;
	if ( $has_screenshot ) {
		if ( UPLOAD_ERR_OK !== (int) $_FILES['payment_screenshot']['error'] || (int) $_FILES['payment_screenshot']['size'] > 5 * MB_IN_BYTES ) {
			$from_dashboard ? samanvay_dashboard_redirect( array( 'dashboard_error' => 'invalid_file' ) ) : samanvay_membership_redirect( array( 'membership_step' => 'payment', 'member_id' => $member_id, 'member_token' => $token, 'payment_error' => 'invalid_file' ) );
		}
		$filetype = wp_check_filetype_and_ext( $_FILES['payment_screenshot']['tmp_name'], $_FILES['payment_screenshot']['name'] );
		if ( ! in_array( $filetype['type'], array( 'image/jpeg', 'image/png', 'image/webp' ), true ) ) {
			$from_dashboard ? samanvay_dashboard_redirect( array( 'dashboard_error' => 'invalid_file' ) ) : samanvay_membership_redirect( array( 'membership_step' => 'payment', 'member_id' => $member_id, 'member_token' => $token, 'payment_error' => 'invalid_file' ) );
		}
		require_once ABSPATH . 'wp-admin/includes/file.php';
		require_once ABSPATH . 'wp-admin/includes/media.php';
		require_once ABSPATH . 'wp-admin/includes/image.php';
		$attachment_id = media_handle_upload( 'payment_screenshot', 0, array( 'post_title' => 'Member payment ' . $member_id ), array( 'test_form' => false ) );
		if ( is_wp_error( $attachment_id ) ) {
			$from_dashboard ? samanvay_dashboard_redirect( array( 'dashboard_error' => 'upload_failed' ) ) : samanvay_membership_redirect( array( 'membership_step' => 'payment', 'member_id' => $member_id, 'member_token' => $token, 'payment_error' => 'upload_failed' ) );
		}
	}
	$now = current_time( 'mysql', true );
	// Determine the payment purpose from the authenticated flow, never a posted type or amount.
	$payment_type = $from_dashboard ? 'membership' : 'registration_first';
	$amount = absint( samanvay_get_theme_setting( 'membership_fee' ) );
	if ( ! $from_dashboard ) {
		$amount += absint( samanvay_get_theme_setting( 'registration_fee' ) );
	}
	$payment_saved = $wpdb->insert( samanvay_member_payments_table(), array( 'member_id' => $member_id, 'user_id' => $member->user_id, 'attachment_id' => $attachment_id, 'transaction_id' => $transaction_id, 'payment_type' => $payment_type, 'amount' => $amount, 'status' => 'pending', 'submitted_at' => $now ), array( '%d', '%d', '%d', '%s', '%s', '%f', '%s', '%s' ) );
	if ( ! $payment_saved ) {
		if ( $attachment_id ) {
			wp_delete_attachment( $attachment_id, true );
		}
		$from_dashboard ? samanvay_dashboard_redirect( array( 'dashboard_error' => 'upload_failed' ) ) : samanvay_membership_redirect( array( 'membership_step' => 'payment', 'member_id' => $member_id, 'member_token' => $token, 'payment_error' => 'upload_failed' ) );
	}
	$is_currently_active = 'active' === $member->status && ! empty( $member->membership_expires ) && strtotime( $member->membership_expires . ' UTC' ) > time();
	$member_status = $is_currently_active ? 'active' : 'payment_review';
	$wpdb->update( samanvay_members_table(), array( 'status' => $member_status, 'payment_token_hash' => null, 'payment_token_expires' => null, 'updated_at' => $now ), array( 'id' => $member_id ), array( '%s', '%s', '%s', '%s' ), array( '%d' ) );
	update_user_meta( $member->user_id, 'samanvay_member_status', $member_status );
	$from_dashboard ? samanvay_dashboard_redirect( array( 'payment_submitted' => 1 ) ) : samanvay_membership_redirect( array( 'membership_step' => 'thanks' ) );
}
add_action( 'admin_post_nopriv_samanvay_submit_member_payment', 'samanvay_handle_member_payment' );
add_action( 'admin_post_samanvay_submit_member_payment', 'samanvay_handle_member_payment' );

function samanvay_expire_memberships_now() {
	global $wpdb;
	$now = current_time( 'mysql', true );
	$user_ids = $wpdb->get_col( $wpdb->prepare( 'SELECT user_id FROM ' . samanvay_members_table() . " WHERE status = 'active' AND membership_expires IS NOT NULL AND membership_expires <= %s", $now ) );
	$wpdb->query( $wpdb->prepare( 'UPDATE ' . samanvay_members_table() . " SET status = 'expired', updated_at = %s WHERE status = 'active' AND membership_expires IS NOT NULL AND membership_expires <= %s", $now, $now ) );
	foreach ( $user_ids as $user_id ) {
		update_user_meta( $user_id, 'samanvay_member_status', 'expired' );
	}
}
add_action( 'samanvay_expire_memberships', 'samanvay_expire_memberships_now' );

function samanvay_members_admin_menu() {
	add_users_page( __( 'Community Members', 'samanvay' ), __( 'Community Members', 'samanvay' ), 'list_users', 'samanvay-members', 'samanvay_render_members_admin_page' );
}
add_action( 'admin_menu', 'samanvay_members_admin_menu' );

function samanvay_handle_payment_review() {
	if ( ! current_user_can( 'list_users' ) ) {
		wp_die( esc_html__( 'You are not allowed to review payments.', 'samanvay' ), 403 );
	}
	$payment_id = isset( $_POST['payment_id'] ) ? absint( $_POST['payment_id'] ) : 0;
	check_admin_referer( 'samanvay_review_payment_' . $payment_id );
	$decision = isset( $_POST['decision'] ) ? sanitize_key( $_POST['decision'] ) : '';
	global $wpdb;
	$payment = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . samanvay_member_payments_table() . ' WHERE id = %d', $payment_id ) );
	if ( ! $payment || ! in_array( $payment->payment_type, array( 'membership', 'registration_first' ), true ) || 'pending' !== $payment->status || ! in_array( $decision, array( 'approve', 'reject' ), true ) ) {
		wp_die( esc_html__( 'Invalid payment review request.', 'samanvay' ), 400 );
	}
	$review_member = $wpdb->get_row( $wpdb->prepare( 'SELECT status FROM ' . samanvay_members_table() . ' WHERE id = %d', $payment->member_id ) );
	if ( ! $review_member || 'trashed' === $review_member->status ) { wp_die( esc_html__( 'Restore this member before reviewing payments.', 'samanvay' ) ); }
	$now = current_time( 'mysql', true );
	$status = 'approve' === $decision ? 'approved' : 'rejected';
	$wpdb->update( samanvay_member_payments_table(), array( 'status' => $status, 'reviewed_at' => $now, 'reviewed_by' => get_current_user_id() ), array( 'id' => $payment_id ), array( '%s', '%s', '%d' ), array( '%d' ) );
	if ( 'approve' === $decision ) {
		$days = max( 1, absint( samanvay_get_theme_setting( 'membership_duration' ) ) );
		$current_member = $wpdb->get_row( $wpdb->prepare( 'SELECT membership_start, membership_expires FROM ' . samanvay_members_table() . ' WHERE id = %d', $payment->member_id ) );
		$base_time = $current_member && $current_member->membership_expires && strtotime( $current_member->membership_expires . ' UTC' ) > time() ? strtotime( $current_member->membership_expires . ' UTC' ) : time();
		$expires = gmdate( 'Y-m-d H:i:s', $base_time + $days * DAY_IN_SECONDS );
		$start = $current_member && $current_member->membership_start ? $current_member->membership_start : $now;
		$wpdb->update( samanvay_members_table(), array( 'status' => 'active', 'membership_start' => $start, 'membership_expires' => $expires, 'updated_at' => $now ), array( 'id' => $payment->member_id ), array( '%s', '%s', '%s', '%s' ), array( '%d' ) );
		update_user_meta( $payment->user_id, 'samanvay_member_status', 'active' );
	} else {
		$current_member = $wpdb->get_row( $wpdb->prepare( 'SELECT status, membership_expires FROM ' . samanvay_members_table() . ' WHERE id = %d', $payment->member_id ) );
		$keep_active = $current_member && 'active' === $current_member->status && $current_member->membership_expires && strtotime( $current_member->membership_expires . ' UTC' ) > time();
		$member_status = $keep_active ? 'active' : 'payment_rejected';
		$wpdb->update( samanvay_members_table(), array( 'status' => $member_status, 'updated_at' => $now ), array( 'id' => $payment->member_id ), array( '%s', '%s' ), array( '%d' ) );
		update_user_meta( $payment->user_id, 'samanvay_member_status', $member_status );
	}
	wp_safe_redirect( add_query_arg( array( 'page' => 'samanvay-members', 'reviewed' => $status ), admin_url( 'users.php' ) ) );
	exit;
}
add_action( 'admin_post_samanvay_review_member_payment', 'samanvay_handle_payment_review' );

/** Protect administrator/staff accounts and require WordPress user-delete privileges. */
function samanvay_can_delete_community_member( $member ) {
	if ( ! samanvay_get_theme_setting( 'member_deletion_enabled' ) ) {
		return false;
	}
	if ( ! $member || ! current_user_can( 'manage_options' ) || ! current_user_can( 'delete_users' ) || (int) $member->user_id === get_current_user_id() ) {
		return false;
	}
	$user = get_userdata( $member->user_id );
	return ! $user || ( current_user_can( 'delete_user', $user->ID ) && ! is_super_admin( $user->ID ) && array( 'samanvay_member' ) === array_values( $user->roles ) );
}

function samanvay_handle_delete_community_member() {
	if ( 'POST' !== $_SERVER['REQUEST_METHOD'] ) {
		wp_die( esc_html__( 'Invalid request.', 'samanvay' ), '', array( 'response' => 405 ) );
	}
	$member_id = isset( $_POST['member_id'] ) ? absint( $_POST['member_id'] ) : 0;
	check_admin_referer( 'samanvay_delete_member_' . $member_id );
	global $wpdb;
	$member = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . samanvay_members_table() . ' WHERE id = %d', $member_id ) );
	if ( ! samanvay_can_delete_community_member( $member ) || ! isset( $_POST['confirm_delete'] ) || '1' !== $_POST['confirm_delete'] ) {
		wp_die( esc_html__( 'Deletion is not allowed or has not been confirmed.', 'samanvay' ), '', array( 'response' => 403 ) );
	}
	if ( ! samanvay_trash_member( $member ) ) {
		wp_die( esc_html__( 'Could not move member to Trash.', 'samanvay' ) );
	}
	wp_safe_redirect( add_query_arg( array( 'page' => 'samanvay-members', 'member_deleted' => 1 ), admin_url( 'users.php' ) ) );
	exit;
}
add_action( 'admin_post_samanvay_delete_community_member', 'samanvay_handle_delete_community_member' );

function samanvay_handle_bulk_delete_members() {
	if ( ! samanvay_get_theme_setting( 'member_deletion_enabled' ) ) {
		wp_die( esc_html__( 'Member deletion is disabled in Theme Settings.', 'samanvay' ), '', array( 'response' => 403 ) );
	}
	if ( 'POST' !== $_SERVER['REQUEST_METHOD'] || ! current_user_can( 'manage_options' ) || ! current_user_can( 'delete_users' ) ) {
		wp_die( esc_html__( 'You cannot delete members.', 'samanvay' ), '', array( 'response' => 403 ) );
	}
	check_admin_referer( 'samanvay_bulk_delete_members' );
	if ( ( $_POST['bulk_action'] ?? '' ) !== 'delete' || ( $_POST['confirm_delete'] ?? '' ) !== '1' ) {
		wp_die( esc_html__( 'Select Move to Trash and confirm.', 'samanvay' ) );
	}
	$raw_ids = isset( $_POST['member_ids'] ) && is_array( $_POST['member_ids'] ) ? $_POST['member_ids'] : array();
	$ids = array_values( array_unique( array_filter( array_map( 'absint', array_filter( $raw_ids, 'is_scalar' ) ) ) ) );
	if ( ! $ids ) { wp_die( esc_html__( 'Select at least one member.', 'samanvay' ) ); }
	global $wpdb;
	$deleted = 0;
	$failed = 0;
	require_once ABSPATH . 'wp-admin/includes/user.php';
	foreach ( $ids as $id ) {
		$member = $wpdb->get_row( $wpdb->prepare( 'SELECT * FROM ' . samanvay_members_table() . ' WHERE id = %d', $id ) );
		if ( ! samanvay_can_delete_community_member( $member ) ) { ++$failed; continue; }
		if ( ! samanvay_trash_member( $member ) ) { ++$failed; continue; }
		++$deleted;
	}
	wp_safe_redirect( add_query_arg( array( 'page' => 'samanvay-members', 'bulk_deleted' => $deleted, 'bulk_failed' => $failed ), admin_url( 'users.php' ) ) );
	exit;
}
add_action( 'admin_post_samanvay_bulk_delete_members', 'samanvay_handle_bulk_delete_members' );

/** Explicit testing reset; never reset the sequence during ordinary deletion. */
function samanvay_reset_member_sequence() {
	if ( 'POST' !== $_SERVER['REQUEST_METHOD'] || ! current_user_can( 'manage_options' ) || ! current_user_can( 'delete_users' ) ) {
		wp_die( esc_html__( 'You cannot reset member IDs.', 'samanvay' ), '', array( 'response' => 403 ) );
	}
	check_admin_referer( 'samanvay_reset_member_sequence' );
	if ( ( $_POST['confirm_reset'] ?? '' ) !== '1' ) {
		wp_die( esc_html__( 'Please confirm the ID reset.', 'samanvay' ) );
	}
	global $wpdb;
	$lock = 'sam_ids_' . md5( $wpdb->prefix );
	if ( '1' !== (string) $wpdb->get_var( $wpdb->prepare( 'SELECT GET_LOCK(%s, 10)', $lock ) ) ) {
		wp_die( esc_html__( 'ID allocation is busy. Please try again.', 'samanvay' ) );
	}
	$result = false;
	$message = '';
	try {
		$count = $wpdb->get_var( 'SELECT COUNT(*) FROM ' . samanvay_members_table() );
		if ( null === $count || $wpdb->last_error ) {
			$message = __( 'Unable to check existing members. Nothing was reset.', 'samanvay' );
		} elseif ( (int) $count > 0 ) {
			$message = __( 'Reset is allowed only when there are no community members.', 'samanvay' );
		} else {
			$result = false !== $wpdb->query( 'TRUNCATE TABLE ' . $wpdb->prefix . 'samanvay_member_sequence' );
			$message = __( 'Could not reset the sequence. Please try again.', 'samanvay' );
		}
	} finally {
		$wpdb->get_var( $wpdb->prepare( 'SELECT RELEASE_LOCK(%s)', $lock ) );
	}
	if ( ! $result ) { wp_die( esc_html( $message ) ); }
	wp_safe_redirect( add_query_arg( array( 'page' => 'samanvay-members', 'ids_reset' => 1 ), admin_url( 'users.php' ) ) );
	exit;
}
add_action( 'admin_post_samanvay_reset_member_sequence', 'samanvay_reset_member_sequence' );

function samanvay_render_members_admin_page() {
	if ( ! current_user_can( 'list_users' ) ) {
		return;
	}
	samanvay_expire_memberships_now();
	global $wpdb;
	$members = $wpdb->get_results( 'SELECT * FROM ' . samanvay_members_table() . " WHERE status <> 'trashed' ORDER BY created_at DESC" );
	$payments = $wpdb->get_results( 'SELECT * FROM ' . samanvay_member_payments_table() . ' ORDER BY submitted_at DESC' );
	$payment_history = array();
	foreach ( $payments as $payment ) {
		$payment_history[ (int) $payment->member_id ][] = $payment;
	}
	?>
	<div class="wrap samanvay-members-admin"><h1><?php esc_html_e( 'Community Members', 'samanvay' ); ?></h1>
	<?php if ( current_user_can( 'manage_options' ) ) : ?><p><a class="button" href="<?php echo esc_url( admin_url( 'users.php?page=samanvay-member-trash' ) ); ?>"><?php esc_html_e( 'View Trashed Members', 'samanvay' ); ?></a></p><?php endif; ?>
	<?php if ( isset( $_GET['ids_reset'] ) ) : ?><div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'ID sequence reset. The next member will receive ASATI01 and ASATI_FAMILY01.', 'samanvay' ); ?></p></div><?php endif; ?>
	<?php if ( current_user_can( 'manage_options' ) && current_user_can( 'delete_users' ) && 0 === (int) $wpdb->get_var( 'SELECT COUNT(*) FROM ' . samanvay_members_table() ) ) : ?>
	<details style="margin:16px 0"><summary><?php esc_html_e( 'Temporary setup tool: reset member and family IDs', 'samanvay' ); ?></summary>
	<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<?php wp_nonce_field( 'samanvay_reset_member_sequence' ); ?><input type="hidden" name="action" value="samanvay_reset_member_sequence">
		<p><?php esc_html_e( 'Only available when the member list is empty. Clears reserved ID numbers so the next registration starts from 01. Normal deletions never reset the sequence.', 'samanvay' ); ?></p>
		<p><label><input type="checkbox" name="confirm_reset" value="1" required> <?php esc_html_e( 'I confirm this is a fresh initialization and old member/family numbers may be reused.', 'samanvay' ); ?></label></p>
		<button type="submit" class="button"><?php esc_html_e( 'Reset IDs to 01', 'samanvay' ); ?></button>
	</form></details>
	<?php endif; ?>
	<?php if ( isset( $_GET['bulk_deleted'] ) ) : ?><div class="notice notice-info is-dismissible"><p><?php echo esc_html( sprintf( __( 'Moved to Trash: %1$d. Skipped or failed: %2$d.', 'samanvay' ), absint( $_GET['bulk_deleted'] ), absint( $_GET['bulk_failed'] ?? 0 ) ) ); ?></p></div><?php endif; ?>
	<?php if ( samanvay_get_theme_setting( 'member_deletion_enabled' ) && current_user_can( 'manage_options' ) && current_user_can( 'delete_users' ) ) : ?>
	<form id="samanvay-bulk-members" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin:16px 0">
		<?php wp_nonce_field( 'samanvay_bulk_delete_members' ); ?><input type="hidden" name="action" value="samanvay_bulk_delete_members">
		<label class="screen-reader-text" for="member-bulk-action">Bulk action</label><select id="member-bulk-action" name="bulk_action" required><option value="">Bulk actions</option><option value="delete">Move selected members to Trash</option></select>
		<label><input type="checkbox" name="confirm_delete" value="1" required> <?php esc_html_e( 'Move selected members to Trash. Their details and payment history will be retained.', 'samanvay' ); ?></label>
		<button class="button action" type="submit"><?php esc_html_e( 'Apply', 'samanvay' ); ?></button>
	</form>
	<script>document.addEventListener('DOMContentLoaded', function () { var all = document.getElementById('member-select-all'); var rows = Array.from(document.querySelectorAll('.member-bulk-checkbox:not(:disabled)')); if (!all) return; all.disabled = !rows.length; all.addEventListener('change', function () { rows.forEach(function (row) { row.checked = all.checked; }); all.indeterminate = false; }); rows.forEach(function (row) { row.addEventListener('change', function () { var count = rows.filter(function (item) { return item.checked; }).length; all.checked = count === rows.length; all.indeterminate = count > 0 && count < rows.length; }); }); document.getElementById('samanvay-bulk-members').addEventListener('submit', function (event) { if (!rows.some(function (row) { return row.checked; })) { event.preventDefault(); window.alert('Please select at least one member.'); } }); });</script>
	<?php endif; ?>
	<?php if ( isset( $_GET['member_deleted'] ) ) : ?><div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Member moved to Trash. Details and payment records have been retained.', 'samanvay' ); ?></p></div><?php endif; ?>
	<?php if ( isset( $_GET['reviewed'] ) ) : ?><div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Payment review saved.', 'samanvay' ); ?></p></div><?php endif; ?>
	<p><?php esc_html_e( 'Review member details and payment screenshots. Approval activates the account for the configured membership duration.', 'samanvay' ); ?></p>
	<table class="widefat striped"><thead><tr><?php if ( samanvay_get_theme_setting( 'member_deletion_enabled' ) ) : ?><td class="check-column"><input type="checkbox" id="member-select-all" aria-label="Select all deletable members"></td><?php endif; ?><th><?php esc_html_e( 'Member', 'samanvay' ); ?></th><th><?php esc_html_e( 'Contact / Location', 'samanvay' ); ?></th><th><?php esc_html_e( 'Membership', 'samanvay' ); ?></th><th><?php esc_html_e( 'Payment history', 'samanvay' ); ?></th></tr></thead><tbody>
	<?php if ( ! $members ) : ?><tr><td colspan="<?php echo samanvay_get_theme_setting( 'member_deletion_enabled' ) ? 5 : 4; ?>"><?php esc_html_e( 'No members found.', 'samanvay' ); ?></td></tr><?php endif; ?>
	<?php foreach ( $members as $member ) : ?>
		<?php $member_payments = isset( $payment_history[ (int) $member->id ] ) ? $payment_history[ (int) $member->id ] : array(); ?>
	<tr><?php if ( samanvay_get_theme_setting( 'member_deletion_enabled' ) ) : ?><th scope="row" class="check-column"><input type="checkbox" class="member-bulk-checkbox" name="member_ids[]" form="samanvay-bulk-members" value="<?php echo esc_attr( $member->id ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Select %s', 'samanvay' ), $member->full_name ) ); ?>" <?php disabled( ! samanvay_can_delete_community_member( $member ) ); ?>></th><?php endif; ?><td><strong><?php echo esc_html( $member->full_name ); ?></strong><br><small><?php echo esc_html( sprintf( __( 'Member ID: %1$s | Family ID: %2$s', 'samanvay' ), $member->member_code ?? '—', $member->family_code ?? '—' ) ); ?></small><br><a href="mailto:<?php echo esc_attr( $member->email ); ?>"><?php echo esc_html( $member->email ); ?></a><br><small><?php echo esc_html( $member->address . ' – ' . $member->pin_code ); ?></small><?php if ( samanvay_can_delete_community_member( $member ) ) : ?><details style="margin-top:12px"><summary><?php esc_html_e( 'Move to Trash', 'samanvay' ); ?></summary><form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><?php wp_nonce_field( 'samanvay_delete_member_' . $member->id ); ?><input type="hidden" name="action" value="samanvay_delete_community_member"><input type="hidden" name="member_id" value="<?php echo esc_attr( $member->id ); ?>"><p><?php esc_html_e( 'Moves this member to Trash and disables their login. Restore or permanently delete them from Trashed Members.', 'samanvay' ); ?></p><p><label><input type="checkbox" name="confirm_delete" value="1" required> <?php echo esc_html( sprintf( __( 'Confirm moving %s to Trash', 'samanvay' ), $member->full_name ) ); ?></label></p><button type="submit" class="button"><?php esc_html_e( 'Move member to Trash', 'samanvay' ); ?></button></form></details><?php endif; ?></td><td><?php echo esc_html( $member->mobile ); ?><br><?php echo esc_html( $member->tehsil . ', ' . $member->district . ', ' . $member->state ); ?></td><td><strong><?php echo esc_html( ucwords( str_replace( '_', ' ', $member->status ) ) ); ?></strong><?php if ( $member->membership_start ) : ?><br><small><?php echo esc_html( sprintf( __( 'Started: %s', 'samanvay' ), get_date_from_gmt( $member->membership_start, get_option( 'date_format' ) ) ) ); ?></small><?php endif; ?><?php if ( $member->membership_expires ) : ?><br><small><?php echo esc_html( sprintf( __( 'Expires: %s', 'samanvay' ), get_date_from_gmt( $member->membership_expires, get_option( 'date_format' ) ) ) ); ?></small><?php endif; ?></td><td><?php if ( $member_payments ) : ?><details class="samanvay-payment-history"<?php echo 'pending' === $member_payments[0]->status ? ' open' : ''; ?>><summary><?php echo esc_html( sprintf( _n( '%d payment', '%d payments', count( $member_payments ), 'samanvay' ), count( $member_payments ) ) ); ?> <span><?php echo esc_html( ucfirst( $member_payments[0]->status ) ); ?></span></summary><div class="samanvay-payment-history__list"><?php foreach ( $member_payments as $payment ) : ?><?php $reviewer = $payment->reviewed_by ? get_userdata( $payment->reviewed_by ) : false; ?><article><?php if ( $payment->attachment_id ) : ?><a class="samanvay-payment-history__image" href="<?php echo esc_url( wp_get_attachment_url( $payment->attachment_id ) ); ?>" target="_blank" rel="noopener"><?php echo wp_get_attachment_image( $payment->attachment_id, array( 72, 72 ) ); ?></a><?php endif; ?><div class="samanvay-payment-history__details"><strong>#<?php echo esc_html( $payment->id ); ?> · <?php echo esc_html( ucfirst( $payment->status ) ); ?></strong><small><?php echo esc_html( 'registration_first' === $payment->payment_type ? __( 'Registration + first membership fee', 'samanvay' ) : ( 'registration' === $payment->payment_type ? __( 'Registration fee', 'samanvay' ) : __( 'Membership fee', 'samanvay' ) ) ); ?><?php if ( null !== $payment->amount ) : ?> · ₹<?php echo esc_html( number_format_i18n( (float) $payment->amount, 2 ) ); ?><?php endif; ?></small><small><?php echo esc_html( sprintf( __( 'Transaction ID: %s', 'samanvay' ), $payment->transaction_id ? $payment->transaction_id : '—' ) ); ?></small><small><?php echo esc_html( sprintf( __( 'Submitted: %s', 'samanvay' ), get_date_from_gmt( $payment->submitted_at, get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) ) ) ); ?></small><?php if ( $payment->reviewed_at ) : ?><small><?php echo esc_html( sprintf( __( 'Reviewed: %1$s%2$s', 'samanvay' ), get_date_from_gmt( $payment->reviewed_at, get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) ), $reviewer ? ' · ' . $reviewer->display_name : '' ) ); ?></small><?php endif; ?><?php if ( in_array( $payment->payment_type, array( 'membership', 'registration_first' ), true ) && 'pending' === $payment->status ) : ?><form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><?php wp_nonce_field( 'samanvay_review_payment_' . $payment->id ); ?><input type="hidden" name="action" value="samanvay_review_member_payment"><input type="hidden" name="payment_id" value="<?php echo esc_attr( $payment->id ); ?>"><button class="button button-primary button-small" name="decision" value="approve"><?php esc_html_e( 'Approve', 'samanvay' ); ?></button> <button class="button button-small" name="decision" value="reject"><?php esc_html_e( 'Reject', 'samanvay' ); ?></button></form><?php endif; ?></div></article><?php endforeach; ?></div></details><?php else : esc_html_e( 'No payments submitted', 'samanvay' ); endif; ?></td></tr>
	<?php endforeach; ?></tbody></table></div>
	<style>.samanvay-members-admin table .check-column{width:2.2em}.samanvay-members-admin table th:nth-child(2){width:22%}.samanvay-members-admin table th:nth-child(3){width:20%}.samanvay-payment-history summary{cursor:pointer;font-weight:600}.samanvay-payment-history summary span{display:inline-block;margin-left:6px;padding:2px 7px;border-radius:10px;background:#f0f0f1;font-size:11px}.samanvay-payment-history__list{display:grid;gap:10px;margin-top:12px;min-width:340px}.samanvay-payment-history__list article{display:flex;gap:12px;padding:10px;border:1px solid #dcdcde;border-radius:6px;background:#fff}.samanvay-payment-history__image img{width:72px;height:72px;border-radius:4px;object-fit:cover}.samanvay-payment-history__details{display:flex;flex:1;flex-direction:column;gap:3px}.samanvay-payment-history__details small{color:#646970}.samanvay-payment-history__details form{margin-top:6px}@media(max-width:1100px){.samanvay-members-admin table{display:block;overflow-x:auto}}</style>
	<?php
}

/**
 * Render a paginated public directory of active members.
 *
 * Usage: [samanvay_active_members view="grid" per_page="12" columns="4" title="सक्रिय सदस्य"]
 *
 * @param array $atts Shortcode attributes.
 * @return string
 */
function samanvay_active_members_shortcode( $atts ) {
	$atts = shortcode_atts( array(
		'per_page' => 12,
		'columns'  => 4,
		'view'     => 'grid',
		'title'    => __( 'हमारे सक्रिय सदस्य', 'samanvay' ),
	), $atts, 'samanvay_active_members' );
	$per_page = max( 1, min( 100, absint( $atts['per_page'] ) ) );
	$columns  = max( 1, min( 4, absint( $atts['columns'] ) ) );
	$view     = 'list' === sanitize_key( $atts['view'] ) ? 'list' : 'grid';
	$page     = isset( $_GET['members_page'] ) ? max( 1, absint( $_GET['members_page'] ) ) : 1;
	$offset   = ( $page - 1 ) * $per_page;
	$now      = current_time( 'mysql', true );

	samanvay_expire_memberships_now();
	global $wpdb;
	$table = samanvay_members_table();
	$total = (int) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$table} WHERE status = 'active' AND membership_expires > %s", $now ) );
	$members = $wpdb->get_results( $wpdb->prepare( "SELECT id, member_code, full_name, state, district, tehsil, profile_picture_id, membership_start FROM {$table} WHERE status = 'active' AND membership_expires > %s ORDER BY full_name ASC LIMIT %d OFFSET %d", $now, $per_page, $offset ) );
	$heading_id = wp_unique_id( 'active-members-title-' );

	ob_start();
	?>
	<section class="active-members-directory active-members-directory--<?php echo esc_attr( $view ); ?> active-members-directory--columns-<?php echo esc_attr( $columns ); ?>" style="--member-grid-columns: <?php echo esc_attr( $columns ); ?>;"<?php echo '' !== trim( (string) $atts['title'] ) ? ' aria-labelledby="' . esc_attr( $heading_id ) . '"' : ''; ?>>
		<?php if ( '' !== trim( (string) $atts['title'] ) ) : ?><header class="active-members-directory__heading"><p><?php esc_html_e( 'सदस्य निर्देशिका', 'samanvay' ); ?></p><h2 id="<?php echo esc_attr( $heading_id ); ?>"><?php echo esc_html( $atts['title'] ); ?></h2><span><?php echo esc_html( sprintf( _n( '%s active member', '%s active members', $total, 'samanvay' ), number_format_i18n( $total ) ) ); ?></span></header><?php endif; ?>
		<?php if ( $members ) : ?>
			<div class="active-members-directory__grid">
				<?php foreach ( $members as $member ) : ?>
					<article class="active-member-card">
						<span class="active-member-card__icon"><?php if ( $member->profile_picture_id ) : echo wp_get_attachment_image( $member->profile_picture_id, 'thumbnail', false, array( 'alt' => $member->full_name ) ); else : ?><svg viewBox="0 0 64 64" aria-hidden="true"><circle cx="32" cy="23" r="12"/><path d="M12 55c1-14 8-21 20-21s19 7 20 21"/></svg><?php endif; ?></span>
						<div class="active-member-card__content"><h3><?php echo esc_html( $member->full_name ); ?></h3><small class="active-member-card__member-id"><?php echo esc_html( sprintf( __( 'सदस्य आईडी: %s', 'samanvay' ), $member->member_code ?? '—' ) ); ?></small><p><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg><span><?php echo esc_html( implode( ', ', array_filter( array( $member->tehsil, $member->district, $member->state ) ) ) ); ?></span></p><?php if ( $member->membership_start ) : ?><small><?php echo esc_html( sprintf( __( 'Member since %s', 'samanvay' ), get_date_from_gmt( $member->membership_start, 'F Y' ) ) ); ?></small><?php endif; ?></div>
					</article>
				<?php endforeach; ?>
			</div>
			<?php
			$total_pages = (int) ceil( $total / $per_page );
			if ( $total_pages > 1 ) :
				$base = remove_query_arg( 'members_page' );
				?>
				<nav class="active-members-directory__pagination" aria-label="<?php esc_attr_e( 'Member directory pages', 'samanvay' ); ?>"><?php echo wp_kses_post( paginate_links( array( 'base' => esc_url_raw( add_query_arg( 'members_page', '%#%', $base ) ), 'format' => '', 'current' => $page, 'total' => $total_pages, 'prev_text' => '←', 'next_text' => '→' ) ) ); ?></nav>
			<?php endif; ?>
		<?php else : ?>
			<div class="active-members-directory__empty"><svg viewBox="0 0 64 64" aria-hidden="true"><circle cx="24" cy="22" r="10"/><circle cx="45" cy="25" r="8"/><path d="M6 55c1-14 7-22 18-22s18 8 19 22M39 36c10 0 16 7 17 19"/></svg><h3><?php esc_html_e( 'No active members found', 'samanvay' ); ?></h3><p><?php esc_html_e( 'Approved members will appear here automatically.', 'samanvay' ); ?></p></div>
		<?php endif; ?>
	</section>
	<?php
	return ob_get_clean();
}
add_shortcode( 'samanvay_active_members', 'samanvay_active_members_shortcode' );

/** Change only the signed-in member's password, regardless of membership status. */
function samanvay_handle_member_change_password() {
	$user = wp_get_current_user();
	if ( ! $user->exists() || ! samanvay_get_member_by_user( $user->ID ) ) {
		wp_safe_redirect( samanvay_get_member_login_url() );
		exit;
	}
	check_admin_referer( 'samanvay_change_member_password', 'samanvay_password_nonce' );
	$values = array();
	foreach ( array( 'current_password', 'new_password', 'confirm_password' ) as $key ) {
		$values[ $key ] = isset( $_POST[ $key ] ) && is_string( $_POST[ $key ] ) ? wp_unslash( $_POST[ $key ] ) : '';
	}
	if ( ! wp_check_password( $values['current_password'], $user->user_pass, $user->ID ) ) {
		samanvay_dashboard_redirect( array( 'dashboard_error' => 'current_password' ) );
	}
	if ( strlen( $values['new_password'] ) < 8 || strlen( $values['new_password'] ) > 4096 || ! hash_equals( $values['new_password'], $values['confirm_password'] ) ) {
		samanvay_dashboard_redirect( array( 'dashboard_error' => 'new_password' ) );
	}
	if ( wp_check_password( $values['new_password'], $user->user_pass, $user->ID ) ) {
		samanvay_dashboard_redirect( array( 'dashboard_error' => 'password_unchanged' ) );
	}
	$result = wp_update_user( array( 'ID' => $user->ID, 'user_pass' => $values['new_password'] ) );
	if ( is_wp_error( $result ) ) {
		samanvay_dashboard_redirect( array( 'dashboard_error' => 'password_update_failed' ) );
	}
	delete_user_meta( $user->ID, 'samanvay_initial_password_pending' );
	samanvay_dashboard_redirect( array( 'password_updated' => 1 ) );
}
add_action( 'admin_post_samanvay_change_member_password', 'samanvay_handle_member_change_password' );
