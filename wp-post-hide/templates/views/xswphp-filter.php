<?php
/**
 * WP Post Hide Filter page.
 *
 * @package WP Post Hide Setup
 * @since 1.0.0
 */

// Exit if directly access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
* Filter View of WordPress hide post
*/
?>
<span class="xswphp_select">
<select name="xswphp_hide[]">
	<option value="" >
		<?php esc_html_e( 'Select the option', 'wp-post-hide' ); ?>
	</option>
<?php if ( 'page' !== $post_type ) : ?>
	<option value="All Hidden" disabled>
		<?php esc_html_e( 'All Hidden', 'wp-post-hide' ); ?>
		<span class='pro'><?php esc_html_e( ' (Pro)', 'wp-post-hide' ); ?></span>
	</option>
	<option value="Front Page" disabled>
		<?php esc_html_e( 'Front Page', 'wp-post-hide' ); ?>
		<span class='pro'><?php esc_html_e( ' (Pro)', 'wp-post-hide' ); ?></span>
	</option>
	<option value="Category Pages" disabled>

		<?php esc_html_e( 'Category Pages', 'wp-post-hide' ); ?>
		<span class='pro'><?php esc_html_e( ' (Pro)', 'wp-post-hide' ); ?></span>

	</option>

	<option value="Tag Pages" disabled>

		<?php esc_html_e( 'Tag Pages', 'wp-post-hide' ); ?>
		<span class='pro'><?php esc_html_e( ' (Pro)', 'wp-post-hide' ); ?></span>

	</option>

	<option value="Author Pages" disabled>

		<?php esc_html_e( 'Author Pages', 'wp-post-hide' ); ?>
		<span class='pro'><?php esc_html_e( ' (Pro)', 'wp-post-hide' ); ?></span>

	</option>

	<option value="Date Archive" disabled>

		<?php esc_html_e( 'Date Archive', 'wp-post-hide' ); ?>
		<span class='pro'><?php esc_html_e( ' (Pro)', 'wp-post-hide' ); ?></span>

	</option>

	<option value="Search Results" disabled>

		<?php esc_html_e( 'Search Results', 'wp-post-hide' ); ?>
		<span class='pro'><?php esc_html_e( ' (Pro)', 'wp-post-hide' ); ?></span>

	</option>

	<option value="Feeds" disabled>

		<?php esc_html_e( 'Feeds', 'wp-post-hide' ); ?>
		<span class='pro'><?php esc_html_e( ' (Pro)', 'wp-post-hide' ); ?></span>

	</option>

	<option value="Recent Post" disabled>

		<?php esc_html_e( 'Recent Post', 'wp-post-hide' ); ?>
		<span class='pro'><?php esc_html_e( ' (Pro)', 'wp-post-hide' ); ?></span>

	</option>

	<option value="Meta rel link" disabled>

		<?php esc_html_e( 'Meta rel link', 'wp-post-hide' ); ?>
		<span class='pro'><?php esc_html_e( ' (Pro)', 'wp-post-hide' ); ?></span>

	</option>

<?php else : ?>

	<option value="Front Page" disabled>

		<?php esc_html_e( 'Front Page', 'wp-post-hide' ); ?>
		<span class='pro'><?php esc_html_e( ' (Pro)', 'wp-post-hide' ); ?></span>

	</option>

	<option value="Hide form pages list everywhere" disabled>

		<?php esc_html_e( 'Hide form pages list everywhere', 'wp-post-hide' ); ?>
		<span class='pro'><?php esc_html_e( ' (Pro)', 'wp-post-hide' ); ?></span>

	</option>

	<option value="Hide but keep in Search" disabled>

		<?php esc_html_e( 'Hide but keep in Search', 'wp-post-hide' ); ?>
		<span class='pro'><?php esc_html_e( ' (Pro)', 'wp-post-hide' ); ?></span>

	</option>

<?php endif; ?>

</select>
</span>
