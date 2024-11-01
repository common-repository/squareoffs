<?php
/**
 * Sanitization helper functions.
 *
 * Used for sanitizing more complex stuff, such as arrays of settings.
 *
 * @package squareoffs
 */

/**
 * Sanitize all settings sections.
 *
 * @param array $data Settings.
 * @return array Settings.
 */
function squareoffs_sanitize_settings( $data ) {

	$settings_sections = array(
		'account'            => array(),
		'comment-moderation' => array(),
		'display'            => array(),
	);

	$data = wp_parse_args( $data, $settings_sections );
	$data = array_intersect_key( $data, $settings_sections );

	$data['account'] = squareoffs_sanitize_settings_account( $data['account'] );
	$data['comment-moderation'] = squareoffs_sanitize_settings_comments( $data['comment-moderation'] );
	$data['display'] = squareoffs_sanitize_settings_display( $data['display'] );

	return $data;

}

/**
 * Sanitize account settings.
 *
 * @param array $data Settings.
 * @return array Settings.
 */
function squareoffs_sanitize_settings_account( $data ) {

	$defaults = array(
		'avatar'   => '',
		'name'     => '',
		'username' => '',
		'about'    => '',
		'email'    => '',
		'twitter'  => '',
	);

	$data = wp_parse_args( $data, $defaults );
	$data = array_intersect_key( $data, $defaults );

	$data['avatar']   = esc_url_raw( $data['avatar'] );
	$data['name']     = sanitize_text_field( $data['name'] );
	$data['username'] = sanitize_text_field( $data['username'] );
	$data['about']    = sanitize_text_field( $data['about'] );
	$data['email']    = sanitize_email( $data['email'] );
	$data['twitter']  = trim( sanitize_text_field( $data['twitter'] ), '@' );

	return $data;

}

/**
 * Sanitize display settings.
 *
 * @param array $data Settings.
 * @return array Settings.
 */
function squareoffs_sanitize_settings_display( $data ) {

	$defaults = array(
		'color-1' => '',
		'color-2' => '',
		'font'    => 'proxima_nova_regular',
	);

	$data = wp_parse_args( $data, $defaults );
	$data = array_intersect_key( $data, $defaults );

	// Sanitize color string, and ensure #.
	$data['color-1'] = '#' . trim( sanitize_text_field( $data['color-1'] ), '#' );
	$data['color-2'] = '#' . trim( sanitize_text_field( $data['color-2'] ), '#' );
	$data['font']    = sanitize_text_field( $data['font'] );

	return $data;
}

/**
 * Sanitize comment moderation settings.
 *
 * @param array $data Settings.
 * @return array Settings.
 */
function squareoffs_sanitize_settings_comments( $data ) {

	$defaults = array(
		'all'           => false,
		'censored'      => false,
		'flagged'       => false,
		'flagged-users' => 3,
	);

	$data = wp_parse_args( $data, $defaults );
	$data = array_intersect_key( $data, $defaults );

	$data['all']           = ! empty( $data['all'] );
	$data['censored']      = ! empty( $data['censored'] );
	$data['flagged']       = ! empty( $data['flagged'] );
	$data['flagged-users'] = absint( $data['flagged-users'] );

	return $data;
}

/**
 * Sanitize new account form data.
 *
 * @param  array $data Dirty data.
 * @return array $data Clean, whitelisted data.
 */
function squareoffs_options_sanitize_squareoffs_setting_account( $data ) {

	$defaults = array(
		'email'    => '',
		'token'    => '',
		'uuid'     => '',
	);

	$data = wp_parse_args( (array) $data, $defaults );
	$data = array_intersect_key( $data, $defaults );

	// Sanitize data.
	$data['email'] = sanitize_email( $data['email'] );
	$data['token'] = sanitize_text_field( $data['token'] );
	$data['uuid']  = sanitize_text_field( $data['uuid'] );

	return $data;
}

/**
 * Sanitize new account form data.
 *
 * @param  array $data Dirty data.
 * @return array $data Clean, whitelisted data.
 */
function squareoffs_options_sanitize_new_account( $data ) {

	$defaults = array(
		'name'       => '',
		'email'      => '',
		'password'   => '',
		'password-2' => '',
		't-and-cs'   => false,
	);

	$data = wp_parse_args( $data, $defaults );
	$data = array_intersect_key( $data, $defaults );

	$data['name']       = sanitize_text_field( $data['name'] );
	$data['email']      = sanitize_email( $data['email'] );
	$data['password']   = sanitize_text_field( $data['password'] );
	$data['password-2'] = sanitize_text_field( $data['password-2'] );
	$data['t-and-cs']   = (bool) $data['t-and-cs'];

	return $data;

}

/**
 * Sanitize connect account form data.
 *
 * @param  array $data Dirty data.
 * @return array $data Clean, whitelisted data.
 */
function squareoffs_options_sanitize_connect_account( $data ) {

	$defaults = array(
		'email'    => '',
		'password' => '',
	);

	$data = wp_parse_args( (array) $data, $defaults );
	$data = array_intersect_key( $data, $defaults );

	$data['email']    = sanitize_email( $data['email'] );
	$data['password'] = sanitize_text_field( $data['password'] );

	return $data;

}

/**
 * Sanitize data form input Squareoffs.
 *
 * @param array $data form input.
 * @return array sanitized data.
 */
function squareoffs_sanitize_squareoff( $data ) {

	$defaults = array(
		'uuid'             => '',
		'question'         => '',
		'side_1_title'     => '',
		'side_1_defense'   => '',
		'side_1_photo'     => '',
		'side_1_photo_url' => '',
		'side_2_title'     => '',
		'side_2_defense'   => '',
		'side_2_photo'     => '',
		'side_2_photo_url' => '',
		'end_date'         => null,
		'category_uuid'    => '',
		'tag_list'         => '',
		'cover_photo'      => '',
		'cover_photo_url'  => '',
	);

	$data = wp_parse_args( (array) $data, $defaults );
	$data = array_intersect_key( $data, $defaults );

	// Sanitize data.
	$data['question']         = esc_textarea( $data['question'] );
	$data['side_1_title']     = sanitize_text_field( $data['side_1_title'] );
	$data['side_1_defense']   = sanitize_text_field( $data['side_1_defense'] );
	$data['side_1_photo']     = sanitize_text_field( $data['side_1_photo'] );
	$data['side_1_photo_url'] = esc_url_raw( $data['side_1_photo_url'] );
	$data['side_2_title']     = sanitize_text_field( $data['side_2_title'] );
	$data['side_2_defense']   = sanitize_text_field( $data['side_2_defense'] );
	$data['side_2_photo']     = sanitize_text_field( $data['side_2_photo'] );
	$data['side_2_photo_url'] = esc_url_raw( $data['side_2_photo_url'] );
	$data['category_uuid']    = sanitize_text_field( $data['category_uuid'] );
	$data['tag_list']         = sanitize_text_field( $data['tag_list'] );
	$data['cover_photo']      = sanitize_text_field( $data['cover_photo'] );
	$data['cover_photo_url']  = esc_url_raw( $data['cover_photo_url'] );

	// Convert date.
	if ( ! empty( $data['end_date'] ) && ! empty( $data['end_date']['date'] ) ) {
		$data['end_date'] = squareoffs_convert_date( $data['end_date'] );
		unset( $date );
	} else {
		unset( $data['end_date'] );
	}

	if ( empty( $data['uuid'] ) ) {
		unset( $data['uuid'] );
	}

	return $data;

}
