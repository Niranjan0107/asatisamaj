<?php
/**
 * Homepage community history and introduction section.
 *
 * @package Samanvay
 */
?>
<section class="home-history" aria-labelledby="home-history-title">
	<div class="site-container">
		<header class="home-history__heading">
			<svg viewBox="0 0 54 18" aria-hidden="true"><path d="M2 10c5-8 10 8 15 0s10 8 15 0 10 8 20 0"/></svg>
			<div><p><?php esc_html_e( 'हमारी विरासत', 'samanvay' ); ?></p><h2 id="home-history-title"><?php esc_html_e( 'समाज का इतिहास / परिचय', 'samanvay' ); ?></h2></div>
			<svg viewBox="0 0 54 18" aria-hidden="true"><path d="M2 10c5-8 10 8 15 0s10 8 15 0 10 8 20 0"/></svg>
		</header>
		<p class="home-history__lead"><?php esc_html_e( 'हमारे समाज की यात्रा परंपरा, परिश्रम, आपसी विश्वास और सेवा के मूल्यों से बनी है। समय के साथ समुदाय ने अपनी पहचान को सहेजते हुए शिक्षा, संगठन और सामाजिक प्रगति की दिशा में निरंतर कदम बढ़ाए हैं।', 'samanvay' ); ?></p>

		<div class="home-history__timeline">
			<article class="home-history__item">
				<span class="home-history__marker"><svg aria-hidden="true" viewBox="0 0 48 48"><path d="M8 40h32M12 40V20l12-9 12 9v20M19 40V27h10v13"/><path d="M7 21 24 7l17 14"/></svg></span>
				<span class="home-history__step"><?php esc_html_e( 'आरंभ', 'samanvay' ); ?></span>
				<h3><?php esc_html_e( 'जड़ें और पहचान', 'samanvay' ); ?></h3>
				<p><?php esc_html_e( 'परिवार, परंपरा और नैतिक मूल्यों ने समाज की मजबूत पहचान तथा सामाजिक आधार का निर्माण किया।', 'samanvay' ); ?></p>
			</article>
			<article class="home-history__item">
				<span class="home-history__marker"><svg aria-hidden="true" viewBox="0 0 48 48"><circle cx="16" cy="16" r="6"/><circle cx="33" cy="16" r="6"/><path d="M5 40v-5c0-7 4-12 11-12h17c7 0 10 5 10 12v5M17 25l7 7 7-7"/></svg></span>
				<span class="home-history__step"><?php esc_html_e( 'संगठन', 'samanvay' ); ?></span>
				<h3><?php esc_html_e( 'एकता और सहभागिता', 'samanvay' ); ?></h3>
				<p><?php esc_html_e( 'आपसी सहयोग, सामुदायिक संवाद और साझा प्रयासों ने परिवारों को एक सशक्त सूत्र में जोड़ा।', 'samanvay' ); ?></p>
			</article>
			<article class="home-history__item">
				<span class="home-history__marker"><svg aria-hidden="true" viewBox="0 0 48 48"><path d="m5 16 19-9 19 9-19 9-19-9Z"/><path d="M12 21v12c8 6 16 6 24 0V21M42 18v15"/></svg></span>
				<span class="home-history__step"><?php esc_html_e( 'प्रगति', 'samanvay' ); ?></span>
				<h3><?php esc_html_e( 'शिक्षा और विकास', 'samanvay' ); ?></h3>
				<p><?php esc_html_e( 'शिक्षा, कौशल, व्यवसाय और जनकल्याण के माध्यम से नई संभावनाओं और अवसरों का विस्तार हुआ।', 'samanvay' ); ?></p>
			</article>
			<article class="home-history__item">
				<span class="home-history__marker"><svg aria-hidden="true" viewBox="0 0 48 48"><path d="M24 42S7 33 7 20c0-7 9-10 17-1 8-9 17-6 17 1 0 13-17 22-17 22Z"/><path d="M24 19v16M17 27h14"/></svg></span>
				<span class="home-history__step"><?php esc_html_e( 'आज और आगे', 'samanvay' ); ?></span>
				<h3><?php esc_html_e( 'सेवा का नया संकल्प', 'samanvay' ); ?></h3>
				<p><?php esc_html_e( 'नई पीढ़ी आधुनिक सोच के साथ संस्कृति, सामाजिक दायित्व और समावेशी प्रगति को आगे बढ़ा रही है।', 'samanvay' ); ?></p>
			</article>
		</div>
	</div>
</section>
