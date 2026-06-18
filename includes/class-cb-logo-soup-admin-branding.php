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
 *
 * @return bool
 */
function is_logo_soup_admin_screen(): bool {
	$screen = get_current_screen();
	if ( ! $screen ) {
		return false;
	}

	return CB_Logo_Soup_Collections::POST_TYPE === $screen->post_type;
}

/**
 * Sparse Cooper Bold branding on Logo Soup admin screens.
 */
final class CB_Logo_Soup_Admin_Branding {

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_filter( 'admin_footer_text', array( $this, 'filter_footer_text' ) );
		add_filter( 'update_footer', array( $this, 'filter_update_footer' ), 999 );
	}

	/**
	 * Enqueue admin branding stylesheet on Logo Soup screens.
	 *
	 * @param string $hook Current admin hook.
	 * @return void
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
	 * Replace the left wp-admin footer with a CooperBold link on Logo Soup screens.
	 *
	 * @param string $text Default left footer text.
	 * @return string Filtered footer HTML.
	 */
	public function filter_footer_text( string $text ): string {
		if ( ! is_logo_soup_admin_screen() ) {
			return $text;
		}

		return sprintf(
			'<a href="https://cooperbold.com" target="_blank" rel="noopener noreferrer" class="cb-logo-soup-footer-brand">%s</a>',
			esc_html__( 'CooperBold', 'cooper-bold-logo-soup' )
		);
	}

	/**
	 * Clear the right wp-admin footer on Logo Soup screens.
	 *
	 * @param string $text Default right footer text.
	 * @return string Filtered footer text.
	 */
	public function filter_update_footer( string $text ): string {
		if ( ! is_logo_soup_admin_screen() ) {
			return $text;
		}

		return '';
	}
}
