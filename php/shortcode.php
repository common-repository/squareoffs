<?php
/**
 * SquareOffs iframe shortcode
 *
 * @package squareoffs
 */

/**
 * Add iframe shortcode
 *
 * Shortcode is [squareoffs_embed]
 * Attributes:
 * size: size of the iframe; valid values: wide, small
 * id: id of the squareoffs poll
 * title: title attribute for the iframe (poll question)
 *
 * @param array $atts shortcode attributes.
 *
 * @return string
 */
function squareoffs_iframe_shortcode( $atts ) {

	$defaults = array(
		'size'  => 'small',
		'id'    => '',
		'align'    => 'left',
		'title' => __( 'SquareOffs Poll', 'squareoffs' ),
	);

	$atts = shortcode_atts( $defaults, $atts, 'squareoffs_embed' );

	if ( empty( $atts['id'] ) ) {
		return '';
	}

	// $style  = '';

	// if ( 'wide' === $atts['size'] ) {

	// 	$width  = '100%';
	// 	$height = '480px';
	// 	$style  = 'max-height: 700px;';

	// } elseif('medium' == $atts["size"]) {

	// 	$width  = '300px';
	// 	$height = '362x';
	// 	$style  = 'max-height: 362px;';
	// } else {

	// 	$width  = '300px';
	// 	$height = '250px';
	// 	$style  = 'max-height: 250px;';
	// }

	// if ( 'left' == $atts['align'] ) {
	// 	$style  .= 'max-width:'.$width.';float: left;  margin: 0px 0px 0px 0px;';
	// }elseif ( 'center' == $atts['align'] ) {
	// 	$style  .= 'max-width:'.$width.';margin: 0 auto; display: block;';
	// }elseif ( 'right' == $atts['align'] ) {
	// 	$style  .= 'max-width:'.$width.';float: right;  margin: 0px 0px 0px 0px;';
	// }

	
	// $src = sprintf( '//squareoffs.com/square_offs/%d/?size=%s',  (int) $atts['id'], esc_html($atts['size'] ) );

	ob_start();
	include( SQUAREOFFS_PLUGIN_PATH . '/php/templates/iframe.php' );
	return ob_get_clean();
}

add_shortcode( 'squareoffs_embed', 'squareoffs_iframe_shortcode' );
