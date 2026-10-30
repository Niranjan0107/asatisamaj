( function () {
	'use strict';
	const lightbox = document.querySelector( '.gallery-lightbox' );
	if ( ! lightbox ) return;
	const content = lightbox.querySelector( '.gallery-lightbox__content' );
	const closeButton = lightbox.querySelector( '.gallery-lightbox__close' );
	let trigger = null;
	function open( button ) {
		trigger = button;
		if ( button.dataset.galleryImage ) {
			const image = document.createElement( 'img' );
			image.src = button.dataset.galleryImage;
			image.alt = button.dataset.galleryAlt || '';
			content.replaceChildren( image );
		} else if ( /^[A-Za-z0-9_-]{11}$/.test( button.dataset.galleryVideo || '' ) ) {
			const iframe = document.createElement( 'iframe' );
			iframe.src = 'https://www.youtube-nocookie.com/embed/' + button.dataset.galleryVideo + '?autoplay=1&rel=0';
			iframe.title = 'YouTube video player';
			iframe.allow = 'autoplay; encrypted-media; picture-in-picture';
			iframe.allowFullscreen = true;
			content.replaceChildren( iframe );
		} else {
			return;
		}
		lightbox.hidden = false;
		document.body.classList.add( 'gallery-lightbox-open' );
		closeButton.focus();
	}
	function close() {
		lightbox.hidden = true;
		content.replaceChildren();
		document.body.classList.remove( 'gallery-lightbox-open' );
		if ( trigger ) trigger.focus();
	}
	document.querySelectorAll( '[data-gallery-image], [data-gallery-video]' ).forEach( button => button.addEventListener( 'click', () => open( button ) ) );
	lightbox.querySelectorAll( '[data-gallery-close]' ).forEach( button => button.addEventListener( 'click', close ) );
	document.addEventListener( 'keydown', event => { if ( 'Escape' === event.key && ! lightbox.hidden ) close(); } );
}() );
