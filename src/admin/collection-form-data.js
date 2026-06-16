/**
 * Read collection editor form state from the wp-admin DOM.
 */

/**
 * @param {HTMLInputElement|null} input Input element.
 * @param {number}                  fallback Fallback when missing or invalid.
 * @return {number}
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
					item.querySelector( '.cb-logo-soup-logo-alt' )?.value ||
					'',
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
	const settingsTable = document.querySelector(
		'.cb-logo-soup-settings-table'
	);
	if ( ! settingsTable ) {
		return {};
	}

	const checkbox = ( name ) =>
		settingsTable.querySelector(
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
			document.getElementById( 'cb_logo_soup_background_color' )
				?.value || '',
		alignBy:
			document.getElementById( 'cb_logo_soup_align_by' )?.value ||
			'visual-center-y',
		gap: readNumber( document.getElementById( 'cb_logo_soup_gap' ), 28 ),
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
