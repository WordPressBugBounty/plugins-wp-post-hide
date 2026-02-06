<?php
/**
 * WP Post Hide Meta box.
 *
 * @package WP Post Hide Setup.
 * @since 1.0.0
 */

// Exit if directly access.

if ( ! defined( 'ABSPATH' ) ) {

	exit;

}
/**
 * For Post meta Box callback
 *
 * @param object $post Object of post.
 */
function xswphp_postbox_callback( $post ) {
	$xswphp = xswphp_meta_data( $post->ID );
	wp_nonce_field( '_xswphp_nonce', 'nonce', true );
	?>
	<fieldset class="inline-edit-col-left xswphp-col-quick">
		<div class="inline-edit-group">
			<label>
				<input class ="xswphp-posts" type="checkbox" name="xswphp[_xswphp_all_hidden]" value="All Hidden" <?php echo esc_html( ( isset( $xswphp['_xswphp_all_hidden'] ) && ( '' !== $xswphp['_xswphp_all_hidden'] ) ) ? 'Checked' : '' ); ?> />
				<span><?php esc_html_e( 'All Check/Uncheck', 'wp-post-hide' ); ?></span>
			</label>
			<br>
			<label  class="alignleft">
				<input class="xswphp-post" type="checkbox" name="xswphp[_xswphp_redirect_page]" value="Redirect Page" <?php echo esc_html( ( isset( $xswphp['_xswphp_redirect_page'] ) && ( '' !== $xswphp['_xswphp_redirect_page'] ) ) ? 'Checked' : '' ); ?> />
				<span class="xswphp-quick-span">
					<?php esc_html_e( 'Redirect 404 page', 'wp-post-hide' ); ?>
				</span>
			</label>

			<label class="alignleft">

				<input class="xswphp-post" type="checkbox" name="xswphp[_xswphp_front_page]" value="Front Page" <?php echo esc_html( ( isset( $xswphp['_xswphp_front_page'] ) && ( '' !== $xswphp['_xswphp_front_page'] ) ) ? 'Checked' : '' ); ?> />

				<span class="xswphp-quick-span">

					<?php esc_html_e( 'Hide from Front Page', 'wp-post-hide' ); ?>

				</span>

			</label>
			<label class="alignleft">

				<input class="xswphp-post" type="checkbox" name="xswphp[_xswphp_category_page]" value="Category Pages" <?php echo esc_html( ( isset( $xswphp['_xswphp_category_page'] ) && ( '' !== $xswphp['_xswphp_category_page'] ) ) ? 'Checked' : '' ); ?> />

				<span class="xswphp-quick-span">

					<?php esc_html_e( 'Hide from Category Pages', 'wp-post-hide' ); ?>

				</span>

			</label>

			<label class="alignleft">

				<input class="xswphp-post"   type="checkbox" name="xswphp[_xswphp_tag_page]" value="Tag Pages" <?php echo esc_html( ( isset( $xswphp['_xswphp_tag_page'] ) && ( '' !== $xswphp['_xswphp_tag_page'] ) ) ? 'Checked' : '' ); ?> />

				<span class="xswphp-quick-span">

					<?php esc_html_e( 'Hide from Tag Pages', 'wp-post-hide' ); ?>

				</span>

			</label>

			<label class="alignleft">

				<input class="xswphp-post" type="checkbox" name="xswphp[_xswphp_author]" value="Author Pages" <?php echo esc_html( ( isset( $xswphp['_xswphp_author'] ) && ( '' !== $xswphp['_xswphp_author'] ) ) ? 'Checked' : '' ); ?> />

				<span class="xswphp-quick-span">

					<?php esc_html_e( 'Hide from Author Pages', 'wp-post-hide' ); ?>

				</span>

			</label>

			<label class="alignleft">

				<input class="xswphp-post" type="checkbox" name="xswphp[_xswphp_archive]" value="Date Archive" <?php echo esc_html( ( isset( $xswphp['_xswphp_archive'] ) && ( '' !== $xswphp['_xswphp_archive'] ) ) ? 'Checked' : '' ); ?> />

				<span class="xswphp-quick-span">

					<?php esc_html_e( 'Hide from Date Archive Pages', 'wp-post-hide' ); ?>

				</span>

			</label>

			<label class="alignleft">

				<input class="xswphp-post" type="checkbox" name="xswphp[_xswphp_search]" value="Search Results" <?php echo esc_html( ( isset( $xswphp['_xswphp_search'] ) && ( '' !== $xswphp['_xswphp_search'] ) ) ? 'Checked' : '' ); ?> />

				<span class="xswphp-quick-span">

					<?php esc_html_e( 'Hide from Search Pages', 'wp-post-hide' ); ?>

				</span>

			</label>

			<label class="alignleft">

				<input class="xswphp-post" type="checkbox" name="xswphp[_xswphp_feeds]" value="Feeds" <?php echo esc_html( ( isset( $xswphp['_xswphp_feeds'] ) && ( '' !== $xswphp['_xswphp_feeds'] ) ) ? 'Checked' : '' ); ?> />

				<span class="xswphp-quick-span">

					<?php esc_html_e( 'Hide from Feeds', 'wp-post-hide' ); ?>

				</span>

			</label>

			<label class="alignleft">

				<input class="xswphp-post" type="checkbox" name="xswphp[_xswphp_recent]" value="Recent Post" <?php echo esc_html( ( isset( $xswphp['_xswphp_recent'] ) && ( '' !== $xswphp['_xswphp_recent'] ) ) ? 'Checked' : '' ); ?> /> 

				<span class="xswphp-quick-span">

					<?php esc_html_e( 'Hide from Recent Post', 'wp-post-hide' ); ?>

				</span>

			</label>

			<label class="alignleft">

				<input class="xswphp-post" type="checkbox" name="xswphp[_xswphp_rel_link]" value="Meta rel link" <?php echo esc_html( ( isset( $xswphp['_xswphp_rel_link'] ) && ( '' !== $xswphp['_xswphp_rel_link'] ) ) ? 'Checked' : '' ); ?> />

				<span class="xswphp-quick-span">

					<?php esc_html_e( 'Hide from Next and pervious rel link', 'wp-post-hide' ); ?>

				</span>

			</label>

			<label class="alignleft">

				<input class="xswphp-post" type="checkbox" name="xswphp[_xswphp_rest_api]" value="REST API" <?php echo esc_html( ( isset( $xswphp['_xswphp_rest_api'] ) && ( '' !== $xswphp['_xswphp_rest_api'] ) ) ? 'Checked' : '' ); ?> />

				<span class="xswphp-quick-span">

					<?php esc_html_e( 'Hide from REST API', 'wp-post-hide' ); ?>

				</span>

			</label>
		</div>

	</fieldset>

	<?php
}



