<?php
declare(strict_types=1);

/**
 * Plugin Name:       Cooper Bold Logo Wrangler
 * Plugin URI:        https://github.com/CooperBold/cooper-bold-logo-soup
 * Description:       Display client and partner logos in a balanced strip. Normalization powered by the open-source Sanity Labs logo-soup library.
 * Version:           1.3.7
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
if ( defined( 'COOPBOLO_FILE' ) ) {
	return;
}

define( 'COOPBOLO_VERSION', '1.3.7' );
define( 'COOPBOLO_FILE', __FILE__ );
define( 'COOPBOLO_PATH', plugin_dir_path( __FILE__ ) );
define( 'COOPBOLO_URL', plugin_dir_url( __FILE__ ) );

/**
 * Basename for the headerless legacy bootstrap file.
 *
 * @return string
 */
function coopbolo_legacy_bootstrap_basename(): string {
	return plugin_basename( COOPBOLO_PATH . 'balanced-logos.php' );
}

/**
 * Replace legacy bootstrap path with the canonical plugin file in active_plugins.
 * Runs only from user-initiated activate or plugin-update hooks.
 *
 * @return void
 */
function coopbolo_migrate_legacy_bootstrap_entry(): void {
	$legacy    = coopbolo_legacy_bootstrap_basename();
	$canonical = plugin_basename( COOPBOLO_FILE );
	if ( $legacy === $canonical ) {
		return;
	}

	$active = (array) get_option( 'active_plugins', array() );
	if ( ! in_array( $legacy, $active, true ) ) {
		return;
	}

	$migrated = array();
	foreach ( $active as $plugin ) {
		if ( $plugin === $legacy ) {
			if ( ! in_array( $canonical, $migrated, true ) ) {
				$migrated[] = $canonical;
			}
			continue;
		}
		if ( $plugin === $canonical ) {
			continue;
		}
		$migrated[] = $plugin;
	}
	update_option( 'active_plugins', array_values( array_unique( $migrated ) ) );
}

/**
 * Remove the legacy bootstrap entry when the canonical plugin is deactivated.
 * Also clears the collections schema version so a later reactivation re-runs the
 * migration (needed if 1.3.4 ran in between and created `cb_logo_collection` posts).
 *
 * @return void
 */
function coopbolo_deactivate(): void {
	$legacy    = coopbolo_legacy_bootstrap_basename();
	$canonical = plugin_basename( COOPBOLO_FILE );

	$active = (array) get_option( 'active_plugins', array() );
	$next   = array_values(
		array_filter(
			$active,
			static function ( $plugin ) use ( $legacy, $canonical ) {
				return $plugin !== $legacy && $plugin !== $canonical;
			}
		)
	);

	if ( $next !== $active ) {
		update_option( 'active_plugins', $next );
	}

	if ( class_exists( 'COOPBOLO_Collections' ) ) {
		delete_option( COOPBOLO_Collections::SCHEMA_OPTION );
	}
}

/**
 * Migrate legacy bootstrap entries after a user-initiated plugin update.
 *
 * @param WP_Upgrader $upgrader Upgrader instance.
 * @param array       $options  Upgrade context.
 * @return void
 */
function coopbolo_maybe_migrate_after_upgrade( $upgrader, array $options ): void {
	unset( $upgrader );

	if ( ( $options['action'] ?? '' ) !== 'update' || ( $options['type'] ?? '' ) !== 'plugin' ) {
		return;
	}

	$plugins  = $options['plugins'] ?? array();
	$canonical = plugin_basename( COOPBOLO_FILE );
	$legacy    = coopbolo_legacy_bootstrap_basename();

	if ( empty( $plugins ) && ! empty( $options['plugin'] ) ) {
		$plugins = array( $options['plugin'] );
	}

	foreach ( $plugins as $plugin ) {
		if ( $plugin === $canonical || $plugin === $legacy ) {
			coopbolo_migrate_legacy_bootstrap_entry();
			return;
		}
	}
}
add_action( 'upgrader_process_complete', 'coopbolo_maybe_migrate_after_upgrade', 10, 2 );

if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
	/**
	 * Admin notice when PHP is too old (includes are not loaded).
	 *
	 * @return void
	 */
	function coopbolo_php_version_notice(): void {
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
	add_action( 'admin_notices', 'coopbolo_php_version_notice' );

	/**
	 * Refuse activation on PHP below 7.4.
	 *
	 * @return void
	 */
	function coopbolo_activate_legacy(): void {
		deactivate_plugins( plugin_basename( COOPBOLO_FILE ) );
		wp_die(
			esc_html__(
				'Cooper Bold Logo Wrangler requires PHP 7.4 or newer.',
				'cooper-bold-logo-wrangler'
			),
			esc_html__( 'Plugin activation failed', 'cooper-bold-logo-wrangler' ),
			array( 'back_link' => true )
		);
	}
	register_activation_hook( COOPBOLO_FILE, 'coopbolo_activate_legacy' );
	return;
}

require_once COOPBOLO_PATH . 'includes/class-coopbolo-plugin.php';

/**
 * Bootstrap the plugin singleton.
 *
 * @return void
 */
function coopbolo_init(): void {
	COOPBOLO_Plugin::instance();
}
add_action( 'plugins_loaded', 'coopbolo_init' );

/**
 * Refuse activation on PHP below 7.4.
 *
 * @return void
 */
function coopbolo_activate(): void {
	if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
		deactivate_plugins( plugin_basename( COOPBOLO_FILE ) );
		wp_die(
			esc_html__(
				'Cooper Bold Logo Wrangler requires PHP 7.4 or newer.',
				'cooper-bold-logo-wrangler'
			),
			esc_html__( 'Plugin activation failed', 'cooper-bold-logo-wrangler' ),
			array( 'back_link' => true )
		);
	}

	coopbolo_migrate_legacy_bootstrap_entry();
}
register_activation_hook( COOPBOLO_FILE, 'coopbolo_activate' );
register_deactivation_hook( COOPBOLO_FILE, 'coopbolo_deactivate' );
