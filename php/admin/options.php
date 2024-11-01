<?php
/**
 * SquareOffs settings
 *
 * @package squareoffs
 */

add_action( 'admin_init', 'squareoffs_options_init' );

/**
 * Setup options page.
 * Register and set section and options for the login data
 */
function squareoffs_options_init() {
	// Actions & Filters.
	add_action( 'admin_notices', 'squareoffs_display_messages' );
	add_action( 'load-squareoffs_page_squareoffs-options', 'squareoffs_options_save_settings' );
	add_filter( 'sanitize_option_squareoffs_setting_account', 'squareoffs_options_sanitize_squareoffs_setting_account' );
}

/**
 * Intro text and output messages for this settings section.
 */
function squareoffs_display_messages() {

	$messages     = Squareoffs_Messages::get_instance();
	$allowed_html = array(
		'a' => array(
			'href' => true,
		),
	);

	foreach ( $messages->get() as $message ) {
		printf(
			'<div class="notice notice-%s">%s</div>',
			esc_attr( $message['status'] ),
			sprintf( '<p>%s</p>', wp_kses( $message['message'], $allowed_html ) )
		);
	}
	$messages->clear();
}

/**
 * Handle saving settings.
 *
 * @return void
 */
function squareoffs_options_save_settings() {
	$user_id = get_current_user_id();

	if ( $user_id && isset( $_GET['squareoffs-disconnect-nonce'] ) ) {

		check_admin_referer( 'squareoffs_disconnect', 'squareoffs-disconnect-nonce' );
		// delete_option( 'squareoffs_user_data_' . $user_id );
		so_delete_user_api_credentials();
		wp_safe_redirect( remove_query_arg( 'squareoffs-disconnect-nonce' ) );
		exit;

	} elseif ( isset( $_POST['squareoffs-connect-account-nonce'] ) && isset( $_POST['squareoffs_setting_account'] ) ) {

		check_admin_referer( 'squareoffs_connect_account', 'squareoffs-connect-account-nonce' );
		$data = squareoffs_options_sanitize_connect_account( wp_unslash( $_POST['squareoffs_setting_account'] ) ); // WPCS: sanitization ok.
		squareoffs_options_connect_account( $data );
		wp_safe_redirect( admin_url( 'admin.php?page=squareoffs-options' ) );
		exit;

	} elseif ( isset( $_POST['squareoffs-new-account-nonce'] ) && isset( $_POST['squareoffs-new-account'] ) ) {

		check_admin_referer( 'squareoffs_new_account', 'squareoffs-new-account-nonce' );
		$data = squareoffs_options_sanitize_new_account( wp_unslash( $_POST['squareoffs-new-account'] ) ); // WPCS: sanitization ok.
		squareoffs_options_create_account( $data );
		wp_safe_redirect( admin_url( 'admin.php?page=squareoffs-options' ) );
		exit;

	} elseif ( isset( $_POST['squareoffs-settings-nonce'] ) && isset( $_POST['squareoffs-settings'] ) ) {

		check_admin_referer( 'squareoffs_settings', 'squareoffs-settings-nonce' );
		$data = squareoffs_sanitize_settings( wp_unslash( $_POST['squareoffs-settings'] ) ); // WPCS: sanitization ok.

	}

}

/**
 * Handle create new account.
 *
 * @param mixed $data Data - expects array of data.
 * @return mixed Array of credentials or null.
 */
function squareoffs_options_create_account( $data ) {

	$valid   = squareoffs_options_create_account_validate( $data );
	$wp_user = get_current_user_id();

	if ( ! $valid || ! $wp_user ) {
		return;
	}

	$api = new Squareoffs_Api();
	$user = $api->create_user( $data );

	if ( $user && ! is_wp_error( $user ) ) {
		update_option( 'squareoffs_user_data_' . $user_id, $meta_value, false );

	} elseif ( is_wp_error( $user ) ) {
		Squareoffs_Messages::get_instance()->add( __( 'Failed to create a user.', 'squareoffs' ) );
	}

}

/**
 * Validate account creation data and add messages.
 *
 * @param  data $data Account creation form data.
 * @return boolean Is valid?
 */
function squareoffs_options_create_account_validate( $data ) {

	$valid    = true;
	$messages = Squareoffs_Messages::get_instance();

	if ( empty( $data['name'] ) ) {
		$messages->add( __( 'Please enter a name.', 'squareoffs' ) );
		$valid = false;
	}

	if ( empty( $data['email'] ) ) {
		$messages->add( __( 'Please enter a valid email.', 'squareoffs' ) );
		$valid = false;
	}

	if ( empty( $data['password'] ) ) {
		$messages->add( __( 'Please enter a password.', 'squareoffs' ) );
		$valid = false;
	}

	if ( $data['password'] !== $data['password'] ) {
		$messages->add( __( 'Passwords do not match.', 'squareoffs' ) );
		$valid = false;
	}

	if ( ! $data['t-and-cs'] ) {
		$messages->add( __( 'You must accept the terms and conditions.', 'squareoffs' ) );
		$valid = false;
	}

	return $valid;
}

/**
 * Connect an existing account.
 *
 * @param  data $data Account connect form data.
 * @return void
 */
function squareoffs_options_connect_account( $data ) {

	$valid   = squareoffs_options_connect_account_validate( $data['email'], $data['password'] );
	$user_id = get_current_user_id();

	if ( ! $valid || ! $user_id ) {
		return false;
	}

	$api = new Squareoffs_Api( $data['email'] );
	$api->authenticate( $data['password'] );
	
	if ( ! $api->is_authenticated() ) {
		Squareoffs_Messages::get_instance()->add( __( 'This email address and password are not valid to login with SquareOffs', 'squareoffs' ) );
		return false;
	}

	// update_option( 'squareoffs_user_data_' . $user_id, $api->get_credentials(), false );
	so_save_user_api_credentials($api->get_credentials());
	return true;
}

