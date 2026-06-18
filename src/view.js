/* global MutationObserver, Splide */
/**
 * Frontend hydration: mount LogoSoup on strip and carousel markup.
 */

import { createRoot } from '@wordpress/element';
import { LogoSoup } from '@sanity-labs/logo-soup/react';
import { toSoupProps } from './shared/to-soup-props';

const mounted = new WeakMap();
const carouselGroups = new WeakSet();

/**
 * Mount LogoSoup on a strip container (idempotent per node).
 *
 * @param {HTMLElement} container Mount node with data-cb-logo-soup.
 */
function mountLogoSoup( container ) {
	if ( mounted.has( container ) ) {
		return;
	}

	const raw = container.getAttribute( 'data-cb-logo-soup' );
	if ( ! raw ) {
		return;
	}

	let config;
	try {
		config = JSON.parse( raw );
	} catch ( error ) {
		return;
	}

	const soupProps = toSoupProps( config );
	if ( ! soupProps ) {
		return;
	}

	container.replaceChildren();
	const root = createRoot( container );
	root.render( <LogoSoup { ...soupProps } /> );
	mounted.set( container, root );
}

/**
 * Render a hidden reference strip and clone normalized logos into carousel slides.
 *
 * @param {HTMLElement} refContainer Hidden reference strip mount.
 * @param {string}      groupId      Carousel group id shared with slide nodes.
 */
function mountCarouselReference( refContainer, groupId ) {
	if ( carouselGroups.has( refContainer ) ) {
		return;
	}
	carouselGroups.add( refContainer );

	const raw = refContainer.getAttribute( 'data-cb-logo-soup' );
	if ( ! raw ) {
		return;
	}

	let config;
	try {
		config = JSON.parse( raw );
	} catch ( error ) {
		return;
	}

	const soupProps = toSoupProps( config );
	if ( ! soupProps ) {
		return;
	}

	refContainer.replaceChildren();
	const root = createRoot( refContainer );
	root.render( <LogoSoup { ...soupProps } /> );

	const distribute = () => {
		const slides = document.querySelectorAll(
			`[data-cb-logo-soup-slide][data-cb-logo-soup-carousel="${ groupId }"]`
		);
		if ( ! slides.length ) {
			return false;
		}

		const row =
			refContainer.querySelector( '[class*="logo-soup"]' ) ||
			refContainer.firstElementChild;
		const logoNodes = row
			? Array.from( row.children ).filter(
					( node ) =>
						node.querySelector( 'img' ) || node.matches( 'img, a' )
			  )
			: [];

		if ( logoNodes.length < slides.length ) {
			return false;
		}

		slides.forEach( ( slide, index ) => {
			const item = logoNodes[ index ];
			if ( ! item || slide.children.length ) {
				return;
			}
			slide.appendChild( item.cloneNode( true ) );
		} );

		return slides.length > 0 && slides[ 0 ].children.length > 0;
	};

	const observer = new MutationObserver( () => {
		if ( distribute() ) {
			observer.disconnect();
		}
	} );
	observer.observe( refContainer, { childList: true, subtree: true } );

	window.setTimeout( () => {
		if ( distribute() ) {
			observer.disconnect();
		}
	}, 50 );
	window.setTimeout( () => {
		distribute();
		observer.disconnect();
	}, 2000 );
}

/**
 * Init Splide on standalone carousel wrappers when theme Splide is available.
 *
 * @param {HTMLElement} carouselRoot Splide root element.
 */
function maybeInitStandaloneSplide( carouselRoot ) {
	if ( carouselRoot.closest( '.brxe-slider-nested' ) ) {
		return;
	}
	if ( typeof Splide === 'undefined' ) {
		return;
	}

	const extensions =
		window.splide && window.splide.Extensions
			? window.splide.Extensions
			: undefined;

	const options = {
		type: 'loop',
		direction: 'ltr',
		height: 'auto',
		gap: '0px',
		autoWidth: true,
		drag: 'free',
		arrows: false,
		pagination: false,
		keyboard: false,
		autoplay: false,
	};

	if ( extensions && extensions.AutoScroll ) {
		options.autoScroll = {
			speed: 0.7,
			pauseOnHover: false,
			pauseOnFocus: false,
			autoStart: true,
		};
	}

	const splide = new Splide( carouselRoot, options );
	splide.mount( extensions || undefined );
}

/**
 * Initialize hidden reference strips and standalone Splide carousels.
 */
function initCarousels() {
	document
		.querySelectorAll( '[data-cb-logo-soup-ref]' )
		.forEach( ( refContainer ) => {
			const groupId = refContainer.getAttribute(
				'data-cb-logo-soup-ref'
			);
			if ( ! groupId ) {
				return;
			}
			mountCarouselReference( refContainer, groupId );
		} );

	document
		.querySelectorAll( '[data-cb-logo-soup-splide="1"]' )
		.forEach( maybeInitStandaloneSplide );
}

/**
 * Mount all strip containers, then initialize carousels.
 */
function init() {
	document.querySelectorAll( '[data-cb-logo-soup]' ).forEach( ( el ) => {
		if ( el.hasAttribute( 'data-cb-logo-soup-ref' ) ) {
			return;
		}
		mountLogoSoup( el );
	} );
	initCarousels();
}

if ( document.readyState === 'loading' ) {
	document.addEventListener( 'DOMContentLoaded', init );
} else {
	init();
}
