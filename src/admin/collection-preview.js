/**
 * Live Cooper Bold Logo Wrangler preview for the collection admin editor.
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
import { useLogoSoupLifecycle } from '../shared/logo-soup-lifecycle';
import { sanitizePreviewConfig, toLwProps } from '../shared/to-lw-props';

export const PREVIEW_UPDATE_EVENT = 'cb-lw-preview-update';

const STRIP_PREVIEW_CONTEXT = { surface: 'collection-preview' };
const CAROUSEL_PREVIEW_CONTEXT = { surface: 'collection-preview-carousel' };

/**
 * @param {Object} props.previewConfig Sanitized preview config.
 * @param {Object} props.logo          Single logo row for this slide.
 * @return {JSX.Element} One carousel slide preview.
 */
function CarouselSlidePreview( { previewConfig, logo } ) {
	const slideSoupProps = useMemo(
		() =>
			toLwProps( {
				...previewConfig,
				logos: [ logo ],
			} ),
		[ previewConfig, logo ]
	);
	const slideLogoSoupProps = useLogoSoupLifecycle( slideSoupProps, {
		context: CAROUSEL_PREVIEW_CONTEXT,
	} );
	return <LogoSoup { ...slideLogoSoupProps } />;
}

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
			'cb-lw-collection-editor'
		);
		editor?.addEventListener( 'input', refresh );

		const settingsTables = document.querySelectorAll(
			'.cb-lw-settings-table'
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
	const stripSoupProps = useMemo(
		() => toLwProps( previewConfig ),
		[ previewConfig ]
	);
	const soupProps = useLogoSoupLifecycle( stripSoupProps, {
		context: STRIP_PREVIEW_CONTEXT,
	} );

	if ( ! soupProps ) {
		return (
			<p className="cb-lw-preview-empty description">
				{ __(
					'Add at least one logo to see the live preview.',
					'cooper-bold-logo-wrangler'
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
				className="cb-lw cb-lw-preview-inner cb-lw-preview-carousel"
				style={ previewStyle }
			>
				<ul className="cb-lw-preview-slides">
					{ previewConfig.logos.map( ( logo, index ) => (
						<li
							key={ logo.id || logo.url || index }
							className="cb-lw-slide logo-slider-slide"
						>
							<CarouselSlidePreview
								previewConfig={ previewConfig }
								logo={ logo }
							/>
						</li>
					) ) }
				</ul>
				<p className="description">
					{ __(
						'Preview shows one slide per logo. Frontend uses cross-logo normalization before distributing into slides.',
						'cooper-bold-logo-wrangler'
					) }
				</p>
			</div>
		);
	}

	return (
		<div
			className="cb-lw cb-lw-preview-inner"
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
	const rootEl = document.getElementById( 'cb-lw-preview-root' );
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
