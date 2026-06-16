( function ( $ ) {
	'use strict';

	function reindexLogoFields() {
		$( '#cb-logo-soup-logo-list .cb-logo-soup-logo-item' ).each( function ( index ) {
			const $item = $( this );
			$item.attr( 'data-index', String( index ) );
			$item.find( 'input[name^="cb_logo_soup_logos"]' ).each( function () {
				const $input = $( this );
				const field = $input
					.attr( 'name' )
					.replace( /^cb_logo_soup_logos\[\d+]/, '' );
				$input.attr( 'name', 'cb_logo_soup_logos[' + index + ']' + field );
			} );
		} );
	}

	function appendLogoItem( logo ) {
		const index = $( '#cb-logo-soup-logo-list .cb-logo-soup-logo-item' ).length;
		const template = wp.template( 'cb-logo-soup-logo-item' );
		const html = template( {
			index,
			id: logo.id || '',
			url: logo.url || '',
			alt: logo.alt || '',
			link: logo.link || '',
		} );
		$( '#cb-logo-soup-logo-list' ).append( html );
	}

	function openMediaFrame() {
		const frame = wp.media( {
			title: 'Select logos',
			button: { text: 'Use selected logos' },
			library: { type: 'image' },
			multiple: true,
		} );

		frame.on( 'select', function () {
			const selection = frame.state().get( 'selection' );
			const existingIds = new Set();
			$( '#cb-logo-soup-logo-list .cb-logo-soup-logo-id' ).each( function () {
				const val = parseInt( $( this ).val(), 10 );
				if ( val ) {
					existingIds.add( val );
				}
			} );

			selection.each( function ( attachment ) {
				const data = attachment.toJSON();
				if ( existingIds.has( data.id ) ) {
					return;
				}
				if ( $( '#cb-logo-soup-logo-list .cb-logo-soup-logo-item' ).length >= 50 ) {
					return;
				}
				appendLogoItem( {
					id: data.id,
					url: data.url,
					alt: data.alt || data.title || '',
					link: '',
				} );
			} );

			reindexLogoFields();
		} );

		frame.open();
	}

	$( function () {
		const $list = $( '#cb-logo-soup-logo-list' );
		if ( ! $list.length ) {
			return;
		}

		$list.sortable( {
			handle: '.cb-logo-soup-logo-handle',
			axis: 'y',
			stop: reindexLogoFields,
		} );

		$( '#cb-logo-soup-add-logos' ).on( 'click', function ( event ) {
			event.preventDefault();
			openMediaFrame();
		} );

		$list.on( 'click', '.cb-logo-soup-remove-logo', function ( event ) {
			event.preventDefault();
			$( this ).closest( '.cb-logo-soup-logo-item' ).remove();
			reindexLogoFields();
		} );
	} );
}( jQuery ) );
