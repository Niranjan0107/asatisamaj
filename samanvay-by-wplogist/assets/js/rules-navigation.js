( function () {
	'use strict';
	const items = Array.from( document.querySelectorAll( '.rules-toc a[href^="#rules-topic-"]' ) ).map( function ( link ) {
		return { link: link, target: document.getElementById( link.hash.slice( 1 ) ) };
	} ).filter( function ( item ) { return item.target; } );
	if ( ! items.length ) { return; }
	let queued = false;
	let selectedTopic = null;
	function activate( current ) {
		items.forEach( function ( item ) {
			item.link.classList.toggle( 'is-active', item === current );
			if ( item === current ) { item.link.setAttribute( 'aria-current', 'location' ); }
			else { item.link.removeAttribute( 'aria-current' ); }
		} );
	}
	function update() {
		queued = false;
		// Native smooth scrolling emits intermediate scroll events. Keep the
		// requested topic selected, including targets near the bottom of the page
		// that cannot physically reach the scroll-margin position.
		if ( selectedTopic ) { activate( selectedTopic ); return; }
		let current = items[0];
		items.forEach( function ( item ) {
			const offset = parseFloat( window.getComputedStyle( item.target ).scrollMarginTop ) || 120;
			if ( item.target.getBoundingClientRect().top <= offset + 8 ) { current = item; }
		} );
		activate( current );
	}
	function schedule() {
		if ( ! queued ) { queued = true; window.requestAnimationFrame( update ); }
	}
	items.forEach( function ( item ) {
		item.link.addEventListener( 'click', function ( event ) {
			if ( event.ctrlKey || event.metaKey || event.shiftKey || event.altKey || event.button !== 0 ) { return; }
			selectedTopic = item;
			activate( item );
		} );
	} );
	function followHash() {
		selectedTopic = items.find( function ( item ) { return item.link.hash === window.location.hash; } ) || null;
		schedule();
	}
	function resumeScrollTracking() { selectedTopic = null; schedule(); }
	window.addEventListener( 'wheel', resumeScrollTracking, { passive: true } );
	window.addEventListener( 'touchmove', resumeScrollTracking, { passive: true } );
	window.addEventListener( 'keydown', function ( event ) {
		if ( /^(INPUT|TEXTAREA|SELECT)$/.test( event.target.tagName ) || event.target.isContentEditable ) { return; }
		if ( [ 'ArrowDown', 'ArrowUp', 'PageDown', 'PageUp', 'Home', 'End', ' ' ].includes( event.key ) ) { resumeScrollTracking(); }
	} );
	window.addEventListener( 'pointerdown', function ( event ) {
		if ( event.clientX >= document.documentElement.clientWidth ) { resumeScrollTracking(); }
	} );
	window.addEventListener( 'scroll', schedule, { passive: true } );
	window.addEventListener( 'resize', schedule );
	window.addEventListener( 'hashchange', followHash );
	window.addEventListener( 'load', schedule );
	followHash();
} )();
