<?php
/**
 * SquareOffs External API wrapper.
 *
 * @package Squareoffs
 */

/**
 * Makes interaction with the SO API easy peasy.
 */
class Squareoffs_Api {

	/**
	 * User credentials.
	 *
	 * @var array
	 */
	private $credentials = array(
		'email'    => '',
		'uuid'     => '',
		'token'    => '',
	);

	/**
	 * API base URL.
	 *
	 * @var string
	 */
	private $api_base = 'https://www.squareoffs.com';

	/**
	 * Allowed endpoints.
	 *
	 * @var array
	 */
	private $endpoints = array(
		'authenticate'       => '/api/v2/sessions',
		'squareoffs'         => '/api/v2/square_offs',
		'squareoff'          => '/api/v2/square_offs/:uuid',
		'squareoff_comments' => '/api/v2/square_offs/:uuid/comments',
		'squareoff_comment'  => '/api/v2/square_offs/:uuid/votes/:vote_uuid/comments/:comment_uuid',
		'delete_comment'     => '/api/v2/square_offs/:uuid/comments/:comment_uuid',
		'user'               => '/api/v2/users/:user_id',
		'user_profile'       => '/api/v2/users/:user_id/profile',
		'user_comments'      => '/api/v2/users/:user_id/comments',
		'comments'           => '/api/v2/users/:publisher_id/square_offs/comments',
		'categories'         => '/api/v2/categories',
	);

	/**
	 * Constructor.
	 *
	 * @param string $email SquareOffs user account email.
	 * @param string $token SquareOffs user auth token.
	 * @param string $uuid  SquareOffs user UUID.
	 */
	public function __construct( $email = null, $token = null, $uuid = null ) {
		$this->update_credentials( array(
			'email' => $email,
			'token' => $token,
			'uuid'  => $uuid,
		) );
	}

	/**
	 * Get user credentials.
	 *
	 * @return array User credentials.
	 */
	public function get_credentials() {
		return $this->credentials;
	}

	/**
	 * Get single user credential.
	 *
	 * @param  string $credential Single user credential.
	 * @return string Credential.
	 */
	public function get_credential( $credential ) {
		$credentials = $this->get_credentials();
		return isset( $credentials[ $credential ] ) ? $credentials[ $credential ] : '';
	}

	/**
	 * Update credentials.
	 * If credential is not passed, that value is not unset.
	 *
	 * @param  array $credentials User credentials.
	 * @return void
	 */
	public function update_credentials( array $credentials ) {
		foreach ( array_keys( $this->credentials ) as $property ) {
			if ( array_key_exists( $property, $credentials ) ) {
				$this->credentials[ $property ] = $credentials[ $property ];
			}
		}
	}

	/**
	 * Authenticate user.
	 *
	 * @param  string $password User account password.
	 * @return bool|WP_Error True on success or WP Error on failure.
	 */
	public function authenticate( $password ) {

		$data = array(
			'user' => array(
				'email'    => $this->get_credential( 'email' ),
				'password' => $password,
			),
		);

		$response = $this->request( 'authenticate', array(
			'method' => 'POST',
			'body'   => $data,
		) );

		if ( $response && ! is_wp_error( $response ) ) {

			$this->update_credentials( array(
				'email' => sanitize_email( $response->email ),
				'uuid'  => sanitize_text_field( $response->uuid ),
				'token' => sanitize_text_field( $response->token ),
			) );

			return true;

		} elseif ( is_wp_error( $response ) ) {
			return $response;
		} else {
			return new WP_Error( 'squareoffs_api', 'Authentication failed.' );
		}

	}

	/**
	 * Is user authenticated.
	 *
	 * @todo unit test.
	 * @return boolean Is user authenticated with SquareOffs.
	 */
	function is_authenticated() {
		$credentials = $this->get_credentials();
		return ( ! empty( $credentials['email'] ) && ! empty( $credentials['token'] ) && ! empty( $credentials['uuid'] ) );
	}

	/**
	 * Create new user.
	 *
	 * @param  array $data User data.
	 * @return mixed Array or WP_Error.
	 */
	function create_user( $data ) {
		return new WP_Error( __( 'Creating new users is not yet supported', 'squareoffs' ) );
	}

	/**
	 * Get the current user.
	 *
	 * @param  string $user_uuid User UUID.
	 * @return array|WP_Error User or error.
	 */
	function get_user( $user_uuid = null ) {

		if ( empty( $user_uuid ) ) {
			$user_uuid = $this->get_credential( 'uuid' );
		}

		$args     = array(
			'user_id' => $user_uuid,
		);
		$response = $this->request( 'user', array(
			'url_args' => $args,
		) );

		if ( ! is_wp_error( $response ) && ! empty( $response->user ) ) {
			return $response->user;
		} else {
			return new WP_Error( 'squareoffs_api', 'Fetching current user failed.' );
		}

	}

