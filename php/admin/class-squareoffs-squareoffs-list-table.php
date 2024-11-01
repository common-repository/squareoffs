<?php
/**
 * SquareOffs List Table.
 *
 * @package squareoffs
 */

/**
 * SquareOffs List Table.
 */
class Squareoffs_SquareOffs_List_Table extends Squareoffs_List_Table {

	private $assoc = true;
	/**
	 * SquareOffs_List_Table constructor.
	 */
	function __construct() {
		parent::__construct( array(
			'singular' => 'squareoff',
			'plural'   => 'squareoffs',
			'ajax'     => false,
		) );
	}

	/**
	 * Get SquareOffs data.
	 *
	 * Returns squareoffs from the api.
	 *
	 * @return void|WP_Error
	 */
	function fetch_items() {

		$squareoffs_api = squareoffs_get_api();

		$meta  = array(
			'per_page'    => 10,
			'total_items' => 0,
			'total_pages' => 0,
		);

		if ( empty( $squareoffs_api ) || is_wp_error( $squareoffs_api ) ) {
			return;
		}

		$data = $squareoffs_api->get_squareoffs( $this->get_pagenum() );
		
		if ( ! $data || is_wp_error( $data ) ) {
			return;
		}

		$this->items = array_map( array( $this, 'format_item' ), $data->square_offs );

		$meta['total_items'] = absint( $data->meta->total_count );
		$meta['total_pages'] = absint( $data->meta->total_pages );

		$this->set_pagination_args( $meta );

	}

		/**
	 * Get all SquareOffs data.
	 *
	 * Returns all squareoffs from the api.
	 *
	 * @return void|WP_Error
	 */
	function fetch_paged_items($pagenum = 1, $per_page = 100) {

		$squareoffs_api = squareoffs_get_api();

		$meta  = array(
			'per_page'    => $per_page,
			'total_items' => 0,
			'total_pages' => 0,
		);

		if ( empty( $squareoffs_api ) || is_wp_error( $squareoffs_api ) ) {
			return;
		}

		$data = $squareoffs_api->get_squareoffs( $pagenum, $meta['per_page'] );
		

		if ( ! $data || is_wp_error( $data ) ) {
			return;
		}
		// $this->assoc = false;
		$data->data = array_map( array( $this, 'format_item' ), $data->square_offs ) ;
		unset($data->square_offs );
		return $data;
	}

	/**
	 * Format & Sanitize raw comment object ready for use in the list table.
	 *
	 * @param  StdClass $item SquareOff Object.
	 * @return array Comment array.
	 */
	function format_item( $item ) {

		$votes1    = $this->squareoffs_check_integer( 'side_1_votes_count', $item );
		$votes2    = $this->squareoffs_check_integer( 'side_2_votes_count', $item );
		$comments1 = $this->squareoffs_check_integer( 'side_1_comments_count', $item );
		$comments2 = $this->squareoffs_check_integer( 'side_2_comments_count', $item );

		if($this->assoc === true){
			$item = array(
				'ID'          => sanitize_text_field( $item->uuid ),
				'uuid'        => sanitize_text_field( $item->uuid ),
				'title'       => sanitize_text_field( $item->question ),
				'squareoffs_side-1'   => sprintf( '%s (%d votes)', sanitize_text_field( $item->side_1_title ), $votes1 ),
				'squareoffs_side-2'   => sprintf( '%s (%d votes)', sanitize_text_field( $item->side_2_title ), $votes2 ),
				'squareoffs_user'     => sanitize_text_field( $item->creator->name ),
				'squareoffs_category' => sanitize_text_field( $item->category->name ),
				'squareoffs_comments' => $comments1 + $comments2,
				'external_id' => absint( $item->external_id ),
				'created_at'  =>  date('Y-m-d H:i:s',strtotime(sanitize_text_field( $item->created_at ))),
			);
		}else{
			$item = array(
				sanitize_text_field( $item->question ),
				sprintf( '%s (%d votes)', sanitize_text_field( $item->side_1_title ), $votes1 ),
				sprintf( '%s (%d votes)', sanitize_text_field( $item->side_2_title ), $votes2 ),
				sanitize_text_field( $item->creator->name ),
				sanitize_text_field( $item->category->name ),
				$comments1 + $comments2,
				absint( $item->external_id ),
				sanitize_text_field( $item->created_at ),
				sanitize_text_field( $item->uuid ),
			);
		}

		return $item;
	}

