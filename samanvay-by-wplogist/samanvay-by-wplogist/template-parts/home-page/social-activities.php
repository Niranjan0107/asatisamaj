<?php
/**
 * Homepage social activities section.
 *
 * @package Samanvay
 */

$activities_image = get_template_directory_uri() . '/assets/images/community-gallery-strip.png';
$activities       = array(
	array(
		'title'       => __( 'शिक्षा एवं प्रतिभा प्रोत्साहन', 'samanvay' ),
		'description' => __( 'विद्यार्थियों के लिए शैक्षणिक सहयोग, मार्गदर्शन और प्रतिभा सम्मान कार्यक्रम।', 'samanvay' ),
		'icon'        => '<path d="M4 7.5 12 4l8 3.5-8 3.5-8-3.5Z"/><path d="M7 9.2V14c3 2.2 7 2.2 10 0V9.2M20 8v6"/>',
	),
	array(
		'title'       => __( 'स्वास्थ्य एवं सेवा शिविर', 'samanvay' ),
		'description' => __( 'समाज के सभी वर्गों के लिए स्वास्थ्य जाँच, रक्तदान और जनकल्याण सेवा।', 'samanvay' ),
		'icon'        => '<path d="M12 21s-7-4.4-7-10a4 4 0 0 1 7-2.6A4 4 0 0 1 19 11c0 5.6-7 10-7 10Z"/><path d="M9 13h6M12 10v6"/>',
	),
	array(
		'title'       => __( 'महिला एवं युवा सहभागिता', 'samanvay' ),
		'description' => __( 'महिलाओं और युवाओं के नेतृत्व, कौशल विकास एवं सामाजिक भागीदारी को प्रोत्साहन।', 'samanvay' ),
		'icon'        => '<circle cx="9" cy="8" r="3"/><circle cx="17" cy="9" r="2.5"/><path d="M3.5 20c.4-4.2 2.3-6 5.5-6s5.1 1.8 5.5 6M14 14.5c3.7-.8 6 1 6.5 4.5"/>',
	),
	array(
		'title'       => __( 'पर्यावरण एवं सामुदायिक सेवा', 'samanvay' ),
		'description' => __( 'वृक्षारोपण, स्वच्छता और सामुदायिक सहयोग से बेहतर भविष्य का निर्माण।', 'samanvay' ),
		'icon'        => '<path d="M12 21V10"/><path d="M12 14c-5 0-8-3-8-8 5 0 8 3 8 8ZM12 11c4.5 0 7-2.6 7-7-4.5 0-7 2.6-7 7Z"/>',
	),
);
?>
<section class="home-activities" aria-labelledby="home-activities-title">
	<div class="site-container">
		<header class="home-activities__heading">
			<svg viewBox="0 0 54 18" aria-hidden="true"><path d="M2 10c5-8 10 8 15 0s10 8 15 0 10 8 20 0"/></svg>
			<h2 id="home-activities-title"><?php esc_html_e( 'सामाजिक गतिविधियाँ', 'samanvay' ); ?></h2>
			<svg viewBox="0 0 54 18" aria-hidden="true"><path d="M2 10c5-8 10 8 15 0s10 8 15 0 10 8 20 0"/></svg>
		</header>
		<p class="home-activities__intro"><?php esc_html_e( 'समाज को संगठित, सशक्त और सहयोगी बनाने के लिए हमारी प्रमुख सामाजिक गतिविधियाँ।', 'samanvay' ); ?></p>
		<div class="home-activities__grid">
			<?php foreach ( $activities as $index => $activity ) : ?>
				<article class="home-activities__card">
					<div class="home-activities__photo home-activities__photo--<?php echo esc_attr( $index + 1 ); ?>" style="background-image:url('<?php echo esc_url( $activities_image ); ?>')" role="img" aria-label="<?php echo esc_attr( $activity['title'] ); ?>">
						<span class="home-activities__icon"><svg viewBox="0 0 24 24" aria-hidden="true"><?php echo wp_kses( $activity['icon'], array( 'path' => array( 'd' => array() ), 'circle' => array( 'cx' => array(), 'cy' => array(), 'r' => array() ) ) ); ?></svg></span>
					</div>
					<div class="home-activities__content">
						<h3><?php echo esc_html( $activity['title'] ); ?></h3>
						<p><?php echo esc_html( $activity['description'] ); ?></p>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</section>
