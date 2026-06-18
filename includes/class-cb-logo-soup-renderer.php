<?php
/**
 * Shared markup renderer for block and shortcode.
 *
 * @package CooperBoldLogoSoup
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Renders logo soup containers and normalizes attributes.
 */
final class CB_Logo_Soup_Renderer {

	/** @var int Carousel group id sequence for unique data attributes per render. */
	private static int $carousel_seq = 0;

	/**
	 * Default attribute values aligned with @sanity-labs/logo-soup.
	 *
	 * @return array<string, mixed>
	 */
	public function get_defaults(): array {
		return array(
			'logos'               => array(),
			'baseSize'            => 48,
			'scaleFactor'         => 0.5,
			'contrastThreshold'   => 10,
			'densityAware'        => true,
			'densityFactor'       => 0.5,
			'cropToContent'       => false,
			'backgroundColor'     => '',
			'alignBy'             => 'visual-center-y',
			'gap'                 => 28,
			'layout'              => 'strip',
			'wrapper'             => 'full',
			'className'           => '',
		);
	}

	/**
	 * Sanitize a raw logos array (e.g. from JSON shortcode attribute).
	 *
	 * @param array<int, mixed> $raw Raw logo rows.
	 * @return array<int, array<string, mixed>>
	 */
	public function sanitize_logos( array $raw ): array {
		return $this->sanitize_attributes( array( 'logos' => $raw ) )['logos'];
	}

	/**
	 * Sanitize attributes from block editor or shortcode.
	 *
	 * @param array<string, mixed> $attributes Raw attributes.
	 * @return array<string, mixed>
	 */
	public function sanitize_attributes( array $attributes ): array {
		$defaults = $this->get_defaults();
		$attrs    = wp_parse_args( $attributes, $defaults );

		$logos = array();

		if ( is_array( $attrs['logos'] ) ) {
			foreach ( $attrs['logos'] as $index => $logo ) {
				if ( ! is_array( $logo ) ) {
					continue;
				}

				$url = isset( $logo['url'] ) ? esc_url_raw( (string) $logo['url'] ) : '';

				if ( '' === $url ) {
					continue;
				}

				$link = '';
				if ( ! empty( $logo['link'] ) ) {
					$raw_link = esc_url_raw( (string) $logo['link'] );
					if ( '' !== $raw_link && 0 !== stripos( $raw_link, 'javascript:' ) ) {
						$link = $raw_link;
					}
				}

				$logos[] = array(
					'id'   => isset( $logo['id'] ) ? absint( $logo['id'] ) : $index + 1,
					'url'  => $url,
					'alt'  => $this->resolve_alt( (string) ( $logo['alt'] ?? '' ), $url ),
					'link' => $link,
				);
			}
		}

		$align_options = array( 'bounds', 'visual-center', 'visual-center-x', 'visual-center-y' );
		$align_by      = in_array( (string) $attrs['alignBy'], $align_options, true )
			? (string) $attrs['alignBy']
			: $defaults['alignBy'];

		$layout_options = array( 'strip', 'carousel' );
		$layout         = in_array( (string) $attrs['layout'], $layout_options, true )
			? (string) $attrs['layout']
			: $defaults['layout'];

		$wrapper_options = array( 'full', 'slides' );
		$wrapper         = in_array( (string) $attrs['wrapper'], $wrapper_options, true )
			? (string) $attrs['wrapper']
			: $defaults['wrapper'];

		return array(
			'logos'             => $logos,
			'baseSize'          => max( 16, min( 256, (int) $attrs['baseSize'] ) ),
			'scaleFactor'       => max( 0, min( 1, (float) $attrs['scaleFactor'] ) ),
			'contrastThreshold' => max( 0, min( 255, (int) $attrs['contrastThreshold'] ) ),
			'densityAware'      => (bool) $attrs['densityAware'],
			'densityFactor'     => $attrs['densityAware']
				? max( 0, min( 1, (float) $attrs['densityFactor'] ) )
				: 0,
			'cropToContent'     => (bool) $attrs['cropToContent'],
			'backgroundColor'   => $this->color( (string) $attrs['backgroundColor'] ),
			'alignBy'           => $align_by,
			'gap'               => $this->gap( $attrs['gap'] ),
			'layout'            => $layout,
			'wrapper'           => 'carousel' === $layout ? $wrapper : 'full',
			'className'         => sanitize_text_field( (string) $attrs['className'] ),
		);
	}