	/**
	 * Default columns
	 *
	 * @param object $item column data.
	 * @param string $column_name column name.
	 *
	 * @return string
	 */
	function column_default( $item, $column_name ) {

		switch ( $column_name ) {
			case 'title':
			case 'squareoffs_side-1':
			case 'squareoffs_side-2':
			case 'squareoffs_user':
			case 'squareoffs_category':
			case 'squareoffs_comments':
			case 'external_id':
				return $item[ $column_name ];
			case 'created_at':
				return DATE('Y-m-d h:i:s A',strtotime($item[ $column_name ]));
			default:
				return $item['title'];
		}

	}

	/**
	 * Display title plus row actions.
	 *
	 * Note escaping, because WordPress does not do anything at the point of output to secure this.
	 *
	 * @param array $item title data.
	 * @return string
	 */
	function column_title( $item ) {

		$base_url = add_query_arg( array(
			'page'   => 'squareoffs',
			'id'     => $item['uuid'],
		), admin_url() );

		$actions = array(
			'squareoffs-edit' => sprintf(
				'<a href="%s">%s</a>',
				esc_url( add_query_arg( 'action', 'squareoffs-edit', $base_url ) ),
				esc_html__( 'Edit', 'squareoffs' )
			),
			'squareoffs-details' => sprintf(
				'<a href="%s">%s</a>',
				esc_url( add_query_arg( 'action', 'squareoffs-details', $base_url ) ),
				esc_html__( 'Details', 'squareoffs' )
			),
			'squareoffs-delete' => sprintf(
				'<a href="%s">%s</a>',
				esc_url( add_query_arg( array(
					'action' => 'squareoffs-delete',
					'nonce'  => wp_create_nonce( 'squareoffs_squareoffs_action' ),
				), $base_url ) ),
				esc_html__( 'Delete', 'squareoffs' )
			),
		);

		return sprintf( '<strong><a class="row-title" href="%s" aria-label="%s">%s</a></strong> %s',
			esc_url( sprintf( '?page=squareoffs&action=squareoffs-edit&id=%s"', $item['ID'] ) ),
			// Translators: Edit <squareoff title>.
			esc_html( sprintf( __( 'Edit “%s”', 'squareoffs' ), $item['title'] ) ),
			esc_html( $item['title'] ),
			$this->row_actions( $actions ) // Already escaped.
		);

	}

	/**
	 * Checkbox column for bulk actions
	 *
	 * @param array $item item data.
	 * @return string
	 */
	function column_cb( $item ) {

		return sprintf(
			'<label class="screen-reader-text" for="cb-select-%1$s">%2$s</label><input type="checkbox" id="cb-select-%1$s" name="%3$s[]" value="%1$s" />',
			esc_attr( $item['ID'] ),
			esc_html( __( 'Select ', 'squareoffs' ) . $item['title'] ),
			esc_attr( $this->_args['singular'] )
		);

	}

	/**
	 * Define columns
	 *
	 * @return array
	 */
	function get_columns() {

		return array(
			'cb'          => '<input type="checkbox" />',
			'title'       => __( 'Question', 'squareoffs' ),
			'squareoffs_side-1'   => __( 'Side 1', 'squareoffs' ),
			'squareoffs_side-2'   => __( 'Side 2', 'squareoffs' ),
			'squareoffs_user'     => __( 'User', 'squareoffs' ),
			'squareoffs_category' => __( 'Category', 'squareoffs' ),
			'squareoffs_comments' => __( 'Comments', 'squareoffs' ),
			'external_id' => __( 'Squareoff ID', 'squareoffs' ),
			'created_at' => __( 'Created', 'squareoffs' ),
		);

	}

	/**
	 * Sortable columns
	 *
	 *  Todo: add sortable columns.
	 *
	 * @return array
	 */
	function get_sortable_columns() {

		return array('created_at');

	}

	/**
	 * Custom User link
	 *
	 * @param array $item link data.
	 */
	function column_squareoffs_user( $item ) {
		echo esc_attr( $item['squareoffs_user'] );
	}

	/**
	 * Custom Category link
	 *
	 * @param array $item category data.
	 */
	function column_squareoffs_category( $item ) {
		echo esc_attr( $item['squareoffs_category'] );
	}

}