/**
 * Validate account connect data and add messages.
 *
 * @param  string $email Email.
 * @param  string $pass  Password.
 * @return boolean Is valid?
 */
function squareoffs_options_connect_account_validate( $email, $pass ) {

	$messages = Squareoffs_Messages::get_instance();

	if ( empty( $email ) && empty( $pass ) ) {
		$messages->add( __( 'Please enter an email address and password', 'squareoffs' ) );
		return false;
	}

	if ( empty( $email ) ) {
		$messages->add( __( 'Please enter a valid email address', 'squareoffs' ) );
		return false;
	}

	if ( empty( $pass ) ) {
		$messages->add( __( 'Please enter a password', 'squareoffs' ) );
		return false;
	}

	if ( ! empty( $email ) && ! is_email( $email ) ) {
		$messages->add( __( 'Please enter a valid email address', 'squareoffs' ) );
		return false;
	}

	return true;

}

/**
 * Retrieve data user profile from API.
 *
 * @param string $user_id uuid of the user.
 *
 * @return array $result for filling the form.
 */
function squareoffs_get_user_profile_data( $user_id ) {

	$squareoffs_api = squareoffs_get_api();

	$userdata   = $squareoffs_api->get_user( $user_id );
	$data       = $squareoffs_api->get_user_profile( $user_id );

	if ( ! $data || is_wp_error( $data ) ) {
		wp_die( esc_html__( 'Fetching user profile failed, please try again.', 'squareoffs' ) );
	}

	$result['comment-moderation']['all']           = $data->pending_all_comment;
	$result['comment-moderation']['flagged']       = $data->flagged_number_user_settings;
	$result['comment-moderation']['flagged-users'] = $data->flagged_number_setting;
	$result['comment-moderation']['censored']      = $data->censored_pending_comment;

	$result['account']['avatar']   = $userdata->avatar;
	$result['account']['name']     = $userdata->name ;
	$result['account']['email']    = $userdata->email;
	$result['account']['username'] = $data->slug;
	$result['account']['about']    = $data->about_me;
	$result['account']['twitter']  = $data->twitter_handle;

	$result['display']['color-1'] = $data->side_positive_color;
	$result['display']['color-2'] = $data->side_oppositive_color;
	$result['display']['font']    = $data->font_family;

	return $result;

}

/**
 * Hook in on admin_init and handle saving a squareoff.
 */
function squareoffs_handle_update_user_profile() {

	$squareoffs_api = squareoffs_get_api();

	if ( ! $squareoffs_api || ! $squareoffs_api->is_authenticated() || ! isset( $_POST['squareoffs-settings'] ) ) {
		return;
	}

	check_admin_referer( 'squareoffs_settings', 'squareoffs-settings-nonce' );

	$data = array();

	$account = squareoffs_sanitize_settings_account( wp_unslash( $_POST['squareoffs-settings']['account'] ) ); // WPCS: sanitization ok.
	$comment = squareoffs_sanitize_settings_comments( wp_unslash( $_POST['squareoffs-settings']['comment-moderation'] ) ); // WPCS: sanitization ok.
	$display = squareoffs_sanitize_settings_display( wp_unslash( $_POST['squareoffs-settings']['display'] ) ); // WPCS: sanitization ok.

	$data['uuid'] = $squareoffs_api->get_credential( 'uuid' );

	$data['pending_all_comment']          = $comment['all'];
	$data['flagged_number_user_settings'] = $comment['flagged'];
	$data['flagged_number_setting']       = $comment['flagged-users'];
	$data['censored_pending_comment']     = $comment['censored'];

	$data['slug']           = $account['username'];
	$data['about_me']       = $account['about'];
	$data['twitter_handle'] = $account['twitter'];

	$data['side_positive_color']   = $display['color-1'];
	$data['side_oppositive_color'] = $display['color-2'];
	$data['font_family']           = $display['font'];

	$response = $squareoffs_api->update_user_profile( $data );

	if ( is_wp_error( $response ) ) {
		Squareoffs_Messages::get_instance()->add( $response->get_error_message() );
		return;
	} else {
		Squareoffs_Messages::get_instance()->add( __( 'User Profile updated.', 'squareoffs' ), 'success' );
		wp_safe_redirect( admin_url( 'admin.php?page=squareoffs-options' ) );
		exit;
	}

}

function so_save_user_api_credentials($credentials){
	$user_id = get_current_user_id();
	if(!$user_id) return false;
	delete_option( 'squareoffs_user_data_' . $user_id );
	update_user_meta( $user_id, 'squareoffs_user_data', $credentials );
}
function so_get_user_api_credentials(){
	$user_id = get_current_user_id();
	if(!$user_id) return false;
	$credentials = get_option( 'squareoffs_user_data_' . $user_id, array() );
	if ( ! empty( $credentials['email'] ) && ! empty( $credentials['token'] ) && ! empty( $credentials['uuid'] ) ) {
		return $credentials;
	}else{
		$credentials = get_user_meta( $user_id, 'squareoffs_user_data', true);
		if ( ! empty( $credentials ) ) {
	        return $credentials;
	    }else{
	    	return false;
	    }
	}
}
function so_delete_user_api_credentials(){
	$user_id = get_current_user_id();
	if(!$user_id) return false;
	delete_option( 'squareoffs_user_data_' . $user_id );
	delete_user_meta( $user_id, 'squareoffs_user_data' );
}
