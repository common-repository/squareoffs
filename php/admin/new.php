<?php
/**
 * SquareOffs add new or edit existing.
 *
 * @package squareoffs
 */

?>

<div class="wrap">

	<h1><?php esc_html_e( 'Add New SquareOff', 'squareoffs' ); ?></h1>

	<?php

	squareoffs_display_messages();

	require_once( SQUAREOFFS_PLUGIN_PATH . 'php/templates/form-squareoff.php' );

	?>

</div>
