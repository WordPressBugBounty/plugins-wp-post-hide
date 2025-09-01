<?php
/**
 * WP Post Hide Setup.
 *
 * @package WordPress Hide Post lite Setup
 * @since 0.0.1
 */

// Exit if directly access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main Class of WordPress Hide Post  lite
 *
 * @class XSWPPH_Main
 * @since 0.0.1
 */
class XSWPPH_Main {
	/**
	 * The single instance of class.
	 *
	 * @var self|null
	 */
	protected static $xswpph_instance = null;

	/**
	 * Main instance function of.
	 *
	 * @return object
	 */
	public static function xswpph_instance() {
		if ( is_null( self::$xswpph_instance ) ) {
			self::$xswpph_instance = new self();
		}
		return self::$xswpph_instance;
	}

	/**
	 * Wphplite Constructor
	 */
	public function __construct() {
		$this->xswpph_define_constants();
		$this->xswpph_includes();
		$this->xswpph_init_hooks();
	}

	/**
	 * Define the plugin constants
	 */
	public function xswpph_define_constants() {
		define( 'XSWPPH_ABSPATH', dirname( XSWPPH_PLUGIN_FILE ) );
		define( 'XSWPPH_BASENAME', plugin_basename( XSWPPH_PLUGIN_FILE ) );
	}

	/**
	 * Hooks into actions and filters
	 */
	public function xswpph_init_hooks() {

		add_action( 'admin_menu', array( 'XSWPPH_Init', 'xswpph_admin_menu' ) );
		add_action( 'admin_init', array( 'XSWPPH_Init', 'xswpph_register_settings' ) );
		add_action( 'init', array( 'XSWPPH_Init', 'xswpph_load_textdomain' ) );
		add_action( 'admin_enqueue_scripts', array( 'XSWPPH_Init', 'xswpph_load_css_js' ) );
		add_filter( 'plugin_action_links_' . XSWPPH_BASENAME, 'xswpph_plugin_link' );
		add_action( 'add_meta_boxes', array( 'XSWPPH_Init', 'xswpph_add_meta_box' ) );
		add_action( 'save_post', array( 'XSWPPH_Init', 'xswpph_save_meta_data' ), 10, 3 );
		add_action( 'pre_get_posts', array( 'XSWPPH_Init', 'xswpph_hidden_posts_pages' ) );
		add_action( 'wp_ajax_xswpph_send_mail', array( 'XSWPPH_Init', 'xswpph_send_mail' ) );

		// Create database tables.
		add_action( 'init', array( 'XSWPPH_Init', 'create_database_tables' ) );

		// Initialize WooCommerce integration.
		if ( class_exists( 'WooCommerce' ) ) {
			new XSWPPH_WooCommerce();
		}

		// Add REST API filters for enabled post types.
		add_action( 'rest_api_init', array( 'XSWPPH_Init', 'setup_rest_api_filters' ) );

		// Add widget filters.
		add_filter( 'widget_posts_args', array( 'XSWPPH_Init', 'xswpph_hidden_recent_posts' ), 10, 2 );
		add_filter( 'widget_recent_entries_args', array( 'XSWPPH_Init', 'xswpph_hidden_recent_posts' ), 10, 2 );
		add_filter( 'wp_widget_recent_posts_args', array( 'XSWPPH_Init', 'xswpph_hidden_recent_posts' ), 10, 2 );
		add_filter( 'get_next_post_where', array( 'XSWPPH_Init', 'xswpph_next_previous_link' ), 10, 1 );
		add_filter( 'get_previous_post_where', array( 'XSWPPH_Init', 'xswpph_next_previous_link' ), 10, 1 );

		// Trigger data migration.
		add_action( 'admin_init', array( $this, 'maybe_migrate_data' ) );

		// Add hidden column to admin tables.
		add_action( 'init', 'xswpph_new_custom_col' );
	}

	/**
	 * Includes the files
	 */
	public function xswpph_includes() {
		include_once XSWPPH_ABSPATH . '/includes/classes/class-xswpph-database.php';
		include_once XSWPPH_ABSPATH . '/includes/classes/class-xswpph-woocommerce.php';
		include_once XSWPPH_ABSPATH . '/templates/views/xswpph-metaboxes.php';
		include_once XSWPPH_ABSPATH . '/includes/functions/xswpph-functions.php';
		include_once XSWPPH_ABSPATH . '/includes/classes/class-xswpph-init.php';
		include_once XSWPPH_ABSPATH . '/templates/xswpph-page.php';
		include_once XSWPPH_ABSPATH . '/templates/xswpph-support.php';
	}

	/**
	 * Maybe migrate data from meta to custom table
	 */
	public function maybe_migrate_data() {
		$data_migrated = get_option( 'xswpph_data_migrated', false );
		if ( ! $data_migrated && current_user_can( 'manage_options' ) ) {
			XSWPPH_Database::migrate_meta_to_table();
		}
	}
}
