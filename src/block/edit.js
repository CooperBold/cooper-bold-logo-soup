import {
	useBlockProps,
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
} from '@wordpress/block-editor';
import {
	Button,
	ExternalLink,
	PanelBody,
	RangeControl,
	SelectControl,
	Spinner,
	TextControl,
	ToggleControl,
} from '@wordpress/components';
import { useEffect, useMemo, useState } from '@wordpress/element';
import apiFetch from '@wordpress/api-fetch';
import { __ } from '@wordpress/i18n';
import { LogoSoup } from '@sanity-labs/logo-soup/react';
import {
	sanitizeLink,
	sanitizePreviewConfig,
	stripPlainText,
	toBalancedLogosProps,
} from '../shared/to-balanced-logos-props';

const ALIGN_OPTIONS = [
	{ label: __( 'Bounds', 'balanced-logos' ), value: 'bounds' },
	{
		label: __( 'Visual center', 'balanced-logos' ),
		value: 'visual-center',
	},
	{
		label: __( 'Visual center (X)', 'balanced-logos' ),
		value: 'visual-center-x',
	},
	{
		label: __( 'Visual center (Y)', 'balanced-logos' ),
		value: 'visual-center-y',
	},
];

const SETTING_KEYS = [
	'baseSize',
	'scaleFactor',
	'contrastThreshold',
	'densityAware',
	'densityFactor',
	'cropToContent',
	'backgroundColor',
	'alignBy',
	'gap',
	'layout',
	'wrapper',
];

const LAYOUT_OPTIONS = [
	{ label: __( 'Strip', 'balanced-logos' ), value: 'strip' },
	{ label: __( 'Carousel', 'balanced-logos' ), value: 'carousel' },
];

const WRAPPER_OPTIONS = [
	{
		label: __( 'Full Splide carousel', 'balanced-logos' ),
		value: 'full',
	},
	{
		label: __( 'Slides only (Bricks nested)', 'balanced-logos' ),
		value: 'slides',
	},
];

function pickCollectionSettings( collection ) {
	if ( ! collection ) {
		return {};
	}
	return SETTING_KEYS.reduce( ( acc, key ) => {
		if ( Object.prototype.hasOwnProperty.call( collection, key ) ) {
			acc[ key ] = collection[ key ];
		}
		return acc;
	}, {} );
}

