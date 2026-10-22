<?php
/**
 * Legacy bootstrap path for sites that activated Balanced Logos as
 * `{plugin-dir}/balanced-logos.php`. Not a second plugin entry (no Plugin Name
 * header). WordPress still includes this file when that path remains in
 * `active_plugins`. This loader only requires the canonical bootstrap; it does
 * not change activation state.
 *
 * @package CooperBoldLogoWrangler
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'COOPBOLO_BOOTSTRAP_FILE' ) ) {
	define( 'COOPBOLO_BOOTSTRAP_FILE', __FILE__ );
}

require_once __DIR__ . '/cooper-bold-logo-wrangler.php';
