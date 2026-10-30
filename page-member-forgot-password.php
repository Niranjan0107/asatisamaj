<?php
/**
 * Template Name: Member Forgot Password
 * Template Post Type: page
 *
 * @package Samanvay
 */

if ( is_user_logged_in() && samanvay_get_member_by_user( get_current_user_id() ) ) {
	wp_safe_redirect( samanvay_get_member_dashboard_url() );
	exit;
}
get_header();
?>
<main id="primary" class="member-portal member-login-page">
	<section class="membership-hero"><div class="site-container"><p class="membership-eyebrow"><?php esc_html_e( 'सदस्य सहायता', 'samanvay' ); ?></p><h1><?php esc_html_e( 'पासवर्ड भूल गए?', 'samanvay' ); ?></h1><p><?php esc_html_e( 'अपना पंजीकृत ईमेल या यूज़रनेम दर्ज करें। हम आपको सुरक्षित रीसेट लिंक भेजेंगे।', 'samanvay' ); ?></p></div></section>
	<section class="member-login-section"><div class="site-container"><div class="member-login-card member-recovery-card"><span class="member-login-card__icon"><svg viewBox="0 0 64 64" aria-hidden="true"><rect x="13" y="27" width="38" height="29" rx="6"/><path d="M21 27v-8a11 11 0 0 1 22 0v8M32 39v7"/></svg></span><h2><?php esc_html_e( 'रीसेट लिंक प्राप्त करें', 'samanvay' ); ?></h2>
	<?php if ( isset( $_GET['reset_requested'] ) ) : ?><div class="membership-alert member-alert--success" role="status"><?php esc_html_e( 'यदि यह सदस्य खाता मौजूद है, तो रीसेट लिंक ईमेल कर दिया गया है। कृपया इनबॉक्स और स्पैम फ़ोल्डर देखें।', 'samanvay' ); ?></div><?php endif; ?><?php if ( isset( $_GET['email_error'] ) ) : ?><div class="membership-alert membership-alert--error" role="alert"><?php esc_html_e( 'रीसेट ईमेल अभी भेजा नहीं जा सका। कृपया साइट की SMTP/ईमेल सेटिंग जाँचें और दोबारा प्रयास करें।', 'samanvay' ); ?></div><?php endif; ?><?php if ( isset( $_GET['reset_error'] ) ) : ?><div class="membership-alert membership-alert--error" role="alert"><?php esc_html_e( 'रीसेट लिंक अमान्य या समाप्त हो गया है। कृपया नया लिंक माँगें।', 'samanvay' ); ?></div><?php endif; ?>
	<form class="member-login-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><input type="hidden" name="action" value="samanvay_member_forgot_password"><?php wp_nonce_field( 'samanvay_member_forgot_password', 'samanvay_forgot_nonce' ); ?><p><label for="forgot-identity"><?php esc_html_e( 'ईमेल या यूज़रनेम', 'samanvay' ); ?></label><input id="forgot-identity" type="text" name="member_identity" autocomplete="username" required></p><button class="membership-submit" type="submit"><?php esc_html_e( 'रीसेट लिंक भेजें', 'samanvay' ); ?> <span>→</span></button></form><p class="member-login-card__footer"><a href="<?php echo esc_url( samanvay_get_member_login_url() ); ?>">← <?php esc_html_e( 'लॉगिन पर वापस जाएँ', 'samanvay' ); ?></a></p></div></div></section>
</main>
<?php get_footer(); ?>
