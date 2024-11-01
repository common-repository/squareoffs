<?php
/**
 * Form part for comment moderation settings.
 *
 * @package squareoffs
 */

?>

<fieldset class="squareoffs-form-section">

	<legend><?php esc_html_e( 'Comment Moderation', 'squareoffs' ); ?></legend>

	<p><?php esc_html_e( 'Manage your comment moderation settings.', 'squareoffs' ); ?></p>

	<div class="squareoffs-form-row">

		<div class="squareoffs-form-row-label"><?php esc_html_e( 'Comment moderation settings', 'squareoffs' ); ?></div>

		<div class="squareoffs-form-row-main">

			<div class="squareoffs-form-checkbox">
				<label>
					<input name="squareoffs-settings[comment-moderation][all]" type="checkbox" <?php checked( $data['comment-moderation']['all'] ); ?> />
					<?php esc_html_e( 'Require approval for all new comments', 'squareoffs' ); ?>
				</label>
			</div>

			<div class="squareoffs-form-checkbox">
				<label>
					<input name="squareoffs-settings[comment-moderation][censored]" type="checkbox" <?php checked( $data['comment-moderation']['censored'] ); ?> />
					<?php esc_html_e( 'Require approval for comments containing censored words', 'squareoffs' ); ?>
				</label>
			</div>

			<div class="squareoffs-form-checkbox">

				<label>
					<input name="squareoffs-settings[comment-moderation][flagged]" type="checkbox" <?php checked( $data['comment-moderation']['flagged'] ); ?> />
					<?php esc_html_e( 'Require approval for comments', 'squareoffs' ); ?>
				</label>

				<label for="squareoffs-comment-moderation-users"><?php esc_html_e( 'flagged by', 'squareoffs' ); ?></label>

				<select name="squareoffs-settings[comment-moderation][flagged-users]" id="squareoffs-comment-moderation-users">
					<?php for ( $i = 1; $i <= 10; $i++ ) : ?>
						<option value="<?php echo absint( $i ); ?>" <?php selected( $data['comment-moderation']['flagged-users'], $i ); ?>>
							<?php
							// translators: Number of users.
							printf( esc_html( _n( '%d user', '%d users', $i, 'squareoffs' ) ), absint( $i ) );
							?>
						</option>
					<?php endfor; ?>
				</select>
			</div>
		</div>
	</div>
</fieldset>
