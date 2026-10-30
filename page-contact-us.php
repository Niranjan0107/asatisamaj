<?php
/**
 * Template Name: Contact Us
 * Template Post Type: page
 *
 * @package Samanvay
 */

get_header();
$phone   = samanvay_get_theme_setting( 'phone' );
$email   = samanvay_get_theme_setting( 'email' );
$address = samanvay_get_theme_setting( 'address' );
$website = samanvay_get_theme_setting( 'website' );
?>
<main id="primary" class="contact-page">
	<section class="contact-page__hero" aria-labelledby="contact-page-title">
		<div class="site-container">
			<p class="contact-page__eyebrow"><?php esc_html_e( 'संपर्क करें', 'samanvay' ); ?></p>
			<h1 id="contact-page-title"><?php esc_html_e( 'हम आपसे जुड़ना चाहते हैं', 'samanvay' ); ?></h1>
			<p><?php esc_html_e( 'समाज, सदस्यता, सहयोग या हमारी गतिविधियों से जुड़ी जानकारी के लिए हमें संदेश भेजें।', 'samanvay' ); ?></p>
		</div>
	</section>

	<section class="contact-page__main" aria-labelledby="contact-form-title">
		<div class="site-container contact-page__layout">
			<div class="contact-page__information">
				<p class="contact-page__eyebrow"><?php esc_html_e( 'हमारे संपर्क विवरण', 'samanvay' ); ?></p>
				<h2><?php esc_html_e( 'आइए संवाद शुरू करें', 'samanvay' ); ?></h2>
				<p class="contact-page__lead"><?php esc_html_e( 'आपके प्रश्न, सुझाव और सहयोग के विचार हमारे लिए महत्वपूर्ण हैं। सुविधानुसार किसी भी माध्यम से संपर्क करें।', 'samanvay' ); ?></p>
				<div class="contact-page__details">
					<article><span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg></span><div><h3><?php esc_html_e( 'पता', 'samanvay' ); ?></h3><p><?php echo nl2br( esc_html( $address ) ); ?></p></div></article>
					<article><span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 3h4l2 5-3 2c2 4 4 6 8 8l2-3 5 2v4c0 1-1 2-2 2C10 22 2 14 2 5c0-1 1-2 3-2Z"/></svg></span><div><h3><?php esc_html_e( 'फोन', 'samanvay' ); ?></h3><a href="tel:<?php echo esc_attr( preg_replace( '/[^0-9+]/', '', $phone ) ); ?>"><?php echo esc_html( $phone ); ?></a></div></article>
					<article><span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M3 5h18v14H3V5Z"/><path d="m3 6 9 7 9-7"/></svg></span><div><h3><?php esc_html_e( 'ईमेल', 'samanvay' ); ?></h3><a href="mailto:<?php echo esc_attr( $email ); ?>"><?php echo esc_html( $email ); ?></a></div></article>
					<article><span><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c3 3 4 6 4 9s-1 6-4 9c-3-3-4-6-4-9s1-6 4-9Z"/></svg></span><div><h3><?php esc_html_e( 'वेबसाइट', 'samanvay' ); ?></h3><a href="<?php echo esc_url( $website ); ?>"><?php echo esc_html( preg_replace( '#^https?://#', '', untrailingslashit( $website ) ) ); ?></a></div></article>
				</div>
			</div>

			<div class="contact-page__form-card">
				<header><p class="contact-page__eyebrow"><?php esc_html_e( 'संदेश भेजें', 'samanvay' ); ?></p><h2 id="contact-form-title"><?php esc_html_e( 'हमसे संपर्क करें', 'samanvay' ); ?></h2><p><?php esc_html_e( 'नीचे अपनी जानकारी भरें। यह फॉर्म अभी केवल डिज़ाइन हेतु है।', 'samanvay' ); ?></p></header>
				<div class="contact-page__form">
				<?php echo do_shortcode('[wplogist_form id="1"]'); ?>
				</div>
				
			</div>
		</div>
	</section>

	<?php while ( have_posts() ) : the_post(); if ( trim( get_the_content() ) ) : ?>
		<section class="contact-page__editor"><div class="site-container entry-content"><?php the_content(); wp_link_pages(); ?></div></section>
	<?php endif; endwhile; ?>
</main>
<?php get_footer(); ?>
