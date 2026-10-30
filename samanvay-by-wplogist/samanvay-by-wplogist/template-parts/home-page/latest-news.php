<?php
/**
 * Homepage latest news section.
 *
 * @package Samanvay
 */

$news_query = new WP_Query( array(
	'post_type'           => 'post',
	'posts_per_page'      => 3,
	'ignore_sticky_posts' => true,
) );
$posts_page_id = (int) get_option( 'page_for_posts' );
$news_url      = $posts_page_id ? get_permalink( $posts_page_id ) : home_url( '/news/' );
?>
<section class="home-news" aria-labelledby="home-news-title">
	<div class="site-container">
		<header class="home-news__heading">
			<div><p><?php esc_html_e( 'समुदाय से जुड़े रहें', 'samanvay' ); ?></p><h2 id="home-news-title"><?php esc_html_e( 'समाचार एवं नवीनतम अपडेट', 'samanvay' ); ?></h2></div>
			<a href="<?php echo esc_url( $news_url ); ?>"><?php esc_html_e( 'सभी समाचार देखें', 'samanvay' ); ?><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
		</header>

		<?php if ( $news_query->have_posts() ) : ?>
			<div class="home-news__grid">
				<?php while ( $news_query->have_posts() ) : $news_query->the_post(); ?>
					<article <?php post_class( 'home-news__card' ); ?>>
						<a class="home-news__media" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( get_the_title() ); ?>">
							<?php if ( has_post_thumbnail() ) : ?>
								<?php the_post_thumbnail( 'large', array( 'loading' => 'lazy' ) ); ?>
							<?php else : ?>
								<span class="home-news__placeholder"><svg aria-hidden="true" viewBox="0 0 64 64"><path d="M8 13h48v38H8V13Z"/><path d="M16 22h20M16 30h32M16 38h32M16 46h20"/><circle cx="48" cy="22" r="4"/></svg></span>
							<?php endif; ?>
						</a>
						<div class="home-news__body">
							<div class="home-news__meta"><span><?php $categories = get_the_category(); echo esc_html( $categories ? $categories[0]->name : __( 'समाचार', 'samanvay' ) ); ?></span><time datetime="<?php echo esc_attr( get_the_date( DATE_W3C ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time></div>
							<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
							<p><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?></p>
							<a class="home-news__more" href="<?php the_permalink(); ?>"><?php esc_html_e( 'पूरा समाचार पढ़ें', 'samanvay' ); ?><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
						</div>
					</article>
				<?php endwhile; ?>
			</div>
		<?php else : ?>
			<div class="home-news__empty"><svg aria-hidden="true" viewBox="0 0 64 64"><path d="M8 13h48v38H8V13Z"/><path d="M16 22h20M16 30h32M16 38h32M16 46h20"/><circle cx="48" cy="22" r="4"/></svg><h3><?php esc_html_e( 'समाचार जल्द उपलब्ध होंगे', 'samanvay' ); ?></h3><p><?php esc_html_e( 'समुदाय की नवीनतम गतिविधियों और घोषणाओं के लिए दोबारा देखें।', 'samanvay' ); ?></p></div>
		<?php endif; ?>
		<?php wp_reset_postdata(); ?>
	</div>
</section>
