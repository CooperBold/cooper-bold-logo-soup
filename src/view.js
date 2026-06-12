import { createRoot } from '@wordpress/element';
import { LogoSoup } from '@sanity-labs/logo-soup/react';
import { toSoupProps } from './shared/to-soup-props';

const mounted = new WeakMap();

/**
 * @param {HTMLElement} container Mount node.
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

function init() {
	document.querySelectorAll( '[data-cb-logo-soup]' ).forEach( mountLogoSoup );
}

if ( document.readyState === 'loading' ) {
	document.addEventListener( 'DOMContentLoaded', init );
} else {
	init();
}
