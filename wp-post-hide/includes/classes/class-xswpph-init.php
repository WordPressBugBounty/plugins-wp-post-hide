<?php
/**
 * WP Post Hide Setup Initialization.
 *
 * @package WordPress Hide Post lite Setup
 * @since 0.0.1
 */

// Exit if directly access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Initialization of WP Post Hide
 *
 * @class XSWPPH_Init
 * @since 1.0.0
 */
class XSWPPH_Init {

	/**
	 * Add menu page of  WP Post Hide.
	 */
	public static function xswpph_admin_menu() {
		add_menu_page(
			esc_html__( 'WP Post Hide', 'xswpph-domain' ),
			esc_html__( 'WP Post Hide', 'xswpph-domain' ),
			'manage_options',
			'xswpph_page',
			'xswpph_page',
			'dashicons-admin-settings',
			40
		);
		add_submenu_page(
			'xswpph_page',
			esc_html__( 'Support', 'xswpph-domain' ),
			esc_html__( 'Support', 'xswpph-domain' ),
			'manage_options',
			'xswpph_support',
			'xswpph_support',
		);
	}

	/**
	 * Load The Css and jQuery.
	 */
	public static function xswpph_load_css_js() {
		wp_register_script( 'xswpph-scripts', plugins_url( 'wp-post-hide/assets/js/xswpph-script.js' ) );
		wp_enqueue_script( 'jquery' );

		$current_page = isset( $_GET['page'] ) ? sanitize_text_field( wp_unslash( $_GET['page'] ) ) : '';
		if ( 'xswpph_page' === $current_page || 'xswpph_support' === $current_page ) {
			wp_register_style( 'xswpph-styles', plugins_url( 'wp-post-hide/assets/css/xswpph-style.css' ) );
			wp_enqueue_style( 'xswpph-styles' );
		}
		wp_localize_script(
			'xswpph-scripts',
			'xswpph_vars',
			array(
				'nonce' => wp_create_nonce( 'xswpph_send_mail_nonce' ),
			)
		);
		wp_enqueue_script( 'xswpph-scripts' );
	}

	/**
	 * Register the setting Fields options.
	 **/
	public static function xswpph_register_settings() {
		register_setting( 'xswpph_options', 'xswpph_post_types' );
		register_setting( 'xswpph_options', 'xswpph_enable' );
		register_setting( 'xswpph_options', 'xswpph_disable_hidden_column' );
	}

	/**
	 * Load the text domain
	 */
	public static function xswpph_load_textdomain() {
		load_plugin_textdomain( 'xswpph-domain', false, dirname( XSWPPH_BASENAME ) . '/languages' );
	}

	/**
	 * Add meta box.
	 */
	public static function xswpph_add_meta_box() {
		$xs_options = get_option( 'xswpph_post_types' );
		if ( isset( $xs_options ) && ! empty( $xs_options ) ) {
			foreach ( $xs_options as $xs_key ) {
				if ( 'page' === $xs_key ) {
					$xswpph_callback = 'xswpph_pagebox_callback';
				} elseif ( 'attachment' === $xs_key ) {
					$xswpph_callback = 'xswpph_attachmentbox_callback';
				} else {
					$xswpph_callback = 'xswpph_postbox_callback';
				}

				add_meta_box(
					'xswpph_meta_box',
					esc_html__( 'Post Visibility', 'xswpph-domain' ),
					$xswpph_callback,
					$xs_key,
					'side'
				);
			}
		}
	}


