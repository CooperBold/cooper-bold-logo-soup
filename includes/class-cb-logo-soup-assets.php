<?php
declare(strict_types=1);
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class CB_Logo_Soup_Assets {

	public const VIEW_SCRIPT_HANDLE = 'cooper-bold-logo-soup-view';
	public const VIEW_STYLE_HANDLE  = 'cooper-bold-logo-soup-view-style';

	public function __construct() {
		add_action( 'init', array( $this, 'register' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'maybe_enqueue' ) );
	}

	public function register(): void {
		$script = CB_LOGO_SOUP_PATH . 'build/view.asset.php';
		if ( file_exists( $script ) ) {
			$asset = include $script;
			wp_register_script(
				self::VIEW_SCRIPT_HANDLE,
				CB_LOGO_SOUP_URL . 'build/view.js',
				$asset['dependencies'],
				$asset['version'],
				true
			);
		}

		$style_candidates = array(
			array(
				'path' => CB_LOGO_SOUP_PATH . 'build/view.scss.asset.php',
				'url'  => CB_LOGO_SOUP_URL . 'build/view.scss.css',
			),
			array(
				'path' => CB_LOGO_SOUP_PATH . 'build/view.asset.php',
				'url'  => CB_LOGO_SOUP_URL . 'build/view.css',
			),
		);
		foreach ( $style_candidates as $candidate ) {
			if ( ! file_exists( $candidate['path'] ) ) {
				continue;
			}
			$asset = include $candidate['path'];
			wp_register_style(
				self::VIEW_STYLE_HANDLE,
				$candidate['url'],
				$asset['dependencies'],
				$asset['version']
			);
			break;
		}
	}

	/**
	 * Enqueue frontend assets when a block or shortcode renders (widgets, FSE, etc.).
	 */
	public static function enqueue_frontend(): void {
		if ( is_admin() ) {
			return;
		}
		if ( wp_script_is( self::VIEW_SCRIPT_HANDLE, 'registered' ) ) {
			wp_enqueue_script( self::VIEW_SCRIPT_HANDLE );
		}
		if ( wp_style_is( self::VIEW_STYLE_HANDLE, 'registered' ) ) {
			wp_enqueue_style( self::VIEW_STYLE_HANDLE );
		}
	}

	public function maybe_enqueue(): void {
		if ( is_admin() ) {
			return;
		}
		$post = get_post();
		if ( ! $post instanceof WP_Post ) {
			return;
		}
		$c = (string) $post->post_content;
		$has_shortcode = has_shortcode( $c, 'logo_soup' )
			|| has_shortcode( $c, 'cooper-bold-logo-soup' );

		if ( ! has_block( 'cooper-bold/logo-soup', $post ) && ! $has_shortcode ) {
			return;
		}
		self::enqueue_frontend();
	}
}
