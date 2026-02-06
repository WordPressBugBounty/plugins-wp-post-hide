<?php
/**
 * WooCommerce Integration for XS WP Post Hide
 *
 * @package WP Post Hide
 */

// Exit if directly access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce integration class
 */
class XSWPHP_WooCommerce {

	/**
	 * Constructor
	 */
	public function __construct() {
		if ( $this->is_woocommerce_active() ) {
			add_action( 'init', array( $this, 'init_hooks' ) );
		}
	}

	/**
	 * Initialize hooks
	 */
	public function init_hooks() {
		// Add WooCommerce specific hiding.
		add_action( 'pre_get_posts', array( $this, 'hide_products' ), 99 );

		// Add REST API filters for WooCommerce.
		add_filter( 'woocommerce_rest_product_object_query', array( $this, 'hide_from_wc_rest_api' ), 10, 1 );
		add_filter( 'woocommerce_rest_product_query', array( $this, 'hide_from_wc_rest_api' ), 10, 1 );

		// Standard WordPress REST API filter for products.
		add_filter( 'rest_product_query', array( $this, 'hide_from_rest_api' ), 10, 1 );

		// Handle product redirects to 404.
		add_action( 'template_redirect', array( $this, 'handle_product_redirect' ), 10 );

		// Handle adjacent product links (next/previous).
		add_filter( 'woocommerce_get_previous_product_excluded_ids', array( $this, 'exclude_products_from_adjacent' ), 10, 1 );
		add_filter( 'woocommerce_get_next_product_excluded_ids', array( $this, 'exclude_products_from_adjacent' ), 10, 1 );
	}

	/**
	 * Check if WooCommerce is active
	 *
	 * @return bool
	 */
	public function is_woocommerce_active() {
		return class_exists( 'WooCommerce' ) && function_exists( 'is_shop' );
	}

	/**
	 * Check if current post is a product
	 *
	 * @param int $post_id Id of post.
	 * @return bool
	 */
	public function is_product( $post_id = null ) {
		if ( ! $post_id ) {
			global $post;
			$post_id = $post ? $post->ID : 0;
		}
		return get_post_type( $post_id ) === 'product';
	}

	/**
	 * Hide products from WooCommerce pages
	 *
	 * @param WP_Query $query Query of product.
	 */
	public function hide_products( $query ) {
		// Handle REST API requests separately.
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			// Check if this is a product query.
			$post_type = $query->get( 'post_type' );
			if ( 'product' === $post_type || ( is_array( $post_type ) && in_array( 'product', $post_type, true ) ) ) {
				$hidden_ids         = XSWPHP_Database::get_hidden_posts_ids( 'product', 'rest_api', true );
				$always_hidden      = XSWPHP_Database::get_hidden_posts_ids( 'product', 'always', true );
				$keep_search_hidden = XSWPHP_Database::get_hidden_posts_ids( 'product', 'keep_search', true );

				$all_hidden = array_unique( array_merge( $hidden_ids, $always_hidden, $keep_search_hidden ) );

				if ( ! empty( $all_hidden ) ) {
					$existing_not_in = $query->get( 'post__not_in' );
					if ( ! is_array( $existing_not_in ) ) {
						$existing_not_in = array();
					}
					$query->set( 'post__not_in', array_unique( array_merge( $existing_not_in, $all_hidden ) ) );
				}
			}
			return;
		}

		// Skip admin and AJAX.
		if ( is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
			return;
		}

		// Only process main query.
		if ( ! $query->is_main_query() ) {
			return;
		}

		$hidden_products    = array();
		$always_hidden      = XSWPHP_Database::get_hidden_posts_ids( 'product', 'always', true );
		$keep_search_hidden = XSWPHP_Database::get_hidden_posts_ids( 'product', 'keep_search', true );

		// Shop Page (uses front_page condition).
		if ( is_shop() ) {
			$shop_hidden     = XSWPHP_Database::get_hidden_posts_ids( 'product', 'front_page', true );
			$hidden_products = array_unique( array_merge( $shop_hidden, $always_hidden, $keep_search_hidden ) );
		}

		// Product Category Pages (uses category_page condition).
		if ( is_product_category() ) {
			$category_hidden = XSWPHP_Database::get_hidden_posts_ids( 'product', 'category_page', true );
			$hidden_products = array_unique( array_merge( $category_hidden, $always_hidden, $keep_search_hidden ) );
		}

		// Product Tag Pages (uses tag_page condition).
		if ( is_product_tag() ) {
			$tag_hidden      = XSWPHP_Database::get_hidden_posts_ids( 'product', 'tag_page', true );
			$hidden_products = array_unique( array_merge( $tag_hidden, $always_hidden, $keep_search_hidden ) );
		}

