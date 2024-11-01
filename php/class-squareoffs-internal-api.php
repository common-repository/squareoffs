<?php
/**
 * SquareOffs Internal REST API.
 *
 * For interacting with the local site.
 *
 * @package squareoffs
 */

/**
 * SquareOffs Internal REST API.
 */
class Squareoffs_Internal_Api {

	/**
	 * Reference to the external API wrapper.
	 *
	 * @var $api
	 */
	private $api;

	/**
	 * Constructor
	 *
	 * @param  Squareoffs_Api $api Squareoffs API wrapper.
	 * @return void
	 */
	public function __construct( Squareoffs_Api $api ) {
		$this->api = $api;
		add_action( 'rest_api_init', array( $this, 'register_routes' ) );
	}

	/**
	 * Register API endpoints.
	 *
	 * @return void
	 */
	public function register_routes() {

		/**
		 * Get squareoffs list endpoint.
		 */
		register_rest_route(
			'squareoffs/v1',
			'/squareoffs/?',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_squareoffs' ),
				'permission_callback' => array( $this, 'default_permission_callback' ),
				'args'                => array(
					'page' => array(
						'default'           => 1,
						'sanitize_callback' => 'absint',
						'validate_callback' => array( $this, 'validate_numeric' ),
					),
					'per_page' => array(
						'default'           => 10,
						'sanitize_callback' => 'absint',
						'validate_callback' => array( $this, 'validate_numeric' ),
					),
				),
			)
		);

		register_rest_route(
			'squareoffs/v1',
			'/squareoffs/(?P<uuid>.{8}-.{4}-.{4}-.{4}-.{12})/?',
			array(
				'methods'             => WP_REST_Server::READABLE,
				'callback'            => array( $this, 'get_squareoff' ),
				'permission_callback' => array( $this, 'default_permission_callback' ),
			)
		);

		register_rest_route(
			'squareoffs/v1',
			'/squareoffs/?',
			array(
				'methods'             => WP_REST_Server::CREATABLE,
				'callback'            => array( $this, 'create_squareoff' ),
				'permission_callback' => array( $this, 'default_permission_callback' ),
				'args'                => array(
					'question'         => array(
						'required' => true,
					),
					'side_1_title'     => array(
						'required' => true,
					),
					'side_1_defense'   => array(),
					'side_1_photo'     => array(),
					'side_2_title'     => array(
						'required' => true,
					),
					'side_2_defense'   => array(),
					'side_2_photo'     => array(),
					'cover_photo'      => array(),
					'category_uuid'    => array(
						'required' => true,
					),
					'end_date'         => array(),
					'tag_list'         => array(),
				),
			)
		);

	}

	/**
	 * Validate a numeric value.
	 *
	 * @param  mixed $value Value to test.
	 * @return boolean Is $value numeric.
	 */
	public function validate_numeric( $value ) {
		return is_numeric( $value );
	}

	/**
	 * Default permission callback. Edit posts.
	 *
	 * @return bool Can the user view this endoint.
	 */
	public function default_permission_callback() {

		if ( ! current_user_can( 'edit_posts' ) ) {
			return false;
		}

		if ( ! $this->api->is_authenticated() ) {
			return new WP_Error( 'squareoffs_internal_api', __( 'You are not authenticated with SquareOffs', 'squareoffs' ) );
		}

		return true;

	}

	/**
	 * Get collection of squareoffs.
	 *
	 * Note this is paginated.
	 *
	 * @param  WP_REST_Request $request Request.
	 * @return array|WP_Error Squareoffs.
	 */
	function get_squareoffs( WP_REST_Request $request ) {

		$squareoffs = array();
		$page       = $request->get_param( 'page' );
		$per_page   = $request->get_param( 'per_page' );
		$results    = $this->api->get_squareoffs( $page, $per_page );

		if ( is_wp_error( $results ) || empty( $results->square_offs ) ) {
			return array();
		}

		foreach ( $results->square_offs as $squareoff ) {
			$squareoffs[] = array(
				'uuid'        => sanitize_text_field( $squareoff->uuid ),
				'external_id' => sanitize_text_field( $squareoff->external_id ),
				'question'    => sanitize_text_field( $squareoff->question ),
			);
		}

		return $squareoffs;
	}

	/**
	 * Get a single squareoff.
	 *
	 * @param  WP_REST_Request $request Request.
	 * @return array Squareoff.
	 */
	function get_squareoff( WP_REST_Request $request ) {

		$uuid       = $request->get_param( 'uuid' );
		$squareoff  = $this->api->get_squareoff( $uuid );

		if ( is_wp_error( $squareoff ) || empty( $squareoff ) ) {
			return array();
		}

		return array(
			'uuid'     => sanitize_text_field( $squareoff->uuid ),
			'external_id' => sanitize_text_field( $squareoff->external_id ),
			'question' => sanitize_text_field( $squareoff->question ),
		);

	}

	/**
	 * Create SquareOff.
	 *
	 * Pass JSON args directoly to the create squareoff API endpoint.
	 *
	 * @param  WP_REST_Request $request Rest Request.
	 * @return array Response data.
	 */
	function create_squareoff( WP_REST_Request $request ) {
		$response = $this->api->create_squareoff( $request->get_json_params() );
		return $response;
	}

}
