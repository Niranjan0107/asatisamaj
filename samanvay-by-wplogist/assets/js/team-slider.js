( function () {
	'use strict';
	document.querySelectorAll( '[data-team-slider]' ).forEach( slider => {
		const viewport = slider.querySelector( '.home-team__viewport' );
		const track = slider.querySelector( '.home-team__grid' );
		const slides = Array.from( track.children );
		const previous = slider.querySelector( '[data-team-prev]' );
		const next = slider.querySelector( '[data-team-next]' );
		let index = 0;
		let timer = null;
		let startX = null;
		function perView() {
			if ( window.matchMedia( '(max-width: 640px)' ).matches ) return Number( slider.dataset.mobile ) || 1;
			if ( window.matchMedia( '(max-width: 1050px)' ).matches ) return Number( slider.dataset.tablet ) || 3;
			return Number( slider.dataset.desktop ) || 6;
		}
		function update() {
			const visible = Math.min( perView(), slides.length );
			const maximum = Math.max( 0, slides.length - visible );
			index = Math.max( 0, Math.min( index, maximum ) );
			const gap = parseFloat( window.getComputedStyle( track ).columnGap ) || 0;
			const width = slides[0] ? slides[0].getBoundingClientRect().width : 0;
			track.style.transform = 'translate3d(' + ( -index * ( width + gap ) ) + 'px,0,0)';
			if ( previous ) previous.disabled = 0 === maximum;
			if ( next ) next.disabled = 0 === maximum;
		}
		function move( direction ) {
			const maximum = Math.max( 0, slides.length - Math.min( perView(), slides.length ) );
			index = direction > 0 ? ( index >= maximum ? 0 : index + 1 ) : ( index <= 0 ? maximum : index - 1 );
			update();
		}
		function stop() {
			if ( timer ) window.clearInterval( timer );
			timer = null;
		}
		function start() {
			stop();
			if ( '1' === slider.dataset.autoplay && ! window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches && slides.length > perView() ) {
				timer = window.setInterval( () => move( 1 ), 4500 );
			}
		}
		if ( previous ) previous.addEventListener( 'click', () => { move( -1 ); start(); } );
		if ( next ) next.addEventListener( 'click', () => { move( 1 ); start(); } );
		slider.addEventListener( 'mouseenter', stop );
		slider.addEventListener( 'mouseleave', start );
		slider.addEventListener( 'focusin', stop );
		slider.addEventListener( 'focusout', start );
		viewport.addEventListener( 'pointerdown', event => { startX = event.clientX; } );
		viewport.addEventListener( 'pointerup', event => {
			if ( null !== startX && Math.abs( event.clientX - startX ) > 45 ) move( event.clientX < startX ? 1 : -1 );
			startX = null;
			start();
		} );
		window.addEventListener( 'resize', update );
		update();
		start();
	} );
}() );
