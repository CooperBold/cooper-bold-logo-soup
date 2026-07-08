/**
 * @jest-environment jsdom
 */

import {
	sanitizePreviewConfig,
	sanitizeCssColor,
	toBalancedLogosProps,
	clamp,
} from './to-balanced-logos-props';

describe( 'sanitizeCssColor', () => {
	it( 'accepts hex, named, and rgb colors', () => {
		expect( sanitizeCssColor( '#AbC' ) ).toBe( '#abc' );
		expect( sanitizeCssColor( 'cornflowerblue' ) ).toBe( 'cornflowerblue' );
		expect( sanitizeCssColor( 'rgb(10, 20, 30)' ) ).toBe( 'rgb(10, 20, 30)' );
		expect( sanitizeCssColor( 'transparent' ) ).toBe( 'transparent' );
	} );

	it( 'rejects invalid colors', () => {
		expect( sanitizeCssColor( 'not-a-color' ) ).toBe( '' );
		expect( sanitizeCssColor( '<script>' ) ).toBe( '' );
	} );
} );

describe( 'clamp', () => {
	it( 'clamps numeric values and uses fallback for NaN', () => {
		expect( clamp( 999, 0, 96, 28 ) ).toBe( 96 );
		expect( clamp( -5, 0, 1, 0.5 ) ).toBe( 0 );
		expect( clamp( 'nope', 0, 1, 0.5 ) ).toBe( 0.5 );
	} );
} );

describe( 'sanitizePreviewConfig', () => {
	it( 'sanitizes logos and drops invalid entries', () => {
		const config = sanitizePreviewConfig( {
			logos: [
				{ url: 'https://example.com/a.png', alt: 'A' },
				{ url: '' },
				{ url: 'https://example.com/b.png', link: 'javascript:alert(1)' },
			],
		} );

		expect( config.logos ).toHaveLength( 2 );
		expect( config.logos[ 0 ].url ).toBe( 'https://example.com/a.png' );
		expect( config.logos[ 1 ].link ).toBeUndefined();
	} );

	it( 'zeros densityFactor when densityAware is false', () => {
		const config = sanitizePreviewConfig( {
			logos: [ { url: 'https://example.com/logo.png' } ],
			densityAware: false,
			densityFactor: 0.9,
		} );

		expect( config.densityAware ).toBe( false );
		expect( config.densityFactor ).toBe( 0 );
	} );

	it( 'clamps densityFactor when densityAware is true', () => {
		const config = sanitizePreviewConfig( {
			logos: [ { url: 'https://example.com/logo.png' } ],
			densityAware: true,
			densityFactor: 5,
		} );

		expect( config.densityFactor ).toBe( 1 );
	} );

	it( 'clamps baseSize, scaleFactor, contrastThreshold, and gap', () => {
		const config = sanitizePreviewConfig( {
			logos: [ { url: 'https://example.com/logo.png' } ],
			baseSize: 999,
			scaleFactor: -1,
			contrastThreshold: 500,
			gap: '2rem',
		} );

		expect( config.baseSize ).toBe( 256 );
		expect( config.scaleFactor ).toBe( 0 );
		expect( config.contrastThreshold ).toBe( 255 );
		expect( config.gap ).toBe( 28 );
	} );

	it( 'includes backgroundColor only when valid', () => {
		const withBg = sanitizePreviewConfig( {
			logos: [ { url: 'https://example.com/logo.png' } ],
			backgroundColor: '#336699',
		} );
		expect( withBg.backgroundColor ).toBe( '#336699' );

		const withoutBg = sanitizePreviewConfig( {
			logos: [ { url: 'https://example.com/logo.png' } ],
			backgroundColor: 'bogus',
		} );
		expect( withoutBg.backgroundColor ).toBeUndefined();
	} );
} );

describe( 'toBalancedLogosProps', () => {
	it( 'returns null when logos are missing', () => {
		expect( toBalancedLogosProps( { logos: [] } ) ).toBeNull();
		expect( toBalancedLogosProps( null ) ).toBeNull();
	} );

	it( 'maps logos and zeros densityFactor when densityAware is false', () => {
		const config = sanitizePreviewConfig( {
			logos: [
				{
					url: 'https://example.com/acme.png',
					alt: 'Acme',
					link: 'https://acme.test',
				},
			],
			densityAware: false,
			densityFactor: 0.8,
		} );

		const props = toBalancedLogosProps( config );

		expect( props ).not.toBeNull();
		expect( props.logos ).toEqual( [
			{ src: 'https://example.com/acme.png', alt: 'Acme' },
		] );
		expect( props.densityAware ).toBe( false );
		expect( props.densityFactor ).toBe( 0 );
	} );

	it( 'preserves densityFactor when densityAware is true', () => {
		const config = sanitizePreviewConfig( {
			logos: [ { url: 'https://example.com/logo.png', alt: 'Logo' } ],
			densityAware: true,
			densityFactor: 0.25,
		} );

		const props = toBalancedLogosProps( config );

		expect( props.densityFactor ).toBe( 0.25 );
	} );
} );
