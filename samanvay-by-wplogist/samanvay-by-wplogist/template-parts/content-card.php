<?php /** Post card. @package Samanvay */
$categories = get_the_category();
?>
<article <?php post_class( 'post-card' ); ?>>
	<a class="post-card__image" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php if ( has_post_thumbnail() ) : the_post_thumbnail( 'medium_large' ); else : ?><span class="post-card__placeholder"><svg viewBox="0 0 90 70"><circle cx="45" cy="35" r="25"/><path d="M20 35h50M45 10c8 8 12 16 12 25S53 52 45 60c-8-8-12-16-12-25s4-17 12-25Z"/><path d="M9 61c12-10 22-11 32-3M81 61c-12-10-22-11-32-3"/></svg></span><?php endif; ?>
		<?php if ( $categories ) : ?><span class="post-card__category"><?php echo esc_html( $categories[0]->name ); ?></span><?php endif; ?>
	</a>
	<div class="post-card__body">
		<div class="post-card__meta"><span><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M7 3v4M17 3v4M3 10h18"/></svg><?php echo esc_html( get_the_date() ); ?></span><span><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21c.5-5 3-8 8-8s7.5 3 8 8"/></svg><?php the_author(); ?></span></div>
		<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<?php the_excerpt(); ?>
		<a class="post-card__more" href="<?php the_permalink(); ?>"><?php esc_html_e( 'पूरा लेख पढ़ें', 'samanvay' ); ?><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
	</div>
</article>
