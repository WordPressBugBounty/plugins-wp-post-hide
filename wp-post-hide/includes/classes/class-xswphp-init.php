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
 * Initialization of WordPress Hide Post
 *
 * @class XSWPHP_Init
 * @since 1.0.0
 */
class XSWPHP_Init {
	/**
	 * Array of post IDs to exclude from adjacent post queries.
	 *
	 * @var int[]
	 */
	protected $xswphp_adjacent_exclude_ids = array();
	/**
	 * XSWphp_init  Constructor
	 */
	public function __construct() {
		add_action( 'wp_ajax_xswphp_send_mail', array( $this, 'xswphp_send_mail' ) );
		add_action( 'wp_ajax_xswphp_verify_purchase_code', array( $this, 'xswphp_verify_purchase_code' ) );
		add_action( 'wp_ajax_xswphp_delete_purchase_code', array( $this, 'xswphp_delete_purchase_code' ) );

		// Create database tables.
		add_action( 'init', array( $this, 'create_database_tables' ) );

		// Initialize WooCommerce integration.
		if ( class_exists( 'XSWPHP_WooCommerce' ) ) {
			new XSWPHP_WooCommerce();
		}
	}
	/**
	 * Add menu page of  WordPress Hide Post.
	 */
	public function xswphp_admin_menu() {
		add_menu_page(
			esc_html__( 'WP Post Hide', 'wp-post-hide' ),
			esc_html__( 'WP Post Hide', 'wp-post-hide' ),
			'manage_options',
			'xswphp_page',
			'xswphp_page',
			'dashicons-admin-settings',
			40
		);
		add_submenu_page(
			'xswphp_page',
			esc_html__( 'Support', 'wp-post-hide' ),
			esc_html__( 'Support', 'wp-post-hide' ),
			'manage_options',
			'xswphp-support',
			array( $this, 'xswphp_support' )
		);
	}
	/**
	 * Callback function of support.
	 */
	public function xswphp_support() {
		include_once XSWPHP_ABSPATH . '/templates/xswphp-support.php';
	}
	/**
	 * Load The Css and jQuery.
	 */
	public function xswphp_load_css_js() {
		wp_register_style( 'xswphp-styles', plugins_url( 'wp-post-hide/assets/css/xswphp-style.css' ), array(), XSWPHP_VERSION );
		wp_register_style( 'select2', plugins_url( 'wp-post-hide/assets/css/select2.min.css' ), array(), XSWPHP_VERSION );
		wp_register_script( 'xswphp-scripts', plugins_url( 'wp-post-hide/assets/js/xswphp-script.js' ), array( 'jquery' ), XSWPHP_VERSION, false );
		wp_register_script( 'select2', plugins_url( 'wp-post-hide/assets/js/select2.min.js' ), array( 'jquery' ), XSWPHP_VERSION, false );
		wp_enqueue_style( 'select2' );
		wp_enqueue_style( 'xswphp-styles' );
		wp_enqueue_script( 'select2' );
		wp_enqueue_script( 'xswphp-scripts' );
		// phpcs:ignore
		if ( isset( $_GET['page'] ) && 'xswphp-support' === $_GET['page']) {
			wp_enqueue_style( 'xswphp-support', plugins_url( 'wp-post-hide/assets/css/xswphp-support.css' ), array(), XSWPHP_VERSION );
			wp_enqueue_script( 'xswphp-support', plugins_url( 'wp-post-hide/assets/js/xswphp-support.js' ), array( 'jquery' ), XSWPHP_VERSION, false );
			wp_localize_script(
				'xswphp-support',
				'xswphp',
				array(
					'nonce' => wp_create_nonce( '_xswphp_nonce' ),
				)
			);
		}
	}

