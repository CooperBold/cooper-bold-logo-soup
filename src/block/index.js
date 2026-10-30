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
