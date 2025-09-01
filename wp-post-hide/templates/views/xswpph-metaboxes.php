<?php
// Exit if directly access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * For Post meta Box callback
 *
 * @param WP_Post $post Post object.
 * @return void
 */
function xswpph_postbox_callback( $post ) {
	$xswpph = xswpph_meta_data( $post->ID );
	wp_nonce_field( 'xswpph_save_meta', 'xswpph_nonce' );
	?>
	<fieldset class="inline-edit-col-left xswpph-col-quick">
		<div class="inline-edit-group">
			<label>
				<input class ="xswpph-posts" type="checkbox" name="xswpph[_xswpph_all_hidden]" value="All Hidden" <?php esc_html_e( ( isset( $xswpph['_xswpph_all_hidden'] ) && ( $xswpph['_xswpph_all_hidden'] != '' ) ) ? 'Checked' : '' ); ?> />
				<span><?php esc_html_e( 'All Check/Uncheck', 'xswpph-domain' ); ?></span>
			</label><br>
			<label class="alignleft">
				<input class="xswpph-post" type="checkbox" name="xswpph[_xswpph_front_page]" value="Front Page" <?php esc_html_e( ( isset( $xswpph['_xswpph_front_page'] ) && ( $xswpph['_xswpph_front_page'] != '' ) ) ? 'Checked' : '' ); ?> />
				<span class="xswpph-quick-span">
					<?php esc_html_e( 'Hide from Front Page', 'xswpph-domain' ); ?>
				</span>
			</label>
			<label class="alignleft">
				<input class="xswpph-post" type="checkbox" name="xswpph[_xswpph_blog_page]" value="Blog Page" <?php esc_html_e( ( isset( $xswpph['_xswpph_blog_page'] ) && ( $xswpph['_xswpph_blog_page'] != '' ) ) ? 'Checked' : '' ); ?> />
				<span class="xswpph-quick-span">
					<?php esc_html_e( 'Hide from Blog Page', 'xswpph-domain' ); ?>
				</span>
			</label>
			<label class="alignleft">
				<input class="xswpph-post" type="checkbox" name="xswpph[_xswpph_category_page]" value="Category Pages" <?php esc_html_e( ( isset( $xswpph['_xswpph_category_page'] ) && ( $xswpph['_xswpph_category_page'] != '' ) ) ? 'Checked' : '' ); ?> />
				<span class="xswpph-quick-span">
					<?php esc_html_e( 'Hide from Category Pages', 'xswpph-domain' ); ?>
				</span>
			</label>
			<label class="alignleft">
				<input class="xswpph-post" type="checkbox" name="xswpph[_xswpph_tag_page]" value="Tag Pages" <?php esc_html_e( ( isset( $xswpph['_xswpph_tag_page'] ) && ( $xswpph['_xswpph_tag_page'] != '' ) ) ? 'Checked' : '' ); ?> />
				<span class="xswpph-quick-span">
					<?php esc_html_e( 'Hide from Tag Pages', 'xswpph-domain' ); ?>
				</span>
			</label>
			<label class="alignleft">
				<input class="xswpph-post" type="checkbox" name="xswpph[_xswpph_author]" value="Author Pages" <?php esc_html_e( ( isset( $xswpph['_xswpph_author'] ) && ( $xswpph['_xswpph_author'] != '' ) ) ? 'Checked' : '' ); ?> />
				<span class="xswpph-quick-span">
					<?php esc_html_e( 'Hide from Author Pages', 'xswpph-domain' ); ?>
				</span>
			</label>
			<label class="alignleft">
				<input class="xswpph-post" type="checkbox" name="xswpph[_xswpph_archive]" value="Date Archive" <?php esc_html_e( ( isset( $xswpph['_xswpph_archive'] ) && ( $xswpph['_xswpph_archive'] != '' ) ) ? 'Checked' : '' ); ?> />
				<span class="xswpph-quick-span">
					<?php esc_html_e( 'Hide from Date Archive Pages', 'xswpph-domain' ); ?>
				</span>
			</label>
			<label class="alignleft">
				<input class="xswpph-post" type="checkbox" name="xswpph[_xswpph_search]" value="Search Results" <?php esc_html_e( ( isset( $xswpph['_xswpph_search'] ) && ( $xswpph['_xswpph_search'] != '' ) ) ? 'Checked' : '' ); ?> />
				<span class="xswpph-quick-span">
					<?php esc_html_e( 'Hide from Search Pages', 'xswpph-domain' ); ?>
				</span>
			</label>
			<label class="alignleft">
				<input class="xswpph-post" type="checkbox" name="xswpph[_xswpph_feeds]" value="Feeds" <?php esc_html_e( ( isset( $xswpph['_xswpph_feeds'] ) && ( $xswpph['_xswpph_feeds'] != '' ) ) ? 'Checked' : '' ); ?> />
				<span class="xswpph-quick-span">
					<?php esc_html_e( 'Hide from Feeds', 'xswpph-domain' ); ?>
				</span>
			</label>
			<label class="alignleft">
				<input class="xswpph-post" type="checkbox" name="xswpph[_xswpph_recent]" value="Recent Post" <?php esc_html_e( ( isset( $xswpph['_xswpph_recent'] ) && ( $xswpph['_xswpph_recent'] != '' ) ) ? 'Checked' : '' ); ?> />
				<span class="xswpph-quick-span">
					<?php esc_html_e( 'Hide from Recent Post', 'xswpph-domain' ); ?>
				</span>
			</label>
			<label class="alignleft">
				<input class="xswpph-post" type="checkbox" name="xswpph[_xswpph_rel_link]" value="Meta rel link" <?php esc_html_e( ( isset( $xswpph['_xswpph_rel_link'] ) && ( $xswpph['_xswpph_rel_link'] != '' ) ) ? 'Checked' : '' ); ?> />
				<span class="xswpph-quick-span">
					<?php esc_html_e( 'Hide from Next and previous rel link', 'xswpph-domain' ); ?>
				</span>
			</label>
			<label class="alignleft">
				<input class="xswpph-post" type="checkbox" name="xswpph[_xswpph_rest_api]" value="REST API" <?php esc_html_e( ( isset( $xswpph['_xswpph_rest_api'] ) && ( $xswpph['_xswpph_rest_api'] != '' ) ) ? 'Checked' : '' ); ?> />
				<span class="xswpph-quick-span">
					<?php esc_html_e( 'Hide from REST API', 'xswpph-domain' ); ?>
				</span>
			</label>
			<label class="alignleft">
				<input class="xswpph-post" type="checkbox" name="xswpph[_xswpph_single_post_page]" value="Single Post Page" <?php esc_html_e( ( isset( $xswpph['_xswpph_single_post_page'] ) && ( $xswpph['_xswpph_single_post_page'] != '' ) ) ? 'Checked' : '' ); ?> />
				<span class="xswpph-quick-span">
					<?php esc_html_e( 'Hide from Single Post Page', 'xswpph-domain' ); ?>
				</span>
			</label>
			<?php if ( class_exists( 'WooCommerce' ) && get_post_type() === 'product' ): ?>
			<label class="alignleft">
				<input class="xswpph-post" type="checkbox" name="xswpph[_xswpph_wc_shop]" value="WC Shop Page" <?php esc_html_e( ( isset( $xswpph['_xswpph_wc_shop'] ) && ( $xswpph['_xswpph_wc_shop'] != '' ) ) ? 'Checked' : '' ); ?> />
				<span class="xswpph-quick-span">
					<?php esc_html_e( 'Hide from WooCommerce Shop Page', 'xswpph-domain' ); ?>
				</span>
			</label>
			<label class="alignleft">
				<input class="xswpph-post" type="checkbox" name="xswpph[_xswpph_wc_product_category]" value="WC Product Category" <?php esc_html_e( ( isset( $xswpph['_xswpph_wc_product_category'] ) && ( $xswpph['_xswpph_wc_product_category'] != '' ) ) ? 'Checked' : '' ); ?> />
				<span class="xswpph-quick-span">
					<?php esc_html_e( 'Hide from WooCommerce Product Categories', 'xswpph-domain' ); ?>
				</span>
			</label>
			<?php endif; ?>
		</div>
	</fieldset>
	<?php
}