	/**
	 * Merge collection data with block or shortcode attributes.
	 *
	 * Collection logos always win when a collection is resolved. Scalar settings from
	 * overrides replace collection values when explicitly provided.
	 *
	 * @param array<string, mixed> $attributes Raw attributes (may include collectionId).
	 * @return array<string, mixed>
	 */
	public function resolve_attributes( array $attributes ): array {
		$collection_id   = isset( $attributes['collectionId'] ) ? absint( $attributes['collectionId'] ) : 0;
		$collection_slug = isset( $attributes['collection'] ) ? sanitize_title( (string) $attributes['collection'] ) : '';

		$from_collection = null;
		if ( $collection_id > 0 ) {
			$from_collection = CB_Logo_Soup_Collections::get_attributes( $collection_id );
		} elseif ( '' !== $collection_slug ) {
			$from_collection = CB_Logo_Soup_Collections::get_attributes( $collection_slug );
		}

		if ( null === $from_collection ) {
			unset( $attributes['collection'], $attributes['collectionId'] );
			return $attributes;
		}

		$merged = $from_collection;
		unset( $merged['collectionId'] );

		$override_keys = array(
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
			'className',
		);

		foreach ( $override_keys as $key ) {
			if ( ! array_key_exists( $key, $attributes ) ) {
				continue;
			}
			$value = $attributes[ $key ];
			if ( 'className' === $key ) {
				if ( '' !== trim( (string) $value ) ) {
					$merged['className'] = (string) $value;
				}
				continue;
			}
			if ( in_array( $key, array( 'densityAware', 'cropToContent' ), true ) ) {
				if ( null !== $value ) {
					$merged[ $key ] = (bool) $value;
				}
				continue;
			}
			if ( '' !== $value && null !== $value ) {
				$merged[ $key ] = $value;
			}
		}

		return $merged;
	}

	/**
	 * Render the mount point HTML for the frontend script.
	 *
	 * @param array<string, mixed> $attributes         Raw or sanitized attributes.
	 * @param string               $wrapper_attributes Optional pre-built wrapper attributes.
	 * @return string
	 */
	public function render( array $attributes, string $wrapper_attributes = '' ): string {
		$attrs = $this->sanitize_attributes( $this->resolve_attributes( $attributes ) );

		if ( empty( $attrs['logos'] ) ) {
			return '';
		}

		if ( 'carousel' === $attrs['layout'] ) {
			return $this->render_carousel( $attrs, $wrapper_attributes );
		}

		return $this->render_strip( $attrs, $wrapper_attributes );
	}

	/**
	 * Render a normalized logo strip (default layout).
	 *
	 * @param array<string, mixed> $attrs              Sanitized attributes.
	 * @param string               $wrapper_attributes Optional pre-built wrapper attributes.
	 * @return string
	 */
	private function render_strip( array $attrs, string $wrapper_attributes = '' ): string {
		CB_Logo_Soup_Assets::enqueue_frontend();

		$config = array(
			'logos'             => $attrs['logos'],
			'baseSize'          => $attrs['baseSize'],
			'scaleFactor'       => $attrs['scaleFactor'],
			'contrastThreshold' => $attrs['contrastThreshold'],
			'densityAware'      => $attrs['densityAware'],
			'densityFactor'     => $attrs['densityAware'] ? $attrs['densityFactor'] : 0,
			'cropToContent'     => $attrs['cropToContent'],
			'alignBy'           => $attrs['alignBy'],
			'gap'               => $attrs['gap'],
		);

		if ( '' !== $attrs['backgroundColor'] ) {
			$config['backgroundColor'] = $attrs['backgroundColor'];
		}

		$json = wp_json_encode( $config );

		if ( false === $json ) {
			return '';
		}

		$bg    = '' !== $attrs['backgroundColor'] ? 'background-color:' . $attrs['backgroundColor'] . ';' : '';
		$style = sprintf( '--cb-logo-size:%dpx;gap:%dpx;%s', $attrs['baseSize'], $attrs['gap'], $bg );

		$aria = count( $attrs['logos'] ) > 1
			? sprintf(
				__( 'Logos: %s', 'cooper-bold-logo-soup' ),
				implode( ', ', wp_list_pluck( $attrs['logos'], 'alt' ) )
			)
			: '';

		$imgs = $this->render_strip_placeholder_logos( $attrs['logos'], $attrs['gap'] );

		$inner_attributes = sprintf(
			'class="cb-logo-soup cb-logo-soup-inner" data-cb-logo-soup="%s" style="%s"%s',
			esc_attr( $json ),
			esc_attr( $style ),
			$aria ? ' aria-label="' . esc_attr( $aria ) . '"' : ''
		);

		if ( '' === $wrapper_attributes ) {
			$classes = array( 'cb-logo-soup', 'cb-logo-soup-wrapper' );
			foreach ( preg_split( '/\s+/', $attrs['className'] ) ?: array() as $part ) {
				$class = sanitize_html_class( $part );
				if ( '' !== $class ) {
					$classes[] = $class;
				}
			}
			$wrapper_attributes = sprintf(
				'class="%s"',
				esc_attr( implode( ' ', array_unique( $classes ) ) )
			);
		} else {
			$wrapper_attributes = $this->ensure_wrapper_class( $wrapper_attributes, 'cb-logo-soup-wrapper' );
		}

		return sprintf(
			'<div %s><div %s>%s</div></div>',
			$wrapper_attributes,
			$inner_attributes,
			$imgs
		);
	}

