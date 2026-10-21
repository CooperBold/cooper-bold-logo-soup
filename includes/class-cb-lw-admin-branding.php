<?php
/**
 * Sparse Cooper Bold branding on Cooper Bold Logo Wrangler admin screens.
 *
 * @package CooperBoldLogoWrangler
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether the current wp-admin screen belongs to Cooper Bold Logo Wrangler.
 *
 * @return bool
 */
function cb_lw_is_admin_screen(): bool {
	$screen = get_current_screen();
	if ( ! $screen ) {
		return false;
	}

	return CB_LW_Collections::POST_TYPE === $screen->post_type;
}

/**
 * Sparse Cooper Bold branding on Cooper Bold Logo Wrangler admin screens.
 */
final class CB_LW_Admin_Branding {

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_filter( 'admin_footer_text', array( $this, 'filter_footer_text' ) );
		add_filter( 'update_footer', array( $this, 'filter_update_footer' ), 999 );
	}

	/**
	 * Enqueue admin branding stylesheet on Cooper Bold Logo Wrangler screens.
	 *
	 * @param string $hook Current admin hook.
	 * @return void
	 */
	public function enqueue_styles( string $hook ): void {
		if ( ! cb_lw_is_admin_screen() ) {
			return;
		}

		wp_enqueue_style(
			'cb-lw-admin-branding',
			CB_LW_URL . 'admin/css/admin-branding.css',
			array(),
			CB_LW_VERSION
		);
	}

	/**
	 * Replace the left wp-admin footer with a CooperBold link on Cooper Bold Logo Wrangler screens.
	 *
	 * @param string $text Default left footer text.
	 * @return string Filtered footer HTML.
	 */
	public function filter_footer_text( string $text ): string {
		if ( ! cb_lw_is_admin_screen() ) {
			return $text;
		}

		return sprintf(
			'<a href="https://cooperbold.com" target="_blank" rel="noopener noreferrer" class="cb-lw-footer-brand">%s</a>',
			esc_html__( 'CooperBold', 'cooper-bold-logo-wrangler' )
		);
	}

	/**
	 * Clear the right wp-admin footer on Cooper Bold Logo Wrangler screens.
	 *
	 * @param string $text Default right footer text.
	 * @return string Filtered footer text.
	 */
	public function filter_update_footer( string $text ): string {
		if ( ! cb_lw_is_admin_screen() ) {
			return $text;
		}

		return '';
	}
}