		// Product Archives (uses archive condition).
		if ( is_post_type_archive( 'product' ) && ! is_shop() ) {
			$archive_hidden  = XSWPHP_Database::get_hidden_posts_ids( 'product', 'archive', true );
			$hidden_products = array_unique( array_merge( $archive_hidden, $always_hidden, $keep_search_hidden ) );
		}

		// Search Results.
		if ( is_search() && $query->is_search() ) {
			$search_hidden   = XSWPHP_Database::get_hidden_posts_ids( 'product', 'search', true );
			$merged_hidden   = array_merge( $search_hidden, $always_hidden );
			$hidden_products = array_diff( $merged_hidden, $keep_search_hidden );
		}

		// Author Pages.
		if ( is_author() ) {
			$author_hidden   = XSWPHP_Database::get_hidden_posts_ids( 'product', 'author', true );
			$hidden_products = array_unique( array_merge( $author_hidden, $always_hidden, $keep_search_hidden ) );
		}

		// Feed Pages.
		if ( is_feed() ) {
			$feed_hidden     = XSWPHP_Database::get_hidden_posts_ids( 'product', 'feeds', true );
			$hidden_products = array_unique( array_merge( $feed_hidden, $always_hidden, $keep_search_hidden ) );
		}

		// Apply the exclusions.
		if ( ! empty( $hidden_products ) ) {
			$existing_not_in = $query->get( 'post__not_in' );
			if ( ! is_array( $existing_not_in ) ) {
				$existing_not_in = array();
			}
			$query->set( 'post__not_in', array_unique( array_merge( $existing_not_in, $hidden_products ) ) );
		}
	}

	/**
	 * Hide from WooCommerce REST API
	 *
	 * @param array $args Arg of api.
	 * @return array
	 */
	public function hide_from_wc_rest_api( $args ) {
		$hidden_ids         = XSWPHP_Database::get_hidden_posts_ids( 'product', 'rest_api', true );
		$always_hidden      = XSWPHP_Database::get_hidden_posts_ids( 'product', 'always', true );
		$keep_search_hidden = XSWPHP_Database::get_hidden_posts_ids( 'product', 'keep_search', true );

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

	/**
	 * Hide from standard REST API
	 *
	 * @param array $args arg of api.
	 * @return array
	 */
	public function hide_from_rest_api( $args ) {
		// Ensure this is for products.
		if ( isset( $args['post_type'] ) && is_array( $args['post_type'] ) ) {
			if ( ! in_array( 'product', $args['post_type'], true ) ) {
				return $args;
			}
		} elseif ( isset( $args['post_type'] ) && 'product' !== $args['post_type'] ) {
			return $args;
		}

		$hidden_ids         = XSWPHP_Database::get_hidden_posts_ids( 'product', 'rest_api', true );
		$always_hidden      = XSWPHP_Database::get_hidden_posts_ids( 'product', 'always', true );
		$keep_search_hidden = XSWPHP_Database::get_hidden_posts_ids( 'product', 'keep_search', true );

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

	/**
	 * Handle product redirect to 404
	 */
	public function handle_product_redirect() {
		if ( is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
			return;
		}

		global $post;

		if ( is_singular( 'product' ) && isset( $post ) && is_object( $post ) ) {
			$redirect_ids = XSWPHP_Database::get_hidden_posts_ids( 'product', 'redirect_page', true );
			$post_id      = strval( $post->ID );

			if ( in_array( $post_id, $redirect_ids, true ) ) {
				global $wp_query;
				$wp_query->set_404();
				status_header( 404 );
				nocache_headers();
				include get_query_template( '404' );
				exit;
			}
		}
	}

	/**
	 * Exclude products from adjacent links (next/previous)
	 *
	 * @param array $excluded_ids Id of excluded.
	 * @return array
	 */
	public function exclude_products_from_adjacent( $excluded_ids ) {
		if ( ! is_array( $excluded_ids ) ) {
			$excluded_ids = array();
		}

		$adjacent_ids = XSWPHP_Database::get_hidden_posts_ids( 'product', 'rel_link', true );
		$always_ids   = XSWPHP_Database::get_hidden_posts_ids( 'product', 'always', true );

		$exclude = array_unique( array_merge( $adjacent_ids, $always_ids ) );

		if ( ! empty( $exclude ) ) {
			$excluded_ids = array_unique( array_merge( $excluded_ids, $exclude ) );
		}

		return $excluded_ids;
	}
}
