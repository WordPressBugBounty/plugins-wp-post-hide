<?php
/**
 * WP Post Hide Support page.
 *
 * @package WP Post Hide Setup.
 * @since 1.0.0
 */

// Exit if directly access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// phpcs:ignore
$selected_tab = isset( $_GET['tab'] ) ? $_GET['tab'] :  'report';
?>
<div class="warp">
	<div id="icon-options-general" class="icon32"></div>
	<h1>
		<?php esc_html_e( 'WP Post Hide', 'wp-post-hide' ); ?>
	</h1>
		<nav class="nav-tab-wrapper wp-clearfix" aria-label="Secondary menu">
			<a class="nav-tab <?php echo ( 'report' === $selected_tab ) ? 'nav-tab-active' : ''; ?>" href="?page=xswphp-support&tab=report" class="nav-tab">
				<?php esc_html_e( 'Report a bug', 'wp-post-hide' ); ?>
			</a>
			<a class="nav-tab <?php echo ( 'request' === $selected_tab ) ? 'nav-tab-active' : ''; ?>" href="?page=xswphp-support&tab=request" class="nav-tab">
				<?php esc_html_e( 'Request a Feature', 'wp-post-hide' ); ?>
			</a>
			<a class="nav-tab <?php echo ( 'hire' === $selected_tab ) ? 'nav-tab-active' : ''; ?>" href="?page=xswphp-support&tab=hire" class="nav-tab">
				<?php esc_html_e( 'Hire US', 'wp-post-hide' ); ?>
			</a>
			<a class="nav-tab <?php echo ( 'review' === $selected_tab ) ? 'nav-tab-active' : ''; ?>" href="?page=xswphp-support&tab=review" class="nav-tab">
				<?php esc_html_e( 'Review', 'wp-post-hide' ); ?>
			</a>

		</nav>
		<div class="tab-content">
			<?php
			switch ( $selected_tab ) {
				case 'request':
					?>
					<div class="xs-send-email-notice xswphp-top-margin">
						<p></p>
						<button type="button" class="notice-dismiss xs-notice-dismiss"><span class="screen-reader-text"><?php esc_html_e( 'Dismiss this notice.', 'wp-post-hide' ); ?></span></button>
					</div>
					<form method="post" class="xswphp_support_form">
						<input type="hidden" name="type" value="request">
						<table class="form-table">
							<tbody>
								<tr valign="top">
									<th>
										<label for='xswphp_name'><?php esc_html_e( 'Your Name:', 'wp-post-hide' ); ?></label>
									</th>
									<td>
										<input type="text" id="xswphp_name" name="xswphp_name" required>
									</td>
								</tr>
								<tr valign="top">
									<th>
										<label for="xswphp_email"><?php esc_html_e( 'Your Email:', 'wp-post-hide' ); ?></label>
									</th>
									<td>
										<input type="email" id="xswphp_email" name="xswphp_email" required>
									</td>
								</tr>
								<tr valign="top">
									<th>
										<label for="xswphp_message"><?php esc_html_e( 'Message:', 'wp-post-hide' ); ?></label>
									</th>
									<td>
										<textarea id="xswphp_message" name="xswphp_message" rows="12", cols="47" required></textarea>
									</td>
								</tr>
							</tbody>
						</table>
						<div class="input-group">
							<?php submit_button( __( 'Send', 'wp-post-hide' ), 'primary xswphp-send-mail' ); ?>
							<span class="spinner xswphp-mail-spinner"></span> 
						</div>
						
					</form>
					<?php
					break;
				case 'hire':
					?>
					<h2 class="xswphp-top-margin"><?php esc_html_e( 'Hire us for Customization and Development of WordPress Plugins and Themes.', 'wp-post-hide' ); ?></h2>
					<div class="xs-send-email-notice xswphp-top-margin">
						<p></p>
						<button type="button" class="notice-dismiss xs-notice-dismiss"><span class="screen-reader-text"><?php esc_html_e( 'Dismiss this notice.', 'wp-post-hide' ); ?></span></button>
					</div>
					<form method="post" class="xswphp_support_form">
						<input type="hidden" name="type" value="hire">
						<table class="form-table">
							<tbody>
								<tr valign="top">
									<th>
										<label for='xswphp_name'><?php esc_html_e( 'Your Name:', 'wp-post-hide' ); ?></label>
									</th>
									<td>
										<input type="text" id="xswphp_name" name="xswphp_name" required="required">
									</td>
								</tr>
								<tr valign="top">
									<th>
										<label for="xswphp_email"><?php esc_html_e( 'Your Email:', 'wp-post-hide' ); ?></label>
									</th>
									<td>
										<input type="email" id="xswphp_email" name="xswphp_email" required="required">
									</td>
								</tr>
								<tr valign="top">
									<th>
										<label for="xswphp_message"><?php esc_html_e( 'Message:', 'wp-post-hide' ); ?></label>
									</th>
									<td>
										<textarea id="xswphp_message" name="xswphp_message" rows="12", cols="47" required="required"></textarea>
									</td>
								</tr>
							</tbody>
						</table>
						<div class="input-group">
							<?php submit_button( __( 'Send', 'wp-post-hide' ), 'primary xswphp-send-mail' ); ?>
							<span class="spinner xswphp-mail-spinner"></span> 
						</div>
					</form>
					<?php
					break;
				case 'review':
					?>
					<p class="about-description xswphp-top-margin"><?php esc_html_e( 'If you like our plugin and support than kindly share your  ', 'wp-post-hide' ); ?> <a href="https://codecanyon.net/item/woocommerce-advanced-product-duplicator/22147932" target="_blank"> <?php esc_html_e( 'feedback', 'wp-post-hide' ); ?> </a><?php esc_html_e( 'Your feedback is valuable.', 'wp-post-hide' ); ?> </p>
					<?php
					break;
				default:
					?>
					<div class="xs-send-email-notice xswphp-top-margin">
						<p></p>
						<button type="button" class="notice-dismiss xs-notice-dismiss"><span class="screen-reader-text"><?php esc_html_e( 'Dismiss this notice.', 'wp-post-hide' ); ?></span></button>
					</div>
					<form method="post" class="xswphp_support_form">
						<input type="hidden" name="type" value="report">
						<table class="form-table">
							<tbody>
								<tr valign="top">
									<th>
										<label for='xswphp_name'><?php esc_html_e( 'Your Name:', 'wp-post-hide' ); ?></label>
									</th>
									<td>
										<input type="text" id="xswphp_name" name="xswphp_name" required="required">
									</td>
								</tr>
								<tr valign="top">
									<th>
										<label for="xswphp_email"><?php esc_html_e( 'Your Email:', 'wp-post-hide' ); ?></label>
									</th>
									<td>
										<input type="email" id="xswphp_email" name="xswphp_email" required="required">
									</td>
								</tr>
								<tr valign="top">
									<th>
										<label for="xswphp_message"><?php esc_html_e( 'Message:', 'wp-post-hide' ); ?></label>
									</th>
									<td>
										<textarea id="xswphp_message" name="xswphp_message" rows="12", cols="47" required="required"></textarea>
									</td>
								</tr>
							</tbody>
						</table>
						<div class="input-group">
							<?php submit_button( __( 'Send', 'wp-post-hide' ), 'primary xswphp-send-mail' ); ?>
							<span class="spinner xswphp-mail-spinner"></span> 
						</div>
						
					</form>
					<?php
					break;
			}
			?>
		</div>
</div>