	/**
	 * Server-side placeholder markup matching LogoSoup post-hydration DOM (div > span > img).
	 *
	 * Page builders that skip view.js still need a stable inner structure so theme
	 * CSS targeting `.cb-logo-soup-inner > div > span` applies before hydration.
	 * Mirrors the @sanity-labs/logo-soup/react output structure.
	 *
	 * @param array<int, array<string, mixed>> $logos Sanitized logo rows.
	 * @param int                               $gap   Gap in pixels.
	 * @return string Placeholder HTML.
	 */
	private function render_strip_placeholder_logos( array $logos, int $gap ): string {
		$half_gap = (int) round( $gap / 2 );
		$spans    = '';

		foreach ( $logos as $logo ) {
			$img = sprintf(
				'<img src="%s" alt="%s" loading="lazy" decoding="async" style="display:block;object-fit:contain" />',
				esc_url( $logo['url'] ),
				esc_attr( $logo['alt'] )
			);

			$inner = '' !== $logo['link']
				? sprintf(
					'<a href="%s" rel="noopener noreferrer">%s</a>',
					esc_url( $logo['link'] ),
					$img
				)
				: $img;

			$spans .= sprintf(
				'<span style="display:inline-block;vertical-align:middle;padding:%1$dpx">%2$s</span>',
				$half_gap,
				$inner
			);
		}

		return sprintf(
			'<div style="text-align:center;text-wrap:balance">%s</div>',
			$spans
		);
	}

