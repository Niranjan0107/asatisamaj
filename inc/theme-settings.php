<?php
/**
 * Theme settings admin page.
 *
 * @package Samanvay
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function samanvay_theme_settings_defaults() {
	return array(
		'sticky_header'    => 1,
		'youtube_videos'   => array(),
		'gallery_images'   => array(),
		'team_members'     => array(
			array( 'image_id' => 0, 'name' => 'राजेश शर्मा', 'role' => 'अध्यक्ष', 'description' => 'समुदाय के समग्र विकास और मूल्यों के संरक्षण के लिए समर्पित।', 'facebook' => '#', 'linkedin' => '#', 'whatsapp' => '#' ),
			array( 'image_id' => 0, 'name' => 'सविता मिश्रा', 'role' => 'सचिव', 'description' => 'संगठन संचालन, समन्वय और सदस्यों के सहयोग के लिए निरंतर प्रयासरत।', 'facebook' => '#', 'linkedin' => '#', 'whatsapp' => '#' ),
			array( 'image_id' => 0, 'name' => 'अमित जैन', 'role' => 'कोषाध्यक्ष', 'description' => 'संसाधनों के पारदर्शी प्रबंधन और वित्तीय अनुशासन के लिए प्रतिबद्ध।', 'facebook' => '#', 'linkedin' => '#', 'whatsapp' => '#' ),
			array( 'image_id' => 0, 'name' => 'विवेक राठौर', 'role' => 'कार्यक्रम संयोजक', 'description' => 'प्रभावी जनहित योजनाओं और सफल आयोजन के लिए सदैव तत्पर।', 'facebook' => '#', 'linkedin' => '#', 'whatsapp' => '#' ),
			array( 'image_id' => 0, 'name' => 'पूजा अग्रवाल', 'role' => 'महिला प्रकोष्ठ प्रमुख', 'description' => 'महिलाओं के सशक्तिकरण और नेतृत्व विकास के लिए समर्पित।', 'facebook' => '#', 'linkedin' => '#', 'whatsapp' => '#' ),
			array( 'image_id' => 0, 'name' => 'रोहित वर्मा', 'role' => 'युवा समन्वयक', 'description' => 'युवाओं को जोड़ना, प्रेरित करना और अवसरों से जोड़ना ही हमारा लक्ष्य।', 'facebook' => '#', 'linkedin' => '#', 'whatsapp' => '#' ),
		),
		'team_desktop'     => 6,
		'team_tablet'      => 3,
		'team_mobile'      => 1,
		'team_autoplay'    => 1,
		'team_arrows'      => 1,
		'registration_fee' => 501,
		'membership_fee'   => 501,
		'membership_duration' => 30,
		'free_lifetime_membership' => 0,
		'member_deletion_enabled' => 1,
		'membership_qr'    => 0,
		'footer_logo'      => 0,
		'footer_about'     => 'हमारा समुदाय सेवा, सहयोग और संस्कारों के मूल्यों पर आधारित है। आइए, मिलकर एक बेहतर समाज का निर्माण करें।',
		'copyright_text'   => 'हमारा समुदाय. सर्वाधिकार सुरक्षित.',
		'header_cta_text'  => 'समुदाय से जुड़ें',
		'footer_cta_title' => 'समुदाय से जुड़ें, बदलाव का हिस्सा बनें',
		'footer_cta_text'  => 'आपकी भागीदारी से हमारे कार्य और प्रभाव और भी मजबूत होते हैं।',
		'footer_btn_text'  => 'अभी सदस्य बनें',
		'address'          => "123, सेवा मार्ग, शक्ति नगर,\nभोपाल, मध्य प्रदेश – 462001",
		'phone'            => '+91 98765 43210',
		'email'            => 'info@samajsewa.org',
		'website'          => 'https://www.samajsewa.org',
		'facebook'         => '#',
		'instagram'        => '#',
		'youtube'          => '#',
		'whatsapp'         => '#',
	);
}

function samanvay_get_theme_setting( $key ) {
	$settings = wp_parse_args( get_option( 'samanvay_theme_settings', array() ), samanvay_theme_settings_defaults() );
	return isset( $settings[ $key ] ) ? $settings[ $key ] : '';
}

function samanvay_get_youtube_id( $url ) {
	$parts = wp_parse_url( trim( (string) $url ) );
	if ( ! $parts || empty( $parts['host'] ) ) {
		return '';
	}
	$host = strtolower( preg_replace( '/^www\./', '', $parts['host'] ) );
	$id   = '';
	if ( 'youtu.be' === $host ) {
		$id = trim( isset( $parts['path'] ) ? $parts['path'] : '', '/' );
	} elseif ( in_array( $host, array( 'youtube.com', 'm.youtube.com', 'music.youtube.com', 'youtube-nocookie.com' ), true ) ) {
		if ( ! empty( $parts['query'] ) ) {
			parse_str( $parts['query'], $query );
			$id = isset( $query['v'] ) ? $query['v'] : '';
		}
		if ( ! $id && ! empty( $parts['path'] ) && preg_match( '#/(?:embed|shorts|live)/([A-Za-z0-9_-]{11})#', $parts['path'], $matches ) ) {
			$id = $matches[1];
		}
	}
	return preg_match( '/^[A-Za-z0-9_-]{11}$/', $id ) ? $id : '';
}

function samanvay_theme_settings_fields() {
	return array(
		'general' => array(
			'label'  => __( 'General', 'samanvay' ),
			'icon'   => 'dashicons-admin-settings',
			'fields' => array(
				'footer_about' => array( __( 'Organization description', 'samanvay' ), 'textarea' ),
			),
		),
		'header' => array(
			'label'  => __( 'Header', 'samanvay' ),
			'icon'   => 'dashicons-align-wide',
			'fields' => array(
				'sticky_header'   => array( __( 'Enable sticky header', 'samanvay' ), 'checkbox' ),
				'header_cta_text' => array( __( 'Header button text', 'samanvay' ), 'text' ),
			),
		),
		'footer' => array(
			'label'  => __( 'Footer', 'samanvay' ),
			'icon'   => 'dashicons-megaphone',
			'fields' => array(
				'footer_logo'      => array( __( 'Footer logo', 'samanvay' ), 'media', __( 'General', 'samanvay' ) ),
				'copyright_text'   => array( __( 'Copyright text', 'samanvay' ), 'text', __( 'General', 'samanvay' ) ),
				'footer_cta_title' => array( __( 'Banner heading', 'samanvay' ), 'text', __( 'CTA', 'samanvay' ) ),
				'footer_cta_text'  => array( __( 'Banner description', 'samanvay' ), 'textarea', __( 'CTA', 'samanvay' ) ),
				'footer_btn_text'  => array( __( 'Button text', 'samanvay' ), 'text', __( 'CTA', 'samanvay' ) ),
			),
		),
		'contact' => array(
			'label'  => __( 'Contact Details', 'samanvay' ),
			'icon'   => 'dashicons-location-alt',
			'fields' => array(
				'address' => array( __( 'Address', 'samanvay' ), 'textarea' ),
				'phone'   => array( __( 'Phone number', 'samanvay' ), 'text' ),
				'email'   => array( __( 'Email address', 'samanvay' ), 'email' ),
				'website' => array( __( 'Website URL', 'samanvay' ), 'url' ),
			),
		),
		'social' => array(
			'label'  => __( 'Social Links', 'samanvay' ),
			'icon'   => 'dashicons-share',
			'fields' => array(
				'facebook'  => array( __( 'Facebook URL', 'samanvay' ), 'url' ),
				'instagram' => array( __( 'Instagram URL', 'samanvay' ), 'url' ),
				'youtube'   => array( __( 'YouTube URL', 'samanvay' ), 'url' ),
				'whatsapp'  => array( __( 'WhatsApp URL', 'samanvay' ), 'url' ),
			),
		),
		'videos' => array(
			'label'  => __( 'Videos', 'samanvay' ),
			'icon'   => 'dashicons-video-alt3',
			'fields' => array(
				'youtube_videos' => array( __( 'YouTube videos', 'samanvay' ), 'youtube_repeater' ),
			),
		),
		'gallery' => array(
			'label'  => __( 'Manage Gallery', 'samanvay' ),
			'icon'   => 'dashicons-format-gallery',
			'fields' => array(
				'gallery_images' => array( __( 'Gallery images', 'samanvay' ), 'gallery_repeater' ),
			),
		),
		'team' => array(
			'label'  => __( 'Team Members', 'samanvay' ),
			'icon'   => 'dashicons-groups',
			'fields' => array(
				'team_desktop'  => array( __( 'Members visible on desktop', 'samanvay' ), 'number', __( 'Slider Settings', 'samanvay' ), array( 'min' => 1, 'max' => 6 ) ),
				'team_tablet'   => array( __( 'Members visible on tablet', 'samanvay' ), 'number', __( 'Slider Settings', 'samanvay' ), array( 'min' => 1, 'max' => 4 ) ),
				'team_mobile'   => array( __( 'Members visible on mobile', 'samanvay' ), 'number', __( 'Slider Settings', 'samanvay' ), array( 'min' => 1, 'max' => 2 ) ),
				'team_autoplay' => array( __( 'Enable automatic sliding', 'samanvay' ), 'checkbox', __( 'Slider Settings', 'samanvay' ), __( 'Automatically move to the next team member.', 'samanvay' ) ),
				'team_arrows'   => array( __( 'Show navigation arrows', 'samanvay' ), 'checkbox', __( 'Slider Settings', 'samanvay' ), __( 'Display previous and next slider buttons.', 'samanvay' ) ),
				'team_members'  => array( __( 'Team members', 'samanvay' ), 'team_repeater', __( 'Members', 'samanvay' ) ),
			),
		),
		'membership' => array(
			'label'  => __( 'Membership', 'samanvay' ),
			'icon'   => 'dashicons-id-alt',
			'fields' => array(
				'registration_fee'   => array( __( 'Registration fee (₹)', 'samanvay' ), 'number', __( 'Payment', 'samanvay' ), array( 'min' => 0, 'max' => 100000 ) ),
				'membership_fee'     => array( __( 'Membership fee (₹)', 'samanvay' ), 'number', __( 'Payment', 'samanvay' ), array( 'min' => 0, 'max' => 100000 ) ),
				'membership_duration' => array( __( 'Membership duration (days)', 'samanvay' ), 'number', __( 'Payment', 'samanvay' ), array( 'min' => 1, 'max' => 3650 ) ),
				'free_lifetime_membership' => array( __( 'Enable free lifetime membership', 'samanvay' ), 'checkbox', __( 'Registration', 'samanvay' ), __( 'New registrations started while enabled skip all fees and become active for life automatically. Existing lifetime members keep their entitlement after this is disabled.', 'samanvay' ) ),
				'member_deletion_enabled' => array( __( 'Enable member deletion', 'samanvay' ), 'checkbox', __( 'Member Management', 'samanvay' ), __( 'Allow administrators to move members to Trash, restore them, or permanently delete members from Trash.', 'samanvay' ) ),
				'membership_qr'      => array( __( 'Payment QR code', 'samanvay' ), 'media', __( 'Payment', 'samanvay' ) ),
			),
		),
	);
}

function samanvay_register_theme_settings() {
	register_setting( 'samanvay_theme_settings_group', 'samanvay_theme_settings', array(
		'type'              => 'array',
		'sanitize_callback' => 'samanvay_sanitize_theme_settings',
		'default'           => samanvay_theme_settings_defaults(),
	) );
}
add_action( 'admin_init', 'samanvay_register_theme_settings' );

function samanvay_sanitize_theme_settings( $input ) {
	$defaults = samanvay_theme_settings_defaults();
	$output   = array();
	$types    = array();
	foreach ( samanvay_theme_settings_fields() as $tab ) {
		foreach ( $tab['fields'] as $key => $field ) {
			$types[ $key ] = $field[1];
		}
	}
	foreach ( $defaults as $key => $default ) {
		$value = isset( $input[ $key ] ) ? $input[ $key ] : $default;
			switch ( isset( $types[ $key ] ) ? $types[ $key ] : 'text' ) {
			case 'gallery_repeater':
				$output[ $key ] = array_values( array_unique( array_filter( array_map( 'absint', (array) $value ) ) ) );
				break;
			case 'number':
				$limits         = array( 'team_desktop' => array( 1, 6 ), 'team_tablet' => array( 1, 4 ), 'team_mobile' => array( 1, 2 ), 'registration_fee' => array( 0, 100000 ), 'membership_fee' => array( 0, 100000 ), 'membership_duration' => array( 1, 3650 ) );
				$number         = absint( $value );
				$output[ $key ] = isset( $limits[ $key ] ) ? max( $limits[ $key ][0], min( $limits[ $key ][1], $number ) ) : $number;
				break;
			case 'team_repeater':
				$output[ $key ] = array();
				foreach ( (array) $value as $member ) {
					if ( ! is_array( $member ) ) {
						continue;
					}
					$name = isset( $member['name'] ) ? sanitize_text_field( $member['name'] ) : '';
					if ( '' === $name ) {
						continue;
					}
					$output[ $key ][] = array(
						'image_id'    => isset( $member['image_id'] ) ? absint( $member['image_id'] ) : 0,
						'name'        => $name,
						'role'        => isset( $member['role'] ) ? sanitize_text_field( $member['role'] ) : '',
						'description' => isset( $member['description'] ) ? sanitize_textarea_field( $member['description'] ) : '',
						'facebook'    => isset( $member['facebook'] ) && '#' !== $member['facebook'] ? esc_url_raw( $member['facebook'] ) : '#',
						'linkedin'    => isset( $member['linkedin'] ) && '#' !== $member['linkedin'] ? esc_url_raw( $member['linkedin'] ) : '#',
						'whatsapp'    => isset( $member['whatsapp'] ) && '#' !== $member['whatsapp'] ? esc_url_raw( $member['whatsapp'] ) : '#',
					);
				}
				break;
			case 'youtube_repeater':
				$output[ $key ] = array();
				foreach ( (array) $value as $video_url ) {
					$video_id = samanvay_get_youtube_id( $video_url );
					if ( $video_id ) {
						$output[ $key ][] = 'https://www.youtube.com/watch?v=' . $video_id;
					}
				}
				$output[ $key ] = array_values( array_unique( $output[ $key ] ) );
				break;
			case 'checkbox':
				$output[ $key ] = isset( $input[ $key ] ) && ! empty( $input[ $key ] ) ? 1 : 0;
				break;
			case 'media':
				$output[ $key ] = absint( $value );
				break;
			case 'url':
				$output[ $key ] = preg_match( '/^#[A-Za-z][A-Za-z0-9_:.\-]*$/', $value ) || '#' === $value ? sanitize_text_field( $value ) : esc_url_raw( $value );
				break;
			case 'email':
				$output[ $key ] = sanitize_email( $value );
				break;
			case 'textarea':
				$output[ $key ] = sanitize_textarea_field( $value );
				break;
			default:
				$output[ $key ] = sanitize_text_field( $value );
		}
	}
	return $output;
}

function samanvay_add_theme_settings_page() {
	add_theme_page( __( 'Theme Settings', 'samanvay' ), __( 'Theme Settings', 'samanvay' ), 'manage_options', 'samanvay-theme-settings', 'samanvay_render_theme_settings_page' );
}
add_action( 'admin_menu', 'samanvay_add_theme_settings_page' );

function samanvay_theme_settings_admin_assets( $hook ) {
	if ( 'appearance_page_samanvay-theme-settings' !== $hook ) {
		return;
	}
	wp_enqueue_media();
	wp_enqueue_style( 'samanvay-theme-settings', get_template_directory_uri() . '/assets/css/theme-settings.css', array(), SAMANVAY_VERSION );
	wp_enqueue_script( 'samanvay-theme-settings', get_template_directory_uri() . '/assets/js/theme-settings.js', array(), SAMANVAY_VERSION, true );
}
add_action( 'admin_enqueue_scripts', 'samanvay_theme_settings_admin_assets' );

function samanvay_render_theme_settings_page() {
	$tabs     = samanvay_theme_settings_fields();
	$settings = wp_parse_args( get_option( 'samanvay_theme_settings', array() ), samanvay_theme_settings_defaults() );
	?>
	<div class="wrap samanvay-settings-notices">
		<h1><?php esc_html_e( 'Theme Settings', 'samanvay' ); ?></h1>
		<?php settings_errors(); ?>
	</div>
	<div class="wrap samanvay-settings-wrap">
		<div class="samanvay-settings-header"><div><h2><?php esc_html_e( 'Samanvay Theme Settings', 'samanvay' ); ?></h2><p><?php esc_html_e( 'Manage key theme content from one place.', 'samanvay' ); ?></p></div><span><?php echo esc_html( 'v' . SAMANVAY_VERSION ); ?></span></div>
		<form action="options.php" method="post" novalidate>
			<?php settings_fields( 'samanvay_theme_settings_group' ); ?>
			<div class="samanvay-settings-layout">
				<nav class="samanvay-settings-tabs" aria-label="<?php esc_attr_e( 'Theme settings sections', 'samanvay' ); ?>">
					<?php foreach ( $tabs as $tab_id => $tab ) : ?>
						<button type="button" class="samanvay-settings-tab" data-tab="<?php echo esc_attr( $tab_id ); ?>"><span class="dashicons <?php echo esc_attr( $tab['icon'] ); ?>"></span><?php echo esc_html( $tab['label'] ); ?></button>
					<?php endforeach; ?>
				</nav>
				<div class="samanvay-settings-panels">
					<?php foreach ( $tabs as $tab_id => $tab ) : ?>
						<section class="samanvay-settings-panel" data-panel="<?php echo esc_attr( $tab_id ); ?>">
							<h2><?php echo esc_html( $tab['label'] ); ?></h2>
							<?php $current_group = ''; ?>
							<?php foreach ( $tab['fields'] as $key => $field ) : ?>
								<?php
								$field_group = isset( $field[2] ) ? $field[2] : '';
								if ( $field_group && $field_group !== $current_group ) :
									$current_group = $field_group;
									?>
									<h3 class="samanvay-settings-group-title"><?php echo esc_html( $field_group ); ?></h3>
								<?php endif; ?>
								<div class="samanvay-setting-field"><label for="samanvay-<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $field[0] ); ?></label>
				<?php if ( 'gallery_repeater' === $field[1] ) : ?>
					<div class="samanvay-gallery-repeater">
						<input type="hidden" name="samanvay_theme_settings[<?php echo esc_attr( $key ); ?>][_empty]" value="0">
						<div class="samanvay-gallery-rows">
							<?php foreach ( (array) $settings[ $key ] as $image_id ) : ?>
								<?php $image_url = wp_get_attachment_image_url( absint( $image_id ), 'thumbnail' ); if ( ! $image_url ) { continue; } ?>
								<div class="samanvay-gallery-row" draggable="true"><button type="button" class="samanvay-gallery-handle" aria-label="<?php esc_attr_e( 'Drag to reorder', 'samanvay' ); ?>"><span class="dashicons dashicons-move"></span></button><img src="<?php echo esc_url( $image_url ); ?>" alt=""><input class="samanvay-gallery-image-id" type="hidden" name="samanvay_theme_settings[<?php echo esc_attr( $key ); ?>][]" value="<?php echo esc_attr( absint( $image_id ) ); ?>"><span class="samanvay-gallery-row__name"><?php echo esc_html( get_the_title( absint( $image_id ) ) ); ?></span><button type="button" class="button-link-delete samanvay-gallery-remove"><?php esc_html_e( 'Remove', 'samanvay' ); ?></button></div>
							<?php endforeach; ?>
						</div>
						<button type="button" class="button button-secondary samanvay-gallery-add"><span class="dashicons dashicons-images-alt2"></span><?php esc_html_e( 'Add Gallery Images', 'samanvay' ); ?></button>
						<p class="description"><?php esc_html_e( 'Select multiple images from the Media Library and drag them to change the display order.', 'samanvay' ); ?></p>
					</div>
				<?php elseif ( 'team_repeater' === $field[1] ) : ?>
					<div class="samanvay-team-repeater">
						<input type="hidden" name="samanvay_theme_settings[<?php echo esc_attr( $key ); ?>][_empty]" value="">
						<div class="samanvay-team-rows">
							<?php foreach ( (array) $settings[ $key ] as $member_index => $member ) : ?>
								<?php
								$member      = wp_parse_args( $member, array( 'image_id' => 0, 'name' => '', 'role' => '', 'description' => '', 'facebook' => '', 'linkedin' => '', 'whatsapp' => '' ) );
								$member_image = $member['image_id'] ? wp_get_attachment_image_url( absint( $member['image_id'] ), 'thumbnail' ) : '';
								?>
								<div class="samanvay-team-row is-collapsed" draggable="true">
									<div class="samanvay-team-row__header"><button type="button" class="samanvay-team-handle" aria-label="<?php esc_attr_e( 'Drag to reorder', 'samanvay' ); ?>"><span class="dashicons dashicons-move"></span></button><button type="button" class="samanvay-team-toggle" aria-expanded="false"><strong class="samanvay-team-row__title"><?php echo esc_html( $member['name'] ? $member['name'] : __( 'New member', 'samanvay' ) ); ?></strong><span class="dashicons dashicons-arrow-down-alt2" aria-hidden="true"></span></button><button type="button" class="button-link-delete samanvay-team-remove"><?php esc_html_e( 'Remove', 'samanvay' ); ?></button></div>
									<div class="samanvay-team-row__body">
										<div class="samanvay-team-image"><div class="samanvay-team-image__preview<?php echo $member_image ? ' has-image' : ''; ?>"><?php if ( $member_image ) : ?><img src="<?php echo esc_url( $member_image ); ?>" alt=""><?php else : ?><span class="dashicons dashicons-admin-users"></span><?php endif; ?></div><input class="samanvay-team-image-id" type="hidden" data-member-field="image_id" name="samanvay_theme_settings[<?php echo esc_attr( $key ); ?>][<?php echo esc_attr( $member_index ); ?>][image_id]" value="<?php echo esc_attr( absint( $member['image_id'] ) ); ?>"><button type="button" class="button samanvay-team-image-select"><?php esc_html_e( 'Select Image', 'samanvay' ); ?></button><button type="button" class="button-link-delete samanvay-team-image-remove"<?php echo $member_image ? '' : ' hidden'; ?>><?php esc_html_e( 'Remove image', 'samanvay' ); ?></button></div>
										<div class="samanvay-team-fields">
											<p><label><?php esc_html_e( 'Name', 'samanvay' ); ?></label><input type="text" data-member-field="name" name="samanvay_theme_settings[<?php echo esc_attr( $key ); ?>][<?php echo esc_attr( $member_index ); ?>][name]" value="<?php echo esc_attr( $member['name'] ); ?>"></p>
											<p><label><?php esc_html_e( 'Designation', 'samanvay' ); ?></label><input type="text" data-member-field="role" name="samanvay_theme_settings[<?php echo esc_attr( $key ); ?>][<?php echo esc_attr( $member_index ); ?>][role]" value="<?php echo esc_attr( $member['role'] ); ?>"></p>
											<p class="wide"><label><?php esc_html_e( 'Description', 'samanvay' ); ?></label><textarea rows="3" data-member-field="description" name="samanvay_theme_settings[<?php echo esc_attr( $key ); ?>][<?php echo esc_attr( $member_index ); ?>][description]"><?php echo esc_textarea( $member['description'] ); ?></textarea></p>
											<p><label>Facebook URL</label><input type="text" inputmode="url" data-member-field="facebook" name="samanvay_theme_settings[<?php echo esc_attr( $key ); ?>][<?php echo esc_attr( $member_index ); ?>][facebook]" value="<?php echo esc_attr( $member['facebook'] ); ?>"></p>
											<p><label>LinkedIn URL</label><input type="text" inputmode="url" data-member-field="linkedin" name="samanvay_theme_settings[<?php echo esc_attr( $key ); ?>][<?php echo esc_attr( $member_index ); ?>][linkedin]" value="<?php echo esc_attr( $member['linkedin'] ); ?>"></p>
											<p><label>WhatsApp URL</label><input type="text" inputmode="url" data-member-field="whatsapp" name="samanvay_theme_settings[<?php echo esc_attr( $key ); ?>][<?php echo esc_attr( $member_index ); ?>][whatsapp]" value="<?php echo esc_attr( $member['whatsapp'] ); ?>"></p>
										</div>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
						<button type="button" class="button button-secondary samanvay-team-add"><span class="dashicons dashicons-plus-alt2"></span><?php esc_html_e( 'Add Team Member', 'samanvay' ); ?></button>
						<p class="description"><?php esc_html_e( 'Drag members using the move handle to change their display order.', 'samanvay' ); ?></p>
					</div>
				<?php elseif ( 'youtube_repeater' === $field[1] ) : ?>
									<div class="samanvay-video-repeater">
										<div class="samanvay-video-rows">
											<?php $video_rows = ! empty( $settings[ $key ] ) && is_array( $settings[ $key ] ) ? $settings[ $key ] : array( '' ); ?>
											<?php foreach ( $video_rows as $video_url ) : ?>
												<div class="samanvay-video-row"><span class="dashicons dashicons-video-alt3"></span><input type="text" inputmode="url" name="samanvay_theme_settings[<?php echo esc_attr( $key ); ?>][]" value="<?php echo esc_attr( $video_url ); ?>" placeholder="https://www.youtube.com/watch?v=..."><button type="button" class="button-link-delete samanvay-video-remove"><?php esc_html_e( 'Remove', 'samanvay' ); ?></button></div>
											<?php endforeach; ?>
										</div>
										<button type="button" class="button button-secondary samanvay-video-add"><span class="dashicons dashicons-plus-alt2"></span><?php esc_html_e( 'Add YouTube Video', 'samanvay' ); ?></button>
										<p class="description"><?php esc_html_e( 'Add YouTube watch, Shorts, Live, embed, or youtu.be links. Thumbnails are loaded automatically.', 'samanvay' ); ?></p>
									</div>
				<?php elseif ( 'checkbox' === $field[1] ) : ?>
					<?php $toggle_help = isset( $field[3] ) ? $field[3] : __( 'Keep the header visible at the top while visitors scroll.', 'samanvay' ); ?>
					<label class="samanvay-toggle"><input id="samanvay-<?php echo esc_attr( $key ); ?>" type="checkbox" name="samanvay_theme_settings[<?php echo esc_attr( $key ); ?>]" value="1" <?php checked( ! empty( $settings[ $key ] ) ); ?>><span aria-hidden="true"></span><em><?php echo esc_html( $toggle_help ); ?></em></label>
								<?php elseif ( 'media' === $field[1] ) : ?>
									<?php $logo_url = $settings[ $key ] ? wp_get_attachment_image_url( absint( $settings[ $key ] ), 'medium' ) : ''; $is_qr = 'membership_qr' === $key; ?>
									<div class="samanvay-media-control">
										<div class="samanvay-media-preview<?php echo $logo_url ? ' has-image' : ''; ?>"><?php if ( $logo_url ) : ?><img src="<?php echo esc_url( $logo_url ); ?>" alt=""><?php else : ?><span class="dashicons dashicons-format-image"></span><em><?php echo esc_html( $is_qr ? __( 'No payment QR selected', 'samanvay' ) : __( 'No footer logo selected', 'samanvay' ) ); ?></em><?php endif; ?></div>
										<input id="samanvay-<?php echo esc_attr( $key ); ?>" class="samanvay-media-id" type="hidden" name="samanvay_theme_settings[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( absint( $settings[ $key ] ) ); ?>">
										<button type="button" class="button samanvay-media-select"><?php echo esc_html( $logo_url ? ( $is_qr ? __( 'Change QR', 'samanvay' ) : __( 'Change Logo', 'samanvay' ) ) : ( $is_qr ? __( 'Select QR', 'samanvay' ) : __( 'Select Logo', 'samanvay' ) ) ); ?></button>
										<button type="button" class="button-link-delete samanvay-media-remove"<?php echo $logo_url ? '' : ' hidden'; ?>><?php echo esc_html( $is_qr ? __( 'Remove QR', 'samanvay' ) : __( 'Remove Logo', 'samanvay' ) ); ?></button>
									</div>
								<?php elseif ( 'textarea' === $field[1] ) : ?>
									<textarea id="samanvay-<?php echo esc_attr( $key ); ?>" name="samanvay_theme_settings[<?php echo esc_attr( $key ); ?>]" rows="4"><?php echo esc_textarea( $settings[ $key ] ); ?></textarea>
				<?php else : ?>
					<?php $number_attributes = 'number' === $field[1] && isset( $field[3] ) ? ' min="' . esc_attr( $field[3]['min'] ) . '" max="' . esc_attr( $field[3]['max'] ) . '" step="1"' : ''; ?>
					<input id="samanvay-<?php echo esc_attr( $key ); ?>" type="<?php echo 'number' === $field[1] ? 'number' : 'text'; ?>"<?php echo in_array( $field[1], array( 'url', 'email' ), true ) ? ' inputmode="' . esc_attr( $field[1] ) . '"' : ''; ?><?php echo $number_attributes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> name="samanvay_theme_settings[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( $settings[ $key ] ); ?>">
								<?php endif; ?></div>
							<?php endforeach; ?>
						</section>
					<?php endforeach; ?>
					<div class="samanvay-settings-actions"><?php submit_button( __( 'Save Theme Settings', 'samanvay' ), 'primary', 'submit', false ); ?></div>
				</div>
			</div>
		</form>
	</div>
	<?php
}
