<?php
/**
 * Template for insert squareoff modal.
 *
 * @package squareoffs
 */

?>

<div class="media-modal wp-core-ui squareoffs-modal" tabindex="0" role="dialog" aria-labelledby="media-frame-title-{{ data.cid }}">
	<div class="media-modal-content">

		<button type="button" class="media-modal-close"><span class="media-modal-icon"><span class="screen-reader-text">{{ data.buttonCloseText }}</span></span></button>

		<div id="media-frame-title-{{ data.cid }}" class="media-frame-title"><h1>{{ data.title }}</div>

		<div class="media-frame-content"></div>

	</div>
</div>
<div class="media-modal-backdrop"></div>
