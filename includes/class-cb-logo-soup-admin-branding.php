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
 * Renders a one-line text credit inside collection edit screens (not wp-admin footer).
 */
final class CB_Logo_Soup_Admin_Branding {

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
	}

	/**
	 * One-line text credit for the collection edit logos meta box.
	 */
	public static function render_credit(): void {
		?>
		<p class="cb-logo-soup-admin-credit">
			<a href="https://cooperbold.com" target="_blank" rel="noopener noreferrer">
				<?php esc_html_e( 'Cooper Bold', 'cooper-bold-logo-soup' ); ?>
			</a>
			<span class="cb-logo-soup-admin-credit-sep" aria-hidden="true">&middot;</span>
			<a href="https://cooperbold.com" target="_blank" rel="noopener noreferrer">
				cooperbold.com
			</a>
		</p>
		<?php
	}

	/**
	 * @param string $hook Current admin hook.
	 */
	public function enqueue_styles( string $hook ): void {
		if ( ! in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
			return;
		}

		if ( ! $this->is_collection_edit_screen() ) {
			return;
		}

		wp_enqueue_style(
			'cb-logo-soup-admin-branding',
			CB_LOGO_SOUP_URL . 'admin/css/admin-branding.css',
			array(),
			CB_LOGO_SOUP_VERSION
		);
	}

	private function is_collection_edit_screen(): bool {
		$screen = get_current_screen();
		if ( ! $screen || CB_Logo_Soup_Collections::POST_TYPE !== $screen->post_type ) {
			return false;
		}

		return 'post' === $screen->base;
	}
}
