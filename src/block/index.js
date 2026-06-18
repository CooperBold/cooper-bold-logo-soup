/**
 * Gutenberg block registration for cooper-bold/logo-soup.
 */

import { registerBlockType } from '@wordpress/blocks';
import metadata from './block.json';
import Edit from './edit';
import './editor.scss';

registerBlockType( metadata.name, {
	...metadata,
	edit: Edit,
	save: () => null,
} );
