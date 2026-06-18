<?php
/**
 * This is main plugin file.
 *
 * @link              http://xfinitysoft.com/
 * @since             0.0.1
 * @package WP Post Hide
 * Plugin Name: WP Hide Post — Hide Posts, Pages, Custom Post Types, and Control Products Visibility for WooCommerce
 * Description: Control the visibility of post type items like pages, posts, and custom post types. Hidden in specific parts but other parts still visible.
 * Plugin URI:http://www.xfinitysoft.com/wp-post-hide/
 * Version: 2.0.4
 * Author:Xfinity Soft
 * Author URI:http://www.xfinitysoft.com/
 * Text Domain:wp-post-hide
 * Domain Path: /languages
 * Requires PHP: 7.4
 * Requires at least: 5.0
 * Tested up to: 7.0
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

// Exit if directly access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Check if pro version is active and deactivate free version if it is.
add_action(
	'admin_init',
	function () {
		if ( is_plugin_active( 'xs-wordpress-hide-post/xs-wordpress-hide-post.php' ) ) {
			deactivate_plugins( plugin_basename( __FILE__ ) );
			add_action(
				'admin_notices',
				function () {
					?>
			<div class="notice notice-warning is-dismissible">
					<p><?php esc_html_e( 'WP Post Hide (Free) has been deactivated because WP Post Hide Pro is already active.', 'wp-post-hide' ); ?></p>
			</div>
					<?php
				}
			);
			return;
		}
	}
);

// Define  XSWPPH_PLUGIN_FILE.
if ( ! defined( 'XSWPHP_PLUGIN_FILE' ) ) {
	define( 'XSWPHP_PLUGIN_FILE', __FILE__ );
}
// Define  XSWPPH_PLUGIN_FILE.
if ( ! defined( 'XSWPHP_VERSION' ) ) {
	define( 'XSWPHP_VERSION', '2.0.4' );
}
if ( ! defined( 'XSWPHP_ROOT_URL' ) ) {
	define( 'XSWPHP_ROOT_URL', plugins_url( '', __FILE__ ) );
}
// Includes main class of wphp.
if ( ! class_exists( 'XSWPHP_Main' ) ) {
	include_once __DIR__ . '/includes/classes/class-xswphp-main.php';
}

/**
 * Main instance of XSWPPH_Main.
 *
 * Returns the main instance of XSWPPH_Main to prevent the need to use globals.
 *
 * @return XSWPHP_Main
 */
function xswphp_main() {
	return new XSWPHP_Main();
}

// Global for backwards compatibility (prefixed key for coding standards).
$GLOBALS['xswphp_wp_post_hide'] = xswphp_main();