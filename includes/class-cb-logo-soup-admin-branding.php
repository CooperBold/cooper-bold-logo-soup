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
 * Renders a muted wordmark footer on collection list and edit screens.
 */
final class CB_Logo_Soup_Admin_Branding {

	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'admin_footer', array( $this, 'render_footer' ) );
	}

	/**
	 * @param string $hook Current admin hook.
	 */
	public function enqueue_styles( string $hook ): void {
		if ( ! $this->is_branding_screen( $hook ) ) {
			return;
		}

		wp_enqueue_style(
			'cb-logo-soup-admin-branding',
			CB_LOGO_SOUP_URL . 'admin/css/admin-branding.css',
			array(),
			CB_LOGO_SOUP_VERSION
		);
	}

	public function render_footer(): void {
		if ( ! $this->is_collection_admin_screen() ) {
			return;
		}

		$logo_url = CB_LOGO_SOUP_URL . 'admin/images/cooper-bold-wordmark.png';
		?>
		<div class="cb-logo-soup-admin-branding">
			<a href="https://cooperbold.com" target="_blank" rel="noopener noreferrer">
				<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php esc_attr_e( 'Cooper Bold', 'cooper-bold-logo-soup' ); ?>" width="156" height="84" />
			</a>
		</div>
		<?php
	}

	/**
	 * @param string $hook Admin hook suffix from admin_enqueue_scripts.
	 */
	private function is_branding_screen( string $hook ): bool {
		if ( ! in_array( $hook, array( 'edit.php', 'post.php', 'post-new.php' ), true ) ) {
			return false;
		}

		return $this->is_collection_admin_screen();
	}

	private function is_collection_admin_screen(): bool {
		$screen = get_current_screen();
		if ( ! $screen || CB_Logo_Soup_Collections::POST_TYPE !== $screen->post_type ) {
			return false;
		}

		return in_array( $screen->base, array( 'edit', 'post' ), true );
	}
}
