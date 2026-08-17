<?php
/**
 * Legacy bootstrap path for sites that activated Balanced Logos as
 * `{plugin-dir}/balanced-logos.php`. Not a second plugin entry (no Plugin Name
 * header) — WordPress still includes this file when it remains in
 * `active_plugins`, and the canonical bootstrap migrates that entry.
 *
 * @package CooperBoldLogoWrangler
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'CB_LW_BOOTSTRAP_FILE' ) ) {
	define( 'CB_LW_BOOTSTRAP_FILE', __FILE__ );
}

require_once __DIR__ . '/cooper-bold-logo-wrangler.php';