export default function Edit( { attributes, setAttributes } ) {
	const { logos, collectionId, layout, wrapper } = attributes;
	const blockProps = useBlockProps( { className: 'cb-balanced-logos-editor' } );
	const [ collections, setCollections ] = useState( [] );
	const [ loadingCollections, setLoadingCollections ] = useState( true );

	useEffect( () => {
		let mounted = true;
		apiFetch( { path: '/cb-balanced-logos/v1/collections' } )
			.then( ( items ) => {
				if ( mounted ) {
					setCollections( Array.isArray( items ) ? items : [] );
				}
			} )
			.catch( () => {
				if ( mounted ) {
					setCollections( [] );
				}
			} )
			.finally( () => {
				if ( mounted ) {
					setLoadingCollections( false );
				}
			} );
		return () => {
			mounted = false;
		};
	}, [] );

	const selectedCollection = useMemo(
		() =>
			collections.find(
				( item ) => Number( item.id ) === Number( collectionId )
			) || null,
		[ collections, collectionId ]
	);

	const usingCollection = Number( collectionId ) > 0;

	const previewAttributes = useMemo( () => {
		if ( usingCollection && selectedCollection ) {
			return {
				...attributes,
				...pickCollectionSettings( selectedCollection ),
				logos: selectedCollection.logos || [],
				layout: layout || selectedCollection.layout || 'strip',
				wrapper: wrapper || selectedCollection.wrapper || 'full',
			};
		}
		return attributes;
	}, [ attributes, layout, selectedCollection, usingCollection, wrapper ] );

	const collectionOptions = [
		{
			label: __( 'Manual logos', 'balanced-logos' ),
			value: '0',
		},
		...collections.map( ( item ) => ( {
			label: item.title,
			value: String( item.id ),
		} ) ),
	];

	const onSelectCollection = ( value ) => {
		const nextId = parseInt( value, 10 ) || 0;
		if ( nextId === 0 ) {
			setAttributes( { collectionId: 0 } );
			return;
		}
		const collection = collections.find(
			( item ) => Number( item.id ) === nextId
		);
		if ( ! collection ) {
			setAttributes( { collectionId: nextId } );
			return;
		}
		setAttributes( {
			collectionId: nextId,
			...pickCollectionSettings( collection ),
			logos: [],
		} );
	};

	const onSelectLogos = ( mediaItems ) => {
		const items = Array.isArray( mediaItems ) ? mediaItems : [ mediaItems ];
		const next = items.map( ( item ) => ( {
			id: item.id,
			url: item.url,
			alt: item.alt || item.title || '',
			link: '',
		} ) );
		setAttributes( { logos: next, collectionId: 0 } );
	};

	const removeLogo = ( index ) => {
		setAttributes( { logos: logos.filter( ( _, i ) => i !== index ) } );
	};

	const updateLogo = ( index, field, value ) => {
		let nextValue = value;
		if ( field === 'alt' || field === 'link' ) {
			nextValue = stripPlainText( value );
		}
		if ( field === 'link' ) {
			nextValue = sanitizeLink( nextValue );
		}
		const next = logos.map( ( logo, i ) =>
			i === index ? { ...logo, [ field ]: nextValue } : logo
		);
		setAttributes( { logos: next } );
	};

	const previewConfig = sanitizePreviewConfig( previewAttributes );
	const previewLogos = previewConfig.logos || [];

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __( 'Collection', 'balanced-logos' ) }
					initialOpen={ true }
				>
					{ loadingCollections ? (
						<Spinner />
					) : (
						<SelectControl
							label={ __(
								'Use collection',
								'balanced-logos'
							) }
							value={ String( collectionId || 0 ) }
							options={ collectionOptions }
							onChange={ onSelectCollection }
							help={
								collections.length === 0
									? __(
											'Create collections under Balanced Logos in the admin menu.',
											'balanced-logos'
									  )
									: __(
											'Choose a saved collection or add logos manually.',
											'balanced-logos'
									  )
							}
						/>
					) }
					{ usingCollection && selectedCollection && (
						<p>
							<ExternalLink
								href={ `/wp-admin/post.php?post=${ selectedCollection.id }&action=edit` }
							>
								{ __(
									'Edit this collection',
									'balanced-logos'
								) }
							</ExternalLink>
						</p>
					) }
				</PanelBody>
				{ ! usingCollection && (
					<PanelBody
						title={ __( 'Logos', 'balanced-logos' ) }
						initialOpen={ true }
					>
						<MediaUploadCheck>
							<MediaUpload
								onSelect={ onSelectLogos }
								allowedTypes={ [ 'image' ] }
								multiple
								gallery
								value={ logos.map( ( logo ) => logo.id ) }
								render={ ( { open } ) => (
									<Button variant="primary" onClick={ open }>
										{ logos.length
											? __(
													'Edit logos',
													'balanced-logos'
											  )
											: __(
													'Add logos',
													'balanced-logos'
											  ) }
									</Button>
								) }
							/>
						</MediaUploadCheck>
						{ logos.map( ( logo, index ) => (
							<div key={ logo.id || logo.url }>
								<TextControl
									label={ __(
										'Alt text',
										'balanced-logos'
									) }
									value={ logo.alt }
									onChange={ ( value ) =>
										updateLogo( index, 'alt', value )
									}
								/>
								<TextControl
									label={ __(
										'Link URL',
										'balanced-logos'
									) }
									value={ logo.link || '' }
									onChange={ ( value ) =>
										updateLogo( index, 'link', value )
									}
								/>
								<Button
									isDestructive
									variant="link"
									onClick={ () => removeLogo( index ) }
								>
									{ __( 'Remove', 'balanced-logos' ) }
								</Button>
							</div>
						) ) }
					</PanelBody>
				) }
				<PanelBody
					title={ __( 'Normalization', 'balanced-logos' ) }
					initialOpen={ false }
				>
					{ usingCollection && (
						<p className="description">
							{ __(
								'Settings come from the selected collection. Edit the collection to change them.',
								'balanced-logos'
							) }
						</p>
					) }
					<RangeControl
						label={ __( 'Base size', 'balanced-logos' ) }
						help={ __(
							'Base height for each logo before normalization.',
							'balanced-logos'
						) }
						value={ previewAttributes.baseSize }
						onChange={ ( value ) =>
							setAttributes( { baseSize: value } )
						}
						min={ 16 }
						max={ 256 }
						step={ 4 }
						disabled={ usingCollection }
					/>
					<RangeControl
						label={ __( 'Scale factor', 'balanced-logos' ) }
						help={ __(
							'How much smaller logos can be relative to the largest mark (0–1).',
							'balanced-logos'
						) }
						value={ previewAttributes.scaleFactor }
						onChange={ ( value ) =>
							setAttributes( { scaleFactor: value } )
						}
						min={ 0 }
						max={ 1 }
						step={ 0.1 }
						disabled={ usingCollection }
					/>
					<RangeControl
						label={ __(
							'Contrast threshold',
							'balanced-logos'
						) }
						help={ __(
							'Minimum contrast used when detecting logo edges (0–255).',
							'balanced-logos'
						) }
						value={ previewAttributes.contrastThreshold }
						onChange={ ( value ) =>
							setAttributes( { contrastThreshold: value } )
						}
						min={ 0 }
						max={ 255 }
						step={ 1 }
						disabled={ usingCollection }
					/>
					<ToggleControl
						label={ __( 'Density aware', 'balanced-logos' ) }
						help={ __(
							'Scale logos based on how visually dense each mark appears.',
							'balanced-logos'
						) }
						checked={ previewAttributes.densityAware }
						onChange={ ( value ) =>
							setAttributes( { densityAware: value } )
						}
						disabled={ usingCollection }
					/>
					<RangeControl
						label={ __(
							'Density factor',
							'balanced-logos'
						) }
						help={ __(
							'Strength of density-based scaling when density aware is on (0–1).',
							'balanced-logos'
						) }
						value={ previewAttributes.densityFactor }
						onChange={ ( value ) =>
							setAttributes( { densityFactor: value } )
						}
						min={ 0 }
						max={ 1 }
						step={ 0.1 }
						disabled={
							usingCollection || ! previewAttributes.densityAware
						}
					/>
					<ToggleControl
						label={ __(
							'Crop to content',
							'balanced-logos'
						) }
						help={ __(
							'Trim transparent padding around each logo before sizing.',
							'balanced-logos'
						) }
						checked={ previewAttributes.cropToContent }
						onChange={ ( value ) =>
							setAttributes( { cropToContent: value } )
						}
						disabled={ usingCollection }
					/>
					<TextControl
						label={ __(
							'Background color',
							'balanced-logos'
						) }
						help={ __(
							'Strip background color (helps contrast detection for light logos).',
							'balanced-logos'
						) }
						value={ previewAttributes.backgroundColor }
						onChange={ ( value ) =>
							setAttributes( { backgroundColor: value } )
						}
						disabled={ usingCollection }
					/>
				</PanelBody>
				<PanelBody
					title={ __( 'Layout', 'balanced-logos' ) }
					initialOpen={ false }
				>
					<SelectControl
						label={ __( 'Display layout', 'balanced-logos' ) }
						help={ __(
							'Carousel outputs one Splide slide per normalized logo. Use slides-only mode inside Bricks nested sliders.',
							'balanced-logos'
						) }
						value={
							layout ||
							previewAttributes.layout ||
							'strip'
						}
						options={ LAYOUT_OPTIONS }
						onChange={ ( value ) =>
							setAttributes( { layout: value } )
						}
					/>
					{ ( layout ||
						previewAttributes.layout ||
						'strip' ) === 'carousel' && (
						<SelectControl
							label={ __(
								'Carousel wrapper',
								'balanced-logos'
							) }
							help={ __(
								'Slides only nests inside an existing Splide list. Full outputs a standalone carousel.',
								'balanced-logos'
							) }
							value={
								wrapper ||
								previewAttributes.wrapper ||
								'full'
							}
							options={ WRAPPER_OPTIONS }
							onChange={ ( value ) =>
								setAttributes( { wrapper: value } )
							}
						/>
					) }
					<SelectControl
						label={ __( 'Align by', 'balanced-logos' ) }
						help={ __(
							'How logos are vertically aligned in the strip.',
							'balanced-logos'
						) }
						value={ previewAttributes.alignBy }
						options={ ALIGN_OPTIONS }
						onChange={ ( value ) =>
							setAttributes( { alignBy: value } )
						}
						disabled={ usingCollection }
					/>
					<RangeControl
						label={ __( 'Gap', 'balanced-logos' ) }
						help={ __(
							'Space between logos in pixels.',
							'balanced-logos'
						) }
						value={ previewAttributes.gap }
						onChange={ ( value ) =>
							setAttributes( { gap: value } )
						}
						min={ 0 }
						max={ 96 }
						step={ 4 }
						disabled={ usingCollection }
					/>
				</PanelBody>
				<p className="cb-balanced-logos-editor-credit">
					<ExternalLink href="https://cooperbold.com">
						CooperBold
					</ExternalLink>
				</p>
			</InspectorControls>
			<div { ...blockProps }>
				{ previewLogos.length === 0 ? (
					<p>
						{ usingCollection
							? __(
									'Selected collection has no logos yet.',
									'balanced-logos'
							  )
							: __(
									'Add at least one logo or choose a collection.',
									'balanced-logos'
							  ) }
					</p>
				) : (
					<LogoSoup
						{ ...toBalancedLogosProps(
							sanitizePreviewConfig( previewAttributes )
						) }
					/>
				) }
			</div>
		</>
	);
}
