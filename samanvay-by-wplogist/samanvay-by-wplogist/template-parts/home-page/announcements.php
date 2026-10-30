<?php
/**
 * Homepage notices and announcements section.
 *
 * @package Samanvay
 */

$announcement_query = new WP_Query( array(
	'post_type'           => 'announcement',
	'posts_per_page'      => 5,
	'ignore_sticky_posts' => true,
) );
$archive_url = get_post_type_archive_link( 'announcement' );
?>
<section class="home-announcements" aria-labelledby="home-announcements-title">
	<div class="site-container home-announcements__layout">
		<header class="home-announcements__intro">
			<span class="home-announcements__intro-icon"><svg aria-hidden="true" viewBox="0 0 64 64"><path d="M10 38V25l34-13v39L10 38Z"/><path d="M44 24h8c3 0 5 2 5 5v5c0 3-2 5-5 5h-8M14 40l4 13h11l-5-16M8 25H4v13h6"/></svg></span>
			<p><?php esc_html_e( 'महत्वपूर्ण जानकारी', 'samanvay' ); ?></p>
			<h2 id="home-announcements-title"><?php esc_html_e( 'सूचनाएँ / घोषणाएँ', 'samanvay' ); ?></h2>
			<span class="home-announcements__rule"></span>
			<p class="home-announcements__description"><?php esc_html_e( 'समाज की महत्वपूर्ण सूचनाएँ, बैठकों की जानकारी और आवश्यक घोषणाएँ एक ही स्थान पर प्राप्त करें।', 'samanvay' ); ?></p>
			<?php if ( $archive_url ) : ?><a class="home-announcements__all" href="<?php echo esc_url( $archive_url ); ?>"><?php esc_html_e( 'सभी घोषणाएँ देखें', 'samanvay' ); ?><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a><?php endif; ?>
		</header>

		<div class="home-announcements__board">
			<?php if ( $announcement_query->have_posts() ) : ?>
				<?php while ( $announcement_query->have_posts() ) : $announcement_query->the_post(); ?>
					<article class="home-announcements__item">
						<time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><strong><?php echo esc_html( get_the_date( 'd' ) ); ?></strong><span><?php echo esc_html( get_the_date( 'M' ) ); ?></span></time>
						<div class="home-announcements__content">
							<div class="home-announcements__meta"><span><?php esc_html_e( 'घोषणा', 'samanvay' ); ?></span><?php if ( get_the_time( 'U' ) >= strtotime( '-7 days' ) ) : ?><b><?php esc_html_e( 'नई', 'samanvay' ); ?></b><?php endif; ?></div>
							<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 18 ) ); ?></p>
						</div>
						<a class="home-announcements__arrow" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( get_the_title() ); ?>"><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
					</article>
				<?php endwhile; ?>
			<?php else : ?>
				<div class="home-announcements__empty"><svg aria-hidden="true" viewBox="0 0 64 64"><path d="M13 9h38v46H13V9Z"/><path d="M21 22h22M21 31h22M21 40h15"/></svg><h3><?php esc_html_e( 'फिलहाल कोई नई घोषणा नहीं है', 'samanvay' ); ?></h3><p><?php esc_html_e( 'नई सूचना प्रकाशित होने पर वह यहाँ दिखाई देगी।', 'samanvay' ); ?></p></div>
			<?php endif; ?>
			<?php wp_reset_postdata(); ?>
		</div>
	</div>
</section>