/**
 * For page metabox callback
 *
 * @param object $post Object of post page.
 */
function xswphp_pagebox_callback( $post ) {

	$xswphp = xswphp_meta_data( $post->ID );
	wp_nonce_field( '_xswphp_nonce', 'nonce', true );
	?>

	<h3>

		<input class ="xswphp-posts" type="checkbox" name="xswphp[_xswphp_all_hidden]" value="All Hidden" <?php echo esc_html( ( isset( $xswphp['_xswphp_all_hidden'] ) && ( '' !== $xswphp['_xswphp_all_hidden'] ) ) ? 'Checked' : '' ); ?> />

		<label><?php esc_html_e( 'All Check/Uncheck', 'wp-post-hide' ); ?></label>

	</h3>
	<input class="xswphp-post" type="checkbox" name="xswphp[_xswphp_redirect_page]" value="Redirect Page" <?php echo esc_html( ( isset( $xswphp['_xswphp_redirect_page'] ) && ( '' !== $xswphp['_xswphp_redirect_page'] ) ) ? 'Checked' : '' ); ?> />

	<label>

		<?php esc_html_e( 'Redirect 404 page', 'wp-post-hide' ); ?>

	</label><br>
	<input class="xswphp-post" type="checkbox" name="xswphp[_xswphp_front_page]" value="Front Page" <?php echo esc_html( ( isset( $xswphp['_xswphp_front_page'] ) && ( '' !== $xswphp['_xswphp_front_page'] ) ) ? 'Checked' : '' ); ?> />

	<label>

		<?php esc_html_e( 'Hide from listing of pages in Front Page', 'wp-post-hide' ); ?>

	</label><br>

	<input class="xswphp-post" type="checkbox" name="xswphp[_xswphp_always]" value="Hide form pages list everywhere" <?php echo esc_html( ( isset( $xswphp['_xswphp_always'] ) && ( '' !== $xswphp['_xswphp_always'] ) ) ? 'Checked' : '' ); ?> />

	<label>

		<?php esc_html_e( 'Hide form pages list everywhere', 'wp-post-hide' ); ?>

	</label><br>

	<input class="xswphp-post" type="checkbox" name="xswphp[_xswphp_keep_search]" value="Hide but keep in Search" <?php echo esc_html( ( isset( $xswphp['_xswphp_keep_search'] ) && ( '' !== $xswphp['_xswphp_keep_search'] ) ) ? 'Checked' : '' ); ?> />

	<label>

		<?php esc_html_e( 'Hide but keep in Search', 'wp-post-hide' ); ?>

	</label><br>

	<input class="xswphp-post" type="checkbox" name="xswphp[_xswphp_rest_api]" value="REST API" <?php echo esc_html( ( isset( $xswphp['_xswphp_rest_api'] ) && ( '' !== $xswphp['_xswphp_rest_api'] ) ) ? 'Checked' : '' ); ?> />

	<label>

		<?php esc_html_e( 'Hide from REST API', 'wp-post-hide' ); ?>

	</label><br>

	<?php
}

