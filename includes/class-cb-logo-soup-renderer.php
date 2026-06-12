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
			foreach ( array_slice( $attrs['logos'], 0, 50 ) as $index => $logo ) {
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

		return array(
			'logos'             => $logos,
			'baseSize'          => max( 16, min( 256, (int) $attrs['baseSize'] ) ),
			'scaleFactor'       => max( 0, min( 1, (float) $attrs['scaleFactor'] ) ),
			'contrastThreshold' => max( 0, min( 255, (int) $attrs['contrastThreshold'] ) ),
			'densityAware'      => (bool) $attrs['densityAware'],
			'densityFactor'     => max( 0, min( 1, (float) $attrs['densityFactor'] ) ),
			'cropToContent'     => (bool) $attrs['cropToContent'],
			'backgroundColor'   => $this->color( (string) $attrs['backgroundColor'] ),
			'alignBy'           => $align_by,
			'gap'               => $this->gap( $attrs['gap'] ),
			'className'         => sanitize_text_field( (string) $attrs['className'] ),
		);
	}

	/**
	 * Render the mount point HTML for the frontend script.
	 *
	 * @param array<string, mixed> $attributes         Raw or sanitized attributes.
	 * @param string               $wrapper_attributes Optional pre-built wrapper attributes.
	 * @return string
	 */
	public function render( array $attributes, string $wrapper_attributes = '' ): string {
		$attrs = $this->sanitize_attributes( $attributes );

		if ( empty( $attrs['logos'] ) ) {
			return '';
		}

		$config = array(
			'logos'             => $attrs['logos'],
			'baseSize'          => $attrs['baseSize'],
			'scaleFactor'       => $attrs['scaleFactor'],
			'contrastThreshold' => $attrs['contrastThreshold'],
			'densityAware'      => $attrs['densityAware'],
			'densityFactor'     => $attrs['densityFactor'],
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

		$imgs = '';
		foreach ( $attrs['logos'] as $logo ) {
			$img = sprintf(
				'<img src="%s" alt="%s" loading="lazy" decoding="async" />',
				esc_url( $logo['url'] ),
				esc_attr( $logo['alt'] )
			);
			$imgs .= '' !== $logo['link']
				? sprintf( '<a href="%s" rel="noopener noreferrer">%s</a>', esc_url( $logo['link'] ), $img )
				: $img;
		}

		if ( '' === $wrapper_attributes ) {
			$classes = array( 'cb-logo-soup' );
			foreach ( preg_split( '/\s+/', $attrs['className'] ) ?: array() as $part ) {
				$class = sanitize_html_class( $part );
				if ( '' !== $class ) {
					$classes[] = $class;
				}
			}
			$wrapper_attributes = sprintf(
				'class="%s" data-cb-logo-soup="%s" style="%s"%s',
				esc_attr( implode( ' ', array_unique( $classes ) ) ),
				esc_attr( $json ),
				esc_attr( $style ),
				$aria ? ' aria-label="' . esc_attr( $aria ) . '"' : ''
			);
		} else {
			$merged = $style;
			if ( preg_match( '/\sstyle="([^"]*)"/', $wrapper_attributes, $matches ) ) {
				$merged             = rtrim( $matches[1], ';' ) . ';' . $style;
				$wrapper_attributes = preg_replace( '/\sstyle="[^"]*"/', '', $wrapper_attributes ) ?? $wrapper_attributes;
			}
			$wrapper_attributes = trim( $wrapper_attributes ) . sprintf(
				' data-cb-logo-soup="%s" style="%s"%s',
				esc_attr( $json ),
				esc_attr( $merged ),
				$aria ? ' aria-label="' . esc_attr( $aria ) . '"' : ''
			);
		}

		return sprintf( '<div %s>%s</div>', $wrapper_attributes, $imgs );
	}

	/**
	 * @param string $alt User alt text.
	 * @param string $url Logo URL.
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
	 * @param string $color Raw CSS color.
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
		if ( preg_match( '/^(rgb|rgba|hsl|hsla)\([^)]+\)$/i', $color ) ) {
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
	 * @param string $name Lowercase color name.
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
	 * @param mixed $value Gap in pixels.
	 */
	private function gap( $value ): int {
		if ( is_string( $value ) && preg_match( '/[a-z%]/i', $value ) ) {
			return 28;
		}
		return max( 0, min( 96, (int) $value ) );
	}
}
