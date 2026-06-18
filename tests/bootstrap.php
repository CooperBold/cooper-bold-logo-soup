<?php
/**
 * Minimal WordPress stubs for CB_Logo_Soup_Renderer unit tests.
 *
 * @package CooperBoldLogoSoup
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', dirname( __DIR__ ) . '/' );
}

if ( ! defined( 'CB_LOGO_SOUP_PATH' ) ) {
	define( 'CB_LOGO_SOUP_PATH', dirname( __DIR__ ) . '/' );
}

if ( ! defined( 'CB_LOGO_SOUP_URL' ) ) {
	define( 'CB_LOGO_SOUP_URL', 'https://example.com/wp-content/plugins/cooper-bold-logo-soup/' );
}

if ( ! function_exists( 'get_current_screen' ) ) {
	/**
	 * @return object|null
	 */
	function get_current_screen() {
		return $GLOBALS['cb_test_current_screen'] ?? null;
	}
}

if ( ! function_exists( 'esc_url' ) ) {
	/**
	 * @param string $url Raw URL.
	 */
	function esc_url( $url ): string {
		return (string) $url;
	}
}

if ( ! function_exists( 'esc_attr' ) ) {
	/**
	 * @param string $text Text.
	 */
	function esc_attr( $text ): string {
		return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' );
	}
}

if ( ! function_exists( 'esc_attr__' ) ) {
	/**
	 * @param string $text Text.
	 */
	function esc_attr__( $text, $domain = 'default' ): string {
		return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' );
	}
}

if ( ! function_exists( 'esc_html__' ) ) {
	/**
	 * @param string $text Text.
	 */
	function esc_html__( $text, $domain = 'default' ): string {
		return htmlspecialchars( (string) $text, ENT_NOQUOTES, 'UTF-8' );
	}
}

if ( ! function_exists( 'add_action' ) ) {
	/**
	 * @param string   $hook Hook name.
	 * @param callable $callback Callback.
	 */
	function add_action( $hook, $callback, $priority = 10, $accepted_args = 1 ): void {
		unset( $hook, $callback, $priority, $accepted_args );
	}
}

if ( ! function_exists( 'add_filter' ) ) {
	/**
	 * @param string   $hook Hook name.
	 * @param callable $callback Callback.
	 */
	function add_filter( $hook, $callback, $priority = 10, $accepted_args = 1 ): void {
		unset( $hook, $callback, $priority, $accepted_args );
	}
}

if ( ! function_exists( 'esc_url_raw' ) ) {
	/**
	 * @param string $url Raw URL.
	 */
	function esc_url_raw( $url ): string {
		$url = trim( (string) $url );
		if ( '' === $url || preg_match( '#^\s*javascript:#i', $url ) ) {
			return '';
		}
		if ( preg_match( '#^(https?://|/)#i', $url ) ) {
			return $url;
		}
		return '';
	}
}

if ( ! function_exists( 'sanitize_text_field' ) ) {
	/**
	 * @param string $str Raw string.
	 */
	function sanitize_text_field( $str ): string {
		return trim( wp_strip_all_tags( (string) $str ) );
	}
}

if ( ! function_exists( 'wp_strip_all_tags' ) ) {
	/**
	 * @param string $string Raw string.
	 */
	function wp_strip_all_tags( $string ): string {
		return strip_tags( (string) $string );
	}
}

if ( ! function_exists( 'sanitize_hex_color' ) ) {
	/**
	 * @param string $color Raw color.
	 */
	function sanitize_hex_color( $color ) {
		if ( preg_match( '|^#([A-Fa-f0-9]{3}){1,2}$|', (string) $color ) ) {
			return $color;
		}
		return '';
	}
}

if ( ! function_exists( 'wp_parse_args' ) ) {
	/**
	 * @param array<string, mixed> $args     Args.
	 * @param array<string, mixed> $defaults Defaults.
	 * @return array<string, mixed>
	 */
	function wp_parse_args( $args, $defaults = array() ): array {
		if ( is_object( $args ) ) {
			$parsed = get_object_vars( $args );
		} else {
			$parsed = (array) $args;
		}
		return array_merge( $defaults, $parsed );
	}
}

if ( ! function_exists( 'wp_parse_url' ) ) {
	/**
	 * @param string $url        URL.
	 * @param int    $component  Component.
	 * @return mixed
	 */
	function wp_parse_url( $url, $component = -1 ) {
		return parse_url( (string) $url, $component );
	}
}

if ( ! function_exists( '__' ) ) {
	/**
	 * @param string $text Text.
	 */
	function __( $text, $domain = 'default' ): string {
		return (string) $text;
	}
}

if ( ! function_exists( 'esc_html_e' ) ) {
	/**
	 * @param string $text Text.
	 */
	function esc_html_e( $text, $domain = 'default' ): void {
		echo esc_html( (string) $text );
	}
}

