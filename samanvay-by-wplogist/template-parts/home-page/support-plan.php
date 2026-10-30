<?php
/** Community support plan and key rules. @package Samanvay */
if ( ! defined( 'ABSPATH' ) ) { exit; }
$rules_url = home_url( '/rules/' );
$icons = array(
	'shield' => '<path d="M12 3c3 2 5 3 8 3v6c0 5-4 8-8 10-4-2-8-5-8-10V6c3 0 5-1 8-3Z"/><path d="m8 12 3 3 5-6"/>',
	'people' => '<circle cx="12" cy="6" r="3"/><circle cx="4" cy="7" r="2"/><circle cx="20" cy="7" r="2"/><path d="M7 22v-7a5 5 0 0 1 10 0v7ZM2 20v-7a3 3 0 0 1 3-3m17 10v-7a3 3 0 0 0-3-3"/>',
	'heart' => '<path d="M12 21 3 12C-3 5 6-2 12 5c6-7 15 0 9 7Z"/>',
	'fee' => '<path d="M8 20a9 9 0 0 1 1-17M5 3h5v5"/><circle cx="16" cy="16" r="6"/><path d="M16 13v6m-3-3h6"/>',
	'clipboard' => '<path d="M8 5H5v17h14V5h-3M9 2h6v5H9Z M9 12h6m-6 4h4"/>',
	'clock' => '<circle cx="12" cy="14" r="8"/><path d="M12 10v5l3 2M9 2h6m-3 0v4m6-1 2 2"/>',
);
$values = array( 'shield' => 'सुरक्षा', 'people' => 'सहयोग', 'heart' => 'संवेदना' );
$cards = array(
	array( 'fee', 'सदस्यता शुल्क', 'निःशुल्क', 'वार्षिक शुल्क: निःशुल्क' ),
	array( 'clipboard', 'लॉकिंग पीरियड', '4 से 6 माह', '(पंजीकरण अवधि अनुसार)' ),
	array( 'clock', 'न्यूनतम सहभागिता', '90%', '(उदाहरण: 10 में 9 सहयोग)' ),
	array( 'heart', 'मानवीय सहायता', 'दिवंगत सदस्य के', 'नॉमिनी हेतु सहयोग' ),
);
?>
<section class="home-support" aria-labelledby="home-support-title">
	<div class="home-support__banner"><div class="site-container home-support__layout">
		<div class="home-support__copy">
			<h2 id="home-support-title"><span>₹2,00,000</span> से शुरुआत</h2>
			<p>योजना का प्रारंभ ₹2,00,000 की सहयोग राशि से किया गया है।<br>सदस्यों की संख्या में वृद्धि के साथ सहयोग राशि में समय-समय पर वृद्धि की जाएगी।</p>
			<a class="home-support__button" href="#home-support-rules">अधिक जानकारी <span aria-hidden="true">→</span></a>
		</div>
		<div class="home-support__values"><?php foreach ( $values as $icon => $label ) : ?><div><svg viewBox="0 0 24 24" aria-hidden="true"><?php echo $icons[ $icon ]; // Static theme SVG paths. ?></svg><strong><?php echo esc_html( $label ); ?></strong></div><?php endforeach; ?></div>
	</div></div>
	<div class="site-container home-support__rules" id="home-support-rules">
		<h2>मुख्य नियम (संक्षेप में)</h2>
		<div class="home-support__cards"><?php foreach ( $cards as $card ) : ?><article class="home-support__card"><svg viewBox="0 0 24 24" aria-hidden="true"><?php echo $icons[ $card[0] ]; // Static theme SVG paths. ?></svg><h3><?php echo esc_html( $card[1] ); ?></h3><strong><?php echo esc_html( $card[2] ); ?></strong><p><?php echo esc_html( $card[3] ); ?></p></article><?php endforeach; ?></div>
		<a class="home-support__button home-support__button--rules" href="<?php echo esc_url( $rules_url ); ?>">पूरी नियमावली देखें <span aria-hidden="true">→</span></a>
	</div>
</section>