	/**
	 * Get user profile.
	 *
	 * @param string $user_uuid User UUID.
	 *
	 * @return array|WP_Error User or error.
	 */
	function get_user_profile( $user_uuid = null ) {

		if ( ! $user_uuid ) {
			$user_uuid = $this->get_credential( 'uuid' );
		}

		$args     = array(
			'user_id' => $user_uuid,
		);
		$response = $this->request( 'user_profile', array(
			'url_args' => $args,
		) );

		if ( ! is_wp_error( $response ) && ! empty( $response->profile ) ) {
			return $response->profile;
		} else {
			return new WP_Error( 'squareoffs_api', 'Fetching user profile failed.' );
		}
	}

	/**
	 * Update User profile.
	 *
	 * @param array $user data user profile.
	 *
	 * @return array|WP_Error User or error.
	 */
	public function update_user_profile( $user ) {

		$required_args = array(
			'uuid',
		);

		$optional_args = array(
			'flagged_number_user_settings',
			'flagged_number_setting',
			'censored_pending_comment',
			'pending_all_comment',
			'alt_name',
			'name',
			'about_me',
			'slug',
			'twitter_handle',
			'side_positive_color',
			'side_oppositive_color',
			'font_family',
		);

		foreach ( $required_args as $arg ) {
			if ( empty( $user[ $arg ] ) ) {
				return new WP_Error( 'squareoffs_api', sprintf( 'Required arg: %s is empty.', $arg ) );
			}
		}

		// Whitelist args to required & optional args.
		$user = array_intersect_key( $user, array_flip( array_merge( $required_args, $optional_args ) ) );

		$args = array(
			'method' => 'PUT',
			'url_args' => array(
				'user_id' => $user['uuid'],
			),
			'body' => array(
				'profile' => $user,
			),
		);

		$response = $this->request( 'user_profile', $args );

		return $response;

	}

	/**
	 * Get comments by user.
	 * Pass $user_id, or default to current user.
	 *
	 * @param  string $user_id UUID.
	 * @return array|WP_Error User of error.
	 */
	function get_user_comments( $user_id = null ) {

		if ( ! $user_id ) {
			$user_id = $this->get_credential( 'uuid' );
		}

		$args     = array(
			'user_id' => $user_id,
		);
		$response = $this->request( 'user_comments', array(
			'url_args' => $args,
		) );

		if ( ! is_wp_error( $response ) && ! empty( $response->comments ) ) {
			return $response->comments;
		} else {
			return new WP_Error( 'squareoffs_api', 'Fetching comments failed.' );
		}
	}

	/**
	 * Get comments on one SquareOff.
	 * Pass $uuid for SquareOff.
	 *
	 * @param string $squareoffs_uuid SquareOff UUID.
	 * @return array|WP_Error Comments of error.
	 */
	function get_squareoff_comments( $squareoffs_uuid ) {

		$response = $this->request(
			'squareoff_comments',
			array(
				'url_args' => array(
					'uuid' => $squareoffs_uuid,
				),
			)
		);

		if ( ! is_wp_error( $response ) && ! empty( $response->comments ) ) {
			return $response->comments;
		} else {
			return new WP_Error( 'squareoffs_api', 'Fetching comments failed.' );
		}

	}

	/**
	 * Get all comments on all squareOffs by publisher.
	 * Note publisher = user.
	 * Pass $user_id, or default to current user.
	 *
	 * @param  string $user_id UUID.
	 * @param  int    $page    Page.
	 * @param  string $status  Status of the comment (for filtering).
	 * @return array|WP_Error  User of error.
	 */
	function get_comments( $user_id = null, $page = 1, $status = 'all' ) {

		if ( ! $user_id ) {
			$user_id = $this->get_credential( 'uuid' );
		}

		$args = array(
			'url_args' => array(
				'publisher_id' => $user_id,
			),
			'query_args' => array(
				'page'     => absint( $page ),
				'per_page' => 10,
			),
		);

		// 1. When fetching `approved` should include both approved and active.
		// 2. Do not actually pass the `all` status. Should be empty.
		if ( 'approved' === $status ) {
			$args['query_args']['current_status'] = 'approved,active';
		} elseif ( 'all' !== $status ) {
			$args['query_args']['current_status'] = $status;
		}

		$response = $this->request( 'comments', $args );

		if ( ! is_wp_error( $response ) && ! empty( $response->comments ) ) {
			return $response;
		} else {
			return new WP_Error( 'squareoffs_api', 'Fetching comments failed.' );
		}
	}

