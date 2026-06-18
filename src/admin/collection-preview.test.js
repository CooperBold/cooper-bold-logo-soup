/* eslint-disable jsdoc/check-tag-names */
/**
 * @jest-environment jsdom
 */

import {
	readAttributesFromDom,
	readLogosFromDom,
	readSettingsFromDom,
} from './collection-form-data';
import { sanitizePreviewConfig, toSoupProps } from '../shared/to-soup-props';

describe( 'collection preview DOM readers', () => {
	beforeEach( () => {
		document.body.innerHTML = `
			<div id="cb-logo-soup-collection-editor">
				<ul id="cb-logo-soup-logo-list">
					<li class="cb-logo-soup-logo-item">
						<input class="cb-logo-soup-logo-id" value="10" />
						<input class="cb-logo-soup-logo-url" value="https://example.com/a.png" />
						<input class="cb-logo-soup-logo-alt" value="Alpha" />
						<input class="cb-logo-soup-logo-link" value="https://alpha.test" />
					</li>
				</ul>
			</div>
			<table class="cb-logo-soup-settings-table cb-logo-soup-settings-essential">
				<tr>
					<td>
						<input id="cb_logo_soup_base_size" value="64" />
						<input id="cb_logo_soup_gap" value="36" />
						<input id="cb_logo_soup_background_color" value="#ffffff" />
					</td>
				</tr>
			</table>
			<table class="cb-logo-soup-settings-table cb-logo-soup-settings-advanced">
				<tr>
					<td>
						<input id="cb_logo_soup_scale_factor" value="0.4" />
						<input id="cb_logo_soup_contrast_threshold" value="12" />
						<input name="cb_logo_soup_settings[densityAware]" type="checkbox" checked="checked" />
						<input id="cb_logo_soup_density_factor" value="0.3" />
						<input name="cb_logo_soup_settings[cropToContent]" type="checkbox" />
						<select id="cb_logo_soup_align_by"><option value="bounds" selected>Bounds</option></select>
					</td>
				</tr>
			</table>
		`;
	} );

	it( 'reads logos from the editor list', () => {
		expect( readLogosFromDom() ).toEqual( [
			{
				id: 10,
				url: 'https://example.com/a.png',
				alt: 'Alpha',
				link: 'https://alpha.test',
			},
		] );
	} );

	it( 'reads settings from the settings table', () => {
		expect( readSettingsFromDom() ).toMatchObject( {
			baseSize: 64,
			scaleFactor: 0.4,
			contrastThreshold: 12,
			densityAware: true,
			densityFactor: 0.3,
			cropToContent: false,
			backgroundColor: '#ffffff',
			alignBy: 'bounds',
			gap: 36,
		} );
	} );

	it( 'merges logos and settings for preview attributes', () => {
		const attrs = readAttributesFromDom();
		expect( attrs.logos ).toHaveLength( 1 );
		expect( attrs.baseSize ).toBe( 64 );
		expect( attrs.gap ).toBe( 36 );
	} );

	it( 'reads densityAware from the advanced settings table', () => {
		expect( readSettingsFromDom().densityAware ).toBe( true );
	} );

	it( 'builds the same toSoupProps path as frontend hydration', () => {
		const config = sanitizePreviewConfig( readAttributesFromDom() );
		const props = toSoupProps( config );

		expect( props ).not.toBeNull();
		expect( props.densityAware ).toBe( true );
		expect( props.densityFactor ).toBe( 0.3 );
		expect( props.baseSize ).toBe( 64 );
		expect( props.gap ).toBe( 36 );
		expect( props.alignBy ).toBe( 'bounds' );
		expect( props.logos ).toEqual( [
			{ src: 'https://example.com/a.png', alt: 'Alpha' },
		] );
	} );
} );
