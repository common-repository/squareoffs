<?php
/**
 * Template for connecting an existing account
 *
 * @package squareoffs
 */

?>

<div class="squareoffs_setting_account_connect">

	<form method="POST" action="<?php echo esc_url( admin_url( 'admin.php?page=squareoffs-options' ) ); ?>">

		<?php wp_nonce_field( 'squareoffs_connect_account', 'squareoffs-connect-account-nonce' ); ?>

		<fieldset class="squareoffs-form-section">

			<legend><?php esc_html_e( 'Connect your SquareOffs account', 'squareoffs' ); ?></legend>

			<div class="squareoffs-form-row">
				<label class="squareoffs-form-row-label" for="squareoffs_setting_email"><?php esc_html_e( 'Email', 'squareoffs' ); ?></label>
				<div class="squareoffs-form-row-main">
					<input name="squareoffs_setting_account[email]" id="squareoffs_setting_email" type="email" class="regular-text" />
				</div>
			</div>
			<div class="squareoffs-form-row">
				<label class="squareoffs-form-row-label" for="squareoffs_setting_password"><?php esc_html_e( 'Password', 'squareoffs' ); ?></label>
				<div class="squareoffs-form-row-main">
					<input name="squareoffs_setting_account[password]" id="squareoffs_setting_password" type="password" class="regular-text" />
				</div>
			</div>

		</fieldset>

		<p class="submit">
			<input name="squareoffs_setting_account[connect]" type="hidden" value="true" readonly />
			<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_html_e( 'Connect your account', 'squareoffs' ); ?>">
		</p>

	</form>

</div>
