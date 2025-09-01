<?php
/**
 * This is main plugin file.
 *
 * @link              http://xfinitysoft.com/
 * @since             0.0.1
 * @package WordPress Hide Post
 * Plugin Name: WP Post Hide
 * Description: Control the visibility of post type items like pages, posts, and custom post types. Hidden in specific parts but other parts still visible.
 * Plugin URI:http://www.xfinitysoft.com/wordpress-post-hide/
 * Version: 2.0.0
 * Author:Xfinity Soft
 * Author URI:http://www.xfinitysoft.com/
 * Text Domain:xswpph-domain
 * Domain Path: /languages
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
					<p><?php esc_html_e( 'WP Post Hide (Free) has been deactivated because WP Post Hide Pro is already active.', 'xswpph-domain' ); ?></p>
			</div>
					<?php
				}
			);
			return;
		}
	}
);

// Define  XSWPPH_PLUGIN_FILE.
if ( ! defined( 'XSWPPH_PLUGIN_FILE' ) ) {
	define( 'XSWPPH_PLUGIN_FILE', __FILE__ );
}

// Includes main class of wphp.
if ( ! class_exists( 'XSWPPH_Main' ) ) {
	include_once __DIR__ . '/includes/classes/class-xswpph-main.php';
}

/**
 * Main instance of XSWPPH_Main.
 *
 * Returns the main instance of XSWPPH_Main to prevent the need to use globals.
 *
 * @return XSWPPH_Main
 */
function xswpph_main() {
	return XSWPPH_Main::xswpph_instance();
}

// Global for backwards compatibility.
$GLOBALS['xs-wp-post-hide'] = xswpph_main();