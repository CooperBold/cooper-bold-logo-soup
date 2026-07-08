<?php
declare(strict_types=1);

/**
 * Plugin Name:       Balanced Logos
 * Plugin URI:        https://github.com/CooperBold/cooper-bold-logo-soup
 * Description:       Display client and partner logos in a balanced strip. Normalization powered by the open-source Sanity Labs logo-soup library.
 * Version:           1.2.15
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * Author:            Cooper Bold
 * Author URI:        https://cooperbold.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       balanced-logos
 *
 * @package CooperBoldBalancedLogos
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CB_BALANCED_LOGOS_VERSION', '1.2.15' );
define( 'CB_BALANCED_LOGOS_FILE', __FILE__ );
define( 'CB_BALANCED_LOGOS_PATH', plugin_dir_path( __FILE__ ) );
define( 'CB_BALANCED_LOGOS_URL', plugin_dir_url( __FILE__ ) );

if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
	/**
	 * Admin notice when PHP is too old (includes are not loaded).
	 *
	 * @return void
	 */
	function cb_balanced_logos_php_version_notice(): void {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		echo '<div class="notice notice-error"><p>';
		echo esc_html__(
			'Balanced Logos requires PHP 7.4 or newer.',
			'balanced-logos'
		);
		echo '</p></div>';
	}
	add_action( 'admin_notices', 'cb_balanced_logos_php_version_notice' );

	/**
	 * Refuse activation on PHP below 7.4.
	 *
	 * @return void
	 */
	function cb_balanced_logos_activate_legacy(): void {
		deactivate_plugins( plugin_basename( CB_BALANCED_LOGOS_FILE ) );
		wp_die(
			esc_html__(
				'Balanced Logos requires PHP 7.4 or newer.',
				'balanced-logos'
			),
			esc_html__( 'Plugin activation failed', 'balanced-logos' ),
			array( 'back_link' => true )
		);
	}
	register_activation_hook( CB_BALANCED_LOGOS_FILE, 'cb_balanced_logos_activate_legacy' );
	return;
}

require_once CB_BALANCED_LOGOS_PATH . 'includes/class-cb-balanced-logos.php';

/**
 * Bootstrap the plugin singleton.
 *
 * @return void
 */
function cb_balanced_logos_init(): void {
	CB_Balanced_Logos::instance();
}
add_action( 'plugins_loaded', 'cb_balanced_logos_init' );

/**
 * Refuse activation on PHP below 7.4.
 *
 * @return void
 */
function cb_balanced_logos_activate(): void {
	if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
		deactivate_plugins( plugin_basename( CB_BALANCED_LOGOS_FILE ) );
		wp_die(
			esc_html__(
				'Balanced Logos requires PHP 7.4 or newer.',
				'balanced-logos'
			),
			esc_html__( 'Plugin activation failed', 'balanced-logos' ),
			array( 'back_link' => true )
		);
	}
}
register_activation_hook( CB_BALANCED_LOGOS_FILE, 'cb_balanced_logos_activate' );
