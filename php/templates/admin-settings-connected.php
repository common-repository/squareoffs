<?php
/**
 * Template for managing settings once connected.
 *
 * @package squareoffs
 */

?>
<form class="squareoffs-form-wide" action="<?php echo esc_url( add_query_arg( array() ) ); ?>" method="POST">

	<?php
	$user_id = $squareoffs_api->get_credential( 'uuid' );
	$data    = squareoffs_get_user_profile_data( $user_id );
	$data    = squareoffs_sanitize_settings( $data );

	require_once( SQUAREOFFS_PLUGIN_PATH . 'php/templates/admin-settings-user.php' );
	require_once( SQUAREOFFS_PLUGIN_PATH . 'php/templates/admin-settings-comment-moderation.php' );
	require_once( SQUAREOFFS_PLUGIN_PATH . 'php/templates/admin-settings-display.php' );
	wp_nonce_field( 'squareoffs_settings', 'squareoffs-settings-nonce' );

	?>

	<button class="button button-primary" type="submit"><?php esc_html_e( 'Update Settings', 'squareoffs' ); ?></button>

</form>
