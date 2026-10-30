<?php /** Sidebar. @package Samanvay */ ?>
<aside class="sidebar single-blog__sidebar" aria-label="<?php esc_attr_e( 'Sidebar', 'samanvay' ); ?>">
	<?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
		<?php dynamic_sidebar( 'sidebar-1' ); ?>
	<?php else : ?>
		<section class="widget widget_search"><h2 class="widget-title"><?php esc_html_e( 'लेख खोजें', 'samanvay' ); ?></h2><?php get_search_form(); ?></section>
		<section class="widget single-blog__recent"><h2 class="widget-title"><?php esc_html_e( 'नवीनतम लेख', 'samanvay' ); ?></h2>
			<ul>
				<?php
				$recent_posts = get_posts( array( 'numberposts' => 5, 'post_status' => 'publish', 'post__not_in' => is_single() ? array( get_the_ID() ) : array() ) );
				foreach ( $recent_posts as $recent_post ) :
					?>
					<li><a href="<?php echo esc_url( get_permalink( $recent_post ) ); ?>"><?php if ( has_post_thumbnail( $recent_post ) ) : echo get_the_post_thumbnail( $recent_post, 'thumbnail' ); else : ?><span class="single-blog__recent-placeholder"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 4h16v16H4zM7 15l3-3 3 3 2-2 3 3M8 8h.01"/></svg></span><?php endif; ?><span><strong><?php echo esc_html( get_the_title( $recent_post ) ); ?></strong><small><?php echo esc_html( get_the_date( '', $recent_post ) ); ?></small></span></a></li>
				<?php endforeach; wp_reset_postdata(); ?>
			</ul>
		</section>
		<section class="widget widget_categories"><h2 class="widget-title"><?php esc_html_e( 'श्रेणियाँ', 'samanvay' ); ?></h2><ul><?php wp_list_categories( array( 'title_li' => '', 'show_count' => true ) ); ?></ul></section>
	<?php endif; ?>
</aside>
