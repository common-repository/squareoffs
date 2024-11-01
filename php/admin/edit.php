<?php
/**
 * SquareOffs add new or edit existing.
 *
 * @package squareoffs
 */

global $squareoff;

$uuid      = isset( $_GET['id'] ) ? sanitize_text_field( wp_unslash( $_GET['id'] ) ) : ''; // WPCS: CSRF ok.
$squareoff = squareoffs_get_squareoff_data( $uuid );

?>

<div class="wrap">

	<h1><?php esc_html_e( 'Edit SquareOff', 'squareoffs' ); ?></h1>

	<?php

	squareoffs_display_messages();

	require_once( SQUAREOFFS_PLUGIN_PATH . 'php/templates/form-squareoff.php' );

	?>

</div>

<?php
