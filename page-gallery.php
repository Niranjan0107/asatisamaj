<?php
/**
 * Template Name: Gallery
 * Template Post Type: page
 *
 * @package Samanvay
 */

get_header();
$image_ids = array_values( array_filter( array_map( 'absint', (array) samanvay_get_theme_setting( 'gallery_images' ) ), 'wp_attachment_is_image' ) );
$videos    = array();
foreach ( (array) samanvay_get_theme_setting( 'youtube_videos' ) as $video_url ) {
	$video_id = samanvay_get_youtube_id( $video_url );
	if ( $video_id ) {
		$videos[] = $video_id;
	}
}
?>
<main id="primary" class="gallery-page">
	<section class="gallery-page__hero" aria-labelledby="gallery-page-title">
		<div class="site-container"><p class="gallery-page__eyebrow"><?php esc_html_e( 'हमारी यादें', 'samanvay' ); ?></p><h1 id="gallery-page-title"><?php esc_html_e( 'फोटो एवं वीडियो गैलरी', 'samanvay' ); ?></h1><p><?php esc_html_e( 'समाज की गतिविधियों, सेवा कार्यों, आयोजनों और यादगार पलों की झलकियाँ देखें।', 'samanvay' ); ?></p></div>
	</section>

	<?php if ( $image_ids ) : ?>
		<section class="gallery-page__section gallery-page__photos" aria-labelledby="gallery-photos-title">
			<div class="site-container"><header class="gallery-page__heading"><svg viewBox="0 0 54 18" aria-hidden="true"><path d="M2 10c5-8 10 8 15 0s10 8 15 0 10 8 20 0"/></svg><div><p><?php esc_html_e( 'चित्रों में हमारी यात्रा', 'samanvay' ); ?></p><h2 id="gallery-photos-title"><?php esc_html_e( 'फोटो गैलरी', 'samanvay' ); ?></h2></div><svg viewBox="0 0 54 18" aria-hidden="true"><path d="M2 10c5-8 10 8 15 0s10 8 15 0 10 8 20 0"/></svg></header>
				<div class="gallery-page__grid">
					<?php foreach ( $image_ids as $image_id ) : ?>
						<?php $full_url = wp_get_attachment_image_url( $image_id, 'full' ); if ( ! $full_url ) { continue; } $alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true ); $label = $alt ? $alt : get_the_title( $image_id ); ?>
						<button class="gallery-page__image" type="button" data-gallery-image="<?php echo esc_url( $full_url ); ?>" data-gallery-alt="<?php echo esc_attr( $label ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Open image: %s', 'samanvay' ), $label ) ); ?>"><?php echo wp_get_attachment_image( $image_id, 'large', false, array( 'loading' => 'lazy' ) ); ?><span><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="m16 16 5 5M8 11h6M11 8v6"/></svg></span></button>
					<?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $videos ) : ?>
		<section class="gallery-page__section gallery-page__videos" aria-labelledby="gallery-videos-title">
			<div class="site-container"><header class="gallery-page__heading"><svg viewBox="0 0 54 18" aria-hidden="true"><path d="M2 10c5-8 10 8 15 0s10 8 15 0 10 8 20 0"/></svg><div><p><?php esc_html_e( 'गतिविधियाँ और आयोजन', 'samanvay' ); ?></p><h2 id="gallery-videos-title"><?php esc_html_e( 'वीडियो गैलरी', 'samanvay' ); ?></h2></div><svg viewBox="0 0 54 18" aria-hidden="true"><path d="M2 10c5-8 10 8 15 0s10 8 15 0 10 8 20 0"/></svg></header>
				<div class="gallery-page__video-grid">
					<?php foreach ( $videos as $index => $video_id ) : ?><button class="gallery-page__video" type="button" data-gallery-video="<?php echo esc_attr( $video_id ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Play video %d', 'samanvay' ), $index + 1 ) ); ?>"><img src="<?php echo esc_url( 'https://i.ytimg.com/vi/' . $video_id . '/hqdefault.jpg' ); ?>" alt="" loading="lazy"><span><svg viewBox="0 0 64 64" aria-hidden="true"><circle cx="32" cy="32" r="28"/><path d="m27 21 18 11-18 11V21Z"/></svg></span></button><?php endforeach; ?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( ! $image_ids && ! $videos ) : ?><section class="gallery-page__empty"><div class="site-container"><svg viewBox="0 0 64 54" aria-hidden="true"><path d="M5 7h54v40H5zM12 38l12-13 9 9 7-7 12 11M17 17h.01"/></svg><h2><?php esc_html_e( 'गैलरी जल्द उपलब्ध होगी', 'samanvay' ); ?></h2><p><?php esc_html_e( 'नई तस्वीरें और वीडियो शीघ्र जोड़े जाएंगे।', 'samanvay' ); ?></p></div></section><?php endif; ?>

	<div class="gallery-lightbox" hidden role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Gallery viewer', 'samanvay' ); ?>"><button class="gallery-lightbox__backdrop" type="button" data-gallery-close aria-label="<?php esc_attr_e( 'Close gallery viewer', 'samanvay' ); ?>"></button><div class="gallery-lightbox__dialog"><button class="gallery-lightbox__close" type="button" data-gallery-close aria-label="<?php esc_attr_e( 'Close', 'samanvay' ); ?>"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m5 5 14 14M19 5 5 19"/></svg></button><div class="gallery-lightbox__content"></div></div></div>

	<?php while ( have_posts() ) : the_post(); if ( trim( get_the_content() ) ) : ?><section class="gallery-page__editor"><div class="site-container entry-content"><?php the_content(); wp_link_pages(); ?></div></section><?php endif; endwhile; ?>
</main>
<?php get_footer(); ?>
