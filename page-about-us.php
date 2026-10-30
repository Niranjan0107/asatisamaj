<?php
/**
 * Template Name: About Us
 * Template Post Type: page
 *
 * @package Samanvay
 */

get_header();
$hero_image    = get_template_directory_uri() . '/assets/images/community-hero.png';
$collage_image = get_template_directory_uri() . '/assets/images/about-community-collage.png';
?>
<main id="primary" class="about-page">
	<section class="about-page__hero" aria-labelledby="about-page-title">
		<div class="site-container about-page__hero-grid">
			<div class="about-page__hero-copy">
				<p class="about-page__eyebrow"><?php esc_html_e( 'हमारे बारे में', 'samanvay' ); ?></p>
				<h1 id="about-page-title"><?php esc_html_e( 'एकता, संस्कार और सेवा से जुड़ा हमारा समाज', 'samanvay' ); ?></h1>
				<p><?php esc_html_e( 'हम एक ऐसा संगठित समुदाय हैं जो अपनी सांस्कृतिक विरासत को सहेजते हुए शिक्षा, सहयोग और सामाजिक प्रगति के लिए मिलकर कार्य करता है।', 'samanvay' ); ?></p>
				<a class="about-page__button" href="#about-story"><?php esc_html_e( 'हमारी यात्रा जानें', 'samanvay' ); ?><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M6 13l6 6 6-6"/></svg></a>
			</div>
			<div class="about-page__hero-image"><img src="<?php echo esc_url( $hero_image ); ?>" alt="<?php esc_attr_e( 'समुदाय के सदस्य एक साथ', 'samanvay' ); ?>"></div>
		</div>
	</section>

	<section class="about-page__story" id="about-story" aria-labelledby="about-story-title">
		<div class="site-container about-page__story-grid">
			<div class="about-page__collage"><img src="<?php echo esc_url( $collage_image ); ?>" alt="<?php esc_attr_e( 'समाज की सेवा और सांस्कृतिक गतिविधियाँ', 'samanvay' ); ?>" loading="lazy"></div>
			<div class="about-page__story-copy">
				<p class="about-page__eyebrow"><?php esc_html_e( 'हमारी पहचान', 'samanvay' ); ?></p>
				<h2 id="about-story-title"><?php esc_html_e( 'असाटी समाज—एक परिवार, एक उद्देश्य', 'samanvay' ); ?></h2>
				<p><?php esc_html_e( 'असाटी समाज की पहचान आपसी विश्वास, परिश्रम, पारिवारिक मूल्यों और सेवा भावना से बनी है। पीढ़ियों से हमारे परिवार संस्कृति और परंपराओं को आगे बढ़ाते हुए समाज के विकास में सक्रिय भूमिका निभाते आए हैं।', 'samanvay' ); ?></p>
				<p><?php esc_html_e( 'आज हम शिक्षा, स्वास्थ्य, महिला सशक्तिकरण, युवा विकास, सांस्कृतिक संरक्षण और जरूरतमंद परिवारों की सहायता के माध्यम से एक समावेशी तथा आत्मनिर्भर समुदाय बनाने के लिए प्रतिबद्ध हैं।', 'samanvay' ); ?></p>
				<ul class="about-page__checks">
					<li><?php esc_html_e( 'हर परिवार की सहभागिता और सम्मान', 'samanvay' ); ?></li>
					<li><?php esc_html_e( 'नई पीढ़ी के लिए शिक्षा और अवसर', 'samanvay' ); ?></li>
					<li><?php esc_html_e( 'संस्कृति, परंपरा और सामाजिक सेवा का संरक्षण', 'samanvay' ); ?></li>
				</ul>
			</div>
		</div>
	</section>

	<section class="about-page__purpose" aria-labelledby="about-purpose-title">
		<div class="site-container">
			<header class="about-page__heading"><svg viewBox="0 0 54 18" aria-hidden="true"><path d="M2 10c5-8 10 8 15 0s10 8 15 0 10 8 20 0"/></svg><h2 id="about-purpose-title"><?php esc_html_e( 'हमारी दिशा', 'samanvay' ); ?></h2><svg viewBox="0 0 54 18" aria-hidden="true"><path d="M2 10c5-8 10 8 15 0s10 8 15 0 10 8 20 0"/></svg></header>
			<div class="about-page__purpose-grid">
				<article><span><svg viewBox="0 0 48 48" aria-hidden="true"><path d="M24 42S7 33 7 20c0-7 9-10 17-1 8-9 17-6 17 1 0 13-17 22-17 22Z"/><path d="M16 29c5-2 7-1 8 4 2-5 4-6 9-4"/></svg></span><h3><?php esc_html_e( 'हमारा उद्देश्य', 'samanvay' ); ?></h3><p><?php esc_html_e( 'समाज के प्रत्येक परिवार को जोड़ना, जरूरत के समय सहयोग देना और सामूहिक विकास के अवसर उपलब्ध कराना।', 'samanvay' ); ?></p></article>
				<article><span><svg viewBox="0 0 48 48" aria-hidden="true"><circle cx="24" cy="24" r="18"/><circle cx="24" cy="24" r="10"/><circle cx="24" cy="24" r="3"/><path d="m28 20 11-11"/></svg></span><h3><?php esc_html_e( 'हमारा मिशन', 'samanvay' ); ?></h3><p><?php esc_html_e( 'शिक्षा, स्वास्थ्य, कौशल, संस्कृति और सेवा आधारित पहलों को पारदर्शी एवं सहभागितापूर्ण तरीके से आगे बढ़ाना।', 'samanvay' ); ?></p></article>
				<article><span><svg viewBox="0 0 48 48" aria-hidden="true"><path d="M5 24s7-12 19-12 19 12 19 12-7 12-19 12S5 24 5 24Z"/><circle cx="24" cy="24" r="6"/></svg></span><h3><?php esc_html_e( 'हमारी दृष्टि', 'samanvay' ); ?></h3><p><?php esc_html_e( 'एक शिक्षित, संगठित, संस्कारवान और आत्मनिर्भर समाज जहाँ हर सदस्य सम्मान और अवसर के साथ आगे बढ़े।', 'samanvay' ); ?></p></article>
			</div>
		</div>
	</section>

	<section class="about-page__values" aria-labelledby="about-values-title">
		<div class="site-container">
			<header class="about-page__heading"><svg viewBox="0 0 54 18" aria-hidden="true"><path d="M2 10c5-8 10 8 15 0s10 8 15 0 10 8 20 0"/></svg><h2 id="about-values-title"><?php esc_html_e( 'हमारे मूल मूल्य', 'samanvay' ); ?></h2><svg viewBox="0 0 54 18" aria-hidden="true"><path d="M2 10c5-8 10 8 15 0s10 8 15 0 10 8 20 0"/></svg></header>
			<div class="about-page__values-grid">
				<div><strong>01</strong><h3><?php esc_html_e( 'एकता', 'samanvay' ); ?></h3><p><?php esc_html_e( 'परिवारों को एक मजबूत सूत्र में जोड़ना।', 'samanvay' ); ?></p></div>
				<div><strong>02</strong><h3><?php esc_html_e( 'पारदर्शिता', 'samanvay' ); ?></h3><p><?php esc_html_e( 'विश्वास और जवाबदेही के साथ कार्य करना।', 'samanvay' ); ?></p></div>
				<div><strong>03</strong><h3><?php esc_html_e( 'समावेशिता', 'samanvay' ); ?></h3><p><?php esc_html_e( 'हर सदस्य को सम्मान और सहभागिता देना।', 'samanvay' ); ?></p></div>
				<div><strong>04</strong><h3><?php esc_html_e( 'सेवा', 'samanvay' ); ?></h3><p><?php esc_html_e( 'जरूरतमंदों के साथ संवेदनशीलता से खड़े रहना।', 'samanvay' ); ?></p></div>
			</div>
		</div>
	</section>

	<?php while ( have_posts() ) : the_post(); if ( trim( get_the_content() ) ) : ?>
		<section class="about-page__editor"><div class="site-container entry-content"><?php the_content(); wp_link_pages(); ?></div></section>
	<?php endif; endwhile; ?>
</main>
<?php get_footer(); ?>
