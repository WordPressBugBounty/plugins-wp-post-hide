<?php
/**
 * WooCommerce Integration for WP Post Hide.
 *
 * @package WP Post Hide.
 */

// Exit if directly access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WooCommerce integration class.
 */
class XSWPPH_WooCommerce {

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
		add_filter( 'woocommerce_rest_product_object_query', array( $this, 'hide_from_rest_api' ), 10, 2 );
		add_filter( 'woocommerce_rest_product_query', array( $this, 'hide_from_rest_api' ), 10, 2 );
	}

	/**
	 * Check if WooCommerce is active.
	 *
	 * @return bool
	 */
	public function is_woocommerce_active() {
		return class_exists( 'WooCommerce' ) && function_exists( 'is_shop' );
	}

	/**
	 * Check if current post is a product.
	 *
	 * @param int $post_id Id of post.
	 * @return bool
	 */
	public function is_product( $post_id = null ) {
		if ( ! $post_id ) {
			global $post;
			$post_id = $post ? $post->ID : 0;
		}
		return 'product' === get_post_type( $post_id );
	}

	/**
	 * Hide products from WooCommerce pages
	 *
	 * @param WP_Query $query WordPress query.
	 */
	public function hide_products( $query ) {
		if ( is_admin() || ! $query->is_main_query() ) {
			return;
		}

		$hidden_products = array();

		if ( is_shop() ) {
			$shop_hidden     = XSWPPH_Database::get_hidden_posts_ids( 'product', 'wc_shop', true );
			$hidden_products = $shop_hidden;
		}

		if ( is_product_category() || is_product_tag() ) {
			$category_hidden = XSWPPH_Database::get_hidden_posts_ids( 'product', 'wc_product_category', true );
			$hidden_products = $category_hidden;
		}

		if ( is_search() && $query->is_search() ) {
			$search_hidden   = XSWPPH_Database::get_hidden_posts_ids( 'product', 'search', true );
			$hidden_products = $search_hidden;
		}

		if ( ! empty( $hidden_products ) ) {
			$existing_not_in = $query->get( 'post__not_in' );
			if ( ! is_array( $existing_not_in ) ) {
				$existing_not_in = array();
			}
			$query->set( 'post__not_in', array_unique( array_merge( $existing_not_in, $hidden_products ) ) );
		}
	}

	/**
	 * Hide from REST API
	 *
	 * @param array $args Params of request.
	 * @return array
	 */
	public function hide_from_rest_api( $args ) {
		if ( ! isset( $args['post_type'] ) || 'product' !== $args['post_type'] ) {
			return $args;
		}

		$hidden_ids = XSWPPH_Database::get_hidden_posts_ids( 'product', 'rest_api', true );

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
	 * Get WooCommerce specific meta keys for pages
	 *
	 * @return array
	 */
	public static function get_woocommerce_meta_keys() {
		return array(
			'_xswpph_wc_shop'             => 'Hide from Shop Page',
			'_xswpph_wc_product_category' => 'Hide from Product Categories',
		);
	}

	/**
	 * Get WooCommerce conditions for database.
	 *
	 * @return array
	 */
	public static function get_woocommerce_conditions() {
		return array(
			'wc_shop'             => 'Hide from Shop Page',
			'wc_product_category' => 'Hide from Product Categories',
		);
	}
}
