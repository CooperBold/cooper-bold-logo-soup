<?php
declare(strict_types=1);

/**
 * Plugin Name:       Cooper Bold Logo Wrangler
 * Plugin URI:        https://github.com/CooperBold/cooper-bold-logo-soup
 * Description:       Display client and partner logos in a balanced strip. Normalization powered by the open-source Sanity Labs logo-soup library.
 * Version:           1.3.3
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

define( 'CB_LW_VERSION', '1.3.3' );
define( 'CB_LW_FILE', __FILE__ );
define( 'CB_LW_PATH', plugin_dir_path( __FILE__ ) );
define( 'CB_LW_URL', plugin_dir_url( __FILE__ ) );

/**
 * Basename for the headerless legacy bootstrap file.
 *
 * @return string
 */
function cb_lw_legacy_bootstrap_basename(): string {
	return plugin_basename( CB_LW_PATH . 'balanced-logos.php' );
}

/**
 * Replace legacy bootstrap path with the canonical plugin file in active_plugins.
 * Runs only from user-initiated activate or plugin-update hooks.
 *
 * @return void
 */
function cb_lw_migrate_legacy_bootstrap_entry(): void {
	$legacy    = cb_lw_legacy_bootstrap_basename();
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
 *
 * @return void
 */
function cb_lw_deactivate(): void {
	$legacy    = cb_lw_legacy_bootstrap_basename();
	$canonical = plugin_basename( CB_LW_FILE );

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
}

/**
 * Migrate legacy bootstrap entries after a user-initiated plugin update.
 *
 * @param WP_Upgrader $upgrader Upgrader instance.
 * @param array       $options  Upgrade context.
 * @return void
 */
function cb_lw_maybe_migrate_after_upgrade( $upgrader, array $options ): void {
	unset( $upgrader );

	if ( ( $options['action'] ?? '' ) !== 'update' || ( $options['type'] ?? '' ) !== 'plugin' ) {
		return;
	}

	$plugins  = $options['plugins'] ?? array();
	$canonical = plugin_basename( CB_LW_FILE );
	$legacy    = cb_lw_legacy_bootstrap_basename();

	if ( empty( $plugins ) && ! empty( $options['plugin'] ) ) {
		$plugins = array( $options['plugin'] );
	}

	foreach ( $plugins as $plugin ) {
		if ( $plugin === $canonical || $plugin === $legacy ) {
			cb_lw_migrate_legacy_bootstrap_entry();
			return;
		}
	}
}
add_action( 'upgrader_process_complete', 'cb_lw_maybe_migrate_after_upgrade', 10, 2 );

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

	cb_lw_migrate_legacy_bootstrap_entry();
}
register_activation_hook( CB_LW_FILE, 'cb_lw_activate' );
register_deactivation_hook( CB_LW_FILE, 'cb_lw_deactivate' );
