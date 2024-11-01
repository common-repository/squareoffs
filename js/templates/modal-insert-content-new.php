<?php
/**
 * Edit/Create SquareOff form template.
 *
 * @package squareoffs
 */

?>

<fieldset class="squareoffs-form-section">

	<legend><?php esc_html_e( 'What is your question?' , 'squareoffs' ); ?></legend>

	<div class="squareoffs-form-row">
		<label for="squareoffs-new-question-{{ data.cid }}" class="squareoffs-form-row-label"><?php esc_html_e( 'Cover Photo' , 'squareoffs' ); ?></label>
		<div class="squareoffs-form-row-main">
			<div class="squareoffs-form-image-preview" id="cover"></div>
			<div class="squareoffs-form-image-actions">
				<button class="button squareoffs-image" id="squareoffs-image-cover-{{ data.cid }}" type="button"><?php esc_html_e( 'Add / Edit a photo (max. 5Mb)' , 'squareoffs' ); ?></button>
				<input id="squareoffs-image-cover-{{ data.cid }}" type="hidden" name="cover_photo" />
			</div>
		</div>
	</div>

	<div class="squareoffs-form-row">
		<label for="squareoffs-new-question-{{ data.cid }}" class="squareoffs-form-row-label"><?php esc_html_e( 'Your question' , 'squareoffs' ); ?> (<?php esc_html_e( 'required' , 'squareoffs' ); ?>)</label>
		<div class="squareoffs-form-row-main">
			<label class="soInputText"><textarea maxlength="100" id="squareoffs-new-question-{{ data.cid }}" name="question" class="regular-text" aria-describedby="squareoffs-new-question-descr-{{ data.cid }}" required="required"></textarea><span>100/100</span></label>
			<p class="description" id="squareoffs-new-question-descr-{{ data.cid }}">
				<?php printf( 'Up to 100 characters<br />Examples: Which style is better for spring?<br />Who will have more passing yards tonight?<br />Who\'s in the right?' , 'squareoffs' ); ?>
			</p>
		</div>
	</div>
</fieldset>

<fieldset class="squareoffs-form-section">

	<legend><?php esc_html_e( 'What two answers will voters choose from?' , 'squareoffs' ); ?></legend>

	<div class="squareoffs-form-row">
		<label for="squareoffs-new-answer-1" class="squareoffs-form-row-label"><?php esc_html_e( 'Side 1 Answer' , 'squareoffs' ); ?> (<?php esc_html_e( 'required' , 'squareoffs' ); ?>)</label>
		<div class="squareoffs-form-row-main">
			<label class="soInputText"><input maxlength="40" type="text" id="squareoffs-new-answer-1-{{ data.cid }}" name="side_1_title" aria-describedby="squareoffs-new-answer-1-descr-{{ data.cid }}" class="regular-text" required="required" /><span></span></label>
			<p class="description" id="squareoffs-new-answer-1-descr-{{ data.cid }}">
				<?php printf( 'Up to 40 characters' , 'squareoffs' ); ?>
			</p>
		</div>
	</div>

	<div class="squareoffs-form-row">
		<label for="squareoffs-new-answer-2-{{ data.cid }}" class="squareoffs-form-row-label"><?php esc_html_e( 'Side 2 Answer' , 'squareoffs' ); ?> (<?php esc_html_e( 'required' , 'squareoffs' ); ?>)</label>
		<div class="squareoffs-form-row-main">
			<label class="soInputText"><input maxlength="40" type="text" id="squareoffs-new-answer-2-{{ data.cid }}" name="side_2_title" aria-describedby="squareoffs-new-answer-2-descr-{{ data.cid }}" class="regular-text" required="required" /><span></span></label>
			<p class="description" id="squareoffs-new-answer-2-descr-{{ data.cid }}">
				<?php printf( 'Up to 40 characters' , 'squareoffs' ); ?>
			</p>
		</div>
	</div>

</fieldset>