	/**
	 * Register the setting Fields options
	 **/
	public function xswphp_register_settings() {
		register_setting(
			'xswphp_options',
			'xswphp_post_types',
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'xswphp_sanitize_post_types_setting' ),
			)
		);
		register_setting(
			'xswphp_options',
			'xswphp_enable',
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'xswphp_sanitize_enable_setting' ),
			)
		);
	}

	/**
	 * Sanitize selected post type slugs for plugin options.
	 *
	 * @param mixed $value Raw option value.
	 * @return string[]
	 */
	public function xswphp_sanitize_post_types_setting( $value ) {
		if ( ! is_array( $value ) ) {
			return array();
		}
		$sanitized = array();
		foreach ( $value as $slug ) {
			$slug = sanitize_key( (string) $slug );
			if ( '' !== $slug ) {
				$sanitized[] = $slug;
			}
		}
		return array_values( array_unique( $sanitized ) );
	}

	/**
	 * Sanitize enable flags array for plugin options.
	 *
	 * @param mixed $value Raw option value.
	 * @return array<string, string>
	 */
	public function xswphp_sanitize_enable_setting( $value ) {
		if ( ! is_array( $value ) ) {
			return array();
		}
		$out = array();
		foreach ( $value as $key => $val ) {
			$key = sanitize_key( (string) $key );
			if ( '' === $key ) {
				continue;
			}
			$out[ $key ] = sanitize_text_field( (string) $val );
		}
		return $out;
	}

	/**
	 * Add meta box.
	 */
	public function xswphp_add_meta_box() {
		$xs_options = get_option( 'xswphp_post_types' );
		if ( isset( $xs_options ) && ! empty( $xs_options ) ) {
			foreach ( $xs_options as $xs_key ) {
				if ( 'page' === $xs_key ) {
					$xswphp_callback = 'xswphp_pagebox_callback';
				} elseif ( 'product' === $xs_key ) {
					$xswphp_callback = 'xswphp_productbox_callback';
				} else {
					$xswphp_callback = 'xswphp_postbox_callback';
				}
				add_meta_box(
					'xswphp_meta_box',
					esc_html__( 'Post Visibility', 'wp-post-hide' ),
					$xswphp_callback,
					$xs_key,
					'side'
				);
			}
		}
	}


	/**
	 * Save the Meta Box data.
	 *
	 * @param integer $post_id Id of post.
	 * @param object  $post  Object of post.
	 * @return Null
	 */
	public function xswphp_save_meta_data( $post_id, $post = null ) {
		// Get post object if not provided.
		if ( ! $post ) {
			$post = get_post( $post_id );
		}

		if ( ! $post ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), '_xswphp_nonce' ) ) {
				return;
		}
		if ( isset( $post->post_type ) && 'page' === $post->post_type ) {
			$meta_key = array(
				'_xswphp_all_hidden',
				'_xswphp_redirect_page',
				'_xswphp_front_page',
				'_xswphp_always',
				'_xswphp_keep_search',
				'_xswphp_rest_api',
			);

		} elseif ( isset( $post->post_type ) && 'attachment' === $post->post_type ) {
			$meta_key = array(
				'_xswphp_all_hidden',
				'_xswphp_redirect_page',
				'_xswphp_front_page',
				'_xswphp_category_page',
				'_xswphp_tag_page',
				'_xswphp_author',
				'_xswphp_archive',
				'_xswphp_search',
				'_xswphp_feeds',
				'_xswphp_recent',
				'_xswphp_rel_link',
				'_xswphp_rest_api',
			);

		} elseif ( isset( $post->post_type ) && 'product' === $post->post_type ) {
			$meta_key = array(
				'_xswphp_all_hidden',
				'_xswphp_redirect_page',
				'_xswphp_front_page',
				'_xswphp_category_page',
				'_xswphp_tag_page',
				'_xswphp_author',
				'_xswphp_archive',
				'_xswphp_search',
				'_xswphp_feeds',
				'_xswphp_recent',
				'_xswphp_rel_link',
				'_xswphp_rest_api',
			);

		} else {
			$meta_key = array(
				'_xswphp_all_hidden',
				'_xswphp_redirect_page',
				'_xswphp_front_page',
				'_xswphp_category_page',
				'_xswphp_tag_page',
				'_xswphp_author',
				'_xswphp_archive',
				'_xswphp_search',
				'_xswphp_feeds',
				'_xswphp_recent',
				'_xswphp_rel_link',
				'_xswphp_rest_api',
			);
		}

		// Handle regular save.
		foreach ( $meta_key as $mkey ) {
			// If checkbox is checked, save the value (sanitized).
			if ( isset( $_POST['xswphp'][ $mkey ] ) ) {
				$value = sanitize_text_field( wp_unslash( $_POST['xswphp'][ $mkey ] ) );
				update_post_meta( $post_id, $mkey, $value );
				$this->save_to_custom_table( $post_id, $mkey, $value );
			} else {
				// If checkbox is NOT checked, clear the value (important!).
				update_post_meta( $post_id, $mkey, '' );
				$this->remove_from_custom_table( $post_id, $mkey );
			}
		}
		XSWPHP_Database::clear_post_cache( $post_id, $post->post_type );
	}
	/**
	 * Add the New Column in Post type
	 *
	 * @param array $defaults Default columns.
	 * @return array $defaults
	 */
	public function xswphp_col_hidden( $defaults ) {
		$before = 'date';
		unset( $defaults['date'] );
		$defaults['xswphp_col_hidden'] = esc_html__( 'Hidden', 'wp-post-hide' );
		$defaults['date']              = esc_html__( 'Date', 'wp-post-hide' );
		return $defaults;
	}

	/**
	 * Add Content in Custom Column
	 *
	 * @param  string $column_name Name of columns.
	 * @param  int    $post_ID Id of post.
	 */
	public function xswphp_col_content( $column_name, $post_ID ) {
		if ( 'xswphp_col_hidden' === $column_name ) {
			$xswphp_output = xswphp_meta_data( $post_ID );
			$count         = 0;
			if ( isset( $xswphp_output ) && ! empty( $xswphp_output ) ) {
				echo '<div id ="xswphp-' . esc_html( $post_ID ) . '">';
				foreach ( $xswphp_output as $mvalue ) {
					if ( '' === $mvalue || 'All Hidden' === $mvalue ) {
						continue;
					}
					echo ( $count > 0 ) ? ' , ' : '';
					echo esc_html( $mvalue );
					++$count;
				}
				echo '</div>';
			}
		}
	}

	/**
	 * Add custom field of filter in post type table.
	 *
	 * @param string $post_type  Name of post type.
	 */
	public function xswphp_field_filter( $post_type ) {
		$posts_types = get_post_types(
			array(
				'show_ui'      => true,
				'show_in_menu' => true,
				'public'       => true,
			),
			'objects'
		);
		if ( isset( $posts_types ) && ! empty( $posts_types ) ) {
			foreach ( $posts_types as $val ) {
				if ( 'attachment' === $val->name ) {
					continue;
				}
				if ( $val->name === $post_type ) {
					include_once XSWPHP_ABSPATH . '/templates/views/xswphp-filter.php';
				}
			}
		}
	}
	/**
	 * Method For front end hidden
	 *
	 * @param object $query Object of pre post query.
	 */
	public function xswphp_hidden_posts_pages( $query ) {
		$font_page_ids       = array();
		$blog_page_ids       = array();
		$always_ids          = array();
		$keep_search_ids     = array();
		$recent_post_ids     = array();
		$next_prev_post_ids  = array();
		$redirect_single_ids = array();
		if ( is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			return;
		}

		// --- Global lists ---
		$always_ids          = (array) $this->xswphp_database_query( '_xswphp_always', 'Hide form pages list everywhere' );
		$keep_search_ids     = (array) $this->xswphp_database_query( '_xswphp_keep_search', 'Hide but keep in Search' );
		$recent_widget_ids   = (array) $this->xswphp_database_query( '_xswphp_recent', 'Recent Posts' );
		$adjacent_ids        = (array) $this->xswphp_database_query( '_xswphp_rel_link', 'Next Prev' );
		$redirect_single_ids = (array) $this->xswphp_database_query( '_xswphp_redirect_page', 'Redirect Page' );
		$front_page_ids      = (array) $this->xswphp_database_query( '_xswphp_front_page', 'Front Page' );
		$front_page_id       = (int) get_option( 'page_on_front' );
		$posts_page_id       = (int) get_option( 'page_for_posts' );
		// Try to get queried object ID as fallback if 'page_id' not set.
		$current_id = (int) ( $query->get( 'page_id' ) ? '' : get_queried_object_id() );
		// ✅ Detect front page or blog page correctly in all setups.
		$is_front = ( ( $front_page_id && $current_id === $front_page_id ) || ( get_option( 'show_on_front' ) === 'posts' && $query->is_home() ) || ( ! $front_page_id && $query->is_front_page() ) );
		$is_blog  = ( ( $posts_page_id && $current_id === $posts_page_id ) || ( get_option( 'show_on_front' ) === 'posts' && $query->is_home() ) );
		// -----------------------
		// Non-main queries (widgets, sidebars, shortcodes)
		// -----------------------
		if ( ! $query->is_main_query() ) {
			$post_type = $query->get( 'post_type' );
			if ( empty( $post_type ) || in_array( $post_type, array( 'post', 'page', 'product' ), true ) ) {
				$hide_for_widgets = array_unique( array_merge( $always_ids, $keep_search_ids, $recent_widget_ids ) );
				if ( ! empty( $hide_for_widgets ) ) {
					$existing_not_in = $query->get( 'post__not_in' );
					if ( ! is_array( $existing_not_in ) ) {
						$existing_not_in = array();
					}
					$query->set( 'post__not_in', array_unique( array_merge( $existing_not_in, $hide_for_widgets ) ) );
				}
				if ( ! empty( $front_page_ids ) ) {
					if ( $is_front || $is_blog || ( function_exists( 'is_shop' ) && is_shop() ) ) {
						$query->set( 'post__not_in', array_unique( array_merge( $existing_not_in, $front_page_ids, $always_ids, $keep_search_ids ) ) );
					}
				}
			}
			return;
		}

		// -----------------------
		// Main query logic (home, archives, shop, etc.)
		// -----------------------
		if ( $query->is_main_query() ) {
			// ✅ Final condition: front, blog, or shop.
			if ( $is_front || $is_blog || ( function_exists( 'is_shop' ) && is_shop() ) ) {
				$hide_ids = array_unique( array_merge( $front_page_ids, $always_ids, $keep_search_ids ) );
				$this->merge_not_in( $query, $hide_ids );
				$query->set( 'post__not_in', $front_page_ids );
			}
			// SEARCH.
			if ( $query->is_search() ) {
				$search_ids = (array) $this->xswphp_database_query( '_xswphp_search', 'Search Results' );
				$hide_ids   = array_unique( array_diff( array_merge( $search_ids, $always_ids ), $keep_search_ids ) );
				$this->merge_not_in( $query, $hide_ids );
			}

			// CATEGORY / TAG / AUTHOR / DATE / FEEDS.
			$mapping = array(
				'is_category' => '_xswphp_category_page',
				'is_tag'      => '_xswphp_tag_page',
				'is_author'   => '_xswphp_author',
				'is_date'     => '_xswphp_archive',
				'is_feed'     => '_xswphp_feeds',
			);

			foreach ( $mapping as $condition => $meta_key ) {
				if ( call_user_func( $condition ) ) {
					$page_ids = (array) $this->xswphp_database_query( $meta_key, ucfirst( str_replace( '_', ' ', $meta_key ) ) );
					$hide_ids = array_unique( array_merge( $page_ids, $always_ids, $keep_search_ids ) );
					$this->merge_not_in( $query, $hide_ids );
				}
			}

			// WooCommerce Product Category.
			if ( $query->is_tax( 'product_cat' ) ) {
				$wc_cat_ids = (array) $this->xswphp_database_query( '_xswphp_category_page', 'WC Product Category' );
				$hide_ids   = array_unique( array_merge( $wc_cat_ids, $always_ids, $keep_search_ids ) );
				$this->merge_not_in( $query, $hide_ids );
			}
		}
	}

	/**
	 * Small helper to merge post__not_in arrays safely.
	 *
	 * @param object $query Query of post.
	 * @param array  $new_ids IDs of new post.
	 */
	private function merge_not_in( $query, $new_ids ) {
		if ( empty( $new_ids ) ) {
			return;
		}
		$existing = $query->get( 'post__not_in' );
		if ( ! is_array( $existing ) ) {
			$existing = array();
		}
		$query->set( 'post__not_in', array_unique( array_merge( $existing, $new_ids ) ) );
	}

	/**
	 * Handle single post, product, page, or attachment redirect to 404, and exclude adjacent posts.
	 *
	 * This method performs two main functions:
	 * 1. Redirects hidden single items (posts/products/pages/attachments) to 404
	 * 2. Excludes hidden items from next/previous navigation links
	 */
	public function xswphp_hide_singular_and_adjacent() {
		// Skip if in admin or REST API request.
		if ( is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
			return;
		}

		global $post;

		// --- Redirect single post, product, page, or attachment to 404 ---
		// Get all items marked for redirect (sanitized with absint)
		$redirect_ids = array_map( 'absint', (array) $this->xswphp_database_query( '_xswphp_redirect_page', 'Redirect Page' ) );

		// Check if current page is a singular item (post, product, page, or attachment).
		if ( is_singular( array( 'post', 'product', 'page' ) ) && isset( $post ) && is_object( $post ) ) {
			$post_id = absint( $post->ID );

			// If this item is marked for redirect, show 404.
			if ( in_array( $post_id, $redirect_ids, true ) ) {
				global $wp_query;
				$wp_query->set_404();
				status_header( 404 );
				nocache_headers();
				include get_query_template( '404' );
				exit;
			}
		}

		// --- Exclude hidden items from next/previous navigation links ---
		// Get items marked to hide from next/prev links and always hidden items.
		$adjacent_ids = array_map( 'absint', (array) $this->xswphp_database_query( '_xswphp_rel_link', 'Next Prev' ) );
		$always_ids   = array_map( 'absint', (array) $this->xswphp_database_query( '_xswphp_always', 'Hide form pages list everywhere' ) );

		// Combine and remove duplicates.
		$exclude = array_unique( array_merge( $adjacent_ids, $always_ids ) );

		// Apply exclusion filters if there are IDs to exclude.
		if ( ! empty( $exclude ) ) {
			$this->xswphp_adjacent_exclude_ids = $exclude;
			add_filter( 'get_previous_post_where', array( $this, 'xswphp_exclude_adjacent_where' ) );
			add_filter( 'get_next_post_where', array( $this, 'xswphp_exclude_adjacent_where' ) );
		}
	}

	/**
	 * Filter adjacent where-clause to exclude specific post IDs.
	 *
	 * Used for next/previous post navigation to skip hidden posts
	 *
	 * @param string $where SQL WHERE clause.
	 * @return string Modified WHERE clause
	 */
	public function xswphp_exclude_adjacent_where( $where ) {
		if ( empty( $this->xswphp_adjacent_exclude_ids ) ) {
			return $where;
		}

		// Sanitize all IDs with absint().
		$ids = array_map( 'absint', $this->xswphp_adjacent_exclude_ids );

		if ( ! empty( $ids ) ) {
			$where .= ' AND p.ID NOT IN (' . implode( ',', $ids ) . ')';
		}

		return $where;
	}

	/**
	 * Filter widget posts arguments to exclude hidden posts
	 *
	 * Applies to Recent Posts widget and other widgets that use widget_posts_args
	 * Excludes posts/pages/attachments marked as:
	 * - Always hidden
	 * - Hidden but keep in search
	 * - Hidden in recent posts
	 *
	 * @param array $args     Query arguments.
	 * @return array Modified query arguments
	 */
	public function xswphp_filter_widget_posts( $args ) {
		// Get all hidden IDs (sanitized with absint).
		$always_ids      = array_map( 'absint', (array) $this->xswphp_database_query( '_xswphp_always', 'Hide form pages list everywhere' ) );
		$keep_search_ids = array_map( 'absint', (array) $this->xswphp_database_query( '_xswphp_keep_search', 'Hide but keep in Search' ) );
		$recent_ids      = array_map( 'absint', (array) $this->xswphp_database_query( '_xswphp_recent', 'Recent Posts' ) );

		// Combine all hidden IDs and remove duplicates.
		$hide_ids = array_unique( array_merge( $always_ids, $keep_search_ids, $recent_ids ) );

		// Add to post__not_in if there are IDs to exclude.
		if ( ! empty( $hide_ids ) ) {
			$existing_not_in = isset( $args['post__not_in'] ) ? $args['post__not_in'] : array();
			if ( ! is_array( $existing_not_in ) ) {
				$existing_not_in = array();
			}
			// phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in -- exclusion list is small & safe
			$args['post__not_in'] = array_unique( array_merge( $existing_not_in, $hide_ids ) );
		}

		return $args;
	}
	/**
	 * Get The hidden Ids by meta key and meta Value
	 *
	 * @param String $meta_key Meta key of hidden id.
	 * @param string $meta_value Meta value of hidden key.
	 * @return array $ids
	 */
	public function xswphp_database_query( $meta_key, $meta_value ) {
		// Try to use the new database method if available.
		$condition = $this->get_condition_from_meta_key( $meta_key );
		if ( $condition && class_exists( 'XSWPHP_Database' ) ) {
			// Get all post types that might have this condition.
			$post_types = array( 'post', 'page', 'attachment' );

			// Add product if WooCommerce is active.
			if ( function_exists( 'is_product' ) ) {
				$post_types[] = 'product';
			}

			// Collect IDs from all post types.
			$all_ids = array();
			foreach ( $post_types as $post_type ) {
				$ids = XSWPHP_Database::get_hidden_posts_ids( $post_type, $condition, true );
				if ( ! empty( $ids ) && is_array( $ids ) ) {
					$all_ids = array_merge( $all_ids, $ids );
				}
			}

			return array_unique( $all_ids );
		}

		// Fallback to original method.
		global $wpdb;
		$post_ids = array();
		// phpcs:ignore WordPress.DB
		$xswphp_query = $wpdb->get_results( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = %s AND meta_value = %s", $meta_key, $meta_value ) );
		foreach ( $xswphp_query as $xswphp_val ) {
			$post_ids[] = $xswphp_val->post_id;
		}
		return $post_ids;
	}
	/**
	 * Send Feedback email.
	 **/
	public function xswphp_send_mail() {
		$data = array();
		if ( isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), '_xswphp_nonce' ) && isset( $_POST['data'] ) && ! empty( $_POST['data'] ) ) {
			parse_str( sanitize_text_field( wp_unslash( $_POST['data'] ) ), $data );
			$server              = isset( $_SERVER['HTTP_HOST'] ) ? sanitize_text_field( wp_unslash( $_SERVER['HTTP_HOST'] ) ) : '';
			$data['plugin_name'] = 'WP Post Hide';
			$data['version']     = 'Free Version';
			$data['website']     = ( isset( $_SERVER['HTTPS'] ) && 'on' === $_SERVER['HTTPS'] ? 'https' : 'http' ) . '://' . $server;
			$to                  = 'xfinitysoft@gmail.com';
			switch ( $data['type'] ) {
				case 'report':
					$subject = 'Report a bug';
					break;
				case 'hire':
					$subject = 'Hire us';
					break;

				default:
					$subject = 'Request a Feature';
					break;
			}

			$body            = '<html><body><table>';
			$body           .= '<tbody>';
			$body           .= '<tr><th>User Name</th><td>' . $data['xswphp_name'] . '</td></tr>';
			$body           .= '<tr><th>User email</th><td>' . $data['xswphp_email'] . '</td></tr>';
			$body           .= '<tr><th>Plugin Name</th><td>' . $data['plugin_name'] . '</td></tr>';
			$body           .= '<tr><th>Version</th><td>' . $data['version'] . '</td></tr>';
			$body           .= '<tr><th>Website</th><td><a href="' . $data['website'] . '">' . $data['website'] . '</a></td></tr>';
			$body           .= '<tr><th>Message</th><td>' . $data['xswphp_message'] . '</td></tr>';
			$body           .= '</tbody>';
			$body           .= '</table></body></html>';
			$admin_email     = get_option( 'admin_email' );
			$headers         = array( 'From: ' . $data['xswphp_name'] . ' <' . $admin_email . '>', 'Reply-To: ' . $data['xswphp_name'] . ' <' . $data['xswphp_email'] . '>', 'Content-Type: text/html; charset=UTF-8' );
			$params          = 'name=' . $data['xswphp_name'];
			$params         .= '&email=' . $data['xswphp_email'];
			$params         .= '&site=' . $data['website'];
			$params         .= '&version=' . $data['version'];
			$params         .= '&plugin_name=' . $data['plugin_name'];
			$params         .= '&type=' . $data['type'];
			$params         .= '&message=' . $data['xswphp_message'];
			$sever_response  = wp_remote_post( 'https://xfinitysoft.com/wp-json/plugin/v1/quote/save/?' . $params );
			$se_api_response = json_decode( wp_remote_retrieve_body( $sever_response ), true );

			if ( $se_api_response['status'] ) {
				$mail = wp_mail( $to, $subject, $body, $headers );
				wp_send_json( array( 'status' => true ) );
			} else {
				wp_send_json( array( 'status' => false ) );
			}
		} else {
			wp_send_json( array( 'status' => false ) );
		}
		wp_die();
	}

	/**
	 * Create database tables
	 */
	public function create_database_tables() {
		XSWPHP_Database::create_tables();
	}

	/**
	 * Save to custom table
	 *
	 * @param int    $post_id Id of post.
	 * @param string $meta_key Meta key of post.
	 * @param string $meta_value Meta value of post.
	 */
	private function save_to_custom_table( $post_id, $meta_key, $meta_value ) {
		if ( empty( $meta_value ) ) {
			return;
		}

		$condition = $this->get_condition_from_meta_key( $meta_key );
		if ( $condition ) {
			XSWPHP_Database::add_hide_condition( $post_id, $condition );
		}
	}

	/**
	 * Remove from custom table
	 *
	 * @param int    $post_id Id of post.
	 * @param string $meta_key Meta keyof post.
	 */
	private function remove_from_custom_table( $post_id, $meta_key ) {
		$condition = $this->get_condition_from_meta_key( $meta_key );
		if ( $condition ) {
			XSWPHP_Database::remove_hide_condition( $post_id, $condition );
		}
	}

	/**
	 * Get condition from meta key
	 *
	 * @param string $meta_key Meta key of post.
	 * @return string|false
	 */
	private function get_condition_from_meta_key( $meta_key ) {
		$mapping = array(
			'_xswphp_front_page'    => 'front_page',
			'_xswphp_category_page' => 'category_page',
			'_xswphp_tag_page'      => 'tag_page',
			'_xswphp_author'        => 'author',
			'_xswphp_archive'       => 'archive',
			'_xswphp_search'        => 'search',
			'_xswphp_feeds'         => 'feeds',
			'_xswphp_recent'        => 'recent',
			'_xswphp_rel_link'      => 'rel_link',
			'_xswphp_redirect_page' => 'redirect_page',
			'_xswphp_always'        => 'always',
			'_xswphp_keep_search'   => 'keep_search',
			'_xswphp_rest_api'      => 'rest_api',
		);

		return isset( $mapping[ $meta_key ] ) ? $mapping[ $meta_key ] : false;
	}

	/**
	 * Hide from REST API.
	 *
	 * @param array $args Array of Rest api.
	 */
	public function hide_from_rest_api( $args ) {
		if ( ! isset( $args['post_type'] ) ) {
			return $args;
		}

		$post_type          = is_array( $args['post_type'] ) ? $args['post_type'][0] : $args['post_type'];
		$hidden_ids         = XSWPHP_Database::get_hidden_posts_ids( $post_type, 'rest_api', true );
		$always_hidden      = XSWPHP_Database::get_hidden_posts_ids( $post_type, 'always', true );
		$keep_search_hidden = XSWPHP_Database::get_hidden_posts_ids( $post_type, 'keep_search', true );

		$all_hidden = array_unique( array_merge( $hidden_ids, $always_hidden, $keep_search_hidden ) );

		if ( ! empty( $all_hidden ) ) {
			$existing_not_in = isset( $args['post__not_in'] ) ? $args['post__not_in'] : array();
			if ( ! is_array( $existing_not_in ) ) {
				$existing_not_in = array();
			}
			// phpcs:ignore WordPressVIPMinimum.Performance.WPQueryParams.PostNotIn_post__not_in -- exclusion list is small & safe
			$args['post__not_in'] = array_unique( array_merge( $existing_not_in, $all_hidden ) );
		}

		return $args;
	}
}
