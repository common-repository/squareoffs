<?php
/**
 * Form part for brand/display settings.
 *
 * @package squareoffs
 */

?>

<fieldset class="squareoffs-form-section">

	<legend>
		<?php
		// Translators: Setttings page for current user.
		printf( esc_html__( 'SquareOffs Account settings for %s', 'squareoffs' ), esc_html( wp_get_current_user()->display_name ) );
		?>
	</legend>

	<div class="squareoffs-form-row">

		<label for="squareoffs-settings-account-avatar" class="squareoffs-form-row-label">
			<?php esc_html_e( 'SquareOffs Avatar', 'squareoffs' ); ?>
		</label>

		<div class="squareoffs-form-row-main">

			<input
				type="hidden"
				id="squareoffs-settings-account-avatar"
				name="squareoffs-settings[account][avatar]"
				value="//squareoffs.com<?php echo esc_url( $data['account']['avatar'] ); ?>"
			/>

			<div class="squareoffs-form-image-preview" id="squareoffs-settings-account-avatar-preview">
				<?php if ( ! empty( $data['account']['avatar'] ) ) : ?>
					<img
						src="<?php echo(strpos(esc_url($data['account']['avatar']), "http")==0?"":"//squareoffs.com")?><?php echo esc_url( $data['account']['avatar'] ); ?>"
						alt="<?php esc_attr_e( 'Your SquareOffs avatar', 'squareoffs' ); ?>"
						style="width:100px;"
					/>
				<?php endif; ?>
			</div>

		</div>

	</div>

	<div class="squareoffs-form-row">

		<label for="squareoffs-settings-account-name" class="squareoffs-form-row-label">
			<?php esc_html_e( 'Name', 'squareoffs' ); ?>
		</label>

		<div class="squareoffs-form-row-main">
			<input
				id="squareoffs-settings-account-name"
				name="squareoffs-settings[account][name]"
				type="text"
				class="regular-text"
				readonly
				value="<?php echo esc_attr( $data['account']['name'] ); ?>"
			/>
		</div>

	</div>

	<div class="squareoffs-form-row">

		<label for="squareoffs-settings-account-username" class="squareoffs-form-row-label">
			<?php esc_html_e( 'Username', 'squareoffs' ); ?>
		</label>

		<div class="squareoffs-form-row-main">
			<input
				id="squareoffs-settings-account-username"
				name="squareoffs-settings[account][username]"
				type="text"
				class="regular-text"
				readonly
				value="<?php echo esc_attr( $data['account']['username'] ); ?>"
			/>
		</div>

	</div>

	<div class="squareoffs-form-row">

		<label for="squareoffs-settings-account-email" class="squareoffs-form-row-label">
			<?php esc_html_e( 'Email', 'squareoffs' ); ?>
		</label>

		<div class="squareoffs-form-row-main">
			<input
				id="squareoffs-settings-account-email"
				name="squareoffs-settings[account][email]"
				type="text"
				class="regular-text"
				readonly
				value="<?php echo esc_attr( $data['account']['email'] ); ?>"
			/>
		</div>

	</div>

	<div class="squareoffs-form-row">

		<label for="squareoffs-settings-account-about" class="squareoffs-form-row-label">
			<?php esc_html_e( 'About me', 'squareoffs' ); ?>
		</label>

		<div class="squareoffs-form-row-main">
			<textarea
				id="squareoffs-settings-account-about"
				name="squareoffs-settings[account][about]"
				class="regular-text"
				aria-describedby="squareoffs-descr-about-me"
			><?php echo esc_textarea( $data['account']['about'] ); ?></textarea>
			<p id="squareoffs-descr-about-me"><?php esc_html_e( 'Provide a short description to be automatically included when your SquareOff is shared on Facebook.', 'squareoffs' ); ?></p>
		</div>

	</div>


	<div class="squareoffs-form-row">

		<label for="squareoffs-settings-account-twitter" class="squareoffs-form-row-label">
			<?php esc_html_e( 'Twitter handle', 'squareoffs' ); ?>
		</label>

		<div class="squareoffs-form-row-main">
			<input
				id="squareoffs-settings-account-twitter"
				name="squareoffs-settings[account][twitter]"
				type="text"
				class="regular-text"
				aria-describedby="squareoffs-descr-twitter"
				value="<?php echo esc_attr( $data['account']['twitter'] ); ?>"
			/>
			<p id="squareoffs-descr-twitter"><?php esc_html_e( 'Don\'t forget to add your handle so all social shares lead back to your profile!', 'squareoffs' ); ?></p>

		</div>

	</div>

	<p>
		<a
			href="<?php echo esc_url( wp_nonce_url( add_query_arg( array() ), 'squareoffs_disconnect', 'squareoffs-disconnect-nonce' ) ); ?>"
			class="button"
			aria-describedby="squareoffs-disconnect-description"
		>
			<?php esc_html_e( 'Disconnect', 'squareoffs' ); ?>
		</a>
		<span class="description" id="squareoffs-disconnect-description">
			<?php esc_html_e( 'Click this button if you wish to disconnect your SquareOffs account from your WordPress site', 'squareoffs' ); ?>
		</span>
	</p>


</fieldset>
