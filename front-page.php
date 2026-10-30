<?php
/**
 * Front page template.
 *
 * Homepage sections will be added as separate template parts.
 *
 * @package Samanvay
 */

get_header();
?>
<main id="primary">
	<?php get_template_part( 'template-parts/home-page/hero' ); ?>
	<?php get_template_part( 'template-parts/home-page/about' ); ?>
		<?php get_template_part( 'template-parts/home-page/sahyog' ); ?>

	<?php get_template_part( 'template-parts/home-page/support-plan' ); ?>
	<?php //get_template_part( 'template-parts/home-page/asati-community' ); ?>
	<?php get_template_part( 'template-parts/home-page/community-history' ); ?>
	<?php get_template_part( 'template-parts/home-page/social-activities' ); ?>
	<?php get_template_part( 'template-parts/home-page/initiatives' ); ?>
	<?php get_template_part( 'template-parts/home-page/events' ); ?>
	<?php get_template_part( 'template-parts/home-page/latest-news' ); ?>
	<?php get_template_part( 'template-parts/home-page/announcements' ); ?>
	<?php get_template_part( 'template-parts/home-page/video-gallery' ); ?>
	<?php get_template_part( 'template-parts/home-page/team' ); ?>
	<?php //get_template_part( 'template-parts/home-page/gallery' ); ?>
</main>
<?php
get_footer();
