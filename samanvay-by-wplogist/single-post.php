<?php
/**
 * Single blog post template.
 *
 * @package Samanvay
 */

get_header();
while ( have_posts() ) :
	the_post();
	$categories = get_the_category();
	$tags       = get_the_tags();
	?>
	<main id="primary" class="single-blog">
		<header class="single-blog__hero">
			<div class="site-container single-blog__hero-inner">
				<?php if ( $categories ) : ?><a class="single-blog__category" href="<?php echo esc_url( get_category_link( $categories[0] ) ); ?>"><?php echo esc_html( $categories[0]->name ); ?></a><?php endif; ?>
				<h1><?php the_title(); ?></h1>
				<div class="single-blog__meta">
					<span><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 3v4M17 3v4M3 10h18"/></svg><?php echo esc_html( get_the_date() ); ?></span>
					<span><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21c.5-5 3-8 8-8s7.5 3 8 8"/></svg><?php the_author_posts_link(); ?></span>
					<span><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h16v12H9l-5 4V5Z"/></svg><?php printf( esc_html( _n( '%s टिप्पणी', '%s टिप्पणियाँ', get_comments_number(), 'samanvay' ) ), esc_html( number_format_i18n( get_comments_number() ) ) ); ?></span>
				</div>
			</div>
		</header>

		<div class="single-blog__body">
			<div class="site-container single-blog__layout">
				<article <?php post_class( 'single-blog__article' ); ?>>
					<?php if ( has_post_thumbnail() ) : ?><figure class="single-blog__featured"><?php the_post_thumbnail( 'full' ); ?></figure><?php endif; ?>
					<div class="entry-content single-blog__content"><?php the_content(); wp_link_pages(); ?></div>
					<?php if ( $categories || $tags ) : ?>
						<footer class="single-blog__terms">
							<?php if ( $categories ) : ?><div><strong><?php esc_html_e( 'श्रेणियाँ', 'samanvay' ); ?></strong><span><?php foreach ( $categories as $category ) : ?><a href="<?php echo esc_url( get_category_link( $category ) ); ?>"><?php echo esc_html( $category->name ); ?></a><?php endforeach; ?></span></div><?php endif; ?>
							<?php if ( $tags ) : ?><div><strong><?php esc_html_e( 'टैग', 'samanvay' ); ?></strong><span><?php foreach ( $tags as $tag ) : ?><a href="<?php echo esc_url( get_tag_link( $tag ) ); ?>"><?php echo esc_html( $tag->name ); ?></a><?php endforeach; ?></span></div><?php endif; ?>
						</footer>
					<?php endif; ?>
					<?php
					the_post_navigation( array(
						'prev_text' => '<span class="single-blog__nav-label">' . esc_html__( 'पिछला लेख', 'samanvay' ) . '</span><strong>%title</strong>',
						'next_text' => '<span class="single-blog__nav-label">' . esc_html__( 'अगला लेख', 'samanvay' ) . '</span><strong>%title</strong>',
					) );
					if ( comments_open() || get_comments_number() ) {
						comments_template();
					}
					?>
				</article>
				<?php get_sidebar(); ?>
			</div>
		</div>
	</main>
	<?php
endwhile;
get_footer();
