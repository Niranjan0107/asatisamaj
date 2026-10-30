<?php
/**
 * Template Name: Members List
 *
 * @package Samanvay
 */

get_header();
?>
<main id="primary" class="members-list-page">
    <div class="site-container">
      <?php echo do_shortcode( '[samanvay_active_members_table per_page="20" title="हमारे सक्रिय सदस्य"]' ); ?>
    </div>
</main>
<?php get_footer(); ?>