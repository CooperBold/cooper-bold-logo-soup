<?php
declare(strict_types=1);

/**
 * Plugin Name:       Cooper Bold Logo Soup
 * Plugin URI:        https://github.com/CooperBold/cooper-bold-logo-soup
 * Description:       Display client and partner logos in a balanced strip using Sanity Labs Logo Soup normalization.
 * Version:           1.0.1
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * Author:            Cooper Bold
 * Author URI:        https://cooperbold.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       cooper-bold-logo-soup
 *
 * @package CooperBoldLogoSoup
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CB_LOGO_SOUP_VERSION', '1.0.1' );
define( 'CB_LOGO_SOUP_FILE', __FILE__ );
define( 'CB_LOGO_SOUP_PATH', plugin_dir_path( __FILE__ ) );
define( 'CB_LOGO_SOUP_URL', plugin_dir_url( __FILE__ ) );

if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
	/**
	 * Admin notice when PHP is too old (includes are not loaded).
	 */
	function cb_logo_soup_php_version_notice(): void {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		echo '<div class="notice notice-error"><p>';
		echo esc_html__(
			'Cooper Bold Logo Soup requires PHP 7.4 or newer.',
			'cooper-bold-logo-soup'
		);
		echo '</p></div>';
	}
	add_action( 'admin_notices', 'cb_logo_soup_php_version_notice' );

	/**
	 * Refuse activation on PHP below 7.4.
	 */
	function cb_logo_soup_activate_legacy(): void {
		deactivate_plugins( plugin_basename( CB_LOGO_SOUP_FILE ) );
		wp_die(
			esc_html__(
				'Cooper Bold Logo Soup requires PHP 7.4 or newer.',
				'cooper-bold-logo-soup'
			),
			esc_html__( 'Plugin activation failed', 'cooper-bold-logo-soup' ),
			array( 'back_link' => true )
		);
	}
	register_activation_hook( CB_LOGO_SOUP_FILE, 'cb_logo_soup_activate_legacy' );
	return;
}

require_once CB_LOGO_SOUP_PATH . 'includes/class-cb-logo-soup.php';

/**
 * Bootstrap the plugin.
 */
function cb_logo_soup_init(): void {
	CB_Logo_Soup::instance();
}
add_action( 'plugins_loaded', 'cb_logo_soup_init' );

/**
 * Refuse activation on PHP below 7.4.
 */
function cb_logo_soup_activate(): void {
	if ( version_compare( PHP_VERSION, '7.4', '<' ) ) {
		deactivate_plugins( plugin_basename( CB_LOGO_SOUP_FILE ) );
		wp_die(
			esc_html__(
				'Cooper Bold Logo Soup requires PHP 7.4 or newer.',
				'cooper-bold-logo-soup'
			),
			esc_html__( 'Plugin activation failed', 'cooper-bold-logo-soup' ),
			array( 'back_link' => true )
		);
	}
}
register_activation_hook( CB_LOGO_SOUP_FILE, 'cb_logo_soup_activate' );
