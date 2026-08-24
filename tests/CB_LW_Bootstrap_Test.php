<?php
/**
 * Guards WordPress.org review constraints on the plugin bootstrap.
 *
 * @package CooperBoldLogoWrangler
 */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class CB_LW_Bootstrap_Test extends TestCase {

	public function test_bootstrap_does_not_migrate_active_plugins_on_plugins_loaded(): void {
		$src = file_get_contents( dirname( __DIR__ ) . '/cooper-bold-logo-wrangler.php' );
		$this->assertNotFalse( $src );
		$this->assertStringNotContainsString( "add_action( 'plugins_loaded', 'cb_lw_migrate_legacy_bootstrap_entry'", $src );
	}

	public function test_bootstrap_migrates_on_user_initiated_hooks_only(): void {
		$src = file_get_contents( dirname( __DIR__ ) . '/cooper-bold-logo-wrangler.php' );
		$this->assertNotFalse( $src );
		$this->assertStringContainsString( 'cb_lw_migrate_legacy_bootstrap_entry', $src );
		$this->assertStringContainsString( 'register_activation_hook( CB_LW_FILE, \'cb_lw_activate\' )', $src );
		$this->assertStringContainsString( 'register_deactivation_hook( CB_LW_FILE, \'cb_lw_deactivate\' )', $src );
		$this->assertStringContainsString( 'upgrader_process_complete', $src );
	}

	public function test_readme_points_to_public_plugin_source(): void {
		$readme = file_get_contents( dirname( __DIR__ ) . '/readme.txt' );
		$this->assertNotFalse( $readme );
		$this->assertStringContainsString( 'https://github.com/CooperBold/cooper-bold-logo-soup', $readme );
		$this->assertStringContainsString( 'src/view.js', $readme );
		$this->assertStringContainsString( 'src/admin/collection-preview.js', $readme );
	}
}
