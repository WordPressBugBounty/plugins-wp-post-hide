<?php
/**
 * WP Post Hide Setting page.
 *
 * @package WP Post Hide Setup.
 * @since 1.0.0
 */

// Exit if directly access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Template of WordPress Hide Post.
 */
function xswphp_page() {
	?>
	<div class="warp">
		<div id="icon-options-general" class="icon32"></div>
		<h1>
			<?php esc_html_e( 'WP Post hide', 'wp-post-hide' ); ?>
			<a href="https://codecanyon.net/item/wordpress-hide-post/24141817" class="xs-pro-link xs-button-main" target="_blank">
				<span style="font-size: 18px; margin-right: 8px;"><img draggable="false" role="img" class="emoji" alt="🚀" src="<?php echo esc_url( XSWPHP_ROOT_URL ); ?>/assets/img/rocket.svg"></span>
				<?php esc_html_e( 'Get Pro', 'wp-post-hide' ); ?>
			</a>
		</h1>
		<?php settings_errors(); ?>
		<h2 class="nav-tab-wrapper">
			<a href="?page=xswphp_page&tab=settings" class="nav-tab">
				<?php esc_html_e( 'Settings', 'wp-post-hide' ); ?>
			</a>
		</h2>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'xswphp_options' );
			do_settings_sections( 'xswphp_options' );
			?>
			<div class="xswphp-col">
				<h3>
					<?php
					esc_html_e( 'Please Check the Post Types you include this functonality', 'wp-post-hide' );
					?>
				</h3>
				<table class="form-table xswphp-form">
					<tbody>
						<input type="checkbox" class="xswphp-posts">&nbsp;
						<?php
						esc_html_e( 'All Check/Uncheck', 'wp-post-hide' );
						$xswphp_posts = xswphp_getpost_types();
						foreach ( $xswphp_posts as $xswphp_key ) :
							?>
							<tr valign="top">
								<td>
									<input type="checkbox" name="xswphp_post_types[]" class="<?php echo ( 'attachment' !== $xswphp_key->name ) ? 'xswphp-post' : ''; ?>  "
									value="<?php echo esc_attr( $xswphp_key->name ); ?>"  <?php echo ( 'attachment' === $xswphp_key->name ) ? 'disabled' : ''; ?>  
									<?php xswphp_check_options( $xswphp_key->name, 'xswphp_post_types' ); ?>
									>&nbsp;<?php echo esc_html( $xswphp_key->label ); ?> <?php echo ( 'attachment' === $xswphp_key->name ) ? "<span class='pro'>" . esc_html__( ' (Pro)', 'wp-post-hide' ) . '</span>' : ''; ?>
									<?php if ( 'attachment' !== $xswphp_key->name ) { ?>
									<tr valign="top" class="xswphp-enables" id="<?php echo esc_html( $xswphp_key->name ); ?>">
										<td>

											&nbsp; &nbsp; &nbsp;

											<?php $xswphp_quick = $xswphp_key->name . 'quick_edit'; ?>

											<input type="checkbox" name="xswphp_enable[<?php echo esc_html( $xswphp_quick ); ?>]" value='<?php echo esc_html( $xswphp_quick ); ?>' disabled>

											<?php esc_html_e( 'Enable for Quick Edit', 'wp-post-hide' ); ?><span class='pro'><?php esc_html_e( ' (Pro)', 'wp-post-hide' ); ?></span>

											&nbsp; &nbsp; &nbsp;

											<?php $xswphp_bulk = $xswphp_key->name . 'bulk_edit'; ?>

											<input type="checkbox" name="xswphp_enable[<?php echo esc_html( $xswphp_bulk ); ?>]" value='<?php echo esc_html( $xswphp_bulk ); ?>' disabled>

												&nbsp;

											<?php esc_html_e( 'Enable for Bulk Edit', 'wp-post-hide' ); ?><span class='pro'><?php esc_html_e( ' (Pro)', 'wp-post-hide' ); ?></span>

										</td>

									</tr>
									<?php } ?>
								</td>

							</tr>

						<?php endforeach; ?>

					</tbody>

				</table>

			</div>

			<div class="xswphp-col">

				<table class="form-table">

					<tbody>

						<tr valign="top">

							<td>

								<h3>
									<?php esc_html_e( 'Additional Options', 'wp-post-hide' ); ?>
								</h3>
								<table class="form-table">
									<tbody>
										<tr valign="top">
											<th scope="row">
												<?php esc_html_e( 'Hidden Column Display', 'wp-post-hide' ); ?>
											</th>
											<td>
												<input type="checkbox" name="xswphp_enable[disable_hidden_column]" value="disable_hidden_column" 
												<?php
												$xswphp_enable = get_option( 'xswphp_enable', array() );
												if ( isset( $xswphp_enable['disable_hidden_column'] ) && $xswphp_enable['disable_hidden_column'] ) {
													echo 'checked';
												}
												?>
												/>
												<?php esc_html_e( 'Disable "Hidden" column in admin post lists', 'wp-post-hide' ); ?>
												<p class="description">
													<?php esc_html_e( 'Check this to hide the "Hidden" column from all post type admin lists.', 'wp-post-hide' ); ?>
												</p>
											</td>
										</tr>
									</tbody>
								</table>
								<?php submit_button(); ?>

							</td>

						</tr>

					</tbody>

				</table>

			</div>

		</form>

	</div>

	<?php
}

?>
