<?php
/**
 * WP Post Hide Functions
 *
 * @package WP Post Hide Setup
 * @since 1.0.0
 */

// Exit if directly access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get all post type.
 */
function xswphp_getpost_types() {
	$xswphp_posts = get_post_types(
		array(
			'show_ui'      => true,
			'show_in_menu' => true,
			'public'       => true,
		),
		'objects'
	);

	// Also include attachment post type.
	$attachment_post_type = get_post_type_object( 'attachment' );
	if ( $attachment_post_type ) {
		$xswphp_posts['attachment'] = $attachment_post_type;
	}

	$xswphp_posts_arr = array();

	foreach ( $xswphp_posts as $xswphp_keys ) {

		$xswphp_posts_arr[ $xswphp_keys->name ] = $xswphp_keys;

	}

	if ( ! empty( $xswphp_posts_arr ) ) {

		ksort( $xswphp_posts_arr );

		return $xswphp_posts_arr;

	} else {

		return false;

	}
}



/**
 * Create new Custom column
 */
function xswphp_new_custom_col() {

	$posts_types = get_post_types(
		array(
			'show_ui'      => true,
			'show_in_menu' => true,
			'public'       => true,
		),
		'objects'
	);

	// Also include attachment post type.
	$attachment_post_type = get_post_type_object( 'attachment' );
	if ( $attachment_post_type ) {
		$posts_types['attachment'] = $attachment_post_type;
	}

	$xswphp_init = new XSWPHP_Init();

	// Check if hidden column is disabled.
	$xswphp_enable         = get_option( 'xswphp_enable', array() );
	$disable_hidden_column = isset( $xswphp_enable['disable_hidden_column'] ) && $xswphp_enable['disable_hidden_column'];

	if ( $disable_hidden_column ) {
		return; // Don't add columns if disabled.
	}

	if ( isset( $posts_types ) && ! empty( $posts_types ) ) {

		foreach ( $posts_types as $val ) {
			if ( 'page' === $val->name ) {
				add_filter( 'manage_page_posts_columns', array( $xswphp_init, 'xswphp_col_hidden' ), 10 );
				add_action( 'manage_page_posts_custom_column', array( $xswphp_init, 'xswphp_col_content' ), 10, 2 );
			} elseif ( 'product' === $val->name ) {
				add_filter( 'manage_edit-product_columns', array( $xswphp_init, 'xswphp_col_hidden' ), 10 );
				add_action( 'manage_product_posts_custom_column', array( $xswphp_init, 'xswphp_col_content' ), 10, 2 );
			} else {
				add_filter( 'manage_' . esc_html( $val->name ) . '_posts_columns', array( $xswphp_init, 'xswphp_col_hidden' ), 10 );
				add_action( 'manage_' . esc_html( $val->name ) . '_posts_custom_column', array( $xswphp_init, 'xswphp_col_content' ), 10, 2 );

			}
		}
	}
}



/**
 * Check options table value exit or not
 *
 * @param string $xswphp_value Value of hidden.
 * @param string $xswphp_name Name of hidden checkbox.
 */
function xswphp_check_options( $xswphp_value, $xswphp_name ) {
	if ( 'attachment' === $xswphp_value ) {
		return;
	}
	$xswphp_options = get_option( $xswphp_name );
	if ( isset( $xswphp_options ) && is_array( $xswphp_options ) ) {
		if ( in_array( $xswphp_value, $xswphp_options, true ) ) {
			echo 'checked';
		}
	}
}

/**
 * Get The Meta data from Post meta
 *
 * @param int $post_id Id of post.
 * @return array $meta_value;
 */
function xswphp_meta_data( $post_id ) {
	$meta_value = array();
	if ( 'page' === get_post_type( $post_id ) ) {
		$meta_key = array(
			'_xswphp_all_hidden',
			'_xswphp_redirect_page',
			'_xswphp_front_page',
			'_xswphp_blog_page',
			'_xswphp_always',
			'_xswphp_keep_search',
			'_xswphp_rest_api',
			'_xswphp_single_post_page',

		);

	} else {

		$meta_key = array(
			'_xswphp_all_hidden',
			'_xswphp_redirect_page',
			'_xswphp_front_page',
			'_xswphp_blog_page',
			'_xswphp_category_page',
			'_xswphp_tag_page',
			'_xswphp_author',
			'_xswphp_archive',
			'_xswphp_search',
			'_xswphp_feeds',
			'_xswphp_recent',
			'_xswphp_rel_link',
			'_xswphp_rest_api',
			'_xswphp_single_post_page',

		);
	}

	foreach ( $meta_key as $mkey ) {

		$meta_value[ $mkey ] = get_post_meta( $post_id, $mkey, true );

	}

	return array_unique( $meta_value );
}

/**
 * Setting link plugin
 *
 * @param array $xswphp_link Link of Settings.
 * @return array $xswphp_link
 */
function xswphp_plugin_link( $xswphp_link ) {

	$setting_link = '<a href="admin.php?page=xswphp_page">Setting</a>';
	$pro_version  = '<a href="https://codecanyon.net/item/wordpress-hide-post/24141817" target="_blank"><span style="font-size: 15px; margin-right: 2px;"><img style="padding-top:3px !important; " draggable="false" role="img" class="emoji" alt="🚀" src="' . esc_url( XSWPHP_ROOT_URL ) . '/assets/img/rocket.svg"></span>' . esc_html__( 'Get Pro', 'wp-post-hide' ) . '</a>';
	array_unshift( $xswphp_link, $setting_link, $pro_version );

	return $xswphp_link;
}