	/**
	 * Render carousel slides for Splide / Bricks nested sliders.
	 *
	 * Uses a hidden reference strip for cross-logo normalization, then distributes
	 * each normalized logo into its own splide__slide (see view.js).
	 *
	 * @param array<string, mixed> $attrs              Sanitized attributes.
	 * @param string               $wrapper_attributes Optional pre-built wrapper attributes.
	 * @return string
	 */
	private function render_carousel( array $attrs, string $wrapper_attributes = '' ): string {
		CB_Logo_Soup_Assets::enqueue_frontend();

		$config     = $this->build_soup_config( $attrs );
		$json       = wp_json_encode( $config );
		$group_id   = 'cb-ls-' . (string) ++self::$carousel_seq;
		$slide_html = '';

		foreach ( array_keys( $attrs['logos'] ) as $index ) {
			$slide_html .= sprintf(
				'<li class="splide__slide logo-slider-slide cb-logo-soup-slide" data-cb-logo-soup-slide="%1$d" data-cb-logo-soup-carousel="%2$s" aria-label="%3$s"></li>',
				(int) $index,
				esc_attr( $group_id ),
				esc_attr( $attrs['logos'][ $index ]['alt'] ?? __( 'Logo', 'cooper-bold-logo-soup' ) )
			);
		}

		$ref_style = sprintf(
			'--cb-logo-size:%dpx;gap:%dpx;%s',
			$attrs['baseSize'],
			$attrs['gap'],
			'' !== $attrs['backgroundColor'] ? 'background-color:' . $attrs['backgroundColor'] . ';' : ''
		);

		$ref_host = sprintf(
			'<div class="cb-logo-soup-carousel-ref" aria-hidden="true"><div class="cb-logo-soup cb-logo-soup-inner" data-cb-logo-soup-ref="%1$s" data-cb-logo-soup="%2$s" style="%3$s"></div></div>',
			esc_attr( $group_id ),
			esc_attr( (string) $json ),
			esc_attr( $ref_style )
		);

		if ( 'slides' === $attrs['wrapper'] ) {
			return sprintf(
				'<div class="cb-logo-soup-carousel-host" data-cb-logo-soup-carousel="%1$s" hidden>%2$s</div>%3$s',
				esc_attr( $group_id ),
				$ref_host,
				$slide_html
			);
		}

		$classes = array( 'cb-logo-soup-carousel', 'splide', 'cb-logo-soup-wrapper' );
		foreach ( preg_split( '/\s+/', $attrs['className'] ) ?: array() as $part ) {
			$class = sanitize_html_class( $part );
			if ( '' !== $class ) {
				$classes[] = $class;
			}
		}

		if ( '' !== $wrapper_attributes ) {
			$wrapper_attributes = $this->ensure_wrapper_class( $wrapper_attributes, 'cb-logo-soup-carousel' );
			$wrapper_attributes = $this->ensure_wrapper_class( $wrapper_attributes, 'splide' );
			$outer_attributes   = trim( $wrapper_attributes ) . sprintf(
				' data-cb-logo-soup-carousel="%s" data-cb-logo-soup-splide="1"',
				esc_attr( $group_id )
			);
		} else {
			$outer_attributes = sprintf(
				'class="%s" data-cb-logo-soup-carousel="%s" data-cb-logo-soup-splide="1"',
				esc_attr( implode( ' ', array_unique( $classes ) ) ),
				esc_attr( $group_id )
			);
		}

		return sprintf(
			'<div %1$s>%2$s<div class="splide__track"><ul class="splide__list">%3$s</ul></div></div>',
			$outer_attributes,
			$ref_host,
			$slide_html
		);
	}

	/**
	 * Build Logo Soup config array for data-cb-logo-soup JSON attributes.
	 *
	 * @param array<string, mixed> $attrs Sanitized attributes.
	 * @return array<string, mixed>
	 */
	private function build_soup_config( array $attrs ): array {
		$config = array(
			'logos'             => $attrs['logos'],
			'baseSize'          => $attrs['baseSize'],
			'scaleFactor'       => $attrs['scaleFactor'],
			'contrastThreshold' => $attrs['contrastThreshold'],
			'densityAware'      => $attrs['densityAware'],
			'densityFactor'     => $attrs['densityAware'] ? $attrs['densityFactor'] : 0,
			'cropToContent'     => $attrs['cropToContent'],
			'alignBy'           => $attrs['alignBy'],
			'gap'               => $attrs['gap'],
		);

		if ( '' !== $attrs['backgroundColor'] ) {
			$config['backgroundColor'] = $attrs['backgroundColor'];
		}

		return $config;
	}

	/**
	 * Ensure a class is present on pre-built wrapper attributes (block output).
	 *
	 * @param string $wrapper_attributes Attribute string from get_block_wrapper_attributes().
	 * @param string $class              Class to add.
	 * @return string Updated attribute string.
	 */
	private function ensure_wrapper_class( string $wrapper_attributes, string $class ): string {
		$sanitized = sanitize_html_class( $class );
		if ( '' === $sanitized ) {
			return $wrapper_attributes;
		}

		if ( preg_match( '/class="([^"]*)"/', $wrapper_attributes, $matches ) ) {
			$classes = array_unique(
				array_filter(
					array_merge(
						preg_split( '/\s+/', $matches[1] ) ?: array(),
						array( $sanitized )
					)
				)
			);

			return preg_replace(
				'/class="[^"]*"/',
				'class="' . esc_attr( implode( ' ', $classes ) ) . '"',
				$wrapper_attributes
			) ?? $wrapper_attributes;
		}

		return trim( $wrapper_attributes ) . sprintf( ' class="%s"', esc_attr( $sanitized ) );
	}

