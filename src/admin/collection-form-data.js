/**
 * Read collection editor form state from the wp-admin DOM.
 */

/**
 * @param {HTMLInputElement|null} input    Input element.
 * @param {number}                fallback Fallback when missing or invalid.
 * @return {number} Parsed number or fallback.
 */
function readNumber( input, fallback ) {
	if ( ! input ) {
		return fallback;
	}
	const value = Number( input.value );
	return Number.isNaN( value ) ? fallback : value;
}

/**
 * @return {Array<Object>} Logo rows from the editor form.
 */
export function readLogosFromDom() {
	const logos = [];
	document
		.querySelectorAll( '#cb-lw-logo-list .cb-lw-logo-item' )
		.forEach( ( item, index ) => {
			const url =
				item.querySelector( '.cb-lw-logo-url' )?.value || '';
			if ( ! url ) {
				return;
			}
			const id =
				parseInt(
					item.querySelector( '.cb-lw-logo-id' )?.value,
					10
				) || index + 1;
			logos.push( {
				id,
				url,
				alt:
					item.querySelector( '.cb-lw-logo-alt' )?.value || '',
				link:
					item.querySelector( '.cb-lw-logo-link' )?.value ||
					'',
			} );
		} );
	return logos;
}

/**
 * @return {Object} Collection settings from the settings meta box.
 */
export function readSettingsFromDom() {
	// Essential and advanced settings use separate tables; query the document
	// so checkboxes in the advanced section are included.
	if ( ! document.getElementById( 'cb_lw_base_size' ) ) {
		return {};
	}

	const checkbox = ( name ) =>
		document.querySelector(
			`input[name="cb_lw_settings[${ name }]"]`
		)?.checked || false;

	return {
		baseSize: readNumber(
			document.getElementById( 'cb_lw_base_size' ),
			48
		),
		scaleFactor: readNumber(
			document.getElementById( 'cb_lw_scale_factor' ),
			0.5
		),
		contrastThreshold: readNumber(
			document.getElementById( 'cb_lw_contrast_threshold' ),
			10
		),
		densityAware: checkbox( 'densityAware' ),
		densityFactor: readNumber(
			document.getElementById( 'cb_lw_density_factor' ),
			0.5
		),
		cropToContent: checkbox( 'cropToContent' ),
		backgroundColor:
			document.getElementById( 'cb_lw_background_color' )?.value ||
			'',
		alignBy:
			document.getElementById( 'cb_lw_align_by' )?.value ||
			'visual-center-y',
		gap: readNumber( document.getElementById( 'cb_lw_gap' ), 28 ),
		layout:
			document.getElementById( 'cb_lw_layout' )?.value || 'strip',
	};
}

/**
 * @return {Object} Block-style attributes for preview sanitization.
 */
export function readAttributesFromDom() {
	return {
		...readSettingsFromDom(),
		logos: readLogosFromDom(),
	};
}
