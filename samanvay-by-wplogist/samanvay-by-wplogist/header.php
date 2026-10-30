<?php /** @package Samanvay */ ?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'samanvay' ); ?></a>
<header class="site-header<?php echo samanvay_get_theme_setting( 'sticky_header' ) ? ' site-header--sticky' : ''; ?>" id="masthead">
	<div class="site-container header-inner">
		<div class="site-branding">
			<?php if ( has_custom_logo() ) : the_custom_logo(); else : ?>
				<a class="brand-mark" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"><span>आपका</span><strong>लोगों</strong></a>
			<?php endif; ?>
		</div>
		<nav class="main-navigation" id="site-navigation" aria-label="<?php esc_attr_e( 'Primary menu', 'samanvay' ); ?>">
			<button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><span aria-hidden="true">☰</span><span class="screen-reader-text"><?php esc_html_e( 'Menu', 'samanvay' ); ?></span></button>
			<?php wp_nav_menu( array( 'theme_location' => 'primary', 'menu_id' => 'primary-menu', 'container' => false, 'fallback_cb' => 'samanvay_header_menu_fallback' ) ); ?>
		</nav>
		<?php $header_member = is_user_logged_in() ? samanvay_get_member_by_user( get_current_user_id() ) : false; ?>
		<a class="header-cta" href="<?php echo esc_url( $header_member ? samanvay_get_member_dashboard_url() : samanvay_get_registration_page_url() ); ?>"><svg aria-hidden="true" viewBox="0 0 24 24"><circle cx="9" cy="8" r="3"/><path d="M3 19v-2c0-3 2-5 5-5h2c3 0 5 2 5 5v2M16 5a3 3 0 0 1 0 6M17 13c2 1 3 2 3 5v1"/></svg><span><?php echo esc_html( $header_member ? __( 'मेरा डैशबोर्ड', 'samanvay' ) : samanvay_get_theme_setting( 'header_cta_text' ) ); ?></span></a>
	</div>
</header>