	/**
	 * Save the Meta Box data.
	 *
	 * @param integer $post_id ID of post.
	 * @param object  $post Detail of post.
	 * @return void
	 */
	public static function xswpph_save_meta_data( $post_id, $post ) {
		// Do nothing during a bulk edit.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		// Check nonce.
		if ( ! isset( $_POST['xswpph_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['xswpph_nonce'] ) ), 'xswpph_save_meta' ) ) {
			return;
		}

		// Check user permissions.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		if ( 'page' === $post->post_type ) {
			$meta_key = array(
				'_xswpph_all_hidden',
				'_xswpph_front_page',
				'_xswpph_blog_page',
				'_xswpph_rest_api',
				'_xswpph_single_post_page',
			);
		} elseif ( 'attachment' === $post->post_type ) {
			$meta_key = array(
				'_xswpph_all_hidden',
				'_xswpph_search',
				'_xswpph_rest_api',
			);
		} else {
			$meta_key = array(
				'_xswpph_all_hidden',
				'_xswpph_front_page',
				'_xswpph_blog_page',
				'_xswpph_category_page',
				'_xswpph_tag_page',
				'_xswpph_author',
				'_xswpph_archive',
				'_xswpph_search',
				'_xswpph_feeds',
				'_xswpph_recent',
				'_xswpph_rel_link',
				'_xswpph_rest_api',
				'_xswpph_single_post_page',
			);

			// Add WooCommerce options for products.
			if ( 'product' === $post->post_type && class_exists( 'WooCommerce' ) ) {
				$meta_key[] = '_xswpph_wc_shop';
				$meta_key[] = '_xswpph_wc_product_category';
			}
		}

		// Get the posted data safely.
		$xswpph_data = isset( $_POST['xswpph'] ) ? map_deep( wp_unslash( $_POST['xswpph'] ), 'sanitize_text_field' ) : array();

		foreach ( $meta_key as $mkey ) {
			if ( isset( $xswpph_data[ $mkey ] ) ) {
				$val = $xswpph_data[ $mkey ];
				update_post_meta( $post_id, $mkey, $val );
				self::save_to_custom_table( $post_id, $mkey, $val );
			} else {
				update_post_meta( $post_id, $mkey, '' );
				self::remove_from_custom_table( $post_id, $mkey );
			}
		}

		// Clear cache.
		XSWPPH_Database::clear_post_cache( $post_id, $post->post_type );
	}

	/**
	 * Method For front end hidden.
	 *
	 * @param object $query Object of query of pages.
	 */
	public static function xswpph_hidden_posts_pages( $query ) {

		if ( ! is_admin() && $query->is_main_query() ) {

			$xswpph_pages    = self::xswpph_database_query( '_xswpph_always', 'Always' );
			$xswpph_seapages = self::xswpph_database_query( '_xswpph_keep_search', '_xswpph_keep_search' );

			if ( $query->is_home() || $query->is_front_page() ) {
				$xswpph_posts = self::xswpph_database_query( '_xswpph_front_page', 'Front Page' );
				$wphp_hidden  = array_merge( $xswpph_posts, $xswpph_pages );
				$wphp_hidden  = array_merge( $wphp_hidden, $xswpph_seapages );
				$query->set( 'post__not_in', $wphp_hidden );
			}

			if ( $query->is_home() && ! $query->is_front_page() ) {
				// This is the blog page (posts page).
				$xswpph_posts    = self::xswpph_database_query( '_xswpph_blog_page', 'Blog Page' );
				$existing_not_in = $query->get( 'post__not_in' );
				if ( ! is_array( $existing_not_in ) ) {
					$existing_not_in = array();
				}
				$wphp_hidden = array_unique( array_merge( $existing_not_in, $xswpph_posts, $xswpph_pages, $xswpph_seapages ) );
				$query->set( 'post__not_in', $wphp_hidden );
			}

			if ( $query->is_search() ) {
				$xswpph_posts = self::xswpph_database_query( '_xswpph_search', 'Search Results' );
				$wphp_hidden  = array_merge( $xswpph_posts, $xswpph_pages );
				if ( get_search_query() ) {
					$query->set( 'post__not_in', $wphp_hidden );
				}
			}

			if ( $query->is_category() ) {
				$xswpph_posts = self::xswpph_database_query( '_xswpph_category_page', 'Category Pages' );
				$wphp_hidden  = array_merge( $xswpph_posts, $xswpph_pages );
				$wphp_hidden  = array_merge( $wphp_hidden, $xswpph_seapages );
				$query->set( 'post__not_in', $wphp_hidden );
			}

			if ( $query->is_tag() ) {
				$xswpph_posts = self::xswpph_database_query( '_xswpph_tag_page', 'Tag Pages' );
				$wphp_hidden  = array_merge( $xswpph_posts, $xswpph_pages );
				$wphp_hidden  = array_merge( $wphp_hidden, $xswpph_seapages );
				$query->set( 'post__not_in', $wphp_hidden );
			}

			if ( $query->is_author() ) {
				$xswpph_posts = self::xswpph_database_query( '_xswpph_author', 'Author Pages' );
				$wphp_hidden  = array_merge( $xswpph_posts, $xswpph_pages );
				$wphp_hidden  = array_merge( $wphp_hidden, $xswpph_seapages );
				$query->set( 'post__not_in', $wphp_hidden );
			}

			if ( $query->is_date() ) {
				$xswpph_posts = self::xswpph_database_query( '_xswpph_archive', 'Date Archive' );
				$wphp_hidden  = array_merge( $xswpph_posts, $xswpph_pages );
				$wphp_hidden  = array_merge( $wphp_hidden, $xswpph_seapages );
				$query->set( 'post__not_in', $wphp_hidden );
			}

			if ( $query->is_feed() ) {
				$xswpph_posts = self::xswpph_database_query( '_xswpph_feeds', 'Feeds' );
				$wphp_hidden  = array_merge( $xswpph_posts, $xswpph_pages );
				$wphp_hidden  = array_merge( $wphp_hidden, $xswpph_seapages );
				$query->set( 'post__not_in', $wphp_hidden );
			}
		}

		// Handle media library hiding in admin.
		$post_type = isset( $_GET['post_type'] ) ? sanitize_text_field( wp_unslash( $_GET['post_type'] ) ) : '';
		if ( is_admin() && 'attachment' === $post_type ) {
			$attachment_hidden = self::xswpph_database_query( '_xswpph_search', 'Search Results' );
			if ( ! empty( $attachment_hidden ) ) {
				$existing_not_in = $query->get( 'post__not_in' );
				if ( ! is_array( $existing_not_in ) ) {
					$existing_not_in = array();
				}
				$query->set( 'post__not_in', array_unique( array_merge( $existing_not_in, $attachment_hidden ) ) );
			}
			return;
		}
	}

	/**
	 * Get The hidden Ids by meta key and meta Value
	 *
	 * @param String $meta_key Key of meta data.
	 * @param string $meta_value Value meta data.
	 * @return array $ids
	 */
	public static function xswpph_database_query( $meta_key, $meta_value ) {
		// Try to use the new database method if available.
		$condition = self::get_condition_from_meta_key( $meta_key );
		if ( $condition && class_exists( 'XSWPPH_Database' ) ) {
			// Get post type from current context if possible.
			global $wp_query;
			$post_type = 'post'; // default.
			if ( isset( $wp_query->query['post_type'] ) ) {
				$post_type = $wp_query->query['post_type'];
			} elseif ( is_page() ) {
				$post_type = 'page';
			} elseif ( function_exists( 'is_product' ) && ( is_shop() || is_product_category() || is_product_tag() ) ) {
				$post_type = 'product';
			}

			return XSWPPH_Database::get_hidden_posts_ids( $post_type, $condition, true );
		}

		// Fallback to original method.
		global $wpdb;
		$post_ids     = array();
		$xswpph_query = $wpdb->get_results( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value = %s", $meta_key, $meta_value ) );
		foreach ( $xswpph_query as $xswpph_val ) {
			$post_ids[] = $xswpph_val->post_id;
		}
		return $post_ids;
	}
	/**
	 * Send mail support team.
	 *
	 * @return void
	 */
	public static function xswpph_send_mail() {
		if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'xswpph_send_mail_nonce' ) ) {
			wp_send_json_error( array( 'message' => 'Nonce verification failed' ) );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => 'Unauthorized' ) );
		}
		$post_data = isset( $_POST['data'] ) ? sanitize_text_field( wp_unslash( $_POST['data'] ) ) : '';
		$data      = array();
		parse_str( $post_data, $data );
		$host                = isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';
		$data['plugin_name'] = 'WP Post hide';
		$data['version']     = 'lite';
		$data['website']     = ( isset( $_SERVER['HTTPS'] ) && 'on' === $_SERVER['HTTPS'] ? 'https' : 'http' ) . '://' . $host;
		$to                  = 'xfinitysoft@gmail.com';
		switch ( $data['type'] ) {
			case 'report':
				$subject = 'Report a bug';
				break;
			case 'hire':
				$subject = 'Hire us to customize/develope Plugin/Theme or WordPress projects';
				break;

			default:
				$subject = 'Request a Feature';
				break;
		}

		$body  = '<html><body><table>';
		$body .= '<tbody>';
		$body .= '<tr><th>User Name</th><td>' . esc_html( $data['xswpph_name'] ) . '</td></tr>';
		$body .= '<tr><th>User email</th><td>' . esc_html( $data['xswpph_email'] ) . '</td></tr>';
		$body .= '<tr><th>Plugin Name</th><td>' . esc_html( $data['plugin_name'] ) . '</td></tr>';
		$body .= '<tr><th>Version</th><td>' . esc_html( $data['version'] ) . '</td></tr>';
		$body .= '<tr><th>Website</th><td><a href="' . esc_url( $data['website'] ) . '">' . esc_html( $data['website'] ) . '</a></td></tr>';
		$body .= '<tr><th>Message</th><td>' . esc_html( $data['xswpph_message'] ) . '</td></tr>';
		$body .= '</tbody>';
		$body .= '</table></body></html>';

		$headers = array( 'Content-Type: text/html; charset=UTF-8' );
		$params  = 'name=' . urlencode( $data['xswpph_name'] );
		$params .= '&email=' . urlencode( $data['xswpph_email'] );
		$params .= '&site=' . urlencode( $data['website'] );
		$params .= '&version=' . urlencode( $data['version'] );
		$params .= '&plugin_name=' . urlencode( $data['plugin_name'] );
		$params .= '&type=' . urlencode( $data['type'] );
		$params .= '&message=' . urlencode( $data['xswpph_message'] );

		$server_response = wp_remote_post( 'https://xfinitysoft.com/wp-json/plugin/v1/quote/save/?' . $params );
		$api_response    = json_decode( wp_remote_retrieve_body( $server_response ), true );

		if ( isset( $api_response['status'] ) && $api_response['status'] ) {
			wp_mail( $to, $subject, $body, $headers );
			wp_send_json( array( 'status' => true ) );
		} else {
			wp_send_json( array( 'status' => false ) );
		}
		wp_die();
	}

	/**
	 * Create database tables
	 *
	 * @return void
	 */
	public static function create_database_tables() {
		XSWPPH_Database::create_tables();
	}

	/**
	 * Setup REST API filters
	 *
	 * @return void
	 */
	public static function setup_rest_api_filters() {
		$enabled_post_types = get_option( 'xswpph_post_types', array() );
		if ( ! empty( $enabled_post_types ) ) {
			foreach ( $enabled_post_types as $post_type ) {
				if ( 'product' !== $post_type && 'attachment' !== $post_type ) {
					add_filter( "rest_{$post_type}_query", array( __CLASS__, 'hide_from_rest_api' ), 10, 2 );
				} elseif ( 'attachment' === $post_type ) {
					add_filter( 'rest_attachment_query', array( __CLASS__, 'hide_from_rest_api' ), 10, 2 );
				}
			}
		}
	}

	/**
	 * Hide from REST API
	 *
	 * @param array           $args    REST API query arguments.
	 * @param WP_REST_Request $request REST request object.
	 * @return array Modified arguments.
	 */
	public static function hide_from_rest_api( $args, $request ) {
		if ( ! isset( $args['post_type'] ) ) {
			return $args;
		}

		$post_type  = is_array( $args['post_type'] ) ? $args['post_type'][0] : $args['post_type'];
		$hidden_ids = XSWPPH_Database::get_hidden_posts_ids( $post_type, 'rest_api', true );

		if ( ! empty( $hidden_ids ) ) {
			$existing_not_in = isset( $args['post__not_in'] ) ? $args['post__not_in'] : array();
			if ( ! is_array( $existing_not_in ) ) {
				$existing_not_in = array();
			}
			$args['post__not_in'] = array_unique( array_merge( $existing_not_in, $hidden_ids ) );
		}

		return $args;
	}

	/**
	 * Hide posts from recent post widget
	 *
	 * @param array $args     Widget query arguments.
	 * @param array $instance Widget instance data.
	 * @return array Modified arguments.
	 */
	public static function xswpph_hidden_recent_posts( $args, $instance ) {
		wp_reset_query();
		$xs_post      = get_option( 'xswpph_post_types' );
		$xswpph_posts = self::xswpph_database_query( '_xswpph_recent', 'Recent Post' );

		if ( empty( $xs_post ) || empty( $xswpph_posts ) ) {
			return $args;
		}

		// Always hide posts marked for hiding in recent posts widget.
		$existing_not_in = isset( $args['post__not_in'] ) ? $args['post__not_in'] : array();
		if ( ! is_array( $existing_not_in ) ) {
			$existing_not_in = array();
		}
		$args['post__not_in'] = array_unique( array_merge( $existing_not_in, $xswpph_posts ) );

		return $args;
	}

	/**
	 * Hide from post navigation
	 *
	 * @param string $where SQL WHERE clause for post navigation.
	 * @return string Modified WHERE clause.
	 */
	public static function xswpph_next_previous_link( $where ) {
		$hidden_on_post_navigation = self::xswpph_database_query( '_xswpph_rel_link', 'Meta rel link' );

		if ( empty( $hidden_on_post_navigation ) ) {
			return $where;
		}

		$ids_placeholders = array_fill( 0, count( $hidden_on_post_navigation ), '%d' );
		$ids_placeholders = implode( ', ', $ids_placeholders );

		global $wpdb;
		$where .= $wpdb->prepare( " AND ID NOT IN ( $ids_placeholders )", ...$hidden_on_post_navigation );

		return $where;
	}

	/**
	 * Save to custom table
	 *
	 * @param int    $post_id    Post ID.
	 * @param string $meta_key   Meta key.
	 * @param string $meta_value Meta value.
	 */
	private static function save_to_custom_table( $post_id, $meta_key, $meta_value ) {
		if ( empty( $meta_value ) ) {
			return;
		}

		$condition = self::get_condition_from_meta_key( $meta_key );
		if ( $condition ) {
			XSWPPH_Database::add_hide_condition( $post_id, $condition );
		}
	}

	/**
	 * Remove from custom table
	 *
	 * @param int    $post_id  Post ID.
	 * @param string $meta_key Meta key.
	 */
	private static function remove_from_custom_table( $post_id, $meta_key ) {
		$condition = self::get_condition_from_meta_key( $meta_key );
		if ( $condition ) {
			XSWPPH_Database::remove_hide_condition( $post_id, $condition );
		}
	}

	/**
	 * Get condition from meta key
	 *
	 * @param string $meta_key Meta key.
	 * @return string|false Condition name or false if not found.
	 */
	private static function get_condition_from_meta_key( $meta_key ) {
		$mapping = array(
			'_xswpph_front_page'          => 'front_page',
			'_xswpph_blog_page'           => 'blog_page',
			'_xswpph_category_page'       => 'category_page',
			'_xswpph_tag_page'            => 'tag_page',
			'_xswpph_author'              => 'author',
			'_xswpph_archive'             => 'archive',
			'_xswpph_search'              => 'search',
			'_xswpph_feeds'               => 'feeds',
			'_xswpph_recent'              => 'recent',
			'_xswpph_rel_link'            => 'rel_link',
			'_xswpph_rest_api'            => 'rest_api',
			'_xswpph_single_post_page'    => 'single_post_page',
			'_xswpph_wc_shop'             => 'wc_shop',
			'_xswpph_wc_product_category' => 'wc_product_category',
		);

		return isset( $mapping[ $meta_key ] ) ? $mapping[ $meta_key ] : false;
	}

	/**
	 * Add the New Column in Post type
	 *
	 * @param array $defaults Default columns.
	 * @return array $defaults Modified columns.
	 */
	public function xswpph_col_hidden( $defaults ) {
		unset( $defaults['date'] );
		$defaults['xswpph_col_hidden'] = esc_html__( 'Hidden', 'xswpph-domain' );
		$defaults['date']              = esc_html__( 'Date', 'xswpph-domain' );
		return $defaults;
	}

	/**
	 * Add Content in Custom Column
	 *
	 * @param string $column_name Column name.
	 * @param int    $post_ID Post ID.
	 */
	public function xswpph_col_content( $column_name, $post_ID ) {
		if ( 'xswpph_col_hidden' === $column_name ) {
			$xswpph_output = xswpph_meta_data( $post_ID );
			$count         = 0;
			if ( isset( $xswpph_output ) && ! empty( $xswpph_output ) ) {
				echo '<div id="xswpph-' . esc_attr( $post_ID ) . '">';
				foreach ( $xswpph_output as $mvalue ) {
					if ( '' === $mvalue || 'All Hidden' === $mvalue ) {
						continue;
					}
					echo ( 0 < $count ) ? ' , ' : '';
					echo esc_html( $mvalue );
					++$count;
				}
				echo '</div>';
			}
		}
	}
}
