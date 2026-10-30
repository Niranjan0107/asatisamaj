<?php
/** Homepage hero section. @package Samanvay */
$hero_image = get_template_directory_uri() . '/assets/images/community-hero.png';
global $wpdb;
$active_member_count = (int) $wpdb->get_var( $wpdb->prepare(
	'SELECT COUNT(*) FROM ' . samanvay_members_table() . " WHERE status = 'active' AND (is_lifetime_free = 1 OR membership_expires > %s)",
	current_time( 'mysql', true )
) );
?>
<section class="home-hero" aria-labelledby="home-hero-title">
	<div class="home-hero__photo" aria-hidden="true"><img src="<?php echo esc_url( $hero_image ); ?>" alt=""></div>
	<svg class="home-hero__mandala" aria-hidden="true" viewBox="0 0 220 220"><circle cx="110" cy="110" r="96"/><circle cx="110" cy="110" r="68"/><circle cx="110" cy="110" r="36"/><path d="M110 9c14 28 14 49 0 67-14-18-14-39 0-67Zm0 202c-14-28-14-49 0-67 14 18 14 39 0 67ZM9 110c28-14 49-14 67 0-18 14-39 14-67 0Zm202 0c-28 14-49 14-67 0 18-14 39-14 67 0Z"/><path d="M39 39c29 9 46 25 48 48-23-2-39-19-48-48Zm142 142c-29-9-46-25-48-48 23 2 39 19 48 48ZM181 39c-9 29-25 46-48 48 2-23 19-39 48-48ZM39 181c9-29 25-46 48-48-2 23-19 39-48 48Z"/><path d="m110 72 11 24 26 3-19 18 5 26-23-12-23 12 5-26-19-18 26-3 11-24Z"/></svg>
	<div class="site-container home-hero__inner">
		<div class="home-hero__content">
			<h1 id="home-hero-title"><span>अखिल भारतीय असाटी समाज</span><strong>॥ एकता में ही शक्ति है ॥</strong></h1>
			<p>अखिल भारतीय असाटी समाज देश-विदेश में निवासरत असाटी समाज के परिवारों को एक मंच पर जोड़ने, सामाजिक एकता को मजबूत करने तथा शिक्षा, सेवा, संस्कार और समाज के सर्वांगीण विकास की दिशा में निरंतर कार्य करने के लिए समर्पित है।</p>
			<div class="home-hero__actions"><a class="home-hero__primary" href="/member-registration/">समुदाय से जुड़ें <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M5 12h14M13 6l6 6-6 6"/></svg></a><a class="home-hero__secondary" href="rules/">विस्तृत नियमावली देखें <svg aria-hidden="true" viewBox="0 0 24 24"><path d="M2 12s4-7 10-7 10 7 10 7-4 7-10 7S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/></svg></a></div>
		</div>
	</div>
	<div class="site-container home-hero__stats">
		<div class="home-hero__stat home-hero__stat--orange">
<svg xmlns="http://www.w3.org/2000/svg" width="244" height="244" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="0.5" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-group preview-icon"><path d="M17 21v-1a2 2 0 00-2-2H9a2 2 0 00-2 2v1"/><path d="M19 10h1a2 2 0 012 2v1"/><path d="M5 10H4a2 2 0 00-2 2v1"/><circle cx="12" cy="11" r="3"/><circle cx="18" cy="4" r="2"/><circle cx="6" cy="4" r="2"/></svg><span><strong><?php echo esc_html( number_format_i18n( $active_member_count ) ); ?></strong><small>सक्रिय सदस्य</small></span></div>
		<div class="home-hero__stat home-hero__stat--coral">
<svg xmlns="http://www.w3.org/2000/svg" width="244" height="244" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-hand-heart preview-icon"><path d="M11 14h2a2 2 0 0 0 0-4h-3c-.6 0-1.1.2-1.4.6L3 16"/><path d="m14.45 13.39 5.05-4.694C20.196 8 21 6.85 21 5.75a2.75 2.75 0 0 0-4.797-1.837.276.276 0 0 1-.406 0A2.75 2.75 0 0 0 11 5.75c0 1.2.802 2.248 1.5 2.946L16 11.95"/><path d="m2 15 6 6"/><path d="m7 20 1.6-1.4c.3-.4.8-.6 1.4-.6h4c1.1 0 2.1-.4 2.8-1.2l4.6-4.4a1 1 0 0 0-2.75-2.91"/></svg><span><strong>120+</strong><small>सेवा पहल</small></span></div>
		<div class="home-hero__stat home-hero__stat--teal">
<svg xmlns="http://www.w3.org/2000/svg" width="244" height="244" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-map-pinned preview-icon"><path d="M18 8c0 3.613-3.869 7.429-5.393 8.795a1 1 0 01-1.214 0C9.87 15.429 6 11.613 6 8a6 6 0 0112 0"/><path d="M4.474 15h-.197a1 1 0 00-.969.753l-1.097 4.35a1.5 1.5 0 001.444 1.898L20.344 22a1.5 1.5 0 001.446-1.897l-1.098-4.35a1 1 0 00-.969-.753h-.197"/><circle cx="12" cy="8" r="2"/></svg><span><strong>45+</strong><small>शहरों में उपस्थिति</small></span></div>
		<div class="home-hero__stat home-hero__stat--gold">
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-handshake preview-icon"><path d="m11 17 2 2a1 1 0 1 0 3-3"/><path d="m14 14 2.5 2.5a1 1 0 1 0 3-3l-3.88-3.88a3 3 0 0 0-4.24 0l-.88.88a1 1 0 1 1-3-3l2.81-2.81a5.79 5.79 0 0 1 7.06-.87l.47.28a2 2 0 0 0 1.42.25L21 4"/><path d="m21 3 1 11h-2"/><path d="M3 3 2 14l6.5 6.5a1 1 0 1 0 3-3"/><path d="M3 4h8"/></svg><span><strong>10+</strong><small>वर्षों का विश्वास</small></span></div>
	</div>
</section>
