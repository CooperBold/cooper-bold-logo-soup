import {
	useBlockProps,
	InspectorControls,
	MediaUpload,
	MediaUploadCheck,
} from '@wordpress/block-editor';
import {
	Button,
	PanelBody,
	RangeControl,
	SelectControl,
	TextControl,
	ToggleControl,
} from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import { LogoSoup } from '@sanity-labs/logo-soup/react';
import {
	sanitizeLink,
	sanitizePreviewConfig,
	stripPlainText,
	toSoupProps,
} from '../shared/to-soup-props';

const ALIGN_OPTIONS = [
	{ label: __( 'Bounds', 'cooper-bold-logo-soup' ), value: 'bounds' },
	{
		label: __( 'Visual center', 'cooper-bold-logo-soup' ),
		value: 'visual-center',
	},
	{
		label: __( 'Visual center (X)', 'cooper-bold-logo-soup' ),
		value: 'visual-center-x',
	},
	{
		label: __( 'Visual center (Y)', 'cooper-bold-logo-soup' ),
		value: 'visual-center-y',
	},
];

export default function Edit( { attributes, setAttributes } ) {
	const { logos } = attributes;
	const blockProps = useBlockProps( { className: 'cb-logo-soup-editor' } );

	const onSelectLogos = ( mediaItems ) => {
		const items = Array.isArray( mediaItems ) ? mediaItems : [ mediaItems ];
		const next = items.slice( 0, 50 ).map( ( item ) => ( {
			id: item.id,
			url: item.url,
			alt: item.alt || item.title || '',
			link: '',
		} ) );
		setAttributes( { logos: next } );
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

	return (
		<>
			<InspectorControls>
				<PanelBody
					title={ __( 'Logos', 'cooper-bold-logo-soup' ) }
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
												'cooper-bold-logo-soup'
										  )
										: __(
												'Add logos',
												'cooper-bold-logo-soup'
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
									'cooper-bold-logo-soup'
								) }
								value={ logo.alt }
								onChange={ ( value ) =>
									updateLogo( index, 'alt', value )
								}
							/>
							<TextControl
								label={ __(
									'Link URL',
									'cooper-bold-logo-soup'
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
								{ __( 'Remove', 'cooper-bold-logo-soup' ) }
							</Button>
						</div>
					) ) }
				</PanelBody>
				<PanelBody
					title={ __( 'Normalization', 'cooper-bold-logo-soup' ) }
					initialOpen={ false }
				>
					<RangeControl
						label={ __( 'Base size', 'cooper-bold-logo-soup' ) }
						value={ attributes.baseSize }
						onChange={ ( value ) =>
							setAttributes( { baseSize: value } )
						}
						min={ 16 }
						max={ 256 }
						step={ 4 }
					/>
					<RangeControl
						label={ __( 'Scale factor', 'cooper-bold-logo-soup' ) }
						value={ attributes.scaleFactor }
						onChange={ ( value ) =>
							setAttributes( { scaleFactor: value } )
						}
						min={ 0 }
						max={ 1 }
						step={ 0.1 }
					/>
					<RangeControl
						label={ __(
							'Contrast threshold',
							'cooper-bold-logo-soup'
						) }
						value={ attributes.contrastThreshold }
						onChange={ ( value ) =>
							setAttributes( { contrastThreshold: value } )
						}
						min={ 0 }
						max={ 255 }
						step={ 1 }
					/>
					<ToggleControl
						label={ __( 'Density aware', 'cooper-bold-logo-soup' ) }
						checked={ attributes.densityAware }
						onChange={ ( value ) =>
							setAttributes( { densityAware: value } )
						}
					/>
					<RangeControl
						label={ __(
							'Density factor',
							'cooper-bold-logo-soup'
						) }
						value={ attributes.densityFactor }
						onChange={ ( value ) =>
							setAttributes( { densityFactor: value } )
						}
						min={ 0 }
						max={ 1 }
						step={ 0.1 }
						disabled={ ! attributes.densityAware }
					/>
					<ToggleControl
						label={ __(
							'Crop to content',
							'cooper-bold-logo-soup'
						) }
						checked={ attributes.cropToContent }
						onChange={ ( value ) =>
							setAttributes( { cropToContent: value } )
						}
					/>
					<TextControl
						label={ __(
							'Background color',
							'cooper-bold-logo-soup'
						) }
						value={ attributes.backgroundColor }
						onChange={ ( value ) =>
							setAttributes( { backgroundColor: value } )
						}
					/>
				</PanelBody>
				<PanelBody
					title={ __( 'Layout', 'cooper-bold-logo-soup' ) }
					initialOpen={ false }
				>
					<SelectControl
						label={ __( 'Align by', 'cooper-bold-logo-soup' ) }
						value={ attributes.alignBy }
						options={ ALIGN_OPTIONS }
						onChange={ ( value ) =>
							setAttributes( { alignBy: value } )
						}
					/>
					<RangeControl
						label={ __( 'Gap', 'cooper-bold-logo-soup' ) }
						value={ attributes.gap }
						onChange={ ( value ) =>
							setAttributes( { gap: value } )
						}
						min={ 0 }
						max={ 96 }
						step={ 4 }
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...blockProps }>
				{ logos.length === 0 ? (
					<p>
						{ __(
							'Add at least one logo',
							'cooper-bold-logo-soup'
						) }
					</p>
				) : (
					<LogoSoup
						{ ...toSoupProps(
							sanitizePreviewConfig( attributes )
						) }
					/>
				) }
			</div>
		</>
	);
}
