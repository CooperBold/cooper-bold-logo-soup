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
				document.execCommand( 'copy' ) ? resolve() : reject();
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
			'.cb-logo-soup-copy-shortcode',
			function ( event ) {
				event.preventDefault();

				const $button = $( this );
				const $input = $button
					.closest( '.cb-logo-soup-shortcode-row' )
					.find( '.cb-logo-soup-shortcode-input' );
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
			new CustomEvent( 'cb-logo-soup-preview-update' )
		);
	}

	function reindexLogoFields() {
		$( '#cb-logo-soup-logo-list .cb-logo-soup-logo-item' ).each(
			function ( index ) {
				const $item = $( this );
				$item.attr( 'data-index', String( index ) );
				$item
					.find( 'input[name^="cb_logo_soup_logos"]' )
					.each( function () {
						const $input = $( this );
						const field = $input
							.attr( 'name' )
							.replace( /^cb_logo_soup_logos\[\d+]/, '' );
						$input.attr(
							'name',
							'cb_logo_soup_logos[' + index + ']' + field
						);
					} );
			}
		);
		dispatchPreviewUpdate();
	}

	function appendLogoItem( logo ) {
		const index = $(
			'#cb-logo-soup-logo-list .cb-logo-soup-logo-item'
		).length;
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
			$( '#cb-logo-soup-logo-list .cb-logo-soup-logo-id' ).each(
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
				if (
					$( '#cb-logo-soup-logo-list .cb-logo-soup-logo-item' )
						.length >= 50
				) {
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

		dispatchPreviewUpdate();
	} );
} )( jQuery );
