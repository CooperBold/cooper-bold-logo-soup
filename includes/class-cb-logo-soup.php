<?php
declare(strict_types=1);
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once CB_LOGO_SOUP_PATH . 'includes/class-cb-logo-soup-assets.php';
require_once CB_LOGO_SOUP_PATH . 'includes/class-cb-logo-soup-renderer.php';

final class CB_Logo_Soup {

	private static ?CB_Logo_Soup $instance = null;
	private CB_Logo_Soup_Renderer $renderer;

	public static function instance(): CB_Logo_Soup {
		return self::$instance ?? ( self::$instance = new self() );
	}

	private function __construct() {
		new CB_Logo_Soup_Assets();
		$this->renderer = new CB_Logo_Soup_Renderer();
		add_action( 'init', array( $this, 'register_block' ) );
		add_shortcode( 'logo_soup', array( $this, 'render_shortcode' ) );
		add_shortcode( 'cooper-bold-logo-soup', array( $this, 'render_shortcode' ) );
	}

	public function register_block(): void {
		$candidates = array(
			CB_LOGO_SOUP_PATH . 'build/block',
			CB_LOGO_SOUP_PATH . 'src/block',
		);
		$dir = '';
		foreach ( $candidates as $candidate ) {
			if ( file_exists( $candidate . '/block.json' ) ) {
				$dir = $candidate;
				break;
			}
		}
		if ( '' === $dir ) {
			return;
		}
		register_block_type( $dir, array( 'render_callback' => array( $this, 'render_block' ) ) );
	}

	public function render_block( array $attributes, string $content, WP_Block $block ): string {
		return $this->renderer->render( $attributes, get_block_wrapper_attributes( array( 'class' => 'cb-logo-soup' ) ) );
	}

	/** @param array<string,string>|string $atts */
	public function render_shortcode( $atts, ?string $content = null, string $tag = 'logo_soup' ): string {
		$a = shortcode_atts(
			array(
				'logos'              => '',
				'base_size'          => '48',
				'scale_factor'       => '0.5',
				'contrast_threshold' => '10',
				'density_aware'      => 'true',
				'density_factor'     => '0.5',
				'crop_to_content'    => 'false',
				'background_color'   => '',
				'align_by'           => 'visual-center-y',
				'gap'                => '28',
				'class'              => '',
			),
			$atts,
			$tag
		);
		return $this->renderer->render(
			array(
				'logos' => $this->parse_logos( (string) $a['logos'] ),
				'baseSize' => $a['base_size'], 'scaleFactor' => $a['scale_factor'],
				'contrastThreshold' => $a['contrast_threshold'],
				'densityAware' => filter_var( $a['density_aware'], FILTER_VALIDATE_BOOLEAN ),
				'densityFactor' => $a['density_factor'],
				'cropToContent' => filter_var( $a['crop_to_content'], FILTER_VALIDATE_BOOLEAN ),
				'backgroundColor' => $a['background_color'], 'alignBy' => $a['align_by'],
				'gap' => $a['gap'], 'className' => $a['class'],
			)
		);
	}

	private function parse_logos( string $value ): array {
		$value = trim( $value );
		if ( '' === $value ) {
			return array();
		}
		if ( 0 === stripos( $value, 'base64:' ) ) {
			$decoded = json_decode(
				base64_decode( substr( $value, 7 ), true ) ?: '',
				true
			);
			return is_array( $decoded ) ? $this->renderer->sanitize_logos( $decoded ) : array();
		}
		if ( '[' === $value[0] ) {
			$decoded = json_decode( html_entity_decode( $value, ENT_QUOTES ), true );
			return is_array( $decoded ) ? $this->renderer->sanitize_logos( $decoded ) : array();
		}
		$logos = array();
		$id = 1;
		foreach ( array_map( 'trim', explode( ',', $value ) ) as $chunk ) {
			if ( '' === $chunk ) {
				continue;
			}
			$p = array_map( 'trim', explode( '|', $chunk, 3 ) );
			$url = esc_url_raw( $p[0] );
			if ( '' === $url ) {
				continue;
			}
			$row = array( 'id' => $id++, 'url' => $url, 'alt' => isset( $p[1] ) ? sanitize_text_field( $p[1] ) : '' );
			if ( isset( $p[2] ) ) {
				$link = esc_url_raw( $p[2] );
				if ( '' !== $link && 0 !== stripos( $link, 'javascript:' ) ) {
					$row['link'] = $link;
				}
			}
			$logos[] = $row;
		}
		return $logos;
	}
}