	/**
	 * Get collection of squareoffs for current user.
	 * Results are paginated. 10 per page. Pagination info available as meta.
	 *
	 * @param  int $page Page number.
	 * @param  int $per_page Number of items to fetch per page.
	 *
	 * @return array|WP_Error
	 */
	public function get_squareoffs( $page = 1, $per_page = 10 ) {

		$args     = array(
			'page' => absint( $page ),
			'per_page' => absint( $per_page ),
		);
		$response = $this->request( 'squareoffs', array(
			'query_args' => $args,
		) );

		if ( isset( $response->square_offs ) ) {
			$response->square_offs = array_map( array( $this, 'parse_squareoff' ), $response->square_offs );
		} else {
			return new WP_Error( 'squareoffs_api', 'Request failed.' );
		}

		return $response;
	}

	/**
	 * Get single squareoff.
	 *
	 * @param  int $uuid UUID of the squareoff.
	 * @return array
	 */
	public function get_squareoff( $uuid ) {

		$args = array(
			'url_args' => array(
				'uuid' => $uuid,
			),
		);

		$response = $this->request( 'squareoff', $args );

		if ( ! isset( $response->square_off ) ) {
			return new WP_Error( 'squareoffs_api', 'Request failed.' );
		}

		return $this->parse_squareoff( $response->square_off );
	}

	/**
	 * Create a squareoff.
	 *
	 * @param  array $squareoff Data.
	 * @return array|WP_Error Response or WP_Error.
	 */
	public function create_squareoff( $squareoff ) {

		$squareoff = wp_parse_args( $squareoff, array(
			'so_type' => 1,
			'status'  => 1,
		) );

		$required_args = array( 'question', 'side_1_title', 'side_2_title', 'category_uuid', 'so_type' );
		$optional_args = array( 'side_1_defense', 'side_2_defense', 'side_1_photo', 'side_2_photo', 'duration', 'tag_list', 'start_date', 'end_date', 'cover_photo' );

		foreach ( $required_args as $arg ) {
			if ( empty( $squareoff[ $arg ] ) ) {
				return new WP_Error( 'squareoffs_api', sprintf( 'Required arg: %s is empty.', $arg ) );
			}
		}

		// Whitelist args to required & optional args.
		$squareoff = array_intersect_key( $squareoff, array_flip( array_merge( $required_args, $optional_args ) ) );
		$squareoff = $this->prepare_squareoff( $squareoff );
// file_put_contents("so.txt", print_r($squareoff, true), FILE_APPEND | LOCK_EX);
		$args = array(
			'method' => 'POST',
			'body' => array(
				'square_off' => $squareoff,
			),
		);

		$response = $this->request( 'squareoffs', $args );

		if ( is_wp_error( $response ) ) {
			return $response;
		} elseif ( isset( $response->square_off ) ) {
			return $response->square_off;
		} else {
			return new WP_Error( 'squareoffs_api', __( 'Creating Squareoff failed.', 'squareoffs' ) );
		}

	}

	/**
	 * Update a squareoff.
	 *
	 * @param  array $squareoff Data.
	 * @return array|WP_Error Response or WP_Error.
	 */
	public function update_squareoff( $squareoff ) {

		$required_args = array(
			'uuid',
		);

		$optional_args = array(
			'question',
			'side_2_defense',
			'side_2_title',
			'side_1_defense',
			'side_1_title',
			'side_1_photo',
			'side_2_photo',
			'category_uuid',
			'so_type',
			'duration',
			'tag_list',
			'start_date',
			'end_date',
			'cover_photo',
		);

		foreach ( $required_args as $arg ) {
			if ( empty( $squareoff[ $arg ] ) ) {
				return new WP_Error( 'squareoffs_api', sprintf( 'Required arg: %s is empty.', $arg ) );
			}
		}

		// Whitelist args to required & optional args.
		$squareoff = array_intersect_key( $squareoff, array_flip( array_merge( $required_args, $optional_args ) ) );
		$squareoff = $this->prepare_squareoff( $squareoff );

		$args = array(
			'method' => 'PUT',
			'url_args' => array(
				'uuid' => $squareoff['uuid'],
			),
			'body' => array(
				'square_off' => $squareoff,
			),
		);

		return $this->request( 'squareoff', $args );
	}

