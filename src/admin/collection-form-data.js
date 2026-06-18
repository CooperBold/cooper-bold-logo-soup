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
		.querySelectorAll( '#cb-logo-soup-logo-list .cb-logo-soup-logo-item' )
		.forEach( ( item, index ) => {
			const url =
				item.querySelector( '.cb-logo-soup-logo-url' )?.value || '';
			if ( ! url ) {
				return;
			}
			const id =
				parseInt(
					item.querySelector( '.cb-logo-soup-logo-id' )?.value,
					10
				) || index + 1;
			logos.push( {
				id,
				url,
				alt:
					item.querySelector( '.cb-logo-soup-logo-alt' )?.value || '',
				link:
					item.querySelector( '.cb-logo-soup-logo-link' )?.value ||
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
	if ( ! document.getElementById( 'cb_logo_soup_base_size' ) ) {
		return {};
	}

	const checkbox = ( name ) =>
		document.querySelector(
			`input[name="cb_logo_soup_settings[${ name }]"]`
		)?.checked || false;

	return {
		baseSize: readNumber(
			document.getElementById( 'cb_logo_soup_base_size' ),
			48
		),
		scaleFactor: readNumber(
			document.getElementById( 'cb_logo_soup_scale_factor' ),
			0.5
		),
		contrastThreshold: readNumber(
			document.getElementById( 'cb_logo_soup_contrast_threshold' ),
			10
		),
		densityAware: checkbox( 'densityAware' ),
		densityFactor: readNumber(
			document.getElementById( 'cb_logo_soup_density_factor' ),
			0.5
		),
		cropToContent: checkbox( 'cropToContent' ),
		backgroundColor:
			document.getElementById( 'cb_logo_soup_background_color' )?.value ||
			'',
		alignBy:
			document.getElementById( 'cb_logo_soup_align_by' )?.value ||
			'visual-center-y',
		gap: readNumber( document.getElementById( 'cb_logo_soup_gap' ), 28 ),
		layout:
			document.getElementById( 'cb_logo_soup_layout' )?.value || 'strip',
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
