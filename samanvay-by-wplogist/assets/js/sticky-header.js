( function () {
	'use strict';

	const header = document.querySelector( '.site-header--sticky' );
	if ( ! header ) {
		return;
	}

	const spacer = document.createElement( 'div' );
	spacer.className = 'site-header-spacer';
	spacer.setAttribute( 'aria-hidden', 'true' );
	header.insertAdjacentElement( 'afterend', spacer );

	let headerHeight = header.offsetHeight;
	let ticking = false;

	function measure() {
		if ( ! header.classList.contains( 'is-sticky-active' ) ) {
			headerHeight = header.offsetHeight;
		}
		spacer.style.height = headerHeight + 'px';
	}

	function update() {
		const shouldStick = window.scrollY > headerHeight;
		header.classList.toggle( 'is-sticky-active', shouldStick );
		spacer.classList.toggle( 'is-active', shouldStick );
		ticking = false;
	}

	function requestUpdate() {
		if ( ! ticking ) {
			window.requestAnimationFrame( update );
			ticking = true;
		}
	}

	measure();
	update();
	window.addEventListener( 'scroll', requestUpdate, { passive: true } );
	window.addEventListener( 'resize', function () {
		header.classList.remove( 'is-sticky-active' );
		spacer.classList.remove( 'is-active' );
		measure();
		requestUpdate();
	} );
}() );