	/**
	 * Prepare raw SquareOff after fetching from the SO API.
	 *
	 * @param  StdClass $squareoff SquareOff.
	 * @return StdClass SquareOff.
	 */
	protected function prepare_squareoff( $squareoff ) {

		// Handle some inconsistency in property names.
		$squareoff['category_id'] = $squareoff['category_uuid'];
		unset( $squareoff['category_uuid'] );

		/**
		 * These props need to be base64 encoded before sending.
		 */
		$props_to_base64_encode = array(
			'side_1_photo',
			'side_2_photo',
			'cover_photo',
		);

		foreach ( $props_to_base64_encode as $prop_name ) {

			if ( empty( $squareoff[ $prop_name ] ) || strpos( $squareoff[ $prop_name ], "data:") !== false ) {
				continue;
			}

			if ( function_exists( 'vip_safe_wp_remote_get' ) ) {
				$response = vip_safe_wp_remote_get( $squareoff[ $prop_name ] );
			} else {
				$response = wp_remote_get( $squareoff[ $prop_name ] ); // @codingStandardsIgnoreLine
			}

			if ( $response && 200 === wp_remote_retrieve_response_code( $response ) ) {
				$body = wp_remote_retrieve_body( $response );
				$squareoff[ $prop_name ] = sprintf(
					'data:%s;base64,%s',
					sanitize_text_field( wp_remote_retrieve_header( $response, 'content-type' ) ),
					base64_encode( $body ) // @codingStandardsIgnoreLine
				);
			} else {
				unset( $squareoff[ $prop_name ] );
			}
		}

		return $squareoff;

	}

	/**
	 * Parse squareoff.
	 *
	 * Ensures data is formatted correctly after fetch.
	 *
	 * @param StdClass $squareoff Squareoff.
	 * @return StdClass Squareoff.
	 */
	public function parse_squareoff( $squareoff ) {

		$disallowed_images = array(
			'/side_up_photos/original/missing.png',
			'/side_down_photos/original/missing.png',
			'/assets/SOProfile-bb102c03d4e8bfd729653d9bbc0a5a04.png',
		);

		$props = array( 'side_1_photo_url', 'side_2_photo_url', 'cover_photo_url' );

		foreach ( $props as $prop ) {
			if ( isset( $squareoff->$prop ) && in_array( $squareoff->$prop, $disallowed_images, true ) ) {
				 $squareoff->$prop = null;
			}
		}

		return $squareoff;
	}

	/**
	 * Delete a squareoff.
	 *
	 * @param  array $uuid    SO UUID.
	 * @return array|WP_Error Response or WP_Error.
	 */
	public function delete_squareoff( $uuid ) {

		$args = array(
			'method' => 'DELETE',
			'url_args' => array(
				'uuid' => $uuid,
			),
		);

		return $this->request( 'squareoff', $args );

	}


	/**
	 * Update comment.
	 *
	 * @param  string $comment_uuid Comment Id.
	 * @param  string $vote_uuid    Vote Id.
	 * @param  string $squareoffs_uuid      Squareoff Id.
	 * @param  array  $comment_data New comment data. Partial updates supported.
	 * @return array|WP_Error       Response array or WP_Error.
	 */
	public function update_comment( $comment_uuid, $vote_uuid, $squareoffs_uuid, $comment_data ) {

		$allowed = array(
			'text'           => 'string',
			'like'           => 'boolean',
			'approved'       => 'boolean', // Set from pending to remove: "approved": "true".
			'flag'           => 'boolean',
			'status'         => 'boolean', // Set to remove: "status": "false".
		);

		$comment_data = array_intersect_key( $comment_data, $allowed );

		foreach ( $allowed as $prop => $type ) {
			if ( isset( $comment_data[ $prop ] ) && gettype( $comment_data[ $prop ] ) !== $type ) {
				unset( $comment_data[ $prop ] );
			}
		}

		$args = array(
			'method' => 'PUT',
			'url_args' => array(
				'uuid'         => $squareoffs_uuid,
				'comment_uuid' => $comment_uuid,
				'vote_uuid'    => $vote_uuid,
			),
			'body' => array(
				'comment' => $comment_data,
			),
		);

		$response = $this->request( 'squareoff_comment', $args );

		if ( ! isset( $response->comment ) ) {
			return new WP_Error( 'squareoffs_api', 'Request failed.' );
		}

		return $response->comment;

	}

