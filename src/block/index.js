/**
 * Gutenberg block registration for cooper-bold/logo-wrangler
 * plus legacy block names still present in existing content.
 */

import { registerBlockType } from '@wordpress/blocks';
import metadata from './block.json';
import Edit from './edit';
import './editor.scss';

const blockSettings = {
	...metadata,
	edit: Edit,
	save: () => null,
};

registerBlockType( metadata.name, blockSettings );

// Keep editor support for posts that still store the old block names.
[ 'cooper-bold/balanced-logos', 'cooper-bold/logo-soup' ].forEach( ( name ) => {
	registerBlockType( name, {
		...blockSettings,
		name,
	} );
} );
