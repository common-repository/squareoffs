<?php
/**
 * SquareOffs functions for adding and editing a SquareOff
 *
 * @package squareoffs
 */

add_action( 'admin_init', 'squareoffs_handle_create_new_squareoff' );

/**
 * Hook in on admin_init and handle saving a squareoff.
 */
function squareoffs_handle_create_new_squareoff() {

	global $squareoff;

	$squareoffs_api = squareoffs_get_api();

	if ( ! $squareoffs_api || ! $squareoffs_api->is_authenticated() ) {
		return;
	}

	if ( isset( $_POST['squareoff-add-new'] ) && isset( $_POST['squareoffs-new'] ) ) {

		check_admin_referer( 'add-squareoff', 'squareoff-add-new' );
		$squareoff = squareoffs_sanitize_squareoff( wp_unslash( $_POST['squareoffs-new'] ) ); // sanitization ok, validation ok.

		if ( squareoffs_validate_new_squareoff( $squareoff ) ) {
			return;
		}

		$squareoffs_api   = squareoffs_get_api();
		$response = $squareoffs_api->create_squareoff( $squareoff );

		if ( is_wp_error( $response ) ) {
			Squareoffs_Messages::get_instance()->add( $response->get_error_message() );
			return;
		} else {
			Squareoffs_Messages::get_instance()->add( __( 'SquareOff created successfully.', 'squareoffs' ), 'success' );
			wp_safe_redirect( admin_url( 'admin.php?page=squareoffs' ) );
			exit;
		}
	}

	if ( isset( $_POST['squareoff-update'] ) && isset( $_POST['squareoffs-new'] ) ) {

		check_admin_referer( 'update-squareoff', 'squareoff-update' );

		// $squareoff = squareoffs_sanitize_squareoff( wp_unslash( $_POST['squareoffs-new'] ) ); // WPCS: sanitization ok.
		$squareoff =  wp_unslash( $_POST['squareoffs-new'] );

		if ( squareoffs_validate_new_squareoff( $squareoff ) ) {
			return;
		}

		$response = $squareoffs_api->update_squareoff( $squareoff );

		if ( is_wp_error( $response ) ) {
			Squareoffs_Messages::get_instance()->add( $response->get_error_message() );
			return;
		} else {
			Squareoffs_Messages::get_instance()->add( __( 'SquareOff updated.', 'squareoffs' ), 'success' );
			wp_safe_redirect( admin_url( 'admin.php?page=squareoffs' ) );
			exit;
		}
	}

}

/**
 * Compose date from input
 *
 * Format = YYYY-MM-DDThh:mm:ssTZD "2016-11-13T19:28:08-06:00".
 *
 * @param array $data form input data.
 *
 * @return string
 */
function squareoffs_convert_date( $data ) {
	// Fallback, handle strings.
	if ( ! is_array( $data ) ) {
		return sanitize_text_field( $data );
	}

	$data = wp_parse_args( $data, array(
		'date' => '',
		'time' => '',
	) );

	$format = 'Y-m-d g:i A';

	// Get a GMT DateTime object, using WP built in functionality.
	$date = DateTime::createFromFormat(
		$format,
		get_gmt_from_date( $data['date'] . ' ' . $data['time'], $format ),
		new DateTimeZone( 'UTC' )
	);

	return $date->format( 'c' );
}

/**
 * Render end date for form.
 *
 * @param string $end_date end date to convert.
 *
 * @return string
 */
function squareoffs_render_date( $end_date ) {
	$date = new DateTime( $end_date );
	return $date->format( get_option( 'date_format' ) . ' @ H:i' );
}

/**
 * Fill options for select category and check selected.
 * Nonce verification is done in admin_init.
 *
 * @param string $select name of the select element.
 */
function squareoffs_render_category_options( $select ) {

	global $squareoff;

	$options = squareoffs_get_categories();

	if ( empty( $options ) ) {
		return;
	}

	$data = ! ( empty( $squareoff[ $select ] ) ) ? $squareoff[ $select ] : '';

	foreach ( $options as $option ) {
		printf(
			'<option value="%s" %s>%s</option>',
			esc_attr( $option->uuid ),
			selected( $data, $option->uuid, false ),
			esc_html( $option->name )
		);
	}

}

/**
 * Sanitize data form input Squareoffs
 *
 * @param array $data form data.
 *
 * @return bool validation error.
 */
function squareoffs_validate_new_squareoff( $data ) {

	$messages = Squareoffs_Messages::get_instance();
	$error    = false;

	if ( empty( $data['question'] ) ) {
		$messages->add( __( 'Please enter a question', 'squareoffs' ) );
		$error = true;
	}

	if ( empty( $data['side_1_title'] ) ) {
		$messages->add( __( 'Please enter an answer for side 1', 'squareoffs' ) );
		$error = true;
	}

	if ( empty( $data['side_2_title'] ) ) {
		$messages->add( __( 'Please enter an answer for side 2', 'squareoffs' ) );
		$error = true;
	}

	return $error;

}

/**
 * Check for empty form input to avoid unset var error.
 * Nonce verification is done in admin_init.
 *
 * @param string $field the field to check.
 */
function squareoffs_render_form_data( $field ) {

	global $squareoff;

	$data = ! ( empty( $squareoff[ $field ] ) ) ? $squareoff[ $field ] : '';

	if ( 'question' === $field ) {
		// echo esc_textarea( $data );
		echo $data;
	} else {
		echo esc_attr( $data );
	}

}

