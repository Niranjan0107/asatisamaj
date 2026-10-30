( function () {
	'use strict';
	const modal = document.querySelector( '.home-video-modal' );
	if ( ! modal ) return;
	const frame = modal.querySelector( '.home-video-modal__frame' );
	const closeButton = modal.querySelector( '.home-video-modal__close' );
	let trigger = null;
	function openVideo( button ) {
		const id = button.dataset.youtubeId;
		if ( ! /^[A-Za-z0-9_-]{11}$/.test( id ) ) return;
		trigger = button;
		frame.innerHTML = '<iframe src="https://www.youtube-nocookie.com/embed/' + id + '?autoplay=1&rel=0" title="YouTube video player" allow="autoplay; encrypted-media; picture-in-picture" allowfullscreen></iframe>';
		modal.hidden = false;
		document.body.classList.add( 'home-video-modal-open' );
		closeButton.focus();
	}
	function closeVideo() {
		modal.hidden = true;
		frame.innerHTML = '';
		document.body.classList.remove( 'home-video-modal-open' );
		if ( trigger ) trigger.focus();
	}
	document.querySelectorAll( '.home-videos__item' ).forEach( button => button.addEventListener( 'click', () => openVideo( button ) ) );
	modal.querySelectorAll( '[data-video-close]' ).forEach( button => button.addEventListener( 'click', closeVideo ) );
	document.addEventListener( 'keydown', event => {
		if ( 'Escape' === event.key && ! modal.hidden ) closeVideo();
	} );
}() );