<fieldset class="squareoffs-form-section">

	<legend><?php esc_html_e( 'Your defense.' , 'squareoffs' ); ?></legend>

	<div class="squareoffs-form-row">
		<label for="squareoffs-new-defend-1-{{ data.cid }}" class="squareoffs-form-row-label"><?php esc_html_e( 'Defend side 1' , 'squareoffs' ); ?></label>
		<div class="squareoffs-form-row-main">
			<label class="soInputText"><textarea maxlength="600" id="squareoffs-new-defend-1-{{ data.cid }}" name="side_1_defense" placeholder="Optional" class="regular-text" aria-describedby="squareoffs-new-defend-1-{{ data.cid }}-desc"></textarea><span></span></label>
			<p class="description desc squareoffs-new-defend-1-desc" id="squareoffs-new-defend-1-{{ data.cid }}-desc"></p>
		</div>
	</div>

	<div class="squareoffs-form-row">
		<div class="squareoffs-form-row-label"></div>
		<div class="squareoffs-form-row-main">
			<div class="squareoffs-form-image-preview"></div>
			<div class="squareoffs-form-image-actions">
				<button class="button squareoffs-image" id="squareoffs-image-side-1-{{ data.cid }}" type="button"><?php esc_html_e( 'Add / Edit a photo for side 1 (max. 5Mb)' , 'squareoffs' ); ?></button>
				<input id="squareoffs-url-side-1-{{ data.cid }}" type="hidden" name="side_1_photo" />
			</div>
		</div>
	</div>

	<div class="squareoffs-form-row">
		<label for="squareoffs-new-defend-2-{{ data.cid }}" class="squareoffs-form-row-label"><?php esc_html_e( 'Defend side 2' , 'squareoffs' ); ?></label>
		<div class="squareoffs-form-row-main">
			<label class="soInputText"><textarea maxlength="600" id="squareoffs-new-defend-2-{{ data.cid }}" name="side_2_defense" placeholder="Optional" class="regular-text" aria-describedby="squareoffs-new-defend-2-{{ data.cid }}-desc"></textarea><span></span></label>
			<p class="description desc squareoffs-new-defend-2-desc" id="squareoffs-new-defend-2-{{ data.cid }}-desc"></p>
		</div>
	</div>

	<div class="squareoffs-form-row">
		<div class="squareoffs-form-row-label"></div>
		<div class="squareoffs-form-row-main">
			<div class="squareoffs-form-image-preview"></div>
			<div class="squareoffs-form-image-actions">
				<button class="button squareoffs-image" id="squareoffs-image-side-2-{{ data.cid }}" type="button"><?php esc_html_e( 'Add / Edit a photo for side 2 (max. 5MB)' , 'squareoffs' ); ?></button>
				<input id="squareoffs-url-side-2-{{ data.cid }}" type="hidden" name="side_2_photo" />
			</div>
		</div>
	</div>

</fieldset>

