<?php /** Page template. @package Samanvay */ get_header(); while ( have_posts() ) : the_post(); ?>
<main id="primary"><header class="page-header"><div class="site-container"><span class="eyebrow"><?php esc_html_e( 'Samanvay', 'samanvay' ); ?></span><h1><?php the_title(); ?></h1></div></header><div class="content-area"><div class="site-container entry-content"><?php the_content(); wp_link_pages(); ?></div></div></main>
<?php if ( comments_open() || get_comments_number() ) { comments_template(); } endwhile; get_footer(); ?>
