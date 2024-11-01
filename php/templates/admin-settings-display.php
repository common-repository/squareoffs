<?php
/**
 * Form part for brand/display settings.
 *
 * @package squareoffs
 */

?>

<fieldset class="squareoffs-form-section">

	<legend aria-describedby="squareoffs-display-description"><?php esc_html_e( 'Display settings', 'squareoffs' ); ?></legend>

	<p id="squareoffs-display-description"><?php esc_html_e( 'Customize what your SquareOffs will look like when embedded on your site.', 'squareoffs' ); ?></p>

	<div class="squareoffs-form-row">

		<label for="squareoffs-settings-display-color-1" class="squareoffs-form-row-label">
			<?php esc_html_e( 'Side 1 Color', 'squareoffs' ); ?>
		</label>

		<div class="squareoffs-form-row-main">
			<input
				id="squareoffs-settings-display-color-1"
				name="squareoffs-settings[display][color-1]"
				type="text"
				class="code squareoffs-color-picker"
				value="<?php echo esc_attr( $data['display']['color-1'] ); ?>"
			/>
		</div>

	</div>

	<div class="squareoffs-form-row">

		<label for="squareoffs-settings-display-color-2" class="squareoffs-form-row-label">
			<?php esc_html_e( 'Side 2 Color', 'squareoffs' ); ?>
		</label>

		<div class="squareoffs-form-row-main">
			<input
				id="squareoffs-settings-display-color-2"
				name="squareoffs-settings[display][color-2]"
				type="text"
				class="code squareoffs-color-picker"
				value="<?php echo esc_attr( $data['display']['color-2'] ); ?>"
			/>
		</div>

	</div>

</fieldset>
