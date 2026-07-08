<?php
/**
 * Sparse Cooper Bold branding on Balanced Logos admin screens.
 *
 * @package CooperBoldBalancedLogos
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether the current wp-admin screen belongs to Balanced Logos.
 *
 * @return bool
 */
function cb_is_balanced_logos_admin_screen(): bool {
	$screen = get_current_screen();
	if ( ! $screen ) {
		return false;
	}

	return CB_Balanced_Logos_Collections::POST_TYPE === $screen->post_type;
}

/**
 * Sparse Cooper Bold branding on Balanced Logos admin screens.
 */
final class CB_Balanced_Logos_Admin_Branding {

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_filter( 'admin_footer_text', array( $this, 'filter_footer_text' ) );
		add_filter( 'update_footer', array( $this, 'filter_update_footer' ), 999 );
	}

	/**
	 * Enqueue admin branding stylesheet on Balanced Logos screens.
	 *
	 * @param string $hook Current admin hook.
	 * @return void
	 */
	public function enqueue_styles( string $hook ): void {
		if ( ! cb_is_balanced_logos_admin_screen() ) {
			return;
		}

		wp_enqueue_style(
			'cb-balanced-logos-admin-branding',
			CB_BALANCED_LOGOS_URL . 'admin/css/admin-branding.css',
			array(),
			CB_BALANCED_LOGOS_VERSION
		);
	}

	/**
	 * Replace the left wp-admin footer with a CooperBold link on Balanced Logos screens.
	 *
	 * @param string $text Default left footer text.
	 * @return string Filtered footer HTML.
	 */
	public function filter_footer_text( string $text ): string {
		if ( ! cb_is_balanced_logos_admin_screen() ) {
			return $text;
		}

		return sprintf(
			'<a href="https://cooperbold.com" target="_blank" rel="noopener noreferrer" class="cb-balanced-logos-footer-brand">%s</a>',
			esc_html__( 'CooperBold', 'balanced-logos' )
		);
	}

	/**
	 * Clear the right wp-admin footer on Balanced Logos screens.
	 *
	 * @param string $text Default right footer text.
	 * @return string Filtered footer text.
	 */
	public function filter_update_footer( string $text ): string {
		if ( ! cb_is_balanced_logos_admin_screen() ) {
			return $text;
		}

		return '';
	}
}
