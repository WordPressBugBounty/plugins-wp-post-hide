<?php
/**
 * Functions of WordPress Hide Post.
 *
 * @link              http://xfinitysoft.com/
 * @since             0.0.1
 * @package WordPress Hide Post
 */

// Exit if directly access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


/**
 * Check options table value exit or not
 *
 * @param string $xswpph_value Value to check.
 * @param string $xswpph_name Option name.
 */
function xswpph_check_options( $xswpph_value, $xswpph_name ) {
	$xswpph_options = get_option( $xswpph_name );
	if ( isset( $xswpph_options ) && is_array( $xswpph_options ) ) {
		if ( in_array( $xswpph_value, $xswpph_options, true ) ) {
			echo 'checked';
		}
	}
}

/**
 * Get The Meta data from Post meta
 *
 * @param int $post_id Post ID.
 * @return array $meta_value Meta values array.
 */
function xswpph_meta_data( $post_id ) {
	$meta_value = array();
	$post_type  = get_post_type( $post_id );

	if ( 'page' === $post_type ) {
		$meta_key = array(
			'_xswpph_all_hidden'       => 'All Hidden',
			'_xswpph_front_page'       => 'Front Page',
			'_xswpph_blog_page'        => 'Blog Page',
			'_xswpph_rest_api'         => 'REST API',
			'_xswpph_single_post_page' => 'Single Post Page',
		);
	} elseif ( 'attachment' === $post_type ) {
		$meta_key = array(
			'_xswpph_all_hidden'    => 'All Hidden',
			'_xswpph_front_page'    => 'Front Page',
			'_xswpph_blog_page'     => 'Blog Page',
			'_xswpph_category_page' => 'Category Pages',
			'_xswpph_tag_page'      => 'Tag Pages',
			'_xswpph_author'        => 'Author Pages',
			'_xswpph_archive'       => 'Date Archive',
			'_xswpph_search'        => 'Search Results',
			'_xswpph_feeds'         => 'Feeds',
			'_xswpph_recent'        => 'Recent Post',
			'_xswpph_rel_link'      => 'Meta rel link',
			'_xswpph_rest_api'      => 'REST API',
		);
	} elseif ( 'product' === $post_type && class_exists( 'WooCommerce' ) ) {
		$meta_key = array(
			'_xswpph_all_hidden'          => 'All Hidden',
			'_xswpph_front_page'          => 'Front Page',
			'_xswpph_blog_page'           => 'Blog Page',
			'_xswpph_category_page'       => 'Category Pages',
			'_xswpph_tag_page'            => 'Tag Pages',
			'_xswpph_author'              => 'Author Pages',
			'_xswpph_archive'             => 'Date Archive',
			'_xswpph_search'              => 'Search Results',
			'_xswpph_feeds'               => 'Feeds',
			'_xswpph_recent'              => 'Recent Post',
			'_xswpph_rel_link'            => 'Meta rel link',
			'_xswpph_rest_api'            => 'REST API',
			'_xswpph_single_post_page'    => 'Single Post Page',
			'_xswpph_wc_shop'             => 'WC Shop Page',
			'_xswpph_wc_product_category' => 'WC Product Category',
		);
	} else {
		$meta_key = array(
			'_xswpph_all_hidden'       => 'All Hidden',
			'_xswpph_front_page'       => 'Front Page',
			'_xswpph_blog_page'        => 'Blog Page',
			'_xswpph_category_page'    => 'Category Pages',
			'_xswpph_tag_page'         => 'Tag Pages',
			'_xswpph_author'           => 'Author Pages',
			'_xswpph_archive'          => 'Date Archive',
			'_xswpph_search'           => 'Search Results',
			'_xswpph_feeds'            => 'Feeds',
			'_xswpph_recent'           => 'Recent Post',
			'_xswpph_rel_link'         => 'Meta rel link',
			'_xswpph_rest_api'         => 'REST API',
			'_xswpph_single_post_page' => 'Single Post Page',
		);
	}

	foreach ( $meta_key as $mkey => $label ) {
		$meta_val = get_post_meta( $post_id, $mkey, true );
		if ( ! empty( $meta_val ) ) {
			$meta_value[ $mkey ] = $label;
		}
	}
	return array_unique( $meta_value );
}
/**
 * Setting link plugin
 *
 * @param array $xswpph_link Plugin action links.
 * @return array $xswpph_link Modified action links.
 */
function xswpph_plugin_link( $xswpph_link ) {
	$setting_link = '<a href="admin.php?page=xswpph_page">Setting</a>';
	array_unshift( $xswpph_link, $setting_link );
	return $xswpph_link;
}

/**
 * Add the Column in Posts types table
 *
 * @return void
 */
function xswpph_new_custom_col() {
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

	$xswpph_init = new XSWPPH_Init();

	// Check if hidden column is disabled.
	$disable_hidden_column = get_option( 'xswpph_disable_hidden_column', '' );

	if ( 'yes' === $disable_hidden_column ) {
		return; // Don't add columns if disabled.
	}

	if ( isset( $posts_types ) && ! empty( $posts_types ) ) {
		foreach ( $posts_types as $val ) {
			if ( 'page' === $val->name ) {
				add_filter( 'manage_page_posts_columns', array( $xswpph_init, 'xswpph_col_hidden' ), 10 );
				add_action( 'manage_page_posts_custom_column', array( $xswpph_init, 'xswpph_col_content' ), 10, 2 );
			} elseif ( 'product' === $val->name ) {
				add_filter( 'manage_edit-product_columns', array( $xswpph_init, 'xswpph_col_hidden' ), 10 );
				add_action( 'manage_product_posts_custom_column', array( $xswpph_init, 'xswpph_col_content' ), 10, 2 );
			} elseif ( 'attachment' === $val->name ) {
				add_filter( 'manage_media_columns', array( $xswpph_init, 'xswpph_col_hidden' ), 10 );
				add_action( 'manage_media_custom_column', array( $xswpph_init, 'xswpph_col_content' ), 10, 2 );
			} else {
				add_filter( 'manage_' . esc_html( $val->name ) . '_posts_columns', array( $xswpph_init, 'xswpph_col_hidden' ), 10 );
				add_action( 'manage_' . esc_html( $val->name ) . '_posts_custom_column', array( $xswpph_init, 'xswpph_col_content' ), 10, 2 );
			}
		}
	}
}
