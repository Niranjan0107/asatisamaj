<?php
/**
 * Homepage Asati community information section.
 *
 * @package Samanvay
 */
?>
<section class="home-asati" aria-labelledby="home-asati-title">
	<svg class="home-asati__pattern home-asati__pattern--left" aria-hidden="true" viewBox="0 0 180 180"><circle cx="90" cy="90" r="72"/><circle cx="90" cy="90" r="48"/><path d="M90 7c12 23 12 40 0 53-12-13-12-30 0-53Zm0 166c-12-23-12-40 0-53 12 13 12 30 0 53ZM7 90c23-12 40-12 53 0-13 12-30 12-53 0Zm166 0c-23 12-40 12-53 0 13-12 30-12 53 0Z"/></svg>
	<div class="site-container home-asati__inner">
		<div class="home-asati__intro">
			<p class="home-asati__eyebrow"><?php esc_html_e( 'हमारी पहचान', 'samanvay' ); ?></p>
			<h2 id="home-asati-title"><?php esc_html_e( 'असाटी समाज के बारे में जानकारी', 'samanvay' ); ?></h2>
			<p><?php esc_html_e( 'असाटी समाज एकता, परिश्रम, शिक्षा और सामाजिक सहयोग के मूल्यों से जुड़ा समुदाय है। हमारी परंपराएं परिवारों को जोड़ती हैं और नई पीढ़ी को संस्कारों के साथ आगे बढ़ने की प्रेरणा देती हैं।', 'samanvay' ); ?></p>
			<p><?php esc_html_e( 'समाज के सदस्य शिक्षा, सेवा, व्यवसाय, संस्कृति और जनकल्याण के क्षेत्रों में मिलकर योगदान देते हैं, ताकि प्रत्येक परिवार को प्रगति और सहभागिता के अवसर मिल सकें।', 'samanvay' ); ?></p>
			<a class="home-asati__button" href="<?php echo esc_url( home_url( '/about/' ) ); ?>"><?php esc_html_e( 'समाज के बारे में और जानें', 'samanvay' ); ?><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
		</div>

		<div class="home-asati__values">
			<article class="home-asati__card">
				<span class="home-asati__icon"><svg aria-hidden="true" viewBox="0 0 52 52"><circle cx="18" cy="16" r="7"/><circle cx="35" cy="16" r="7"/><path d="M5 43v-5c0-8 5-13 13-13h17c8 0 12 5 12 13v5M19 27l7 7 7-7"/></svg></span>
			<div><h3><?php esc_html_e( 'एकता और सहयोग', 'samanvay' ); ?></h3><p><?php esc_html_e( 'परिवारों को जोड़कर पारस्परिक सहयोग और सामूहिक विकास को बढ़ावा देना।', 'samanvay' ); ?></p></div>
			</article>
			<article class="home-asati__card">
				<span class="home-asati__icon"><svg aria-hidden="true" viewBox="0 0 52 52"><path d="M6 17 26 7l20 10-20 10L6 17Z"/><path d="M14 22v13c8 6 16 6 24 0V22M45 19v15"/></svg></span>
				<div><h3><?php esc_html_e( 'शिक्षा और प्रगति', 'samanvay' ); ?></h3><p><?php esc_html_e( 'विद्यार्थियों और युवाओं को शिक्षा, कौशल तथा आगे बढ़ने के अवसर प्रदान करना।', 'samanvay' ); ?></p></div>
			</article>
			<article class="home-asati__card">
				<span class="home-asati__icon"><svg aria-hidden="true" viewBox="0 0 52 52"><path d="M26 44S8 34 8 20c0-7 9-10 18-1 9-9 18-6 18 1 0 14-18 24-18 24Z"/><path d="M16 31c5-2 8-1 10 4 2-5 5-6 10-4"/></svg></span>
				<div><h3><?php esc_html_e( 'संस्कार और सेवा', 'samanvay' ); ?></h3><p><?php esc_html_e( 'सांस्कृतिक मूल्यों को सहेजते हुए समाज सेवा और जरूरतमंदों की सहायता करना।', 'samanvay' ); ?></p></div>
			</article>
		</div>
	</div>
	<svg class="home-asati__pattern home-asati__pattern--right" aria-hidden="true" viewBox="0 0 180 180"><circle cx="90" cy="90" r="72"/><circle cx="90" cy="90" r="48"/><path d="M90 7c12 23 12 40 0 53-12-13-12-30 0-53Zm0 166c-12-23-12-40 0-53 12 13 12 30 0 53ZM7 90c23-12 40-12 53 0-13 12-30 12-53 0Zm166 0c-23 12-40 12-53 0 13-12 30-12 53 0Z"/></svg>
</section>
