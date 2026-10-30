<?php
/** Homepage upcoming events section. @package Samanvay */
$events_image = get_template_directory_uri() . '/assets/images/upcoming-events.png';
$events = array(
	array( '15', 'जून', '2025', 'सेवा सप्ताह – पौधारोपण अभियान', 'साहब नगर उद्यान', 'रविवार, सुबह 10:00 बजे', 'आइए, पर्यावरण संरक्षण में योगदान दें और अधिक से अधिक पेड़ लगाकर प्रकृति को हरा-भरा बनाएं।', 'coral', 'one' ),
	array( '22', 'जून', '2025', 'नि:शुल्क स्वास्थ्य जाँच शिविर', 'समुदाय भवन, सेक्टर 9', 'रविवार, शाम 5 बजे', 'मुफ्त स्वास्थ्य जाँच, परामर्श और दवाइयों की सुविधा सभी के लिए उपलब्ध।', 'teal', 'two' ),
	array( '05', 'जुलाई', '2025', 'सांस्कृतिक संध्या', 'सामुदायिक हॉल', 'शनिवार, शाम 6 बजे', 'संगीत, नृत्य और लोक कला की सुंदर प्रस्तुति के साथ एक यादगार शाम।', 'orange', 'three' ),
);
?>
<section class="home-events" id="events" aria-labelledby="events-title">
	<div class="site-container"><br>
		<div class="home-events__heading"><svg aria-hidden="true" viewBox="0 0 48 16"><path d="M2 9c6-8 12 8 18 0s12 8 18 0c3-4 6-4 8-1"/></svg><h2 id="events-title">आगामी कार्यक्रम</h2><svg aria-hidden="true" viewBox="0 0 48 16"><path d="M2 8c2-3 5-3 8 1 6 8 12-8 18 0s12-8 18 0"/></svg></div>
		<div class="home-events__grid" style="display:none;">
			<?php foreach ( $events as $event ) : ?>
				<article class="event-card event-card--<?php echo esc_attr( $event[7] ); ?>">
					<div class="event-card__media"><time datetime="<?php echo esc_attr( '2025-' . ( 'जुलाई' === $event[1] ? '07-05' : ( '22' === $event[0] ? '06-22' : '06-15' ) ) ); ?>"><strong><?php echo esc_html( $event[0] ); ?></strong><span><?php echo esc_html( $event[1] ); ?></span><small><?php echo esc_html( $event[2] ); ?></small></time><div class="event-card__photo event-card__photo--<?php echo esc_attr( $event[8] ); ?>" role="img" aria-label="<?php echo esc_attr( $event[3] ); ?>"></div></div>
					<div class="event-card__body"><h3><?php echo esc_html( $event[3] ); ?></h3><div class="event-card__meta"><span><svg aria-hidden="true" viewBox="0 0 24 24"><path d="M20 10c0 6-8 12-8 12S4 16 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg><?php echo esc_html( $event[4] ); ?></span><span><svg aria-hidden="true" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l4 2"/></svg><?php echo esc_html( $event[5] ); ?></span></div><p><?php echo esc_html( $event[6] ); ?></p><a href="#">और जानकारी <span>→</span></a></div>
				</article>
			<?php endforeach; ?>
		</div>
		<br>
		<div class="home-announcements__board">
							<div class="home-announcements__empty sam-reveal sam-reveal--up is-revealed" style="--sam-reveal-delay: 420ms;"><svg aria-hidden="true" viewBox="0 0 64 64"><path d="M13 9h38v46H13V9Z"></path><path d="M21 22h22M21 31h22M21 40h15"></path></svg><h3>फिलहाल कोई नई घोषणा नहीं है</h3><p>नई सूचना प्रकाशित होने पर वह यहाँ दिखाई देगी।</p></div>
								</div>
		<style>.event-card__photo{background-image:url('<?php echo esc_url( $events_image ); ?>')}</style>
	</div>
</section>
