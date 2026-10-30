<?php
/**
 * Samanvay theme functions.
 *
 * @package Samanvay
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SAMANVAY_VERSION', '7.0.0' );

require_once get_template_directory() . '/inc/customizer.php';
require_once get_template_directory() . '/inc/theme-settings.php';
require_once get_template_directory() . '/inc/membership.php';
require_once get_template_directory() . '/inc/member-trash.php';
require_once get_template_directory() . '/inc/member-table.php';

function samanvay_setup() {
	load_theme_textdomain( 'samanvay', get_template_directory() . '/languages' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'custom-logo', array( 'height' => 80, 'width' => 260, 'flex-height' => true, 'flex-width' => true ) );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' ) );
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'style.css' );
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'samanvay' ),
		'footer'  => __( 'Footer Menu', 'samanvay' ),
	) );
}
add_action( 'after_setup_theme', 'samanvay_setup' );

function samanvay_register_announcement_post_type() {
	register_post_type( 'announcement', array(
		'labels' => array(
			'name'          => __( 'Announcements', 'samanvay' ),
			'singular_name' => __( 'Announcement', 'samanvay' ),
			'add_new_item'  => __( 'Add New Announcement', 'samanvay' ),
			'edit_item'     => __( 'Edit Announcement', 'samanvay' ),
			'view_item'     => __( 'View Announcement', 'samanvay' ),
			'search_items'  => __( 'Search Announcements', 'samanvay' ),
		),
		'public'       => true,
		'has_archive'  => true,
		'show_in_rest' => true,
		'menu_icon'    => 'dashicons-megaphone',
		'rewrite'      => array( 'slug' => 'announcements' ),
		'supports'     => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
	) );
}
add_action( 'init', 'samanvay_register_announcement_post_type' );

function samanvay_maybe_flush_announcement_rewrite_rules() {
	if ( '3.9.0' !== get_option( 'samanvay_announcement_rewrite_version' ) ) {
		flush_rewrite_rules( false );
		update_option( 'samanvay_announcement_rewrite_version', '3.9.0' );
	}
}
add_action( 'init', 'samanvay_maybe_flush_announcement_rewrite_rules', 20 );

function samanvay_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'samanvay_content_width', 760 );
}
add_action( 'after_setup_theme', 'samanvay_content_width', 0 );

function samanvay_assets() {
	wp_enqueue_style( 'samanvay-fonts', 'https://fonts.googleapis.com/css2?family=Noto+Sans+Devanagari:wght@400;500;600;700;800&display=swap', array(), null );
	// Refresh cached CSS whenever the active theme stylesheet changes.
	$stylesheet_path = get_stylesheet_directory() . '/style.css';
	$stylesheet_modified = is_file( $stylesheet_path ) ? filemtime( $stylesheet_path ) : false;
	$stylesheet_version = $stylesheet_modified ? SAMANVAY_VERSION . '.' . $stylesheet_modified : SAMANVAY_VERSION;
	wp_enqueue_style( 'samanvay-style', get_stylesheet_uri(), array( 'samanvay-fonts' ), $stylesheet_version );
	wp_enqueue_script( 'samanvay-navigation', get_template_directory_uri() . '/assets/js/navigation.js', array(), SAMANVAY_VERSION, true );
	if ( samanvay_get_theme_setting( 'sticky_header' ) ) {
		wp_enqueue_script( 'samanvay-sticky-header', get_template_directory_uri() . '/assets/js/sticky-header.js', array(), SAMANVAY_VERSION, true );
	}
	$is_about_page   = is_page_template( 'page-about-us.php' ) || is_page( array( 'about', 'about-us' ) );
	$is_contact_page = is_page_template( 'page-contact-us.php' ) || is_page( array( 'contact', 'contact-us' ) );
	$is_team_page    = is_page_template( 'page-team.php' ) || is_page( 'team' );
	$is_gallery_page = is_page_template( 'page-gallery.php' ) || is_page( 'gallery' );
	if ( is_page_template( 'page-rules.php' ) || is_page( 'rules' ) ) {
		wp_enqueue_script( 'samanvay-rules-navigation', get_template_directory_uri() . '/assets/js/rules-navigation.js', array(), (string) filemtime( get_template_directory() . '/assets/js/rules-navigation.js' ), true );
	}
	$is_member_page  = is_page_template( 'page-member-registration.php' ) || is_page( 'member-registration' );
	$is_login_page   = is_page_template( 'page-member-login.php' ) || is_page( 'member-login' );
	$is_dashboard_page = is_page_template( 'page-member-dashboard.php' ) || is_page( 'member-dashboard' );
	if ( $is_dashboard_page ) {
		wp_enqueue_script( 'samanvay-dashboard-tabs', get_template_directory_uri() . '/assets/js/member-dashboard-tabs.js', array(), (string) filemtime( get_template_directory() . '/assets/js/member-dashboard-tabs.js' ), true );
	}
	$is_forgot_page  = is_page_template( 'page-member-forgot-password.php' ) || is_page( 'member-forgot-password' );
	$is_reset_page   = is_page_template( 'page-member-reset-password.php' ) || is_page( 'member-reset-password' );
	if ( is_front_page() || $is_about_page || $is_contact_page || $is_team_page || $is_gallery_page || $is_member_page || $is_login_page || $is_dashboard_page || $is_forgot_page || $is_reset_page || is_home() || is_archive() || is_singular( 'post' ) ) {
		wp_enqueue_script( 'samanvay-home-reveal', get_template_directory_uri() . '/assets/js/home-reveal.js', array(), SAMANVAY_VERSION, true );
	}
	if ( $is_member_page || $is_dashboard_page || $is_reset_page ) {
		$selected_location = array( 'state' => '', 'district' => '', 'tehsil' => '' );
		if ( $is_dashboard_page && is_user_logged_in() ) {
			$dashboard_member = samanvay_get_member_by_user( get_current_user_id() );
			if ( $dashboard_member ) {
				$selected_location = array( 'state' => $dashboard_member->state, 'district' => $dashboard_member->district, 'tehsil' => $dashboard_member->tehsil );
			}
		}
		wp_enqueue_script( 'samanvay-member-registration', get_template_directory_uri() . '/assets/js/member-registration.js', array(), (string) filemtime( get_template_directory() . '/assets/js/member-registration.js' ), true );
		wp_localize_script( 'samanvay-member-registration', 'samanvayMembership', array(
			'locationsUrl'  => get_template_directory_uri() . '/assets/data-india-locations.json',
			'locationError' => __( 'Location data could not be loaded. Please refresh and try again.', 'samanvay' ),
			'selected'      => $selected_location,
		) );
	}
	if ( is_front_page() ) {
		wp_enqueue_script( 'samanvay-video-gallery', get_template_directory_uri() . '/assets/js/video-gallery.js', array(), SAMANVAY_VERSION, true );
		wp_enqueue_script( 'samanvay-team-slider', get_template_directory_uri() . '/assets/js/team-slider.js', array(), SAMANVAY_VERSION, true );
	}
	if ( $is_gallery_page ) {
		wp_enqueue_script( 'samanvay-gallery-lightbox', get_template_directory_uri() . '/assets/js/gallery-lightbox.js', array(), SAMANVAY_VERSION, true );
	}
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'samanvay_assets' );

function samanvay_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'Sidebar', 'samanvay' ),
		'id'            => 'sidebar-1',
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'samanvay_widgets_init' );

function samanvay_excerpt_length() { return 20; }
add_filter( 'excerpt_length', 'samanvay_excerpt_length' );

function samanvay_menu_fallback() {
	echo '<ul id="primary-menu">';
	wp_list_pages( array( 'title_li' => '', 'depth' => 1 ) );
	echo '</ul>';
}

function samanvay_header_menu_fallback() {
	$items = array(
		__( 'होम', 'samanvay' )          => home_url( '/' ),
		__( 'हमारे बारे में', 'samanvay' ) => home_url( '/about/' ),
		__( 'हमारा समुदाय', 'samanvay' )   => home_url( '/community/' ),
		__( 'हमारे कार्य', 'samanvay' )    => home_url( '/our-work/' ),
		__( 'कार्यक्रम', 'samanvay' )      => home_url( '/events/' ),
		__( 'गैलरी', 'samanvay' )         => home_url( '/gallery/' ),
		__( 'संपर्क करें', 'samanvay' )    => home_url( '/contact/' ),
	);
	echo '<ul id="primary-menu">';
	foreach ( $items as $label => $url ) {
		echo '<li><a href="' . esc_url( $url ) . '">' . esc_html( $label ) . '</a></li>';
	}
	echo '</ul>';
}



/**
 * Samanvay - Donation Section Settings
 */

