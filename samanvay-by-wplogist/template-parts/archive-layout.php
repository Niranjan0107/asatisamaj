<?php
/**
 * Shared blog and archive layout.
 *
 * @package Samanvay
 */

if ( is_home() ) {
	$posts_page_id = (int) get_option( 'page_for_posts' );
	$archive_title = $posts_page_id ? get_the_title( $posts_page_id ) : __( 'समाचार एवं लेख', 'samanvay' );
	$description   = __( 'समाज की गतिविधियों, उपलब्धियों, आयोजनों और उपयोगी जानकारियों से जुड़े नवीनतम लेख पढ़ें।', 'samanvay' );
} else {
	$archive_title = get_the_archive_title();
	$description   = get_the_archive_description();
}
?>
<main id="primary" class="blog-archive">
	<header class="blog-archive__hero">
		<div class="site-container">
			<p class="blog-archive__eyebrow"><?php esc_html_e( 'समुदाय समाचार', 'samanvay' ); ?></p>
			<h1><?php echo wp_kses_post( $archive_title ); ?></h1>
			<?php if ( $description ) : ?><div class="blog-archive__description"><?php echo wp_kses_post( wpautop( $description ) ); ?></div><?php endif; ?>
		</div>
	</header>

	<section class="blog-archive__content" aria-label="<?php esc_attr_e( 'Blog posts', 'samanvay' ); ?>">
		<div class="site-container">
			<?php if ( have_posts() ) : ?>
				<div class="blog-archive__heading">
					<div><p><?php esc_html_e( 'नई जानकारियाँ', 'samanvay' ); ?></p><h2><?php esc_html_e( 'नवीनतम लेख और अपडेट', 'samanvay' ); ?></h2></div>
					<svg viewBox="0 0 54 18" aria-hidden="true"><path d="M2 10c5-8 10 8 15 0s10 8 15 0 10 8 20 0"/></svg>
				</div>
				<div class="posts-grid blog-archive__grid">
					<?php while ( have_posts() ) : the_post(); get_template_part( 'template-parts/content', 'card' ); endwhile; ?>
				</div>
				<?php the_posts_pagination( array( 'mid_size' => 1, 'prev_text' => '<span aria-hidden="true">←</span><span class="screen-reader-text">' . esc_html__( 'Previous page', 'samanvay' ) . '</span>', 'next_text' => '<span class="screen-reader-text">' . esc_html__( 'Next page', 'samanvay' ) . '</span><span aria-hidden="true">→</span>' ) ); ?>
			<?php else : ?>
				<div class="blog-archive__empty"><?php get_template_part( 'template-parts/content', 'none' ); ?></div>
			<?php endif; ?>
		</div>
	</section>
</main>
