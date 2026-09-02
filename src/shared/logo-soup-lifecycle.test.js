/**
 * @jest-environment jsdom
 */

import {
	clearLogoSoupLoading,
	markLogoSoupLoading,
	onLogoSoupError,
	SOUP_LOADING_CLASS,
	withLogoSoupLifecycle,
} from './logo-soup-lifecycle';

describe( 'logo-soup-lifecycle', () => {
	it( 'marks and clears loading class on container', () => {
		const container = document.createElement( 'div' );
		markLogoSoupLoading( container );
		expect( container.classList.contains( SOUP_LOADING_CLASS ) ).toBe( true );
		clearLogoSoupLoading( container );
		expect( container.classList.contains( SOUP_LOADING_CLASS ) ).toBe( false );
	} );

	it( 'warns when failures are reported', () => {
		const warn = jest.spyOn( console, 'warn' ).mockImplementation( () => {} );
		onLogoSoupError( [ { src: 'https://example.com/broken.png', error: 'fail' } ], {
			surface: 'test',
		} );
		expect( warn ).toHaveBeenCalled();
		warn.mockRestore();
	} );

	it( 'merges lifecycle callbacks into soup props', () => {
		const container = document.createElement( 'div' );
		markLogoSoupLoading( container );
		const priorError = jest.fn();
		const priorNormalized = jest.fn();
		const failures = [ { src: 'https://example.com/x.png', error: 'x' } ];

		const merged = withLogoSoupLifecycle(
			{
				logos: [ { src: 'https://example.com/x.png', alt: 'X' } ],
				onError: priorError,
				onNormalized: priorNormalized,
			},
			{ container, context: { surface: 'test' } }
		);

		merged.onError( failures );
		expect( priorError ).toHaveBeenCalledWith( failures );
		expect( container.classList.contains( SOUP_LOADING_CLASS ) ).toBe( false );

		merged.onNormalized( [] );
		expect( priorNormalized ).toHaveBeenCalledWith( [] );
		expect( container.classList.contains( SOUP_LOADING_CLASS ) ).toBe( false );
	} );
} );
