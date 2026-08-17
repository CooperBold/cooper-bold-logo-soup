<?php
declare(strict_types=1);

/**
 * Plugin Name:       Cooper Bold Logo Wrangler
 * Plugin URI:        https://github.com/CooperBold/cooper-bold-logo-soup
 * Description:       Display client and partner logos in a balanced strip. Normalization powered by the open-source Sanity Labs logo-soup library.
 * Version:           1.3.0
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * Author:            Cooper Bold
 * Author URI:        https://cooperbold.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       cooper-bold-logo-wrangler
 *
 * @package CooperBoldLogoWrangler
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Allow balanced-logos.php (legacy bootstrap) to load this file without double-init.
if ( defined( 'CB_LW_FILE' ) ) {
	return;
}

define( 'CB_LW_VERSION', '1.3.0' );
define( 'CB_LW_FILE', __FILE__ );
define( 'CB_LW_PATH', plugin_dir_path( __FILE__ ) );
define( 'CB_LW_URL', plugin_dir_url( __FILE__ ) );

/**
 * Move active_plugins entries from balanced-logos.php to this file so upgrades
 * keep working without listing two plugin headers in wp-admin.
 *
 * @return void
 */
function cb_lw_migrate_legacy_bootstrap_entry(): void {
	if ( ! defined( 'CB_LW_BOOTSTRAP_FILE' ) ) {
		return;
	}

	$legacy    = plugin_basename( CB_LW_BOOTSTRAP_FILE );
	$canonical = plugin_basename( CB_LW_FILE );
	if ( $legacy === $canonical ) {
		return;
	}

	$active = (array) get_option( 'active_plugins', array() );
	if ( ! in_array( $legacy, $active, true ) ) {
		return;
	}

	$migrated = array();
	foreach ( $active as $plugin ) {
		$migrated[] = ( $plugin === $legacy ) ? $canonical : $plugin;
	}
	update_option( 'active_plugins', array_values( array_unique( $migrated ) ) );
}
add_action( 'plugins_loaded', 'cb_lw_migrate_legacy_bootstrap_entry', 0 );

if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
	/**
	 * Admin notice when PHP is too old (includes are not loaded).
	 *
	 * @return void
	 */
	function cb_lw_php_version_notice(): void {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		echo '<div class="notice notice-error"><p>';
		echo esc_html__(
			'Cooper Bold Logo Wrangler requires PHP 7.4 or newer.',
			'cooper-bold-logo-wrangler'
		);
		echo '</p></div>';
	}
	add_action( 'admin_notices', 'cb_lw_php_version_notice' );

	/**
	 * Refuse activation on PHP below 7.4.
	 *
	 * @return void
	 */
	function cb_lw_activate_legacy(): void {
		deactivate_plugins( plugin_basename( CB_LW_FILE ) );
		wp_die(
			esc_html__(
				'Cooper Bold Logo Wrangler requires PHP 7.4 or newer.',
				'cooper-bold-logo-wrangler'
			),
			esc_html__( 'Plugin activation failed', 'cooper-bold-logo-wrangler' ),
			array( 'back_link' => true )
		);
	}
	register_activation_hook( CB_LW_FILE, 'cb_lw_activate_legacy' );
	return;
}

require_once CB_LW_PATH . 'includes/class-cb-lw.php';

/**
 * Bootstrap the plugin singleton.
 *
 * @return void
 */
function cb_lw_init(): void {
	CB_LW::instance();
}
add_action( 'plugins_loaded', 'cb_lw_init' );

/**
 * Refuse activation on PHP below 7.4.
 *
 * @return void
 */
function cb_lw_activate(): void {
	if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
		deactivate_plugins( plugin_basename( CB_LW_FILE ) );
		wp_die(
			esc_html__(
				'Cooper Bold Logo Wrangler requires PHP 7.4 or newer.',
				'cooper-bold-logo-wrangler'
			),
			esc_html__( 'Plugin activation failed', 'cooper-bold-logo-wrangler' ),
			array( 'back_link' => true )
		);
	}
}
register_activation_hook( CB_LW_FILE, 'cb_lw_activate' );
