<?php
/**
 * SquareOffs admin menu and submenus
 *
 * @package squareoffs
 */

add_action( 'admin_menu', 'squareoffs_plugin_menu' );
add_action( 'admin_enqueue_scripts', 'squareoffs_admin_scripts', 10, 2 );
add_action( 'admin_init', 'squareoffs_handle_squareoffs_actions' );
add_action( 'admin_init', 'squareoffs_handle_comment_actions' );
add_action( 'admin_init', 'squareoffs_handle_update_user_profile' );
add_action( 'admin_notices', 'squareoffs_not_connected_admin_notice' );

/**
 * SquareOffs admin menu and submenus
 */
function squareoffs_plugin_menu() {

	$api = squareoffs_get_api();

	if ( $api && $api->is_authenticated() ) {

		add_menu_page(
			__( 'SquareOffs', 'squareoffs' ),
			'SquareOffs',
			'edit_posts',
			'squareoffs',
			'squareoffs_render_list_page',
			SQUAREOFFS_PLUGIN_URL . 'images/squareoffs-icon-nav.png'
		);

		add_submenu_page(
			'squareoffs',
			__( 'SquareOffs Comments Moderation', 'squareoffs' ),
			__( 'Comments', 'squareoffs' ),
			'edit_posts',
			'squareoffs-comments',
			'squareoffs_render_comments_squareoffs'
		);

	} else {

		add_menu_page(
			__( 'SquareOffs', 'squareoffs' ),
			'SquareOffs',
			'edit_posts',
			'squareoffs',
			'squareoffs_options_page',
			SQUAREOFFS_PLUGIN_URL . 'images/squareoffs-icon-nav.png'
		);

	} // End if().

	add_submenu_page(
		'squareoffs',
		__( 'SquareOffs Settings', 'squareoffs' ),
		__( 'Settings', 'squareoffs' ),
		'edit_posts',
		'squareoffs-options',
		'squareoffs_options_page'
	);
}

/**
 * Load the admins scripts and styles.
 *
 * @param  string $screen Current screen ID.
 * @return void
 */