/**
 * Retreive data SquareOff from API.
 * Todo: add tags to the array $result.
 *
 * @param string $uuid id of the SquareOff.
 *
 * @return array $result for filling the form.
 */
function squareoffs_get_squareoff_data( $uuid ) {

	$squareoffs_api = squareoffs_get_api();
	$data   = $squareoffs_api->get_squareoff( $uuid );

	if ( ! $data || is_wp_error( $data ) ) {
		wp_die( esc_html__( 'Fetching SquareOff failed, please try again.', 'squareoffs' ) );
	}

	$result['uuid']             = sanitize_text_field( $data->uuid );
	$result['question']         = esc_textarea( $data->question );
	$result['question']         = esc_textarea( $data->question );
	$result['side_1_title']     = sanitize_text_field( $data->side_1_title );
	$result['side_1_defense']   = sanitize_text_field( $data->side_1_defense );
	$result['side_1_photo_url'] = esc_url_raw( $data->side_1_photo_url );
	$result['side_2_title']     = sanitize_text_field( $data->side_2_title );
	$result['side_2_defense']   = sanitize_text_field( $data->side_2_defense );
	$result['side_2_photo_url'] = esc_url_raw( $data->side_2_photo_url );
	$result['end_date']         = sanitize_text_field( $data->end_date );
	$result['category_uuid']    = sanitize_text_field( $data->category->uuid );
	$result['cover_photo_url']  = esc_url_raw( $data->cover_photo_url );

	// Get tags and convert them into a string, comma separated list.
	if ( ! empty( $data->tags ) ) {
		$tags = implode(',',
			array_map( function( $get_name ) {
				return $get_name->name;
			}, $data->tags )
		);
		$result['tag_list'] = sanitize_text_field( $tags );

	} else {
		$result['tag_list'] = '';
	}

	return $result;

}

/**
 * Render photos.
 *
 * @param string $field URL photo.
 */
function squareoffs_render_form_photo( $field ) {

	global $squareoff;

	if ( empty( $squareoff[ $field ] ) ) {
		return;
	}

	$img = sanitize_text_field( $squareoff[ $field ] );

	// Discussion: this is maybe not descriptive of what's on the image.
	// Alternative: add the image base url.
	$alt['side_1_photo_url'] = __( 'Image side 1', 'squareoffs' );
	$alt['side_2_photo_url'] = __( 'Image side 2', 'squareoffs' );
	$alt['cover_photo_url']  = __( 'Cover image', 'squareoffs' );

	$alt_text = ( isset( $alt[ $field ] ) ) ? $alt[ $field ] : '';

	printf( '<img src="%s" alt="%s">', esc_url( $img ), esc_attr( $alt_text ) );

}

/**
 * Render text submit button.
 */
function squareoffs_render_submit_button() {
	if ( ! empty( $_GET['action'] ) && ( 'squareoffs-edit' === sanitize_text_field( wp_unslash( $_GET['action'] ) ) ) ) { // WPCS: CSRF ok.
		esc_html_e( 'Update this SquareOff!' , 'squareoffs' );
	} else {
		esc_html_e( 'Create a new SquareOff!' , 'squareoffs' );
	}

}

/**
 * Render the end date field.
 *
 * @param  array $squareoff SquareOff.
 * @return void
 */
function squareoffs_render_end_date_field( $squareoff = null ) {
	global $squareoff;

	if ( ! ( empty( $squareoff['end_date'] ) ) ) {
		$date = new DateTime( $squareoff['end_date'] );
		$date->setTimezone( new DateTimeZone( 'UTC' ) );
	} else {
		$date = null;
	}

	squareoffs_render_date_field( 'squareoffs-new[end_date]', $date );
}

/**
 * Print out HTML for a date field.
 *
 * @param string   $name  Timestamp.
 * @param DateTime $date  DateTime object.
 * @param string   $id    Field ID. Defaults to a random unique ID.
 * @return void
 */
function squareoffs_render_date_field( $name, $date = null, $id = null ) {

	$id = ! empty( $id ) ? $id : 'squareoffs-datepicker-' . uniqid();

	echo '<div class="squareoffs-date squareoffs-jquery-ui-theme">';

	printf(
		'<label for="%s"><span class="screen-reader-text">%s</span><span class="dashicons dashicons-calendar" aria-hidden="true"></span></label>',
		esc_attr( $id . '-date' ),
		esc_html__( 'Date (YYYY-MM-DD)', 'squareoffs' )
	);

	printf(
		'<input type="text" id="%s" name="%s" class="squareoffs-date-input regular-text squareoffs-input-medium" value="%s" data-date-iso="%s"/>',
		esc_attr( $id . '-date' ),
		esc_attr( $name . '[date]' ),
		esc_attr( empty( $date ) ? "" : $date->format( 'Y-m-d' ) ),
		esc_attr( empty( $date ) ? "" : $date->format( 'c' ) )
	);

	printf(
		'<label for="%s"><span class="screen-reader-text">%s</span><span class="dashicons dashicons-clock" aria-hidden="true"></span></label>',
		esc_attr( $id . '-time' ),
		esc_html__( 'Time (eg 11:00 PM)', 'squareoffs' )
	);

	printf(
		'<input type="text" class="squareoffs-time-input" id="%s" name="%s" value="%s"/>',
		esc_attr( $id . '-time' ),
		esc_attr( $name . '[time]' ),
		esc_attr( empty( $date ) ? "" : $date->format( 'g:i A' ) )
	);
	echo '<span class="dashicons dashicons-trash" id="clearEndDate" style="cursor:pointer;"></span>';
	echo '</div>';

}
