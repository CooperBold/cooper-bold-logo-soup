/**
 * Live Balanced Logos preview for the collection admin editor.
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
import { sanitizePreviewConfig, toBalancedLogosProps } from '../shared/to-balanced-logos-props';

export const PREVIEW_UPDATE_EVENT = 'cb-balanced-logos-preview-update';

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
			'cb-balanced-logos-collection-editor'
		);
		editor?.addEventListener( 'input', refresh );

		const settingsTables = document.querySelectorAll(
			'.cb-balanced-logos-settings-table'
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
		() => toBalancedLogosProps( previewConfig ),
		[ previewConfig ]
	);

	if ( ! soupProps ) {
		return (
			<p className="cb-balanced-logos-preview-empty description">
				{ __(
					'Add at least one logo to see the live preview.',
					'balanced-logos'
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
				className="cb-balanced-logos cb-balanced-logos-preview-inner cb-balanced-logos-preview-carousel"
				style={ previewStyle }
			>
				<ul className="cb-balanced-logos-preview-slides">
					{ previewConfig.logos.map( ( logo, index ) => (
						<li
							key={ logo.id || logo.url || index }
							className="cb-balanced-logos-slide logo-slider-slide"
						>
							<LogoSoup
								{ ...toBalancedLogosProps( {
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
						'balanced-logos'
					) }
				</p>
			</div>
		);
	}

	return (
		<div
			className="cb-balanced-logos cb-balanced-logos-preview-inner"
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
	const rootEl = document.getElementById( 'cb-balanced-logos-preview-root' );
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
