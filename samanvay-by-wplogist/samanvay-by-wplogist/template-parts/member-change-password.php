<?php
/** Member dashboard password form. @package Samanvay */
if ( ! defined( 'ABSPATH' ) ) { exit; }
?>
<section class="member-dashboard-card" id="change-password">
	<header><h2><?php esc_html_e( 'पासवर्ड बदलें', 'samanvay' ); ?></h2></header>
	<form class="membership-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
		<input type="hidden" name="action" value="samanvay_change_member_password">
		<?php wp_nonce_field( 'samanvay_change_member_password', 'samanvay_password_nonce' ); ?>
		<div class="membership-grid">
		<?php foreach ( array( 'current_password' => 'वर्तमान पासवर्ड', 'new_password' => 'नया पासवर्ड', 'confirm_password' => 'नए पासवर्ड की पुष्टि करें' ) as $key => $label ) : ?>
			<p class="membership-field<?php echo 'current_password' === $key ? ' membership-field--wide' : ''; ?>"><label for="dashboard-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $label ); ?> *</label><span class="membership-password"><input id="dashboard-<?php echo esc_attr( $key ); ?>" name="<?php echo esc_attr( $key ); ?>" type="password" autocomplete="<?php echo 'current_password' === $key ? 'current-password' : 'new-password'; ?>" <?php echo 'current_password' !== $key ? 'minlength="8"' : ''; ?> maxlength="4096" required><button type="button" class="membership-password-toggle" aria-label="<?php esc_attr_e( 'Show password', 'samanvay' ); ?>" aria-pressed="false"><svg viewBox="0 0 24 24" aria-hidden="true"><path class="password-eye" d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/><circle class="password-eye" cx="12" cy="12" r="2.8"/><path class="password-eye-slash" d="M4 4 20 20"/></svg></button></span></p>
		<?php endforeach; ?>
		</div>
		<p class="membership-note"><?php esc_html_e( 'नया पासवर्ड कम से कम 8 अक्षरों का रखें।', 'samanvay' ); ?></p>
		<button type="submit" class="membership-submit"><?php esc_html_e( 'पासवर्ड अपडेट करें', 'samanvay' ); ?></button>
	</form>
</section>
