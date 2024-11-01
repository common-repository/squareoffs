<?php
/**
 * Settings page template
 *
 * @package squareoffs
 */

?>
<style>
	.col-70{
		width: 65%;
		display: inline-block;
	}
	.col-30{
		width: 30%;
		display: inline-block;
		vertical-align: top;
		padding-top: 47px;
		padding-left: 15px;
	}

	.col-30 iframe{
		width: 100%;
		max-width: 350px;
	}

	@media only screen and (max-width: 1075px) {
		.col-70, .col-30{
			width: 100%;
		}
	}
</style>
<div class="wrap">
	<div class="col-70">
		<h1><?php esc_html_e( 'SquareOffs Settings', 'squareoffs' ); ?></h1>

		<?php

		settings_fields( 'squareoffs_options_section' );
		$squareoffs_api = squareoffs_get_api();

		if(!$squareoffs_api) $api_connected = false;
        else $api_connected = $squareoffs_api->get_user_profile();

        if(is_wp_error($api_connected)) $api_connected = false;
        else{
            $api_connected = true;
        }


		if ( ! $squareoffs_api || ! $api_connected ) {
			require_once( SQUAREOFFS_PLUGIN_PATH . 'php/templates/admin-settings-account.php' );
		} else {
			require_once( SQUAREOFFS_PLUGIN_PATH . 'php/templates/admin-settings-connected.php' );
		}

		?>
	</div>
	<div class="col-30">
		<h2>Using the SquareOffs Plugin in WordPress: A Walkthrough</h2>
		<iframe src="https://www.youtube.com/embed/P9SJWUdoSNs" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
	</div>
</div>
