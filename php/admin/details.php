<?php
/**
 * SquareOffs view squareOff.
 *
 * @package squareoffs
 */

$uuid      = isset( $_GET['id'] ) ? sanitize_text_field( wp_unslash( $_GET['id'] ) ) : ''; // WPCS: CSRF ok.
$squareoffs_api    = squareoffs_get_api();
$squareoff = $squareoffs_api->get_squareoff( $uuid );

?>

<div class="wrap">
	<?php require_once( SQUAREOFFS_PLUGIN_PATH . 'php/templates/squareoff-details.php' ); ?>
</div>

<?php
