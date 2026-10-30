<?php
/** Homepage about section. @package Samanvay */
$collage = get_template_directory_uri() . '/assets/images/about-community-collage.png';
?>
<section class="home-about" aria-labelledby="home-about-title">
	<div class="site-container home-about__grid">
		<div class="home-about__collage">
			<div class="home-about__photo home-about__photo--one" role="img" aria-label="स्कूली शिक्षा"></div>
			<div class="home-about__photo home-about__photo--two" role="img" aria-label="महिला समुदाय"></div>
			<div class="home-about__photo home-about__photo--three" role="img" aria-label="वृक्षारोपण सेवा"></div>
			<div class="home-about__photo home-about__photo--four" role="img" aria-label="सांस्कृतिक कार्यक्रम"></div>
			<style>.home-about__photo{background-image:url('<?php echo esc_url( $collage ); ?>')}</style>
		</div>
		<div class="home-about__content">
			<p class="home-about__eyebrow">हमारा संकल्प</p>
			<h2 id="home-about-title">संगठन • शिक्षा • संस्कार • सेवा • सहयोग • सम्मान</h2>
			<p class="home-about__intro">अखिल भारतीय असाटी समाज देश-विदेश में निवासरत असाटी समाज के परिवारों को एक मंच पर जोड़ने, सामाजिक एकता को मजबूत करने तथा शिक्षा, सेवा, संस्कार और समाज के सर्वांगीण विकास की दिशा में निरंतर कार्य करने के लिए समर्पित है।
हमारा प्रयास है कि समाज का प्रत्येक परिवार संगठन से जुड़े, हमारी नई पीढ़ी को शिक्षा एवं संस्कार के बेहतर अवसर मिलें और समाज की प्रतिभाओं को उचित सम्मान एवं प्रोत्साहन मिले।</p>
			<div class="home-about__values">
				<div><svg aria-hidden="true" viewBox="0 0 52 52"><path d="M5 31c7-3 12-2 17 2l5 4 8-5c5-3 10-3 15 0-6 7-13 11-22 15-7-3-14-5-23-7v-9Z"/><path d="M27 27s-11-6-11-14c0-8 9-10 13-4 4-6 13-4 13 4 0 8-15 14-15 14Z"/></svg><span><strong>समर्पित</strong><small>निःस्वार्थ सेवा</small></span></div>
				<div><svg aria-hidden="true" viewBox="0 0 52 52"><path d="M26 4 42 10v13c0 12-7 20-16 25-9-5-16-13-16-25V10l16-6Z"/><circle cx="27" cy="21" r="7"/><path d="m33 32 5 5M35 37l4-4"/></svg><span><strong>पारदर्शी</strong><small>और जवाबदेह</small></span></div>
				<div><svg aria-hidden="true" viewBox="0 0 52 52"><circle cx="18" cy="15" r="6"/><circle cx="34" cy="15" r="6"/><path d="M10 43V29c0-6 4-10 8-10s8 4 8 10v14M42 43V29c0-6-4-10-8-10s-8 4-8 10M18 28l8 8 8-8"/></svg><span><strong>समावेशी</strong><small>और समानता</small></span></div>
				<div><svg aria-hidden="true" viewBox="0 0 52 52"><path d="M18 43h16M16 47h20M20 38h12"/><path d="M26 37c-9 0-15-6-13-14 2 5 6 6 9 8-3-8 2-15 7-22 0 8 6 10 7 17 1 7-4 11-10 11Z"/><path d="M26 12V5M15 16l-5-5M37 16l5-5"/></svg><span><strong>संस्कृति</strong><small>और परंपरा</small></span></div>
			</div>
			<a class="home-about__button" href="<?php echo esc_url( home_url( '/about/' ) ); ?>">हमारे बारे में और जानें <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a>
		</div>
	</div>
</section>
