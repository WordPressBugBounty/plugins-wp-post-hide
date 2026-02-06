<?php
/**
 * WP Post Hide Setup
 *
 * @package WP Post Hide Setup
 * @since 1.0.0
 */

// Exit if directly access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main Class of WP Post Hide
 *
 * @class XSWPHP_Main
 * @since 1.0.0
 */
class XSWPHP_Main {

	/**
	 * Wphp Constructor
	 */
	public function __construct() {
		$this->xswphp_define_constants();
		$this->xswphp_includes();
		$this->xswphp_init_hooks();
	}

	/**
	 * Define the plugin constants
	 */
	public function xswphp_define_constants() {
		define( 'XSWPHP_ABSPATH', dirname( XSWPHP_PLUGIN_FILE ) );
		define( 'XSWPHP_BASENAME', plugin_basename( XSWPHP_PLUGIN_FILE ) );
	}

	/**
	 * Hooks into actions and filters
	 */
	public function xswphp_init_hooks() {
		$xswphp_init = new XSWPHP_Init();
		add_action( 'admin_menu', array( $xswphp_init, 'xswphp_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $xswphp_init, 'xswphp_load_css_js' ) );
		add_action( 'admin_init', array( $xswphp_init, 'xswphp_register_settings' ) );
		add_action( 'init', 'xswphp_new_custom_col' );
		add_filter( 'plugin_action_links_' . XSWPHP_BASENAME, 'xswphp_plugin_link' );
		add_action( 'add_meta_boxes', array( $xswphp_init, 'xswphp_add_meta_box' ) );
		add_action( 'restrict_manage_posts', array( $xswphp_init, 'xswphp_field_filter' ), 10, 2 );

		add_action( 'save_post', array( $xswphp_init, 'xswphp_save_meta_data' ), 10, 2 );
		add_action(
			'pending_to_publish',
			function () {
				remove_action( 'save_post', array( $xswphp_init, 'xswphp_save_meta_data' ), 10 );
			}
		);
		add_action( 'pre_get_posts', array( $xswphp_init, 'xswphp_hidden_posts_pages' ) );
		add_action( 'template_redirect', array( $xswphp_init, 'xswphp_hide_singular_and_adjacent' ) );
		// Filter widgets (Recent Posts, etc.) to exclude hidden posts/attachments.
		add_filter( 'widget_posts_args', array( $xswphp_init, 'xswphp_filter_widget_posts' ), 10, 1 );

		// Add REST API filters for enabled post types.
		$enabled_post_types = get_option( 'xswphp_post_types', array() );
		if ( ! empty( $enabled_post_types ) ) {
			foreach ( $enabled_post_types as $post_type ) {
				if ( 'product' !== $post_type && 'attachment' !== $post_type ) {
					add_filter( "rest_{$post_type}_query", array( $xswphp_init, 'hide_from_rest_api' ), 10, 1 );
				}
			}
		}

		// Trigger data migration.
		add_action( 'admin_init', array( $this, 'maybe_migrate_data' ) );
	}

	/**
	 * Maybe migrate data from meta to custom table
	 */
	public function maybe_migrate_data() {
		$data_migrated = get_option( 'xswphp_data_migrated', false );
		if ( ! $data_migrated && current_user_can( 'manage_options' ) ) {
			XSWPHP_Database::migrate_meta_to_table();
		}
	}

	/**
	 * Includes the files
	 */
	public function xswphp_includes() {

		include_once XSWPHP_ABSPATH . '/includes/classes/class-xswphp-database.php';
		include_once XSWPHP_ABSPATH . '/includes/classes/class-xswphp-woocommerce.php';
		include_once XSWPHP_ABSPATH . '/includes/classes/class-xswphp-init.php';
		include_once XSWPHP_ABSPATH . '/templates/xswphp-page.php';
		include_once XSWPHP_ABSPATH . '/templates/views/xswphp-metaboxes.php';
		include_once XSWPHP_ABSPATH . '/includes/functions/xswphp-functions.php';
	}
}
