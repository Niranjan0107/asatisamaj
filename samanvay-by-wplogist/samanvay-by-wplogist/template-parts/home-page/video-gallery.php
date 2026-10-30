<?php
/** Homepage YouTube video gallery. @package Samanvay */
$video_urls = samanvay_get_theme_setting( 'youtube_videos' );
$videos     = array();
foreach ( (array) $video_urls as $video_url ) {
	$video_id = samanvay_get_youtube_id( $video_url );
	if ( $video_id ) {
		$videos[] = $video_id;
	}
}
if ( ! $videos ) {
	return;
}
?>
<section class="home-videos" aria-labelledby="home-videos-title">
	<div class="site-container">
		<header class="home-videos__heading">
			<svg viewBox="0 0 54 18" aria-hidden="true"><path d="M2 10c5-8 10 8 15 0s10 8 15 0 10 8 20 0"/></svg>
			<h2 id="home-videos-title"><?php esc_html_e( 'वीडियो गैलरी', 'samanvay' ); ?></h2>
			<svg viewBox="0 0 54 18" aria-hidden="true"><path d="M2 10c5-8 10 8 15 0s10 8 15 0 10 8 20 0"/></svg>
		</header>
		<div class="home-videos__grid">
			<?php foreach ( $videos as $index => $video_id ) : ?>
				<button class="home-videos__item" type="button" data-youtube-id="<?php echo esc_attr( $video_id ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Play video %d', 'samanvay' ), $index + 1 ) ); ?>">
					<img src="<?php echo esc_url( 'https://i.ytimg.com/vi/' . $video_id . '/hqdefault.jpg' ); ?>" alt="" loading="lazy">
					<span class="home-videos__play"><svg aria-hidden="true" viewBox="0 0 64 64"><circle cx="32" cy="32" r="28"/><path d="m27 21 18 11-18 11V21Z"/></svg></span>
				</button>
			<?php endforeach; ?>
		</div>
	</div>
	<div class="home-video-modal" hidden role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'YouTube video player', 'samanvay' ); ?>">
		<div class="home-video-modal__backdrop" data-video-close></div>
		<div class="home-video-modal__dialog">
			<button class="home-video-modal__close" type="button" data-video-close aria-label="<?php esc_attr_e( 'Close video', 'samanvay' ); ?>"><svg aria-hidden="true" viewBox="0 0 24 24"><path d="m5 5 14 14M19 5 5 19"/></svg></button>
			<div class="home-video-modal__frame"></div>
		</div>
	</div>
</section>