/**
 * For Product meta Box callback (WooCommerce).
 *
 * @param object $post Object of product post type.
 */
function xswphp_productbox_callback( $post ) {

	$xswphp = xswphp_meta_data( $post->ID );
	wp_nonce_field( '_xswphp_nonce', 'nonce', true );
	?>

	<fieldset class="inline-edit-col-left xswphp-col-quick">

		<div class="inline-edit-group">

			<label>

				<input class ="xswphp-posts" type="checkbox" name="xswphp[_xswphp_all_hidden]" value="All Hidden" <?php echo esc_html( ( isset( $xswphp['_xswphp_all_hidden'] ) && ( '' !== $xswphp['_xswphp_all_hidden'] ) ) ? 'Checked' : '' ); ?> />

				<span><?php esc_html_e( 'All Check/Uncheck', 'wp-post-hide' ); ?></span>

			</label><br>
			<label  class="alignleft">
				<input class="xswphp-post" type="checkbox" name="xswphp[_xswphp_redirect_page]" value="Redirect Page" <?php echo esc_html( ( isset( $xswphp['_xswphp_redirect_page'] ) && ( '' !== $xswphp['_xswphp_redirect_page'] ) ) ? 'Checked' : '' ); ?> />
				<span class="xswphp-quick-span">
					<?php esc_html_e( 'Redirect 404 page', 'wp-post-hide' ); ?>
				</span>
			</label>

			<label class="alignleft">

				<input class="xswphp-post" type="checkbox" name="xswphp[_xswphp_front_page]" value="Front Page" <?php echo esc_html( ( isset( $xswphp['_xswphp_front_page'] ) && ( '' !== $xswphp['_xswphp_front_page'] ) ) ? 'Checked' : '' ); ?> />

				<span class="xswphp-quick-span">

					<?php esc_html_e( 'Hide from Shop Page', 'wp-post-hide' ); ?>

				</span>

			</label>
			<label class="alignleft">

				<input class="xswphp-post" type="checkbox" name="xswphp[_xswphp_category_page]" value="Category Pages" <?php echo esc_html( ( isset( $xswphp['_xswphp_category_page'] ) && ( '' !== $xswphp['_xswphp_category_page'] ) ) ? 'Checked' : '' ); ?> />

				<span class="xswphp-quick-span">

					<?php esc_html_e( 'Hide from Product Category Pages', 'wp-post-hide' ); ?>

				</span>

			</label>

			<label class="alignleft">

				<input class="xswphp-post"   type="checkbox" name="xswphp[_xswphp_tag_page]" value="Tag Pages" <?php echo esc_html( ( isset( $xswphp['_xswphp_tag_page'] ) && ( '' !== $xswphp['_xswphp_tag_page'] ) ) ? 'Checked' : '' ); ?> />

				<span class="xswphp-quick-span">

					<?php esc_html_e( 'Hide from Product Tag Pages', 'wp-post-hide' ); ?>

				</span>

			</label>

			<label class="alignleft">

				<input class="xswphp-post" type="checkbox" name="xswphp[_xswphp_author]" value="Author Pages" <?php echo esc_html( ( isset( $xswphp['_xswphp_author'] ) && ( '' !== $xswphp['_xswphp_author'] ) ) ? 'Checked' : '' ); ?> />

				<span class="xswphp-quick-span">

					<?php esc_html_e( 'Hide from Author Pages', 'wp-post-hide' ); ?>

				</span>

			</label>

			<label class="alignleft">

				<input class="xswphp-post" type="checkbox" name="xswphp[_xswphp_archive]" value="Date Archive" <?php echo esc_html( ( isset( $xswphp['_xswphp_archive'] ) && ( '' !== $xswphp['_xswphp_archive'] ) ) ? 'Checked' : '' ); ?> />

				<span class="xswphp-quick-span">

					<?php esc_html_e( 'Hide from Product Archive Pages', 'wp-post-hide' ); ?>

				</span>

			</label>

			<label class="alignleft">

				<input class="xswphp-post" type="checkbox" name="xswphp[_xswphp_search]" value="Search Results" <?php echo esc_html( ( isset( $xswphp['_xswphp_search'] ) && ( '' !== $xswphp['_xswphp_search'] ) ) ? 'Checked' : '' ); ?> />

				<span class="xswphp-quick-span">

					<?php esc_html_e( 'Hide from Search Pages', 'wp-post-hide' ); ?>

				</span>

			</label>

			<label class="alignleft">

				<input class="xswphp-post" type="checkbox" name="xswphp[_xswphp_feeds]" value="Feeds" <?php echo esc_html( ( isset( $xswphp['_xswphp_feeds'] ) && ( '' !== $xswphp['_xswphp_feeds'] ) ) ? 'Checked' : '' ); ?> />

				<span class="xswphp-quick-span">

					<?php esc_html_e( 'Hide from Feeds', 'wp-post-hide' ); ?>

				</span>

			</label>

			<label class="alignleft">

				<input class="xswphp-post" type="checkbox" name="xswphp[_xswphp_recent]" value="Recent Product" <?php echo esc_html( ( isset( $xswphp['_xswphp_recent'] ) && ( '' !== $xswphp['_xswphp_recent'] ) ) ? 'Checked' : '' ); ?> />

				<span class="xswphp-quick-span">

					<?php esc_html_e( 'Hide from Recent Product', 'wp-post-hide' ); ?>

				</span>

			</label>

			<label class="alignleft">

				<input class="xswphp-post" type="checkbox" name="xswphp[_xswphp_rel_link]" value="Meta rel link" <?php echo esc_html( ( isset( $xswphp['_xswphp_rel_link'] ) && ( '' !== $xswphp['_xswphp_rel_link'] ) ) ? 'Checked' : '' ); ?> />

				<span class="xswphp-quick-span">

					<?php esc_html_e( 'Hide from Next and previous rel link', 'wp-post-hide' ); ?>

				</span>

			</label>

			<label class="alignleft">

				<input class="xswphp-post" type="checkbox" name="xswphp[_xswphp_rest_api]" value="REST API" <?php echo esc_html( ( isset( $xswphp['_xswphp_rest_api'] ) && ( '' !== $xswphp['_xswphp_rest_api'] ) ) ? 'Checked' : '' ); ?> />

				<span class="xswphp-quick-span">

					<?php esc_html_e( 'Hide from REST API', 'wp-post-hide' ); ?>

				</span>

			</label>
		</div>

	</fieldset>

	<?php
}

?>