<fieldset class="squareoffs-form-section">

	<legend><?php esc_html_e( 'Details' , 'squareoffs' ); ?></legend>

	<div class="squareoffs-form-row">
		<div class="squareoffs-form-row-label"><?php esc_html_e( 'End Date' , 'squareoffs' ); ?></div>
		<div class="squareoffs-form-row-main">
			<fieldset>
				<legend class="screen-reader-text"><?php esc_html_e( 'End Date' , 'squareoffs' ); ?></legend>
				<?php squareoffs_render_date_field( 'end_date', null, 'squareoffs-new-end-date-{{ data.cid }}' ); ?>
			</fieldset>
		</div>
	</div>

	<div class="squareoffs-form-row">
		<label for="squareoffs-new-categor-{{ data.cid }}" class="squareoffs-form-row-label"><?php esc_html_e( 'Category' , 'squareoffs' ); ?></label>
		<div class="squareoffs-form-row-main">
			<select id="squareoffs-new-category-{{ data.cid }}" name="category_uuid" class="regular-text">
				<option value="">Please Choose</option>
				<# _.each( data.categories, function( category ) { #>
					<option value="{{ category.uuid }}">{{ category.name }}</option>
				<# }); #>
			</select>
		</div>
	</div>

	<div class="squareoffs-form-row">
		<label for="squareoffs-new-tags-{{ data.cid }}" class="squareoffs-form-row-label"><?php esc_html_e( 'Tags' , 'squareoffs' ); ?></label>
		<div class="squareoffs-form-row-main">
			<textarea id="squareoffs-new-tags-{{ data.cid }}" name="tag_list" class="regular-text" aria-describedby="squareoffs-new-tags-descr-{{ data.cid }}" ></textarea>
			<p class="description" id="squareoffs-new-tags-descr-{{ data.cid }}"><?php esc_html_e( 'Comma separated' , 'squareoffs' ); ?></p>
		</div>
	</div>

	<div class="squareoffs-form-row">
		<label for="squareoffs-new-size-{{ data.cid }}" class="squareoffs-form-row-label"><?php esc_html_e( 'Size to insert' , 'squareoffs' ); ?></label>
	</div>
	<div class="squareoffs-form-row squareoffs-cols">
		<div><label for="squareoffs-new-size-small-{{ data.cid }}"><img src="<?php echo plugin_dir_url( __FILE__ ).'../../images/small.jpg'; ?>"/></label><label for="squareoffs-new-size-small-{{ data.cid }}"><input id="squareoffs-new-size-small-{{ data.cid }}" type="radio" name="size" value="small" <# if ( 'small' === data.selectedSize ) { #>checked="checked"<# } #>/><?php esc_html_e( 'Small' , 'squareoffs' ); ?></label></div>

		<div><label for="squareoffs-new-size-medium-{{ data.cid }}"><img src="<?php echo plugin_dir_url( __FILE__ ).'../../images/medium.jpg'; ?>"/></label><label for="squareoffs-new-size-medium-{{ data.cid }}"><input id="squareoffs-new-size-medium-{{ data.cid }}" type="radio" name="size" value="medium" <# if ( 'medium' === data.selectedSize ) { #>checked="checked"<# } #>/><?php esc_html_e( 'Medium' , 'squareoffs' ); ?></label></div>

		<div><label for="squareoffs-new-size-wide-{{ data.cid }}"><img src="<?php echo plugin_dir_url( __FILE__ ).'../../images/wide.jpg'; ?>"/></label><label for="squareoffs-new-size-wide-{{ data.cid }}"><input id="squareoffs-new-size-wide-{{ data.cid }}" type="radio" name="size" value="wide" checked="checked" <# if ( 'wide' === data.selectedSize ) { #>checked="checked"<# } #>/><?php esc_html_e( 'Wide' , 'squareoffs' ); ?></label></div>
	</div>
	<p><i style="font-size: 13px;font-style: normal!important;">Small embed does not display the cover photo. By default, the cover photo will be displayed in all other embed sizes, Feeds, and SquareOffs.com.</i></p>
	<div class="squareoffs-form-row">
		<label for="squareoffs-new-size-{{ data.cid }}" class="squareoffs-form-row-label"><?php esc_html_e( 'Margin' , 'squareoffs' ); ?></label>
	</div>
	<div class="soSpacing">
		<div><input type="text" name='marginTop' class="soIMargin" value="{{ data.marginTop }}"/></div>
		<div>
			<input type="text" name='marginLeft' class="soIMargin" value="{{ data.marginLeft }}"/>
			<input type="text" name='marginRight' class="soIMargin" value="{{ data.marginRight }}"/>
		</div>
		<div><input type="text" name='marginBottom' class="soIMargin" value="{{ data.marginBottom }}"/></div>
	</div>

</fieldset>

<div class="media-frame-toolbar media-toolbar">
	<div class="media-toolbar-primary squareoffs-form-controls">
		<span class="spinner squareoffs-loading"></span>
		<p class="squareoffs-form-error notice error inline"><?php esc_html_e( 'Error: Please fill out all required fields (you can use the Back buttons)' , 'squareoffs' ); ?></p>
		<button class="squareoffs-form-back button"><?php esc_html_e( 'Back' , 'squareoffs' ); ?></button>
		<button class="squareoffs-form-next button button-primary"><?php esc_html_e( 'Next' , 'squareoffs' ); ?></button>
		<button type="button" class="button media-button button-primary button-large modal-submit" disabled="disabled">{{ data.buttonSubmitText }}</button>
	</div>
</div>
