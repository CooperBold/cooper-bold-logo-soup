/* global jQuery, navigator */

// Collection editor admin UI: logo list, media picker, and shortcode copy.
( function ( $ ) {
	'use strict';

	function copyToClipboard( text ) {
		if ( navigator.clipboard && navigator.clipboard.writeText ) {
			return navigator.clipboard.writeText( text );
		}

		return new Promise( function ( resolve, reject ) {
			const textarea = document.createElement( 'textarea' );
			textarea.value = text;
			textarea.setAttribute( 'readonly', '' );
			textarea.style.position = 'fixed';
			textarea.style.left = '-9999px';
			document.body.appendChild( textarea );
			textarea.select();

			try {
				if ( document.execCommand( 'copy' ) ) {
					resolve();
				} else {
					reject();
				}
			} catch ( error ) {
				reject( error );
			} finally {
				document.body.removeChild( textarea );
			}
		} );
	}

	function initCopyButtons() {
		$( document ).on(
			'click',
			'.cb-lw-copy-shortcode',
			function ( event ) {
				event.preventDefault();

				const $button = $( this );
				const $input = $button
					.closest( '.cb-lw-shortcode-row' )
					.find( '.cb-lw-shortcode-input' );
				const shortcode = $input.val();

				if ( ! shortcode ) {
					return;
				}

				const originalHtml = $button.html();

				copyToClipboard( shortcode )
					.then( function () {
						$button.addClass( 'is-copied' );
						setTimeout( function () {
							$button.removeClass( 'is-copied' );
							$button.html( originalHtml );
						}, 2000 );
					} )
					.catch( function () {
						$input.trigger( 'focus' );
						$input[ 0 ].select();
					} );
			}
		);
	}

	function dispatchPreviewUpdate() {
		document.dispatchEvent(
			new CustomEvent( 'cb-lw-preview-update' )
		);
	}

	function reindexLogoFields() {
		$( '#cb-lw-logo-list .cb-lw-logo-item' ).each(
			function ( index ) {
				const $item = $( this );
				$item.attr( 'data-index', String( index ) );
				$item
					.find( 'input[name^="coopbolo_logos"]' )
					.each( function () {
						const $input = $( this );
						const field = $input
							.attr( 'name' )
							.replace( /^coopbolo_logos\[\d+]/, '' );
						$input.attr(
							'name',
							'coopbolo_logos[' + index + ']' + field
						);
					} );
			}
		);
		dispatchPreviewUpdate();
	}

	function appendLogoItem( logo ) {
		const index = $(
			'#cb-lw-logo-list .cb-lw-logo-item'
		).length;
		const template = wp.template( 'cb-lw-logo-item' );
		const html = template( {
			index,
			id: logo.id || '',
			url: logo.url || '',
			alt: logo.alt || '',
			link: logo.link || '',
		} );
		$( '#cb-lw-logo-list' ).append( html );
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
			$( '#cb-lw-logo-list .cb-lw-logo-id' ).each(
				function () {
					const val = parseInt( $( this ).val(), 10 );
					if ( val ) {
						existingIds.add( val );
					}
				}
			);

			selection.each( function ( attachment ) {
				const data = attachment.toJSON();
				if ( existingIds.has( data.id ) ) {
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
		initCopyButtons();

		const $list = $( '#cb-lw-logo-list' );
		if ( ! $list.length ) {
			return;
		}

		$list.sortable( {
			handle: '.cb-lw-logo-handle',
			axis: 'y',
			stop: reindexLogoFields,
		} );

		$( '#cb-lw-add-logos' ).on( 'click', function ( event ) {
			event.preventDefault();
			openMediaFrame();
		} );

		$list.on( 'click', '.cb-lw-remove-logo', function ( event ) {
			event.preventDefault();
			$( this ).closest( '.cb-lw-logo-item' ).remove();
			reindexLogoFields();
		} );

		dispatchPreviewUpdate();
	} );
} )( jQuery );
