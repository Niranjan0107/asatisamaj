<?php
/** Site footer. @package Samanvay */
$footer_phone   = samanvay_get_theme_setting( 'phone' );
$footer_email   = samanvay_get_theme_setting( 'email' );
$footer_website = samanvay_get_theme_setting( 'website' );
$footer_logo_id = absint( samanvay_get_theme_setting( 'footer_logo' ) );
$social_links   = array(
	'facebook'  => samanvay_get_theme_setting( 'facebook' ),
	'instagram' => samanvay_get_theme_setting( 'instagram' ),
	'youtube'   => samanvay_get_theme_setting( 'youtube' ),
	'whatsapp'  => samanvay_get_theme_setting( 'whatsapp' ),
);
?>
<footer class="site-footer" id="community">
	<div class="site-container footer-shell">
		<section class="footer-join" aria-labelledby="footer-join-title">
			<svg class="footer-join__pattern footer-join__pattern--left" aria-hidden="true" viewBox="0 0 160 160"><circle cx="80" cy="80" r="67"/><circle cx="80" cy="80" r="42"/><path d="M80 5c10 21 10 36 0 48-10-12-10-27 0-48Zm0 150c-10-21-10-36 0-48 10 12 10 27 0 48ZM5 80c21-10 36-10 48 0-12 10-27 10-48 0Zm150 0c-21 10-36 10-48 0 12-10 27-10 48 0Z"/></svg>
			<div class="footer-join__icon"><svg aria-hidden="true" viewBox="0 0 64 54"><circle cx="22" cy="14" r="8"/><circle cx="43" cy="14" r="8"/><circle cx="10" cy="20" r="5"/><circle cx="54" cy="20" r="5"/><path d="M8 45v-8c0-7 5-12 12-12h24c7 0 12 5 12 12v8M20 29v17h24V29M2 44V33c0-5 4-8 9-8M62 44V33c0-5-4-8-9-8"/></svg></div>
			<div class="footer-join__copy"><h2 id="footer-join-title"><?php echo esc_html( samanvay_get_theme_setting( 'footer_cta_title' ) ); ?></h2><p><?php echo esc_html( samanvay_get_theme_setting( 'footer_cta_text' ) ); ?></p></div>
			<a class="footer-join__button" href="<?php echo esc_url( samanvay_get_registration_page_url() ); ?>"><?php echo esc_html( samanvay_get_theme_setting( 'footer_btn_text' ) ); ?> <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
			<svg class="footer-join__pattern footer-join__pattern--right" aria-hidden="true" viewBox="0 0 160 160"><circle cx="80" cy="80" r="67"/><circle cx="80" cy="80" r="42"/><path d="M80 5c10 21 10 36 0 48-10-12-10-27 0-48Zm0 150c-10-21-10-36 0-48 10 12 10 27 0 48ZM5 80c21-10 36-10 48 0-12 10-27 10-48 0Zm150 0c-21 10-36 10-48 0 12-10 27-10 48 0Z"/></svg>
		</section>

		<div class="footer-grid">
			<div class="footer-about">
				<?php if ( $footer_logo_id ) : ?>
					<a class="footer-custom-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"><?php echo wp_get_attachment_image( $footer_logo_id, 'medium', false, array( 'class' => 'footer-logo-image' ) ); ?></a>
				<?php else : ?>
					<a class="footer-brand" href="<?php echo esc_url( home_url( '/' ) ); ?>"><span>आपका</span><strong>लोगों</strong></a>
				<?php endif; ?>
				<p><?php echo nl2br( esc_html( samanvay_get_theme_setting( 'footer_about' ) ) ); ?></p>
				<div class="footer-socials"><a href="<?php echo esc_url( $social_links['facebook'] ); ?>" aria-label="Facebook"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 8h3V4h-3c-3 0-5 2-5 5v3H6v4h3v6h4v-6h3l1-4h-4V9c0-1 .4-1 1-1Z"/></svg></a><a href="<?php echo esc_url( $social_links['instagram'] ); ?>" aria-label="Instagram"><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r="1"/></svg></a><a href="<?php echo esc_url( $social_links['youtube'] ); ?>" aria-label="YouTube"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M21 8s0-3-3-3H6C3 5 3 8 3 8v8s0 3 3 3h12c3 0 3-3 3-3V8Z"/><path class="social-play" d="m10 9 5 3-5 3V9Z"/></svg></a><a href="<?php echo esc_url( $social_links['whatsapp'] ); ?>" aria-label="WhatsApp"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 11a8 8 0 0 1-12 7l-5 2 2-5A8 8 0 1 1 20 11Z"/><path d="M8 7c1 4 3 6 7 8"/></svg></a></div>
			</div>
			<nav class="footer-column" aria-label="त्वरित लिंक"><h3>त्वरित लिंक</h3><ul><li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">होम</a></li><li><a href="<?php echo esc_url( home_url( '/about/' ) ); ?>">हमारे बारे में</a></li><li><a href="<?php echo esc_url( home_url( '/community/' ) ); ?>">हमारा समुदाय</a></li><li><a href="<?php echo esc_url( home_url( '/our-work/' ) ); ?>">हमारे कार्य</a></li><li><a href="<?php echo esc_url( home_url( '/events/' ) ); ?>">कार्यक्रम</a></li><li><a href="<?php echo esc_url( home_url( '/gallery/' ) ); ?>">गैलरी</a></li><li><a href="<?php echo esc_url( home_url( '/contact/' ) ); ?>">संपर्क करें</a></li></ul></nav>
			<div class="footer-column"><h3>हमारी पहल</h3><ul><li>शिक्षा सहयोग</li><li>महिला सशक्तिकरण</li><li>स्वास्थ्य एवं कल्याण</li><li>युवा एवं रोजगार</li><li>सांस्कृतिक संरक्षण</li><li>जरूरतमंद सहायता</li></ul></div>
			<div class="footer-column footer-contact"><h3>संपर्क जानकारी</h3><p><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg><span><?php echo nl2br( esc_html( samanvay_get_theme_setting( 'address' ) ) ); ?></span></p><p><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M5 3h4l2 5-3 2c2 4 4 6 8 8l2-3 5 2v4c0 1-1 2-2 2C10 22 2 14 2 5c0-1 1-2 3-2Z"/></svg><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $footer_phone ) ); ?>"><?php echo esc_html( $footer_phone ); ?></a></p><p><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M3 5h18v14H3V5Z"/><path d="m3 6 9 7 9-7"/></svg><a href="mailto:<?php echo esc_attr( $footer_email ); ?>"><?php echo esc_html( $footer_email ); ?></a></p><p><svg aria-hidden="true" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c3 3 4 6 4 9s-1 6-4 9c-3-3-4-6-4-9s1-6 4-9Z"/></svg><a href="<?php echo esc_url( $footer_website ); ?>"><?php echo esc_html( preg_replace( '#^https?://#', '', untrailingslashit( $footer_website ) ) ); ?></a></p></div>
			<div class="footer-column footer-news"><h3>नवाचार पत्रिका</h3><p>हमारे कार्यों की जानकारी और आगामी कार्यक्रमों की अपडेट सीधे पाएं।</p><form action="#" method="post"><label class="screen-reader-text" for="footer-email">अपना ईमेल दर्ज करें</label><input id="footer-email" type="email" name="email" placeholder="अपना ईमेल दर्ज करें" required><button type="submit">सब्सक्राइब करें</button></form></div>
		</div>
		<div class="footer-bottom"><span>&copy; <?php echo esc_html( gmdate( 'Y' ) . ' ' . samanvay_get_theme_setting( 'copyright_text' ) ); ?></span><span><a href="#">गोपनीयता नीति</a><i></i><a href="#">उपयोग की शर्तें</a></span></div>
	</div>
</footer>
<?php wp_footer(); ?>
</body>
</html>
