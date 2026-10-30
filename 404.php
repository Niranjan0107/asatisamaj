<?php /** 404 template. @package Samanvay */ get_header(); ?>
<main id="primary"><section class="page-header"><div class="site-container"><span class="eyebrow">404</span><h1><?php esc_html_e( 'This path seems to have wandered.', 'samanvay' ); ?></h1><p><?php esc_html_e( 'Let’s help you find your way back to the community.', 'samanvay' ); ?></p><p><a class="sam-button" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Return Home', 'samanvay' ); ?></a></p></div></section></main>
<?php get_footer(); ?>