function squareoffs_admin_scripts( $screen ) {
	$allowed_screens = array(
		'squareoffs_page_squareoffs-options',
		'toplevel_page_squareoffs',
		'post.php',
		'page.php',
		'post-new.php',
		'page-new.php'
	);

	wp_register_script( 'squareoffs-upload-image', SQUAREOFFS_PLUGIN_URL . 'js/upload-image.js', array( 'jquery' ), SQUAREOFFS_PLUGIN_VERSION, true );

	// Datepicker script and styles.
	wp_register_style( 'squareoffs-jquery-ui-datTimeepicker', SQUAREOFFS_PLUGIN_URL . 'css/wp-datepicker-styling/datepicker.css', false, SQUAREOFFS_PLUGIN_VERSION );
	wp_register_style( 'squareoffs-jquery-ui-datepicker', "https://cdnjs.cloudflare.com/ajax/libs/antd/4.11.2/antd.compact.min.css", false, SQUAREOFFS_PLUGIN_VERSION );
	wp_register_style( 'squareoffs-jquery-timepicker', SQUAREOFFS_PLUGIN_URL . 'js/jquery-timepicker/jquery.timepicker.min.css', false, SQUAREOFFS_PLUGIN_VERSION );
	wp_register_style( 'squareoffs-datepicker', null, [ 'squareoffs-jquery-ui-datepicker', 'squareoffs-jquery-timepicker' ], SQUAREOFFS_PLUGIN_VERSION );
	wp_register_script( 'squareoffs-jquery-timepicker', SQUAREOFFS_PLUGIN_URL . 'js/jquery-timepicker/jquery.timepicker.min.js', [ 'jquery' ], SQUAREOFFS_PLUGIN_VERSION, false );
	wp_register_script( 'squareoffs-datepicker', SQUAREOFFS_PLUGIN_URL . 'js/datepicker.js', [ 'squareoffs-jquery-timepicker', 'jquery-ui-datepicker', 'jquery' ], SQUAREOFFS_PLUGIN_VERSION, false );

	wp_register_style( 'squareoffs-admin', SQUAREOFFS_PLUGIN_URL . 'css/admin.css', array( 'wp-color-picker', 'squareoffs-datepicker' ), SQUAREOFFS_PLUGIN_VERSION );
	wp_register_style( 'datatable', '//cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css', false, SQUAREOFFS_PLUGIN_VERSION );
	wp_register_script( 'squareoffs-admin', SQUAREOFFS_PLUGIN_URL . 'js/admin.js', array( 'wp-color-picker', 'squareoffs-upload-image', 'squareoffs-datepicker' ), SQUAREOFFS_PLUGIN_VERSION, true );
	wp_register_script( 'cropperjs', 'https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.9/cropper.min.js', array(), SQUAREOFFS_PLUGIN_VERSION, true );
	wp_register_script( 'datatable', '//cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js', array(), SQUAREOFFS_PLUGIN_VERSION, true );

	$translation_array = array(
		'title'  => __( 'Choose image', 'squareoffs' ),
		'button' => __( 'Insert image', 'squareoffs' ),
	);

	wp_localize_script( 'squareoffs-upload-image', 'squareoffs_upload_image', $translation_array );

	// wp_localize_script( 'jquery', 'soVars', array("soURL" => SQUAREOFFS_PLUGIN_URL, 'api_connected' => (squareoffs_get_api()?true:false)) );
	// wp_register_script(
 //        'soguten',
 //        plugins_url( SQUAREOFFS_PLUGIN_URL.'js/soguten.js', __FILE__ ),
 //        array(
	// 		'wp-components',
	// 		'wp-blocks',
	// 		'wp-element',
	// 		'wp-data',
	// 		'wp-date',
	// 		'wp-utils',
	// 		'wp-i18n',
	// 	),
 //        SQUAREOFFS_PLUGIN_VERSION, true
 //    );

	if ( in_array( $screen, $allowed_screens, true ) ) {
		wp_enqueue_media();
		wp_enqueue_style( 'squareoffs-admin' );
		wp_enqueue_style( 'squareoffs-iframe' );
		wp_enqueue_style( 'squareoffs-jquery-ui-datTimeepicker' );
		wp_enqueue_style( 'squareoffs-jquery-timepicker' );
		wp_enqueue_style( 'ssquareoffs-datepicker' );
		wp_enqueue_script( 'squareoffs-admin' );
		wp_enqueue_script( 'cropperjs' );
		if(@$_GET['page'] == 'squareoffs'){
			wp_enqueue_style( 'datatable' );
			wp_enqueue_script( 'datatable' );
		}
	}
}

/**
 * Handle comment actions.
 *
 * Delete, flag, approve.
 * Calls squareoffs_do_action_redirect after an action is performed.
 *
 * @return void
 */
function squareoffs_handle_squareoffs_actions() {

	$squareoffs_api = squareoffs_get_api();

	if ( ! $squareoffs_api || ! $squareoffs_api->is_authenticated() ) {
		return;
	}

	$id     = isset( $_GET['id'] ) ? sanitize_text_field( wp_unslash( $_GET['id'] ) ) : '';  // WPCS: CSRF ok.
	$action = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';  // WPCS: CSRF ok.

	if ( isset( $_GET['action'] ) && 'squareoffs-delete' === $action ) {
		check_admin_referer( 'squareoffs_squareoffs_action', 'nonce' );
		$response = $squareoffs_api->delete_squareoff( $id );
		squareoffs_handle_action_messages( $response, __( 'SquareOff deleted.', 'squareoff' ) );
		squareoffs_do_action_redirect();
	}

}

/**
 * Handle comment actions.
 *
 * Delete, flag, approve.
 * Calls squareoffs_do_action_redirect after an action is performed.
 *
 * @return void
 */