	/**
	 * Delete comment.
	 * Note: Not used at the moment. Use Remove instead.
	 *
	 * @param  string $comment_uuid Comment ID.
	 * @param  string $squareoffs_uuid      Squareoff Id.
	 * @return array|WP_Error       Response array or WP_Error.
	 */
	public function delete_comment( $comment_uuid, $squareoffs_uuid ) {

		$args = array(
			'method' => 'DELETE',
			'url_args' => array(
				'uuid'         => $squareoffs_uuid,
				'comment_uuid' => $comment_uuid,
			),
		);

		$response = $this->request( 'delete_comment', $args );

		if ( ! isset( $response->destroy ) || ! $response->destroy ) {
			new WP_Error( 'squareoffs_api', 'Request failed.' );
		}

		return true;

	}

	/**
	 * Get all categories.
	 *
	 * @return array|WP_Error Categories.
	 */
	public function get_categories() {

		$response = $this->request( 'categories', array(
			'method' => 'GET',
		) );

		if ( ! isset( $response->categories ) || ! is_array( $response->categories ) ) {
			return new WP_Error( 'squareoffs_api', 'Request failed.' );
		}

		return $response->categories;
	}

	/**
	 * Make generic request.
	 *
	 * @param  string $endpoint Endpoints must be a recognised endpoint defined in Squareoffs_Api::$endpoints.
	 * @param  array  $args    Request args. Pass extra headers, body and query args.
	 * @return array|WP_Error  Data or WP Error on failure.
	 */
	protected function request( $endpoint, $args = array() ) {

		if ( ! array_key_exists( $endpoint, $this->endpoints ) ) {
			return new WP_Error( 'squareoffs_api', 'Endpoint not valid' );
		}

		$args = wp_parse_args( $args, array(
			'headers'  => array(),
			'body'     => array(),
			'url_args' => array(),
			'method'   => 'GET',
		) );

		$request_args = array(
			'method'  => $args['method'],
			'headers' => $this->get_headers( $args['headers'] ),
			'timeout' => 60,
		);

		// Add body option. JSON encoded.
		// Note body is invalid for get requests.
		if ( ! empty( $args['body'] ) && 'GET' !== $request_args['method'] ) {
			$request_args['body'] = wp_json_encode( $args['body'] );
		}

		// Build endpoint URL.
		$url = esc_url_raw( untrailingslashit( $this->api_base ) . $this->endpoints[ $endpoint ] );

		// Replace URL args.
		if ( ! empty( $args['url_args'] ) ) {
			foreach ( $args['url_args'] as $option => $value ) {
				$url = str_replace( ":$option", $value, $url );
			}
		}

		// Add request query_args or remove option.
		if ( ! empty( $args['query_args'] ) ) {
			$args['query_args'] = array_map( 'urlencode', $args['query_args'] );
			$url = add_query_arg( $args['query_args'], $url );
		}

		$valid = $this->validate_request_args( $request_args );

		if ( is_wp_error( $valid ) ) {
			return $valid;
		}

		// Make the request.
		$response = wp_safe_remote_request( $url, $request_args );

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code = wp_remote_retrieve_response_code( $response );

		if ( $code < 200 || $code >= 300 ) {
			return new WP_Error( 'squareoffs_api', sprintf( 'Request failed. Response code: %s.', $code ) );
		}

		return json_decode( wp_remote_retrieve_body( $response ) );

	}

	/**
	 * Validate the request args.
	 *
	 * @param  array $args Request args.
	 * @return boolean|WP_Error Valid or WP_Error
	 */
	function validate_request_args( $args ) {

		// Check valid request method.
		if ( ! in_array( $args['method'], array( 'GET', 'POST', 'PUT', 'DELETE' ), true ) ) {
			return new WP_Error( 'squareoffs_api', 'Request method not supported' );
		}

		return true;
	}

	/**
	 * Get request headers.
	 * Required and auth headers are added automatically.
	 *
	 * @param  array $headers Extra headers to merge with defaults.
	 * @return array Headers
	 */
	private function get_headers( $headers = array() ) {

		$default_headers = array(
			'Content-Type' => 'application/json',
		);

		return wp_parse_args(
			$headers,
			array_merge(
				$default_headers,
				$this->get_auth_headers()
			)
		);

	}

	/**
	 * Get auth headers.
	 *
	 * @return array Auth headers.
	 */
	private function get_auth_headers() {

		$email = $this->get_credential( 'email' );
		$token = $this->get_credential( 'token' );
		$uuid  = $this->get_credential( 'uuid' );

		if ( empty( $email ) || empty( $token ) || empty( $uuid ) ) {
			return array();
		}

		return array(
			'Authorization' => sprintf( 'Token token="%s", email="%s"', $token, $email ),
			'Publisher'     => $uuid,
		);
	}

}