/**
 * For page metabox callback
 *
 * @param WP_Post $post Post object.
 * @return void
 */
function xswpph_pagebox_callback( $post ) {
	$xswpph = xswpph_meta_data( $post->ID );
	wp_nonce_field( 'xswpph_save_meta', 'xswpph_nonce' );
	?>
	<h3>
		<input class ="xswpph-posts" type="checkbox" name="xswpph[_xswpph_all_hidden]" value="All Hidden" <?php esc_html_e( ( isset( $xswpph['_xswpph_all_hidden'] ) && ( $xswpph['_xswpph_all_hidden'] != '' ) ) ? 'Checked' : '' ); ?> />
		<label><?php esc_html_e( 'All Check/Uncheck', 'xswpph-domain' ); ?></label>
	</h3>
	<input class="xswpph-post" type="checkbox" name="xswpph[_xswpph_front_page]" value="Front Page" <?php esc_html_e( ( isset( $xswpph['_xswpph_front_page'] ) && ( $xswpph['_xswpph_front_page'] != '' ) ) ? 'Checked' : '' ); ?> />
	<label>
		<?php esc_html_e( 'Hide from listing of pages in Front Page', 'xswpph-domain' ); ?>
	</label><br>
	<input class="xswpph-post" type="checkbox" name="xswpph[_xswpph_blog_page]" value="Blog Page" <?php esc_html_e( ( isset( $xswpph['_xswpph_blog_page'] ) && ( $xswpph['_xswpph_blog_page'] != '' ) ) ? 'Checked' : '' ); ?> />
	<label>
		<?php esc_html_e( 'Hide from Blog Page', 'xswpph-domain' ); ?>
	</label><br>
	<input class="xswpph-post" type="checkbox" name="xswpph[_xswpph_rest_api]" value="REST API" <?php esc_html_e( ( isset( $xswpph['_xswpph_rest_api'] ) && ( $xswpph['_xswpph_rest_api'] != '' ) ) ? 'Checked' : '' ); ?> />
	<label>
		<?php esc_html_e( 'Hide from REST API', 'xswpph-domain' ); ?>
	</label><br>
	<input class="xswpph-post" type="checkbox" name="xswpph[_xswpph_single_post_page]" value="Single Post Page" <?php esc_html_e( ( isset( $xswpph['_xswpph_single_post_page'] ) && ( $xswpph['_xswpph_single_post_page'] != '' ) ) ? 'Checked' : '' ); ?> />
	<label>
		<?php esc_html_e( 'Hide from Single Post Page', 'xswpph-domain' ); ?>
	</label><br>
	<?php
}