if ( ! function_exists( 'esc_html' ) ) {
	/**
	 * @param string $text Text.
	 */
	function esc_html( $text ): string {
		return htmlspecialchars( (string) $text, ENT_QUOTES, 'UTF-8' );
	}
}

if ( ! function_exists( 'absint' ) ) {
	/**
	 * @param mixed $maybeint Value.
	 */
	function absint( $maybeint ): int {
		return abs( (int) $maybeint );
	}
}

if ( ! class_exists( 'WP_Post' ) ) {
	/**
	 * Minimal WP_Post stub for unit tests.
	 */
	class WP_Post {
		/** @var object */
		public $filter;

		/**
		 * @param object $data Post data.
		 */
		public function __construct( $data ) {
			foreach ( get_object_vars( $data ) as $key => $value ) {
				$this->$key = $value;
			}
		}
	}
}

if ( ! function_exists( 'get_post' ) ) {
	/**
	 * @param int $post_id Post ID.
	 */
	function get_post( $post_id ) {
		$id = (int) $post_id;
		return $GLOBALS['cb_test_posts'][ $id ] ?? null;
	}
}

if ( ! function_exists( 'get_post_meta' ) ) {
	/**
	 * @param int    $post_id Post ID.
	 * @param string $key Meta key.
	 * @param bool   $single Single value.
	 * @return mixed
	 */
	function get_post_meta( $post_id, $key, $single = false ) {
		$id = (int) $post_id;
		if ( ! isset( $GLOBALS['cb_test_post_meta'][ $id ][ $key ] ) ) {
			return $single ? '' : array();
		}
		$value = $GLOBALS['cb_test_post_meta'][ $id ][ $key ];
		return $single ? $value : array( $value );
	}
}

if ( ! function_exists( 'get_posts' ) ) {
	/**
	 * @param array<string, mixed> $args Query args.
	 * @return array<int, WP_Post>
	 */
	function get_posts( $args = array() ): array {
		if ( isset( $args['name'] ) ) {
			$slug = (string) $args['name'];
			if ( isset( $GLOBALS['cb_test_posts_by_slug'][ $slug ] ) ) {
				return array( $GLOBALS['cb_test_posts_by_slug'][ $slug ] );
			}
			return array();
		}
		return array_values( $GLOBALS['cb_test_posts'] ?? array() );
	}
}

if ( ! function_exists( 'sanitize_title' ) ) {
	/**
	 * @param string $title Raw title.
	 */
	function sanitize_title( $title ): string {
		$title = strtolower( trim( (string) $title ) );
		return preg_replace( '/[^a-z0-9-]+/', '-', $title ) ?? '';
	}
}

if ( ! function_exists( 'sanitize_html_class' ) ) {
	/**
	 * @param string $class CSS class name.
	 */
	function sanitize_html_class( $class, $fallback = '' ): string {
		$class = preg_replace( '/[^A-Za-z0-9_-]/', '', (string) $class ) ?? '';
		return '' !== $class ? $class : (string) $fallback;
	}
}

if ( ! function_exists( 'wp_json_encode' ) ) {
	/**
	 * @param mixed $data Data to encode.
	 * @return string|false
	 */
	function wp_json_encode( $data, $options = 0, $depth = 512 ) {
		return json_encode( $data, $options, $depth );
	}
}

if ( ! function_exists( 'is_admin' ) ) {
	function is_admin(): bool {
		return false;
	}
}

if ( ! function_exists( 'wp_script_is' ) ) {
	/**
	 * @param string $handle Script handle.
	 */
	function wp_script_is( $handle, $status = 'enqueued' ): bool {
		unset( $handle, $status );
		return false;
	}
}

if ( ! function_exists( 'wp_style_is' ) ) {
	/**
	 * @param string $handle Style handle.
	 */
	function wp_style_is( $handle, $status = 'enqueued' ): bool {
		unset( $handle, $status );
		return false;
	}
}

if ( ! function_exists( 'wp_enqueue_script' ) ) {
	/**
	 * @param string $handle Script handle.
	 */
	function wp_enqueue_script( $handle, $src = '', $deps = array(), $ver = false, $args = false ): void {
		unset( $handle, $src, $deps, $ver, $args );
	}
}

if ( ! function_exists( 'wp_enqueue_style' ) ) {
	/**
	 * @param string $handle Style handle.
	 */
	function wp_enqueue_style( $handle, $src = '', $deps = array(), $ver = false, $media = 'all' ): void {
		unset( $handle, $src, $deps, $ver, $media );
	}
}

require_once CB_LOGO_SOUP_PATH . 'includes/class-cb-logo-soup-assets.php';
require_once CB_LOGO_SOUP_PATH . 'includes/class-cb-logo-soup-renderer.php';
require_once CB_LOGO_SOUP_PATH . 'includes/class-cb-logo-soup-collections.php';
require_once CB_LOGO_SOUP_PATH . 'includes/class-cb-logo-soup-admin-branding.php';
