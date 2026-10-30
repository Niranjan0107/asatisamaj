( function () {
	'use strict';

	if ( window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches ) {
		return;
	}

	const groups = [
		{ selector: '.home-hero__content > *, .home-hero__photo, .home-hero__stats', direction: 'up' },
		{ selector: '.home-about__photo', direction: 'left' },
		{ selector: '.home-about__content > *', direction: 'right' },
		{ selector: '.home-asati__intro > *', direction: 'left' },
		{ selector: '.home-asati__card', direction: 'right' },
		{ selector: '.home-history__heading, .home-history__lead, .home-history__item', direction: 'up' },
		{ selector: '.home-activities__heading, .home-activities__intro, .home-activities__card', direction: 'up' },
		{ selector: '.home-initiatives__heading, .initiative-card', direction: 'up' },
		{ selector: '.home-events__heading, .event-card', direction: 'up' },
		{ selector: '.home-news__heading, .home-news__card, .home-news__empty', direction: 'up' },
		{ selector: '.home-announcements__intro > *, .home-announcements__item, .home-announcements__empty', direction: 'up' },
		{ selector: '.home-videos__heading, .home-videos__item', direction: 'up' },
		{ selector: '.home-team__heading, .team-card, .home-team__button', direction: 'up' },
		{ selector: '.home-gallery__heading, .home-gallery__item, .home-gallery__action', direction: 'up' },
		{ selector: '.about-page__hero-copy > *', direction: 'left' },
		{ selector: '.about-page__hero-image', direction: 'right' },
		{ selector: '.about-page__collage', direction: 'left' },
		{ selector: '.about-page__story-copy > *', direction: 'right' },
		{ selector: '.about-page__heading, .about-page__purpose-grid article, .about-page__values-grid > div, .about-page__editor .entry-content > *', direction: 'up' },
		{ selector: '.contact-page__hero .site-container > *', direction: 'up' },
		{ selector: '.contact-page__information > *, .contact-page__details article', direction: 'left' },
		{ selector: '.contact-page__form-card', direction: 'right' },
		{ selector: '.contact-page__editor .entry-content > *', direction: 'up' },
		{ selector: '.blog-archive__hero .site-container > *, .blog-archive__heading, .blog-archive__grid .post-card, .blog-archive .navigation.pagination, .blog-archive__empty', direction: 'up' },
		{ selector: '.single-blog__hero-inner > *, .single-blog__featured, .single-blog__content > *, .single-blog__terms, .single-blog .post-navigation, .single-blog .comments-area', direction: 'up' },
		{ selector: '.single-blog__sidebar .widget', direction: 'right' },
		{ selector: '.team-page__hero .site-container > *, .team-page__heading, .team-page__grid .team-card, .team-page__empty, .team-page__editor .entry-content > *', direction: 'up' },
		{ selector: '.gallery-page__hero .site-container > *, .gallery-page__heading, .gallery-page__image, .gallery-page__video, .gallery-page__empty .site-container > *, .gallery-page__editor .entry-content > *', direction: 'up' },
		{ selector: '.membership-hero .site-container > *, .membership-steps, .membership-alert, .membership-card, .member-login-card, .member-dashboard__summary article, .member-dashboard-card', direction: 'up' },
	];

	const elements = [];

	groups.forEach( function ( group ) {
		document.querySelectorAll( group.selector ).forEach( function ( element, index ) {
			element.classList.add( 'sam-reveal', 'sam-reveal--' + group.direction );
			element.style.setProperty( '--sam-reveal-delay', Math.min( index * 70, 420 ) + 'ms' );
			elements.push( element );
		} );
	} );

	if ( ! ( 'IntersectionObserver' in window ) ) {
		elements.forEach( function ( element ) {
			element.classList.add( 'is-revealed' );
		} );
		return;
	}

	const observer = new IntersectionObserver( function ( entries ) {
		entries.forEach( function ( entry ) {
			if ( entry.isIntersecting ) {
				entry.target.classList.add( 'is-revealed' );
				observer.unobserve( entry.target );
			}
		} );
	}, {
		rootMargin: '0px 0px -8% 0px',
		threshold: 0.12,
	} );

	elements.forEach( function ( element ) {
		observer.observe( element );
	} );
}() );
