<?php
/** Homepage team section. @package Samanvay */
$fallback_image = get_template_directory_uri() . '/assets/images/team-member.png';
$members        = samanvay_get_theme_setting( 'team_members' );
$desktop_count  = max( 1, min( 6, absint( samanvay_get_theme_setting( 'team_desktop' ) ) ) );
$tablet_count   = max( 1, min( 4, absint( samanvay_get_theme_setting( 'team_tablet' ) ) ) );
$mobile_count   = max( 1, min( 2, absint( samanvay_get_theme_setting( 'team_mobile' ) ) ) );
$autoplay       = ! empty( samanvay_get_theme_setting( 'team_autoplay' ) );
$show_arrows    = ! empty( samanvay_get_theme_setting( 'team_arrows' ) );
if ( ! is_array( $members ) || ! $members ) {
	return;
}
?>
<section class="home-team" aria-labelledby="team-title"><div class="site-container"><div class="home-team__heading"><svg aria-hidden="true" viewBox="0 0 48 16"><path d="M2 9c6-8 12 8 18 0s12 8 18 0c3-4 6-4 8-1"/></svg><h2 id="team-title">संस्थापक मंडल</h2><svg aria-hidden="true" viewBox="0 0 48 16"><path d="M2 8c2-3 5-3 8 1 6 8 12-8 18 0s12-8 18 0"/></svg></div>
	<div class="home-team__slider" data-team-slider data-autoplay="<?php echo $autoplay ? '1' : '0'; ?>" data-desktop="<?php echo esc_attr( $desktop_count ); ?>" data-tablet="<?php echo esc_attr( $tablet_count ); ?>" data-mobile="<?php echo esc_attr( $mobile_count ); ?>" style="--team-desktop:<?php echo esc_attr( $desktop_count ); ?>;--team-tablet:<?php echo esc_attr( $tablet_count ); ?>;--team-mobile:<?php echo esc_attr( $mobile_count ); ?>;">
		<?php if ( $show_arrows && count( $members ) > 1 ) : ?><button class="home-team__arrow home-team__arrow--prev" type="button" data-team-prev aria-label="<?php esc_attr_e( 'Previous team members', 'samanvay' ); ?>"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m15 5-7 7 7 7"/></svg></button><?php endif; ?>
		<div class="home-team__viewport"><div class="home-team__grid">
	<?php foreach ( $members as $index => $member ) : ?>
		<?php
		$member     = wp_parse_args( $member, array( 'image_id' => 0, 'name' => '', 'role' => '', 'description' => '', 'facebook' => '', 'linkedin' => '', 'whatsapp' => '' ) );
		$image_url  = $member['image_id'] ? wp_get_attachment_image_url( absint( $member['image_id'] ), 'medium' ) : $fallback_image;
		$card_style = 4 === $index % 6 ? 'pink' : ( 5 === $index % 6 ? 'orange' : 'teal' );
		?>
		<article class="team-card team-card--<?php echo esc_attr( $card_style ); ?>"><img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $member['name'] ); ?>"><h3><?php echo esc_html( $member['name'] ); ?></h3><h4><?php echo esc_html( $member['role'] ); ?></h4><p><?php echo esc_html( $member['description'] ); ?></p><div class="team-card__socials">
			<?php if ( $member['facebook'] ) : ?><a href="<?php echo esc_url( $member['facebook'] ); ?>" aria-label="Facebook"><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M14 8h3V4h-3c-3 0-5 2-5 5v3H6v4h3v6h4v-6h3l1-4h-4V9c0-1 .4-1 1-1Z"/></svg></a><?php endif; ?>
			<?php if ( $member['linkedin'] ) : ?><a href="<?php echo esc_url( $member['linkedin'] ); ?>" aria-label="LinkedIn"><svg aria-hidden="true" viewBox="0 0 24 24"><rect x="3" y="9" width="4" height="12"/><circle cx="5" cy="5" r="2"/><path d="M11 21V9h4v2c1-2 6-3 6 3v7h-4v-6c0-3-2-3-3-1v7h-3Z"/></svg></a><?php endif; ?>
			<?php if ( $member['whatsapp'] ) : ?><a href="<?php echo esc_url( $member['whatsapp'] ); ?>" aria-label="WhatsApp"><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M20 11a8 8 0 0 1-12 7l-5 2 2-5A8 8 0 1 1 20 11Z"/><path d="M8 7c1 4 3 6 7 8"/></svg></a><?php endif; ?>
		</div></article>
	<?php endforeach; ?>
		</div></div>
		<?php if ( $show_arrows && count( $members ) > 1 ) : ?><button class="home-team__arrow home-team__arrow--next" type="button" data-team-next aria-label="<?php esc_attr_e( 'Next team members', 'samanvay' ); ?>"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="m9 5 7 7-7 7"/></svg></button><?php endif; ?>
	</div>
	<a class="home-team__button" href="<?php echo esc_url( home_url( '/team/' ) ); ?>">पूरी टीम देखें <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a></div></section>
