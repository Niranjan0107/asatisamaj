<?php
/**
 * Template Name: Member Login
 * Template Post Type: page
 *
 * @package Samanvay
 */

if ( is_user_logged_in() && samanvay_get_member_by_user( get_current_user_id() ) ) {
	wp_safe_redirect( samanvay_get_member_dashboard_url() );
	exit;
}
get_header();
$login_error = isset( $_GET['login_error'] ) ? sanitize_key( wp_unslash( $_GET['login_error'] ) ) : '';
?>
<main id="primary" class="member-portal member-login-page">
	<section class="membership-hero"><div class="site-container"><p class="membership-eyebrow"><?php esc_html_e( 'सदस्य पोर्टल', 'samanvay' ); ?></p><h1><?php esc_html_e( 'सदस्य लॉगिन', 'samanvay' ); ?></h1><p><?php esc_html_e( 'अपनी प्रोफ़ाइल, सदस्यता और भुगतान की जानकारी प्रबंधित करने के लिए लॉगिन करें।', 'samanvay' ); ?></p></div></section>
	<section class="member-login-section"><div class="site-container"><div class="member-login-card">
		<span class="member-login-card__icon"><svg viewBox="0 0 64 64" aria-hidden="true"><circle cx="32" cy="21" r="12"/><path d="M11 55c1-15 8-23 21-23s20 8 21 23"/></svg></span><h2><?php esc_html_e( 'अपने खाते में प्रवेश करें', 'samanvay' ); ?></h2>
		<?php if ( isset( $_GET['password_reset'] ) ) : ?><div class="membership-alert member-alert--success" role="status"><?php esc_html_e( 'आपका पासवर्ड बदल गया है। अब नए पासवर्ड से लॉगिन करें।', 'samanvay' ); ?></div><?php endif; ?><?php if ( $login_error ) : ?><div class="membership-alert membership-alert--error" role="alert"><?php esc_html_e( 'ईमेल/यूज़रनेम या पासवर्ड सही नहीं है।', 'samanvay' ); ?></div><?php endif; ?>
		<form class="member-login-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"><input type="hidden" name="action" value="samanvay_member_login"><?php wp_nonce_field( 'samanvay_member_login', 'samanvay_login_nonce' ); ?><p><label for="member-identity"><?php esc_html_e( 'ईमेल या यूज़रनेम', 'samanvay' ); ?></label><input id="member-identity" type="text" name="member_identity" autocomplete="username" required></p><p><label for="login-password"><?php esc_html_e( 'पासवर्ड', 'samanvay' ); ?></label><input id="login-password" type="password" name="member_password" autocomplete="current-password" required></p><label class="member-login-remember"><input type="checkbox" name="remember" value="1"> <?php esc_html_e( 'मुझे याद रखें', 'samanvay' ); ?></label><button class="membership-submit" type="submit"><?php esc_html_e( 'लॉगिन करें', 'samanvay' ); ?> <span>→</span></button></form>
		<p class="member-login-card__footer"><a href="<?php echo esc_url( samanvay_get_member_forgot_password_url() ); ?>"><?php esc_html_e( 'पासवर्ड भूल गए?', 'samanvay' ); ?></a><span>·</span><a href="<?php echo esc_url( samanvay_get_registration_page_url() ); ?>"><?php esc_html_e( 'नया पंजीकरण', 'samanvay' ); ?></a></p>
	</div></div></section>
</main>
<?php get_footer(); ?>
