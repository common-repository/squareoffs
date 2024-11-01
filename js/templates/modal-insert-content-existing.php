<?php
/**
 * Template for add existing squareoff modal content.
 *
 * @package squareoffs
 */

?>

<# if ( ! data.squareoff ) { #>
<div class="squareoffs-form-row">

	<label class="squareoffs-form-row-label" for="squareoffs-select-squareoff-{{ data.cid }}">SquareOff ID</label>

	<div class="squareoffs-form-row-main">
		<span class="spinner squareoffs-loading <# if ( data.loading ) { #>is-active<# } #>"></span>
		<input type="text" id="squareoffs-select-squareoff-{{ data.cid }}" class="squareoffs-input-id" aria-describedby="squareoffs-select-squareoff-{{ data.cid }}-description">
		<p id="squareoffs-select-squareoff-{{ data.cid }}-description" class="description desc">You can find the ID in the list of SquareOffs, in the last column</p>
	</div>
</div>
<# } #>

<div class="squareoffs-form-row">
	<label class="squareoffs-form-row-label" for="squareoffs-select-size-{{ data.cid }}">Size</label>
	<!-- div class="squareoffs-form-row-main">
		<select class="squareoffs-select-size regular-text" id="squareoffs-select-size-{{ data.cid }}">
			<option value="small" <# if ( 'small' === data.selectedSize ) { #>selected="selected"<# } #>>Small</option>
			<option value="medium" <# if ( 'medium' === data.selectedSize ) { #>selected="selected"<# } #>>Medium</option>
			<option value="wide" <# if ( 'wide' === data.selectedSize ) { #>selected="selected"<# } #>>Wide</option>
		</select>
	</div -->
</div>

<div class="squareoffs-form-row squareoffs-cols">
		<div><label for="squareoffs-new-size-small-{{ data.cid }}"><img src="<?php echo plugin_dir_url( __FILE__ ).'../../images/small.jpg'; ?>"/></label><label for="squareoffs-new-size-small-{{ data.cid }}"><input id="squareoffs-new-size-small-{{ data.cid }}" type="radio" name="size" value="small" <# if ( 'small' === data.selectedSize ) { #>checked="checked"<# } #>/><?php esc_html_e( 'Small' , 'squareoffs' ); ?></label></div>

		<div><label for="squareoffs-new-size-medium-{{ data.cid }}"><img src="<?php echo plugin_dir_url( __FILE__ ).'../../images/medium.jpg'; ?>"/></label><label for="squareoffs-new-size-medium-{{ data.cid }}"><input id="squareoffs-new-size-medium-{{ data.cid }}" type="radio" name="size" value="medium" <# if ( 'medium' === data.selectedSize ) { #>checked="checked"<# } #>/><?php esc_html_e( 'Medium' , 'squareoffs' ); ?></label></div>

		<div><label for="squareoffs-new-size-wide-{{ data.cid }}"><img src="<?php echo plugin_dir_url( __FILE__ ).'../../images/wide.jpg'; ?>"/></label><label for="squareoffs-new-size-wide-{{ data.cid }}"><input id="squareoffs-new-size-wide-{{ data.cid }}" type="radio" name="size" value="wide" <# if ( 'wide' === data.selectedSize ) { #>checked="checked"<# } #>/><?php esc_html_e( 'Wide' , 'squareoffs' ); ?></label></div>
	</div>
	<div class="squareoffs-form-row">
		<label for="squareoffs-new-size-{{ data.cid }}" class="squareoffs-form-row-label"><?php esc_html_e( 'Spacing' , 'squareoffs' ); ?></label>
	</div>
	<div class="soSpacing">
		<div><input type="text" name='marginTop' class="soIMargin" value="{{ data.marginTop }}"/></div>
		<div>
			<input type="text" name='marginLeft' class="soIMargin" value="{{ data.marginLeft }}"/>
			<input type="text" name='marginRight' class="soIMargin" value="{{ data.marginRight }}"/>
		</div>
		<div><input type="text" name='marginBottom' class="soIMargin" value="{{ data.marginBottom }}"/></div>
	</div>
	<p><i style="font-size: 13px;font-style: normal!important;">Small embed does not display the cover photo. By default, the cover photo will be displayed in all other embed sizes, Feeds, and SquareOffs.com.</i></p>
<div class="media-frame-toolbar media-toolbar">
	<div class="media-toolbar-primary squareoffs-form-controls">
		<button type="button" class="button media-button button-primary button-large modal-submit" disabled="disabled">{{ data.buttonSubmitText }}</button>
	</div>
</div>