/**
 * For attachment metabox callback
 *
 * @param WP_Post $post Post object.
 * @return void
 */
function xswpph_attachmentbox_callback( $post ) {
	$xswpph = xswpph_meta_data( $post->ID );
	wp_nonce_field( 'xswpph_save_meta', 'xswpph_nonce' );
	?>
	<h3>
		<input class ="xswpph-posts" type="checkbox" name="xswpph[_xswpph_all_hidden]" value="All Hidden" <?php esc_html_e( ( isset( $xswpph['_xswpph_all_hidden'] ) && ( $xswpph['_xswpph_all_hidden'] != '' ) ) ? 'Checked' : '' ); ?> />
		<label><?php esc_html_e( 'All Check/Uncheck', 'xswpph-domain' ); ?></label>
	</h3>
	<input class="xswpph-post" type="checkbox" name="xswpph[_xswpph_front_page]" value="Front Page" <?php esc_html_e( ( isset( $xswpph['_xswpph_front_page'] ) && ( $xswpph['_xswpph_front_page'] != '' ) ) ? 'Checked' : '' ); ?> />
	<label>
		<?php esc_html_e( 'Hide from Front Page', 'xswpph-domain' ); ?>
	</label><br>
	<input class="xswpph-post" type="checkbox" name="xswpph[_xswpph_blog_page]" value="Blog Page" <?php esc_html_e( ( isset( $xswpph['_xswpph_blog_page'] ) && ( $xswpph['_xswpph_blog_page'] != '' ) ) ? 'Checked' : '' ); ?> />
	<label>
		<?php esc_html_e( 'Hide from Blog Page', 'xswpph-domain' ); ?>
	</label><br>
	<input class="xswpph-post" type="checkbox" name="xswpph[_xswpph_category_page]" value="Category Pages" <?php esc_html_e( ( isset( $xswpph['_xswpph_category_page'] ) && ( $xswpph['_xswpph_category_page'] != '' ) ) ? 'Checked' : '' ); ?> />
	<label>
		<?php esc_html_e( 'Hide from Category Pages', 'xswpph-domain' ); ?>
	</label><br>
	<input class="xswpph-post" type="checkbox" name="xswpph[_xswpph_tag_page]" value="Tag Pages" <?php esc_html_e( ( isset( $xswpph['_xswpph_tag_page'] ) && ( $xswpph['_xswpph_tag_page'] != '' ) ) ? 'Checked' : '' ); ?> />
	<label>
		<?php esc_html_e( 'Hide from Tag Pages', 'xswpph-domain' ); ?>
	</label><br>
	<input class="xswpph-post" type="checkbox" name="xswpph[_xswpph_author]" value="Author Pages" <?php esc_html_e( ( isset( $xswpph['_xswpph_author'] ) && ( $xswpph['_xswpph_author'] != '' ) ) ? 'Checked' : '' ); ?> />
	<label>
		<?php esc_html_e( 'Hide from Author Pages', 'xswpph-domain' ); ?>
	</label><br>
	<input class="xswpph-post" type="checkbox" name="xswpph[_xswpph_archive]" value="Date Archive" <?php esc_html_e( ( isset( $xswpph['_xswpph_archive'] ) && ( $xswpph['_xswpph_archive'] != '' ) ) ? 'Checked' : '' ); ?> />
	<label>
		<?php esc_html_e( 'Hide from Date Archive Pages', 'xswpph-domain' ); ?>
	</label><br>
	<input class="xswpph-post" type="checkbox" name="xswpph[_xswpph_search]" value="Search Results" <?php esc_html_e( ( isset( $xswpph['_xswpph_search'] ) && ( $xswpph['_xswpph_search'] != '' ) ) ? 'Checked' : '' ); ?> />
	<label>
		<?php esc_html_e( 'Hide from Search Pages', 'xswpph-domain' ); ?>
	</label><br>
	<input class="xswpph-post" type="checkbox" name="xswpph[_xswpph_feeds]" value="Feeds" <?php esc_html_e( ( isset( $xswpph['_xswpph_feeds'] ) && ( $xswpph['_xswpph_feeds'] != '' ) ) ? 'Checked' : '' ); ?> />
	<label>
		<?php esc_html_e( 'Hide from Feeds', 'xswpph-domain' ); ?>
	</label><br>
	<input class="xswpph-post" type="checkbox" name="xswpph[_xswpph_recent]" value="Recent Post" <?php esc_html_e( ( isset( $xswpph['_xswpph_recent'] ) && ( $xswpph['_xswpph_recent'] != '' ) ) ? 'Checked' : '' ); ?> />
	<label>
		<?php esc_html_e( 'Hide from Recent Post', 'xswpph-domain' ); ?>
	</label><br>
	<input class="xswpph-post" type="checkbox" name="xswpph[_xswpph_rel_link]" value="Meta rel link" <?php esc_html_e( ( isset( $xswpph['_xswpph_rel_link'] ) && ( $xswpph['_xswpph_rel_link'] != '' ) ) ? 'Checked' : '' ); ?> />
	<label>
		<?php esc_html_e( 'Hide from Next and previous rel link', 'xswpph-domain' ); ?>
	</label><br>
	<input class="xswpph-post" type="checkbox" name="xswpph[_xswpph_rest_api]" value="REST API" <?php esc_html_e( ( isset( $xswpph['_xswpph_rest_api'] ) && ( $xswpph['_xswpph_rest_api'] != '' ) ) ? 'Checked' : '' ); ?> />
	<label>
		<?php esc_html_e( 'Hide from REST API', 'xswpph-domain' ); ?>
	</label><br>
	<?php
}
?>