function squareoffs_handle_comment_actions() {

	$squareoffs_api = squareoffs_get_api();

	if ( ! $squareoffs_api || ! $squareoffs_api->is_authenticated() ) {
		return;
	}

	// Note - nonce validation is handled later.
	$id      = isset( $_GET['id'] ) ? sanitize_text_field( wp_unslash( $_GET['id'] ) ) : '';  // WPCS: CSRF ok.
	$squareoffs_id   = isset( $_GET['squareoffs_id'] ) ? sanitize_text_field( wp_unslash( $_GET['squareoffs_id'] ) ) : '';  // WPCS: CSRF ok.
	$vote_id = isset( $_GET['vote_id'] ) ? sanitize_text_field( wp_unslash( $_GET['vote_id'] ) ) : '';  // WPCS: CSRF ok.
	$action  = isset( $_GET['action'] ) ? sanitize_text_field( wp_unslash( $_GET['action'] ) ) : '';  // WPCS: CSRF ok.

	if ( ! empty( $action ) && 'squareoffs-comment-remove' === $action ) {
		check_admin_referer( 'squareoffs_comment_action', 'nonce' );
		$response = $squareoffs_api->update_comment( $id, $vote_id, $squareoffs_id, array(
			'status'   => false,
			'approved' => false,
		) );
		squareoffs_handle_action_messages( $response, __( 'Comment removed.', 'squareoff' ) );
		squareoffs_do_action_redirect();
	}

	if ( ! empty( $action ) && 'squareoffs-comment-approve' === $action ) {
		check_admin_referer( 'squareoffs_comment_action', 'nonce' );
		$response = $squareoffs_api->update_comment( $id, $vote_id, $squareoffs_id, array(
			'status'   => true,
			'approved' => true,
		) );
		squareoffs_handle_action_messages( $response, __( 'Comment approved.', 'squareoff' ) );
		squareoffs_do_action_redirect();
	}

	if ( ! empty( $action ) && 'squareoffs-comment-pending' === $action ) {
		check_admin_referer( 'squareoffs_comment_action', 'nonce' );
		$response = $squareoffs_api->update_comment( $id, $vote_id, $squareoffs_id, array(
			'status'   => true,
			'approved' => false,
		) );
		squareoffs_handle_action_messages( $response, __( 'Comment set to pending.', 'squareoff' ) );
		squareoffs_do_action_redirect();
	}

}

/**
 * Do the action redirect.
 * Redirect back to current page, stripping all the action query args.
 *
 * @return void
 */
function squareoffs_do_action_redirect() {
	wp_safe_redirect( remove_query_arg( array( 'nonce', 'id', 'squareoffs_id', 'vote_id', 'action' ) ) );
	exit;
}


/**
 * Admin notice that is displayed when the plugin is activated but the user is not connected to SquareOffs.
 */
function squareoffs_not_connected_admin_notice() {

	$squareoffs_api = squareoffs_get_api();

	if ( $squareoffs_api && $squareoffs_api->is_authenticated() ) {
		return;
	}
	$class   = 'notice notice-error is-dismissible';
	$message = __( 'Please connect to your SquareOffs account, <a href="'.get_admin_url().'admin.php?page=squareoffs-options" target="_blank">login</a> or <a href="https://www.squareoffs.com/registration/new/" target="_blank">create a new account</a> on squareoffs.com.', 'squareoffs' );
	$dismiss = __( 'Dismiss this warning', 'squareoffs' );

	printf( '<div class="%1$s"><p>%2$s</p><button type="button" class="notice-dismiss"><span class="screen-reader-text">%3$s</span></button></div>',
		esc_attr( $class ),
		wp_kses( $message ,
			array(
				'a' => array(
					'href' => array(),
					'target' => array(),
					'title' => array(),
				),
			)
		),
		esc_html( $dismiss )
	);
}

/**
 * Handle action messages.
 *
 * Add messages to Squareoffs_Messages
 * Displays error messages
 * Or success message if no errors.
 *
 * @param  mixed  $response        Response.
 * @param  string $success_message Message.
 */
function squareoffs_handle_action_messages( $response, $success_message = null ) {
	if ( is_wp_error( $response ) ) {
		foreach ( $response->get_error_messages() as $message ) {
			Squareoffs_Messages::get_instance()->add( $message );
		}
	} elseif ( $success_message ) {
		Squareoffs_Messages::get_instance()->add( $success_message, 'success' );
	}
}
/**
 * Render the list table of SquareOffs.
 */
function squareoffs_render_list_page() {

	require_once( SQUAREOFFS_PLUGIN_PATH . 'php/admin/list.php' );

}

/**
 * Include add new SquareOffs page
 */
function squareoffs_render_new_squareoffs() {

	require_once( SQUAREOFFS_PLUGIN_PATH . 'php/admin/new.php' );

}

/**
 * Include comments moderation page
 */
function squareoffs_render_comments_squareoffs() {

	require_once( SQUAREOFFS_PLUGIN_PATH . 'php/admin/comments.php' );

}

/**
 * Output settings page
 */
function squareoffs_options_page() {

	require_once( SQUAREOFFS_PLUGIN_PATH . 'php/admin/settings.php' );

}
