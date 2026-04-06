<?php
/**
 * Database handling for WP Post Hide
 *
 * @package WP Post Hide
 */

// Exit if directly access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Database management class
 */
class XSWPHP_Database {

	/**
	 * Create database tables
	 *
	 * @return void
	 */
	public static function create_tables() {
		$current_db_version = 1;
		$db_version         = get_option( 'xswphp_db_version', 0 );

		if ( $current_db_version === (int) $db_version ) {
			return;
		}

		global $wpdb;

		$xswphp_posts_visibility_table = $wpdb->prefix . 'xswphp_posts_visibility';
		$charset_collate               = $wpdb->get_charset_collate();

		$xswphp_posts_visibility = "CREATE TABLE $xswphp_posts_visibility_table (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            post_id BIGINT(20) UNSIGNED NOT NULL,
            `condition` VARCHAR(100) NOT NULL,
            PRIMARY KEY (id),
            INDEX pid_con (post_id,`condition`)
        ) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		dbDelta( $xswphp_posts_visibility );

		update_option( 'xswphp_db_version', $current_db_version );
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
		$cache_key = 'xswphp_' . $post_type . '_' . $condition;

		$hidden_posts = wp_cache_get( $cache_key, 'xswphp' );
		if ( false !== $hidden_posts ) {
			return $hidden_posts;
		}

		$hidden_posts = get_transient( $cache_key );
		if ( false !== $hidden_posts ) {
			wp_cache_set( $cache_key, $hidden_posts, 'xswphp' );
			return $hidden_posts;
		}

		global $wpdb;
		$table_name = esc_sql( $wpdb->prefix . 'xswphp_posts_visibility' );

		if ( 'all' === $condition ) {
			$sql = $wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT DISTINCT post_id FROM {$table_name} WHERE post_id IN (SELECT ID FROM {$wpdb->posts} WHERE post_type = %s)",
				$post_type
			);
		} else {
			$sql = $wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT DISTINCT post_id FROM {$table_name} WHERE `condition` = %s AND post_id IN (SELECT ID FROM {$wpdb->posts} WHERE post_type = %s)",
				$condition,
				$post_type
			);
		}
       // phpcs:ignore WordPress.DB
		$hidden_posts = $wpdb->get_col( $sql );

		if ( empty( $hidden_posts ) && $fallback ) {
			// Fallback to meta table.
			$meta_key = self::get_meta_key_from_condition( $condition );
			if ( $meta_key ) {
				$sql = $wpdb->prepare(
					"SELECT DISTINCT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND post_id IN (SELECT ID FROM {$wpdb->posts} WHERE post_type = %s)",
					$meta_key,
					$post_type
				);
				// phpcs:ignore WordPress.DB
				$hidden_posts = $wpdb->get_col( $sql );
			}
		}

		if ( ! is_array( $hidden_posts ) ) {
			$hidden_posts = array();
		}

		wp_cache_set( $cache_key, $hidden_posts, 'xswphp' );
		set_transient( $cache_key, $hidden_posts, WEEK_IN_SECONDS );

		return $hidden_posts;
	}

	/**
	 * Check if post is hidden in custom table
	 *
	 * @param int     $post_id The post id.
	 * @param string  $condition The condition.
	 * @param boolean $fallback Should check meta table.
	 * @return boolean
	 */
	public static function is_post_hidden( $post_id, $condition, $fallback = true ) {
		global $wpdb;
		$table_name = esc_sql( $wpdb->prefix . 'xswphp_posts_visibility' );
		// phpcs:ignore WordPress.DB
		$hidden_post = (int) $wpdb->get_var(
			$wpdb->prepare(
				// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
				"SELECT COUNT(*) FROM {$table_name} WHERE post_id = %d AND `condition` = %s",
				$post_id,
				$condition
			)
		);

		if ( $hidden_post ) {
			return true;
		}

		if ( $fallback ) {
			$meta_key = self::get_meta_key_from_condition( $condition );
			if ( $meta_key ) {
				return get_post_meta( $post_id, $meta_key, true );
			}
		}

		return false;
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
		$table_name = $wpdb->prefix . 'xswphp_posts_visibility';
		// phpcs:ignore WordPress.DB
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
		$table_name = $wpdb->prefix . 'xswphp_posts_visibility';
		// phpcs:ignore WordPress.DB
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
	 * Clear cache for post
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
			'redirect_page',
			'always',
			'keep_search',
			'rest_api',
			'single_post_page',
		);

		foreach ( $conditions as $condition ) {
			$cache_key = 'xswphp_' . $post_type . '_' . $condition;
			wp_cache_delete( $cache_key, 'xswphp' );
			delete_transient( $cache_key );
		}
	}

	/**
	 * Get meta key from condition
	 *
	 * @param string $condition Conditon meta key.
	 * @return string|false
	 */
	private static function get_meta_key_from_condition( $condition ) {
		$mapping = array(
			'front_page'    => '_xswphp_front_page',
			'category_page' => '_xswphp_category_page',
			'tag_page'      => '_xswphp_tag_page',
			'author'        => '_xswphp_author',
			'archive'       => '_xswphp_archive',
			'search'        => '_xswphp_search',
			'feeds'         => '_xswphp_feeds',
			'recent'        => '_xswphp_recent',
			'rel_link'      => '_xswphp_rel_link',
			'redirect_page' => '_xswphp_redirect_page',
			'always'        => '_xswphp_always',
			'keep_search'   => '_xswphp_keep_search',
			'rest_api'      => '_xswphp_rest_api',
		);

		return isset( $mapping[ $condition ] ) ? $mapping[ $condition ] : false;
	}

	/**
	 * Migrate data from meta to table
	 */
	public static function migrate_meta_to_table() {
		$data_migrated = get_option( 'xswphp_data_migrated', false );
		if ( $data_migrated ) {
			return;
		}

		global $wpdb;
		$table_name = $wpdb->prefix . 'xswphp_posts_visibility';

		$meta_keys = array(
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

		foreach ( $meta_keys as $meta_key => $condition ) {
			// phpcs:ignore WordPress.DB
			$posts = $wpdb->get_results(
				$wpdb->prepare(
					"SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value != ''",
					$meta_key
				)
			);

			foreach ( $posts as $post ) {
				$exist = self::is_post_hidden( $post->post_id, $condition, false );
				if ( ! $exist ) {
					self::add_hide_condition( $post->post_id, $condition );
				}
				delete_post_meta( $post->post_id, $meta_key );
			}
		}

		update_option( 'xswphp_data_migrated', true );
	}
}
