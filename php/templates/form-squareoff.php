<?php
/**
 * Edit/Create SquareOff form template.
 *
 * @package squareoffs
 */

$action = ! empty( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : ''; // WPCS: CSRF ok.

?>

<form class="squareoffs-form-wide" action="<?php echo esc_url( add_query_arg( array() ) ); ?>" method="POST">

	<?php if ( 'squareoffs-edit' === $action ) : ?>
		<?php wp_nonce_field( 'update-squareoff', 'squareoff-update' ); ?>
		<input type="hidden" name="squareoffs-new[uuid]" value="<?php squareoffs_render_form_data( 'uuid' ); ?>" />
	<?php else : ?>
		<?php wp_nonce_field( 'add-squareoff', 'squareoff-add-new' ); ?>
	<?php endif; ?>

	<fieldset class="squareoffs-form-section">

		<legend><?php esc_html_e( 'What is your question?' , 'squareoffs' ); ?></legend>

		<div class="squareoffs-form-row">
			<label for="squareoffs-new-question" class="squareoffs-form-row-label"><?php esc_html_e( 'Your question' , 'squareoffs' ); ?> (<?php esc_html_e( 'required' , 'squareoffs' ); ?>)</label>
			<div class="squareoffs-form-row-main">
				<textarea maxlength="100" id="squareoffs-new-question" name="squareoffs-new[question]" class="regular-text" aria-describedby="squareoffs-new-question-descr" required="required"><?php squareoffs_render_form_data( 'question' ); ?></textarea>
				<p class="description" id="squareoffs-new-question-descr">
					<?php
					printf( '%s<br />%s: <br />%s<br />%s<br />%s',
						esc_html__( 'Up to 100 characters' , 'squareoffs' ),
						esc_html__( 'Examples' , 'squareoffs' ),
						esc_html__( 'Who will have more passing yards tonight?' , 'squareoffs' ),
						esc_html__( 'What\'s your perfect vacation destination?' , 'squareoffs' ),
						esc_html__( 'Who gets your vote?' , 'squareoffs' )
					)
					?>
				</p>
			</div>
		</div>

	</fieldset>

	<fieldset class="squareoffs-form-section">

		<legend><?php esc_html_e( 'What two answers will voters choose from?' , 'squareoffs' ); ?></legend>

		<div class="squareoffs-form-row">
			<label for="squareoffs-new-answer-1" class="squareoffs-form-row-label"><?php esc_html_e( 'Side 1 Answer' , 'squareoffs' ); ?> (<?php esc_html_e( 'required' , 'squareoffs' ); ?>)</label>
			<div class="squareoffs-form-row-main">
				<input maxlength="20" type="text" id="squareoffs-new-answer-1" name="squareoffs-new[side_1_title]" aria-describedby="squareoffs-new-answer-1-descr" class="regular-text" required="required" value="<?php squareoffs_render_form_data( 'side_1_title' ); ?>"/>
				<p class="description" id="squareoffs-new-answer-1-descr">
					<?php esc_html_e( 'Up to 20 characters.' , 'squareoffs' ); ?>
				</p>
			</div>
		</div>

		<div class="squareoffs-form-row">
			<label for="squareoffs-new-defend-1" class="squareoffs-form-row-label"><?php esc_html_e( 'Defend side 1' , 'squareoffs' ); ?></label>
			<div class="squareoffs-form-row-main">
				<textarea maxlength="600" id="squareoffs-new-defend-1" name="squareoffs-new[side_1_defense]" placeholder="Optional" class="regular-text" aria-describedby="squareoffs-new-defend-descr"><?php squareoffs_render_form_data( 'side_1_defense' ); ?></textarea>
				<p class="description" id="squareoffs-new-defend-descr">
					<?php esc_html_e( 'Up to 600 characters. Provide some background info on each side, such as statistics, quotes, or relevant URLs to send your readers to.' , 'squareoffs' ); ?>
				</p>
			</div>
		</div>

		<div class="squareoffs-form-row">
			<div class="squareoffs-form-row-label"></div>
			<div class="squareoffs-form-row-main">

				<input id="squareoffs-url-side-1" type="hidden" name="squareoffs-new[side_1_photo]" />

				<div class="squareoffs-form-image-preview" id="squareoffs-image-side-1-preview"><?php squareoffs_render_form_photo( 'side_1_photo_url' ); ?></div>

				<div class="squareoffs-form-image-actions">
					<button class="button" id="squareoffs-image-side-1" type="button">
						<?php esc_html_e( 'Add / Edit a photo for side 1 (max. 5Mb)' , 'squareoffs' ); ?>
					</button>
				</div>

			</div>
		</div>

		<div class="squareoffs-form-row">
			<label for="squareoffs-new-answer-2" class="squareoffs-form-row-label"><?php esc_html_e( 'Side 2 Answer' , 'squareoffs' ); ?> (<?php esc_html_e( 'required' , 'squareoffs' ); ?>)</label>
			<div class="squareoffs-form-row-main">
				<input maxlength="20" type="text" id="squareoffs-new-answer-2" name="squareoffs-new[side_2_title]" class="regular-text" required="required" value="<?php squareoffs_render_form_data( 'side_2_title' ); ?>"/>
			</div>
		</div>

		<div class="squareoffs-form-row">
			<label for="squareoffs-new-defend-2" class="squareoffs-form-row-label"><?php esc_html_e( 'Defend side 2' , 'squareoffs' ); ?></label>
			<div class="squareoffs-form-row-main">
				<textarea maxlength="600" id="squareoffs-new-defend-2" name="squareoffs-new[side_2_defense]" placeholder="Optional" class="regular-text"><?php squareoffs_render_form_data( 'side_2_defense' ); ?></textarea>
			</div>
		</div>

		<div class="squareoffs-form-row">
			<div class="squareoffs-form-row-label"></div>
			<div class="squareoffs-form-row-main">

				<input id="squareoffs-url-side-2" type="hidden" name="squareoffs-new[side_2_photo]" />

				<div class="squareoffs-form-image-preview" id="squareoffs-image-side-2-preview"><?php squareoffs_render_form_photo( 'side_2_photo_url' ); ?></div>

				<div class="squareoffs-form-image-actions">
					<button class="button" id="squareoffs-image-side-2" type="button">
						<?php esc_html_e( 'Add / Edit a photo for side 2 (max. 5MB)' , 'squareoffs' ); ?>
					</button>
				</div>
			</div>
		</div>

	</fieldset>

	<fieldset class="squareoffs-form-section">

		<legend><?php esc_html_e( 'Details' , 'squareoffs' ); ?></legend>

		<div class="squareoffs-form-row">

			<div class="squareoffs-form-row-label"><?php esc_html_e( 'End date SquareOff' , 'squareoffs' ); ?></div>
			<div class="squareoffs-form-row-main">
				<fieldset>
					<legend class="screen-reader-text"><?php esc_html_e( 'End date SquareOff' , 'squareoffs' ); ?></legend>
					<?php squareoffs_render_end_date_field(); ?>
				</fieldset>
			</div>
		</div>

		<div class="squareoffs-form-row">
			<label for="squareoffs-new-category" class="squareoffs-form-row-label"><?php esc_html_e( 'Category' , 'squareoffs' ); ?></label>
			<div class="squareoffs-form-row-main">
				<select id="squareoffs-new-category" name="squareoffs-new[category_uuid]" class="regular-text">
					<?php squareoffs_render_category_options( 'category_uuid' ); ?>
				</select>
			</div>
		</div>

		<div class="squareoffs-form-row">
			<label for="squareoffs-new-tags" class="squareoffs-form-row-label"><?php esc_html_e( 'Tags' , 'squareoffs' ); ?></label>
			<div class="squareoffs-form-row-main">
				<textarea id="squareoffs-new-tags" name="squareoffs-new[tag_list]" class="regular-text" aria-describedby="squareoffs-new-tags-descr"><?php squareoffs_render_form_data( 'tag_list' ); ?></textarea>
				<p class="description" id="squareoffs-new-tags-descr"><?php esc_html_e( 'Comma separated' , 'squareoffs' ); ?></p>
			</div>
		</div>

		<div class="squareoffs-form-row">
			<div class="squareoffs-form-row-label"></div>
			<div class="squareoffs-form-row-main">

				<input id="squareoffs-url-cover" type="hidden" name="squareoffs-new[cover_photo]" />

				<div class="squareoffs-form-image-preview" id="squareoffs-url-cover-preview"><?php squareoffs_render_form_photo( 'cover_photo_url' ); ?></div>

				<div class="squareoffs-form-image-actions">
					<button class="button" id="squareoffs-image-cover" type="button">
						<?php esc_html_e( 'Add / Edit a cover photo (max. 5Mb)' , 'squareoffs' ); ?>
					</button>
				</div>

			</div>
		</div>

	</fieldset>

	<div class="squareoffs-form-section">
		<div class="squareoffs-form-row squareoffs-form-submit">
			<button type="submit" class="button button-primary"><?php squareoffs_render_submit_button(); ?></button>
		</div>
	</div>

</form>
