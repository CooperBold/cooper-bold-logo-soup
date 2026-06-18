/**
 * Live Logo Soup preview for the collection admin editor.
 */
import {
	createRoot,
	useCallback,
	useEffect,
	useMemo,
	useState,
} from '@wordpress/element';
import { __ } from '@wordpress/i18n';
import { LogoSoup } from '@sanity-labs/logo-soup/react';
import { readAttributesFromDom } from './collection-form-data';
import { sanitizePreviewConfig, toSoupProps } from '../shared/to-soup-props';

export const PREVIEW_UPDATE_EVENT = 'cb-logo-soup-preview-update';

/**
 * @return {JSX.Element} Live preview or empty-state message.
 */
export function CollectionPreview() {
	const [ attributes, setAttributes ] = useState( readAttributesFromDom );

	const refresh = useCallback( () => {
		setAttributes( readAttributesFromDom() );
	}, [] );

	useEffect( () => {
		document.addEventListener( PREVIEW_UPDATE_EVENT, refresh );

		const editor = document.getElementById(
			'cb-logo-soup-collection-editor'
		);
		editor?.addEventListener( 'input', refresh );

		const settingsTables = document.querySelectorAll(
			'.cb-logo-soup-settings-table'
		);
		settingsTables.forEach( ( table ) => {
			table.addEventListener( 'input', refresh );
			table.addEventListener( 'change', refresh );
		} );

		return () => {
			document.removeEventListener( PREVIEW_UPDATE_EVENT, refresh );
			editor?.removeEventListener( 'input', refresh );
			settingsTables.forEach( ( table ) => {
				table.removeEventListener( 'input', refresh );
				table.removeEventListener( 'change', refresh );
			} );
		};
	}, [ refresh ] );

	const previewConfig = useMemo(
		() => sanitizePreviewConfig( attributes ),
		[ attributes ]
	);
	const soupProps = useMemo(
		() => toSoupProps( previewConfig ),
		[ previewConfig ]
	);

	if ( ! soupProps ) {
		return (
			<p className="cb-logo-soup-preview-empty description">
				{ __(
					'Add at least one logo to see the live preview.',
					'cooper-bold-logo-soup'
				) }
			</p>
		);
	}

	const previewStyle = {
		'--cb-logo-size': `${ previewConfig.baseSize }px`,
		gap: `${ previewConfig.gap }px`,
	};
	if ( previewConfig.backgroundColor ) {
		previewStyle.backgroundColor = previewConfig.backgroundColor;
	}

	const isCarousel = ( attributes.layout || 'strip' ) === 'carousel';

	if ( isCarousel ) {
		return (
			<div
				className="cb-logo-soup cb-logo-soup-preview-inner cb-logo-soup-preview-carousel"
				style={ previewStyle }
			>
				<ul className="cb-logo-soup-preview-slides">
					{ previewConfig.logos.map( ( logo, index ) => (
						<li
							key={ logo.id || logo.url || index }
							className="cb-logo-soup-slide logo-slider-slide"
						>
							<LogoSoup
								{ ...toSoupProps( {
									...previewConfig,
									logos: [ logo ],
								} ) }
							/>
						</li>
					) ) }
				</ul>
				<p className="description">
					{ __(
						'Preview shows one slide per logo. Frontend uses cross-logo normalization before distributing into slides.',
						'cooper-bold-logo-soup'
					) }
				</p>
			</div>
		);
	}

	return (
		<div
			className="cb-logo-soup cb-logo-soup-preview-inner"
			style={ previewStyle }
		>
			<LogoSoup { ...soupProps } />
		</div>
	);
}

/**
 * Mount the preview React tree when the admin editor is present.
 */
export function mountCollectionPreview() {
	const rootEl = document.getElementById( 'cb-logo-soup-preview-root' );
	if ( ! rootEl ) {
		return;
	}
	const root = createRoot( rootEl );
	root.render( <CollectionPreview /> );
}

if ( document.readyState === 'loading' ) {
	document.addEventListener( 'DOMContentLoaded', mountCollectionPreview );
} else {
	mountCollectionPreview();
}