add_action('admin_menu', function () {
    add_menu_page(
        'सहयोग सेटिंग्स',
        'सहयोग सेटिंग्स',
        'manage_options',
        'samaj-donation-settings',
        'samaj_donation_settings_page',
        'dashicons-heart',
        26
    );
});

add_action('admin_init', function () {

    register_setting(
        'samaj_donation_group',
        'samaj_donation_options',
        array(
            'type' => 'array',
            'sanitize_callback' => 'samaj_sanitize_donation_options',
            'default' => array()
        )
    );
});

function samaj_sanitize_donation_options($input) {

    $input = is_array($input) ? $input : array();

    return array(
        'enabled' => !empty($input['enabled']) ? '1' : '0',
        'deceased_name' => sanitize_text_field($input['deceased_name'] ?? ''),
        'nominee_name' => sanitize_text_field($input['nominee_name'] ?? ''),
        'account_number' => sanitize_text_field($input['account_number'] ?? ''),
        'upi_id' => sanitize_text_field($input['upi_id'] ?? ''),
        'qr_image' => esc_url_raw($input['qr_image'] ?? '')
    );
}

function samaj_donation_settings_page() {

    if (!current_user_can('manage_options')) {
        return;
    }

    $options = get_option('samaj_donation_options', array());

    $enabled = $options['enabled'] ?? '0';
    $deceased = $options['deceased_name'] ?? '';
    $nominee = $options['nominee_name'] ?? '';
    $account = $options['account_number'] ?? '';
    $upi = $options['upi_id'] ?? '';
    $qr = $options['qr_image'] ?? '';

    ?>
    <div class="wrap">
        <h1>दिवंगत परिवार सहयोग सेटिंग्स</h1>
        <p>यहाँ से वेबसाइट के सहयोग सेक्शन को अपडेट करें।</p>

        <form method="post" action="options.php">

            <?php settings_fields('samaj_donation_group'); ?>

            <table class="form-table">

                <tr>
                    <th>सहयोग सेक्शन</th>
                    <td>
                        <label>
                            <input type="checkbox"
                                name="samaj_donation_options[enabled]"
                                value="1"
                                <?php checked($enabled, '1'); ?>>
                            Enable Donation Section
                        </label>
                    </td>
                </tr>

                <tr>
                    <th>मृतक का नाम</th>
                    <td>
                        <input type="text"
                            class="regular-text"
                            name="samaj_donation_options[deceased_name]"
                            value="<?php echo esc_attr($deceased); ?>">
                    </td>
                </tr>

                <tr>
                    <th>नामिनी का नाम</th>
                    <td>
                        <input type="text"
                            class="regular-text"
                            name="samaj_donation_options[nominee_name]"
                            value="<?php echo esc_attr($nominee); ?>">
                    </td>
                </tr>

                <tr>
                    <th>बैंक खाता संख्या</th>
                    <td>
                        <input type="text"
                            class="regular-text"
                            name="samaj_donation_options[account_number]"
                            value="<?php echo esc_attr($account); ?>">
                    </td>
                </tr>

                <tr>
                    <th>UPI ID</th>
                    <td>
                        <input type="text"
                            class="regular-text"
                            name="samaj_donation_options[upi_id]"
                            value="<?php echo esc_attr($upi); ?>">
                    </td>
                </tr>

                <tr>
                    <th>QR Code Image</th>
                    <td>
                        <input type="url"
                            id="samaj_qr_image"
                            class="regular-text"
                            name="samaj_donation_options[qr_image]"
                            value="<?php echo esc_attr($qr); ?>">

                        <button type="button"
                            class="button"
                            id="samaj_upload_qr">
                            Upload QR Code
                        </button>

                        <div style="margin-top:15px">
                            <img id="samaj_qr_preview"
                                src="<?php echo esc_url($qr); ?>"
                                style="max-width:180px;<?php echo empty($qr) ? 'display:none;' : ''; ?>">
                        </div>
                    </td>
                </tr>

            </table>

            <?php submit_button('Save Donation Settings'); ?>

        </form>
    </div>
    <?php
}

/**
 * WordPress Media Uploader
 */
add_action('admin_enqueue_scripts', function ($hook) {

    if ($hook !== 'toplevel_page_samaj-donation-settings') {
        return;
    }

    wp_enqueue_media();

    wp_add_inline_script('jquery-core', "
        jQuery(function($) {

            $('#samaj_upload_qr').on('click', function(e) {
                e.preventDefault();

                var uploader = wp.media({
                    title: 'Select QR Code',
                    button: { text: 'Use QR Code' },
                    library: { type: 'image' },
                    multiple: false
                });

                uploader.on('select', function() {
                    var image = uploader.state()
                        .get('selection')
                        .first()
                        .toJSON();

                    $('#samaj_qr_image').val(image.url);
                    $('#samaj_qr_preview')
                        .attr('src', image.url)
                        .show();
                });

                uploader.open();
            });

        });
    ");
});
