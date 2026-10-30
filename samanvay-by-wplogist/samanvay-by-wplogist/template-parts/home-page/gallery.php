<?php
/**
 * Homepage gallery strip.
 *
 * @package Samanvay
 */

$managed_ids   = array_slice( array_values( array_filter( array_map( 'absint', (array) samanvay_get_theme_setting( 'gallery_images' ) ), 'wp_attachment_is_image' ) ), 0, 5 );
$gallery_image = get_template_directory_uri() . '/assets/images/community-gallery-strip.png';
$gallery_items = array(
	__( 'बच्चों के लिए शिक्षा सहयोग', 'samanvay' ),
	__( 'युवा स्वयंसेवकों का समुदाय', 'samanvay' ),
	__( 'निःशुल्क स्वास्थ्य सेवा', 'samanvay' ),
	__( 'महिला समुदाय संवाद', 'samanvay' ),
	__( 'पर्यावरण संरक्षण अभियान', 'samanvay' ),
);
?>
<section class="home-gallery" aria-labelledby="home-gallery-title">
	<div class="home-gallery__inner">
		<header class="home-gallery__heading">
			<svg viewBox="0 0 54 18" aria-hidden="true"><path d="M2 10c5-8 10 8 15 0s10 8 15 0 10 8 20 0"/></svg>
			<h2 id="home-gallery-title"><?php esc_html_e( 'हमारी झलक', 'samanvay' ); ?></h2>
			<svg viewBox="0 0 54 18" aria-hidden="true"><path d="M2 10c5-8 10 8 15 0s10 8 15 0 10 8 20 0"/></svg>
		</header>

		<div class="home-gallery__grid<?php echo $managed_ids ? ' home-gallery__grid--managed' : ''; ?>">
			<?php if ( $managed_ids ) : ?>
				<?php foreach ( $managed_ids as $image_id ) : ?>
					<?php $image_url = wp_get_attachment_image_url( $image_id, 'large' ); if ( ! $image_url ) { continue; } $image_alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true ); ?>
					<figure class="home-gallery__item"><span class="home-gallery__photo home-gallery__photo--managed" style="background-image:url('<?php echo esc_url( $image_url ); ?>')" role="img" aria-label="<?php echo esc_attr( $image_alt ? $image_alt : get_the_title( $image_id ) ); ?>"></span></figure>
				<?php endforeach; ?>
			<?php else : ?>
				<?php foreach ( $gallery_items as $index => $label ) : ?>
					<figure class="home-gallery__item home-gallery__item--<?php echo esc_attr( $index + 1 ); ?>"><span class="home-gallery__photo" style="background-image:url('<?php echo esc_url( $gallery_image ); ?>')" role="img" aria-label="<?php echo esc_attr( $label ); ?>"></span></figure>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
		<div class="home-gallery__action"><a href="<?php echo esc_url( home_url( '/gallery/' ) ); ?>"><?php esc_html_e( 'पूरी गैलरी देखें', 'samanvay' ); ?><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a></div>
	</div>
</section>
