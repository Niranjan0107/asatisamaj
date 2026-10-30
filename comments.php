<?php
/** Comments template. @package Samanvay */
if ( post_password_required() ) { return; }
?>
<section class="comments-area" id="comments">
	<?php if ( have_comments() ) : ?><h2><?php printf( esc_html( _nx( 'One response', '%1$s responses', get_comments_number(), 'comments title', 'samanvay' ) ), esc_html( number_format_i18n( get_comments_number() ) ) ); ?></h2><ol class="comment-list"><?php wp_list_comments( array( 'style' => 'ol', 'short_ping' => true, 'avatar_size' => 48 ) ); ?></ol><?php the_comments_navigation(); endif; ?>
	<?php comment_form(); ?>
</section>
