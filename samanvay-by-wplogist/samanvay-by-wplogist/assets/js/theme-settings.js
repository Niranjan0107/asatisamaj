( function () {
	'use strict';
	const tabs = document.querySelectorAll( '.samanvay-settings-tab' );
	const panels = document.querySelectorAll( '.samanvay-settings-panel' );
	if ( ! tabs.length ) return;
	function activate( id ) {
		tabs.forEach( tab => tab.classList.toggle( 'is-active', tab.dataset.tab === id ) );
		panels.forEach( panel => panel.classList.toggle( 'is-active', panel.dataset.panel === id ) );
		window.location.hash = id;
	}
	tabs.forEach( tab => tab.addEventListener( 'click', () => activate( tab.dataset.tab ) ) );
	const requested = window.location.hash.replace( '#', '' );
	activate( document.querySelector( '[data-tab="' + requested + '"]' ) ? requested : tabs[0].dataset.tab );

	document.querySelectorAll( '.samanvay-media-control' ).forEach( control => {
		const input = control.querySelector( '.samanvay-media-id' );
		const preview = control.querySelector( '.samanvay-media-preview' );
		const select = control.querySelector( '.samanvay-media-select' );
		const remove = control.querySelector( '.samanvay-media-remove' );
		const isQr = input.id === 'samanvay-membership_qr';
		const labels = isQr ? { title: 'Select Payment QR Code', use: 'Use as Payment QR', change: 'Change QR', select: 'Select QR', empty: 'No payment QR selected' } : { title: 'Select Footer Logo', use: 'Use as Footer Logo', change: 'Change Logo', select: 'Select Logo', empty: 'No footer logo selected' };
		let frame;
		select.addEventListener( 'click', () => {
			if ( frame ) {
				frame.open();
				return;
			}
			frame = wp.media( { title: labels.title, button: { text: labels.use }, multiple: false, library: { type: 'image' } } );
			frame.on( 'select', () => {
				const image = frame.state().get( 'selection' ).first().toJSON();
				const source = image.sizes && image.sizes.medium ? image.sizes.medium.url : image.url;
				input.value = image.id;
				preview.innerHTML = '<img src="' + source + '" alt="">';
				preview.classList.add( 'has-image' );
				select.textContent = labels.change;
				remove.hidden = false;
			} );
			frame.open();
		} );
		remove.addEventListener( 'click', () => {
			input.value = '';
			preview.innerHTML = '<span class="dashicons dashicons-format-image"></span><em>' + labels.empty + '</em>';
			preview.classList.remove( 'has-image' );
			select.textContent = labels.select;
			remove.hidden = true;
		} );
	} );

	document.querySelectorAll( '.samanvay-video-repeater' ).forEach( repeater => {
		const rows = repeater.querySelector( '.samanvay-video-rows' );
		const add = repeater.querySelector( '.samanvay-video-add' );
		add.addEventListener( 'click', () => {
			const row = document.createElement( 'div' );
			row.className = 'samanvay-video-row';
			row.innerHTML = '<span class="dashicons dashicons-video-alt3"></span><input type="text" inputmode="url" name="samanvay_theme_settings[youtube_videos][]" value="" placeholder="https://www.youtube.com/watch?v=..."><button type="button" class="button-link-delete samanvay-video-remove">Remove</button>';
			rows.appendChild( row );
			row.querySelector( 'input' ).focus();
		} );
		repeater.addEventListener( 'click', event => {
			const remove = event.target.closest( '.samanvay-video-remove' );
			if ( ! remove ) return;
			const row = remove.closest( '.samanvay-video-row' );
			if ( rows.children.length === 1 ) {
				row.querySelector( 'input' ).value = '';
			} else {
				row.remove();
			}
		} );
	} );

	document.querySelectorAll( '.samanvay-gallery-repeater' ).forEach( repeater => {
		const rows = repeater.querySelector( '.samanvay-gallery-rows' );
		const add = repeater.querySelector( '.samanvay-gallery-add' );
		let dragged = null;
		function appendImage( image ) {
			if ( rows.querySelector( '[value="' + image.id + '"]' ) ) return;
			const source = image.sizes && image.sizes.thumbnail ? image.sizes.thumbnail.url : image.url;
			const row = document.createElement( 'div' );
			row.className = 'samanvay-gallery-row';
			row.draggable = true;
			row.innerHTML = '<button type="button" class="samanvay-gallery-handle" aria-label="Drag to reorder"><span class="dashicons dashicons-move"></span></button><img src="' + source + '" alt=""><input class="samanvay-gallery-image-id" type="hidden" name="samanvay_theme_settings[gallery_images][]" value="' + image.id + '"><span class="samanvay-gallery-row__name"></span><button type="button" class="button-link-delete samanvay-gallery-remove">Remove</button>';
			row.querySelector( '.samanvay-gallery-row__name' ).textContent = image.title || 'Gallery image';
			rows.appendChild( row );
		}
		add.addEventListener( 'click', () => {
			const frame = wp.media( { title: 'Select Gallery Images', button: { text: 'Add to Gallery' }, multiple: true, library: { type: 'image' } } );
			frame.on( 'select', () => frame.state().get( 'selection' ).toJSON().forEach( appendImage ) );
			frame.open();
		} );
		repeater.addEventListener( 'click', event => {
			const remove = event.target.closest( '.samanvay-gallery-remove' );
			if ( remove ) remove.closest( '.samanvay-gallery-row' ).remove();
		} );
		rows.addEventListener( 'pointerdown', event => {
			const handle = event.target.closest( '.samanvay-gallery-handle' );
			if ( handle ) handle.closest( '.samanvay-gallery-row' ).dataset.allowDrag = '1';
		} );
		rows.addEventListener( 'dragstart', event => {
			const row = event.target.closest( '.samanvay-gallery-row' );
			if ( ! row || '1' !== row.dataset.allowDrag ) {
				event.preventDefault();
				return;
			}
			dragged = row;
			dragged.classList.add( 'is-dragging' );
			event.dataTransfer.effectAllowed = 'move';
		} );
		rows.addEventListener( 'dragover', event => {
			event.preventDefault();
			if ( ! dragged ) return;
			const target = event.target.closest( '.samanvay-gallery-row' );
			if ( ! target || target === dragged ) return;
			const box = target.getBoundingClientRect();
			rows.insertBefore( dragged, event.clientY < box.top + box.height / 2 ? target : target.nextSibling );
		} );
		rows.addEventListener( 'dragend', () => {
			if ( dragged ) {
				dragged.classList.remove( 'is-dragging' );
				delete dragged.dataset.allowDrag;
			}
			dragged = null;
		} );
	} );

	document.querySelectorAll( '.samanvay-team-repeater' ).forEach( repeater => {
		const rows = repeater.querySelector( '.samanvay-team-rows' );
		const add = repeater.querySelector( '.samanvay-team-add' );
		let dragged = null;
		function reindex() {
			rows.querySelectorAll( '.samanvay-team-row' ).forEach( ( row, index ) => {
				row.querySelectorAll( '[data-member-field]' ).forEach( field => {
					field.name = 'samanvay_theme_settings[team_members][' + index + '][' + field.dataset.memberField + ']';
				} );
			} );
		}
		function newRow() {
			const row = document.createElement( 'div' );
			row.className = 'samanvay-team-row';
			row.draggable = true;
			row.innerHTML = '<div class="samanvay-team-row__header"><button type="button" class="samanvay-team-handle" aria-label="Drag to reorder"><span class="dashicons dashicons-move"></span></button><button type="button" class="samanvay-team-toggle" aria-expanded="true"><strong class="samanvay-team-row__title">New member</strong><span class="dashicons dashicons-arrow-down-alt2" aria-hidden="true"></span></button><button type="button" class="button-link-delete samanvay-team-remove">Remove</button></div><div class="samanvay-team-row__body"><div class="samanvay-team-image"><div class="samanvay-team-image__preview"><span class="dashicons dashicons-admin-users"></span></div><input class="samanvay-team-image-id" type="hidden" data-member-field="image_id" value="0"><button type="button" class="button samanvay-team-image-select">Select Image</button><button type="button" class="button-link-delete samanvay-team-image-remove" hidden>Remove image</button></div><div class="samanvay-team-fields"><p><label>Name</label><input type="text" data-member-field="name" value=""></p><p><label>Designation</label><input type="text" data-member-field="role" value=""></p><p class="wide"><label>Description</label><textarea rows="3" data-member-field="description"></textarea></p><p><label>Facebook URL</label><input type="text" inputmode="url" data-member-field="facebook" value=""></p><p><label>LinkedIn URL</label><input type="text" inputmode="url" data-member-field="linkedin" value=""></p><p><label>WhatsApp URL</label><input type="text" inputmode="url" data-member-field="whatsapp" value=""></p></div></div>';
			return row;
		}
		reindex();
		add.addEventListener( 'click', () => {
			const row = newRow();
			rows.appendChild( row );
			reindex();
			row.querySelector( '[data-member-field="name"]' ).focus();
		} );
		repeater.addEventListener( 'input', event => {
			if ( 'name' !== event.target.dataset.memberField ) return;
			const title = event.target.closest( '.samanvay-team-row' ).querySelector( '.samanvay-team-row__title' );
			title.textContent = event.target.value.trim() || 'New member';
		} );
		repeater.addEventListener( 'click', event => {
			const toggle = event.target.closest( '.samanvay-team-toggle' );
			if ( toggle ) {
				const row = toggle.closest( '.samanvay-team-row' );
				const collapsed = row.classList.toggle( 'is-collapsed' );
				toggle.setAttribute( 'aria-expanded', collapsed ? 'false' : 'true' );
				return;
			}
			const removeMember = event.target.closest( '.samanvay-team-remove' );
			if ( removeMember ) {
				removeMember.closest( '.samanvay-team-row' ).remove();
				reindex();
				return;
			}
			const removeImage = event.target.closest( '.samanvay-team-image-remove' );
			if ( removeImage ) {
				const control = removeImage.closest( '.samanvay-team-image' );
				control.querySelector( '.samanvay-team-image-id' ).value = '0';
				control.querySelector( '.samanvay-team-image__preview' ).innerHTML = '<span class="dashicons dashicons-admin-users"></span>';
				control.querySelector( '.samanvay-team-image__preview' ).classList.remove( 'has-image' );
				removeImage.hidden = true;
				return;
			}
			const selectImage = event.target.closest( '.samanvay-team-image-select' );
			if ( ! selectImage ) return;
			const control = selectImage.closest( '.samanvay-team-image' );
			const frame = wp.media( { title: 'Select Team Member Image', button: { text: 'Use this image' }, multiple: false, library: { type: 'image' } } );
			frame.on( 'select', () => {
				const image = frame.state().get( 'selection' ).first().toJSON();
				const source = image.sizes && image.sizes.thumbnail ? image.sizes.thumbnail.url : image.url;
				control.querySelector( '.samanvay-team-image-id' ).value = image.id;
				control.querySelector( '.samanvay-team-image__preview' ).innerHTML = '<img src="' + source + '" alt="">';
				control.querySelector( '.samanvay-team-image__preview' ).classList.add( 'has-image' );
				control.querySelector( '.samanvay-team-image-remove' ).hidden = false;
			} );
			frame.open();
		} );
		rows.addEventListener( 'pointerdown', event => {
			const handle = event.target.closest( '.samanvay-team-handle' );
			if ( handle ) handle.closest( '.samanvay-team-row' ).dataset.allowDrag = '1';
		} );
		rows.addEventListener( 'dragstart', event => {
			const row = event.target.closest( '.samanvay-team-row' );
			if ( ! row || '1' !== row.dataset.allowDrag ) {
				event.preventDefault();
				return;
			}
			dragged = row;
			dragged.classList.add( 'is-dragging' );
			event.dataTransfer.effectAllowed = 'move';
		} );
		rows.addEventListener( 'dragover', event => {
			event.preventDefault();
			if ( ! dragged ) return;
			const target = event.target.closest( '.samanvay-team-row' );
			if ( ! target || target === dragged ) return;
			const box = target.getBoundingClientRect();
			rows.insertBefore( dragged, event.clientY < box.top + box.height / 2 ? target : target.nextSibling );
		} );
		rows.addEventListener( 'dragend', () => {
			if ( dragged ) {
				dragged.classList.remove( 'is-dragging' );
				delete dragged.dataset.allowDrag;
			}
			dragged = null;
			reindex();
		} );
	} );
}() );
