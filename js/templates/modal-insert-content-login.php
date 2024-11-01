<?php
/**
* Template for Login modal content.
*
* Allows selecting between create or insert exsisting.
*
* @package squareoffs
*/

?>
<fieldset class="squareoffs-form-section">

	<legend>Connect your SquareOffs account</legend>
	<div id="notif"></div>
	<div id="logincont">
		<div class="squareoffs-form-row">
			<label class="squareoffs-form-row-label" for="soEmail">Email</label>
			<div class="squareoffs-form-row-main">
				<input name="soEmail" id="soEmail" type="email" class="regular-text">
			</div>
		</div>
		<div class="squareoffs-form-row">
			<label class="squareoffs-form-row-label" for="soPassword">Password</label>
			<div class="squareoffs-form-row-main">
				<input name="soPassword" id="soPassword" type="password" class="regular-text">
			</div>
		</div>
		<div class="squareoffs-form-row">
			<button type="button" class="button button-primary button-large squareoffs-select-loginSubmit">Connect your account</button>
			<button type="button" class="button button-large squareoffs-select-loginCancel">Cancel</button>
		</div>
	</div>
</fieldset>