	/**
	 * Resolve alt text from user input or the logo filename.
	 *
	 * @param string $alt User alt text.
	 * @param string $url Logo URL.
	 * @return string Non-empty alt text.
	 */
	private function resolve_alt( string $alt, string $url ): string {
		$alt = trim( sanitize_text_field( $alt ) );
		if ( '' !== $alt ) {
			return $alt;
		}
		$path = wp_parse_url( $url, PHP_URL_PATH );
		$name = is_string( $path ) ? preg_replace( '/\.[^.]+$/', '', basename( $path ) ) : '';
		$name = ucwords( trim( str_replace( array( '-', '_' ), ' ', (string) $name ) ) );
		return '' !== $name ? $name : __( 'Logo', 'cooper-bold-logo-soup' );
	}

	/**
	 * Sanitize a CSS color value (hex, functional notation, or named color).
	 *
	 * @param string $color Raw CSS color.
	 * @return string Safe color string or empty when invalid.
	 */
	private function color( string $color ): string {
		$color = trim( $color );
		if ( '' === $color ) {
			return '';
		}
		$hex = sanitize_hex_color( $color );
		if ( $hex ) {
			return $hex;
		}
		if ( preg_match( '/^(rgb|rgba|hsl|hsla)\(\s*[\d.%\s,-]+\s*\)$/i', $color ) ) {
			return sanitize_text_field( $color );
		}
		$lower = strtolower( $color );
		if ( in_array( $lower, array( 'transparent', 'currentcolor' ), true ) ) {
			return $lower;
		}
		if ( preg_match( '/^[a-z]+$/', $lower ) && $this->is_named_css_color( $lower ) ) {
			return $lower;
		}
		return '';
	}

	/**
	 * Whether a lowercase string is a valid CSS named color.
	 *
	 * @param string $name Lowercase color name.
	 * @return bool
	 */
	private function is_named_css_color( string $name ): bool {
		static $names = null;
		if ( null === $names ) {
			$list  = 'aliceblue,antiquewhite,aqua,aquamarine,azure,beige,bisque,black,blanchedalmond,blue,blueviolet,brown,burlywood,cadetblue,chartreuse,chocolate,coral,cornflowerblue,cornsilk,crimson,cyan,darkblue,darkcyan,darkgoldenrod,darkgray,darkgreen,darkgrey,darkkhaki,darkmagenta,darkolivegreen,darkorange,darkorchid,darkred,darksalmon,darkseagreen,darkslateblue,darkslategray,darkslategrey,darkturquoise,darkviolet,deeppink,deepskyblue,dimgray,dimgrey,dodgerblue,firebrick,floralwhite,forestgreen,fuchsia,gainsboro,ghostwhite,gold,goldenrod,gray,green,greenyellow,grey,honeydew,hotpink,indianred,indigo,ivory,khaki,lavender,lavenderblush,lawngreen,lemonchiffon,lightblue,lightcoral,lightcyan,lightgoldenrodyellow,lightgray,lightgreen,lightgrey,lightpink,lightsalmon,lightseagreen,lightskyblue,lightslategray,lightslategrey,lightsteelblue,lightyellow,lime,limegreen,linen,magenta,maroon,mediumaquamarine,mediumblue,mediumorchid,mediumpurple,mediumseagreen,mediumslateblue,mediumspringgreen,mediumturquoise,mediumvioletred,midnightblue,mintcream,mistyrose,moccasin,navajowhite,navy,oldlace,olive,olivedrab,orange,orangered,orchid,palegoldenrod,palegreen,paleturquoise,palevioletred,papayawhip,peachpuff,peru,pink,plum,powderblue,purple,rebeccapurple,red,rosybrown,royalblue,saddlebrown,salmon,sandybrown,seagreen,seashell,sienna,silver,skyblue,slateblue,slategray,slategrey,snow,springgreen,steelblue,tan,teal,thistle,tomato,turquoise,violet,wheat,white,whitesmoke,yellow,yellowgreen';
			$names = array_flip( explode( ',', $list ) );
		}
		return isset( $names[ $name ] );
	}

	/**
	 * Clamp gap to a pixel value; reject non-numeric CSS units.
	 *
	 * @param mixed $value Gap in pixels.
	 * @return int Clamped gap.
	 */
	private function gap( $value ): int {
		if ( is_string( $value ) && preg_match( '/[a-z%]/i', $value ) ) {
			return 28;
		}
		return max( 0, min( 96, (int) $value ) );
	}
}
