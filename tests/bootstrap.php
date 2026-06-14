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

if ( ! function_exists( 'absint' ) ) {
	/**
	 * @param mixed $maybeint Value.
	 */
	function absint( $maybeint ): int {
		return abs( (int) $maybeint );
	}
}

require_once CB_LOGO_SOUP_PATH . 'includes/class-cb-logo-soup-renderer.php';
