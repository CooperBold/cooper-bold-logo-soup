<?php
/**
 * Sparse Cooper Bold branding on Logo Soup admin screens.
 *
 * @package CooperBoldLogoSoup
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether the current wp-admin screen belongs to Logo Soup.
 */
function is_logo_soup_admin_screen(): bool {
	$screen = get_current_screen();
	if ( ! $screen ) {
		return false;
	}

	return CB_Logo_Soup_Collections::POST_TYPE === $screen->post_type;
}

/**
 * Replaces the left wp-admin footer text with a Cooper Bold wordmark on Logo Soup screens.
 */
final class CB_Logo_Soup_Admin_Branding {

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_filter( 'admin_footer_text', array( $this, 'filter_footer_text' ) );
	}

	/**
	 * @param string $hook Current admin hook.
	 */
	public function enqueue_styles( string $hook ): void {
		if ( ! is_logo_soup_admin_screen() ) {
			return;
		}

		wp_enqueue_style(
			'cb-logo-soup-admin-branding',
			CB_LOGO_SOUP_URL . 'admin/css/admin-branding.css',
			array(),
			CB_LOGO_SOUP_VERSION
		);
	}

	/**
	 * @param string $text Default left footer text.
	 */
	public function filter_footer_text( string $text ): string {
		if ( ! is_logo_soup_admin_screen() ) {
			return $text;
		}

		$logo_url = CB_LOGO_SOUP_URL . 'admin/images/cooper-bold-wordmark.png';

		return sprintf(
			'<a href="https://cooperbold.com" target="_blank" rel="noopener noreferrer" class="cb-logo-soup-footer-brand"><img src="%s" alt="%s" /></a>',
			esc_url( $logo_url ),
			esc_attr__( 'Cooper Bold', 'cooper-bold-logo-soup' )
		);
	}
}
