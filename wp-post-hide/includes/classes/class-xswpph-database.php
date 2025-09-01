<?php
/**
 * Database handling for WP Post Hide.
 *
 * @package WP Post Hide
 */

// Exit if directly access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Database management class.
 */
class XSWPPH_Database {

	/**
	 * Create database tables.
	 *
	 * @return void
	 */
	public static function create_tables() {
		$current_db_version = 1;
		$db_version         = get_option( 'xswpph_db_version', 0 );

		if ( $current_db_version === (int) $db_version ) {
			return;
		}

		global $wpdb;

		$xswpph_posts_visibility_table = $wpdb->prefix . 'xswpph_posts_visibility';
		$charset_collate               = $wpdb->get_charset_collate();

		$xswpph_posts_visibility = "CREATE TABLE $xswpph_posts_visibility_table (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            post_id BIGINT(20) UNSIGNED NOT NULL,
            `condition` VARCHAR(100) NOT NULL,
            PRIMARY KEY (id),
            INDEX pid_con (post_id,`condition`)
        ) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( $xswpph_posts_visibility );

		update_option( 'xswpph_db_version', $current_db_version );
	}

	/**
	 * Get hidden post IDs with caching
	 *
	 * @param string  $post_type The post type.
	 * @param string  $condition The hide condition.
	 * @param boolean $fallback Should it fallback to meta table.
	 * @return array
	 */
	public static function get_hidden_posts_ids( $post_type = 'post', $condition = 'all', $fallback = true ) {
		$cache_key = 'xswpph_' . $post_type . '_' . $condition;

		$hidden_posts = wp_cache_get( $cache_key, 'xswpph' );
		if ( false !== $hidden_posts ) {
			return $hidden_posts;
		}

		$hidden_posts = get_transient( $cache_key );
		if ( false !== $hidden_posts ) {
			wp_cache_set( $cache_key, $hidden_posts, 'xswpph' );
			return $hidden_posts;
		}

		global $wpdb;
		$table_name = $wpdb->prefix . 'xswpph_posts_visibility';

		if ( 'all' === $condition ) {
			$sql = $wpdb->prepare( "SELECT DISTINCT post_id FROM {$table_name} WHERE post_id IN (SELECT ID FROM {$wpdb->posts} WHERE post_type = %s)", $post_type );
		} else {
			$sql = $wpdb->prepare(
				"SELECT DISTINCT post_id FROM {$table_name} WHERE `condition` = %s AND post_id IN (SELECT ID FROM {$wpdb->posts} WHERE post_type = %s)",
				$condition,
				$post_type
			);
		}

		$hidden_posts = $wpdb->get_col( $sql );

		if ( empty( $hidden_posts ) && $fallback ) {
			// Fallback to meta table.
			$meta_key = self::get_meta_key_from_condition( $condition );
			if ( $meta_key ) {
				$sql          = $wpdb->prepare(
					"SELECT DISTINCT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value != '' AND post_id IN (SELECT ID FROM {$wpdb->posts} WHERE post_type = %s)",
					$meta_key,
					$post_type
				);
				$hidden_posts = $wpdb->get_col( $sql );
			}
		}

		if ( ! is_array( $hidden_posts ) ) {
			$hidden_posts = array();
		}

		wp_cache_set( $cache_key, $hidden_posts, 'xswpph' );
		set_transient( $cache_key, $hidden_posts, WEEK_IN_SECONDS );

		return $hidden_posts;
	}

	/**
	 * Add hiding condition to database
	 *
	 * @param int    $post_id The post id.
	 * @param string $condition The condition.
	 * @return boolean
	 */
	public static function add_hide_condition( $post_id, $condition ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'xswpph_posts_visibility';

		$result = $wpdb->insert(
			$table_name,
			array(
				'post_id'   => $post_id,
				'condition' => $condition,
			),
			array(
				'%d',
				'%s',
			)
		);

		return false !== $result;
	}

	/**
	 * Remove hiding condition from database
	 *
	 * @param int    $post_id The post id.
	 * @param string $condition The condition.
	 * @return boolean
	 */
	public static function remove_hide_condition( $post_id, $condition ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'xswpph_posts_visibility';

		$result = $wpdb->delete(
			$table_name,
			array(
				'post_id'   => $post_id,
				'condition' => $condition,
			),
			array(
				'%d',
				'%s',
			)
		);

		// Also remove from post meta for cleanup.
		$meta_key = self::get_meta_key_from_condition( $condition );
		if ( $meta_key ) {
			delete_post_meta( $post_id, $meta_key );
		}

		return false !== $result;
	}

	/**
	 * Clear cache for post.
	 *
	 * @param int    $post_id The post id.
	 * @param string $post_type The post type.
	 */
	public static function clear_post_cache( $post_id, $post_type = null ) {
		if ( ! $post_type ) {
			$post_type = get_post_type( $post_id );
		}

		$conditions = array(
			'all',
			'front_page',
			'blog_page',
			'category_page',
			'tag_page',
			'author',
			'archive',
			'search',
			'feeds',
			'recent',
			'rel_link',
			'rest_api',
			'single_post_page',
			'wc_shop',
			'wc_product_category',
		);

		foreach ( $conditions as $condition ) {
			$cache_key = 'xswpph_' . $post_type . '_' . $condition;
			wp_cache_delete( $cache_key, 'xswpph' );
			delete_transient( $cache_key );
		}
	}

	/**
	 * Get meta key from condition
	 *
	 * @param string $condition Condition of hidden.
	 * @return string|false
	 */
	private static function get_meta_key_from_condition( $condition ) {
		$mapping = array(
			'front_page'          => '_xswpph_front_page',
			'blog_page'           => '_xswpph_blog_page',
			'category_page'       => '_xswpph_category_page',
			'tag_page'            => '_xswpph_tag_page',
			'author'              => '_xswpph_author',
			'archive'             => '_xswpph_archive',
			'search'              => '_xswpph_search',
			'feeds'               => '_xswpph_feeds',
			'recent'              => '_xswpph_recent',
			'rel_link'            => '_xswpph_rel_link',
			'rest_api'            => '_xswpph_rest_api',
			'single_post_page'    => '_xswpph_single_post_page',
			'wc_shop'             => '_xswpph_wc_shop',
			'wc_product_category' => '_xswpph_wc_product_category',
		);

		return isset( $mapping[ $condition ] ) ? $mapping[ $condition ] : false;
	}

	/**
	 * Migrate data from meta to table
	 */
	public static function migrate_meta_to_table() {
		$data_migrated = get_option( 'xswpph_data_migrated', false );
		if ( $data_migrated ) {
			return;
		}

		global $wpdb;
		$table_name = $wpdb->prefix . 'xswpph_posts_visibility';

		$meta_keys = array(
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

		foreach ( $meta_keys as $meta_key => $condition ) {
			$posts = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value != ''",
					$meta_key
				)
			);

			foreach ( $posts as $post ) {
				$exist = $wpdb->get_var(
					$wpdb->prepare(
						"SELECT COUNT(*) FROM {$table_name} WHERE post_id = %d AND `condition` = %s",
						$post->post_id,
						$condition
					)
				);

				if ( ! $exist ) {
					self::add_hide_condition( $post->post_id, $condition );
				}
				delete_post_meta( $post->post_id, $meta_key );
			}
		}

		update_option( 'xswpph_data_migrated', true );
	}
}
