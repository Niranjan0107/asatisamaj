<?php
/**
 * Template Name: Team
 * Template Post Type: page
 *
 * @package Samanvay
 */

get_header();
$members        = samanvay_get_theme_setting( 'team_members' );
$fallback_image = get_template_directory_uri() . '/assets/images/team-member.png';
?>
<main id="primary" class="team-page">
	<section class="team-page__hero" aria-labelledby="team-page-title">
		<div class="site-container">
			<p class="team-page__eyebrow"><?php esc_html_e( 'हमारे नेतृत्वकर्ता', 'samanvay' ); ?></p>
			<h1 id="team-page-title"><?php esc_html_e( 'हमारी टीम एवं आयोजक', 'samanvay' ); ?></h1>
			<p><?php esc_html_e( 'सेवा, सहयोग और सामाजिक विकास के संकल्प के साथ समुदाय को आगे बढ़ाने वाले समर्पित सदस्यों से मिलिए।', 'samanvay' ); ?></p>
		</div>
	</section>

	<section class="team-page__members" aria-label="<?php esc_attr_e( 'Team members', 'samanvay' ); ?>">
		<div class="site-container">
			<?php if ( is_array( $members ) && $members ) : ?>
				<header class="team-page__heading"><svg viewBox="0 0 54 18" aria-hidden="true"><path d="M2 10c5-8 10 8 15 0s10 8 15 0 10 8 20 0"/></svg><div><p><?php esc_html_e( 'एक टीम, एक उद्देश्य', 'samanvay' ); ?></p><h2><?php esc_html_e( 'समर्पित सदस्य', 'samanvay' ); ?></h2></div><svg viewBox="0 0 54 18" aria-hidden="true"><path d="M2 10c5-8 10 8 15 0s10 8 15 0 10 8 20 0"/></svg></header>
				<div class="team-page__grid">
					<?php foreach ( $members as $index => $member ) : ?>
						<?php
						$member     = wp_parse_args( $member, array( 'image_id' => 0, 'name' => '', 'role' => '', 'description' => '', 'facebook' => '', 'linkedin' => '', 'whatsapp' => '' ) );
						$image_url  = $member['image_id'] ? wp_get_attachment_image_url( absint( $member['image_id'] ), 'medium' ) : $fallback_image;
						$card_style = 4 === $index % 6 ? 'pink' : ( 5 === $index % 6 ? 'orange' : 'teal' );
						?>
						<article class="team-card team-card--<?php echo esc_attr( $card_style ); ?>">
							<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $member['name'] ); ?>" loading="lazy">
							<h3><?php echo esc_html( $member['name'] ); ?></h3><h4><?php echo esc_html( $member['role'] ); ?></h4><p><?php echo esc_html( $member['description'] ); ?></p>
							<div class="team-card__socials">
								<?php if ( $member['facebook'] ) : ?><a href="<?php echo esc_url( $member['facebook'] ); ?>" aria-label="<?php echo esc_attr( sprintf( __( '%s on Facebook', 'samanvay' ), $member['name'] ) ); ?>"><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M14 8h3V4h-3c-3 0-5 2-5 5v3H6v4h3v6h4v-6h3l1-4h-4V9c0-1 .4-1 1-1Z"/></svg></a><?php endif; ?>
								<?php if ( $member['linkedin'] ) : ?><a href="<?php echo esc_url( $member['linkedin'] ); ?>" aria-label="<?php echo esc_attr( sprintf( __( '%s on LinkedIn', 'samanvay' ), $member['name'] ) ); ?>"><svg aria-hidden="true" viewBox="0 0 24 24"><rect x="3" y="9" width="4" height="12"/><circle cx="5" cy="5" r="2"/><path d="M11 21V9h4v2c1-2 6-3 6 3v7h-4v-6c0-3-2-3-3-1v7h-3Z"/></svg></a><?php endif; ?>
								<?php if ( $member['whatsapp'] ) : ?><a href="<?php echo esc_url( $member['whatsapp'] ); ?>" aria-label="<?php echo esc_attr( sprintf( __( '%s on WhatsApp', 'samanvay' ), $member['name'] ) ); ?>"><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M20 11a8 8 0 0 1-12 7l-5 2 2-5A8 8 0 1 1 20 11Z"/><path d="M8 7c1 4 3 6 7 8"/></svg></a><?php endif; ?>
							</div>
						</article>
					<?php endforeach; ?>
				</div>
			<?php else : ?>
				<div class="team-page__empty"><svg viewBox="0 0 64 54" aria-hidden="true"><circle cx="22" cy="14" r="8"/><circle cx="43" cy="14" r="8"/><path d="M8 45v-8c0-7 5-12 12-12h24c7 0 12 5 12 12v8"/></svg><h2><?php esc_html_e( 'टीम सदस्य जल्द जोड़े जाएंगे', 'samanvay' ); ?></h2><p><?php esc_html_e( 'कृपया कुछ समय बाद पुनः देखें।', 'samanvay' ); ?></p></div>
			<?php endif; ?>
		</div>
	</section>

	<?php while ( have_posts() ) : the_post(); if ( trim( get_the_content() ) ) : ?>
		<section class="team-page__editor"><div class="site-container entry-content"><?php the_content(); wp_link_pages(); ?></div></section>
	<?php endif; endwhile; ?>
</main>
<?php get_footer(); ?>
