<?php
/**
 * Comments List Table.
 *
 * @package squareoffs
 */

/**
 * SquareOffs List Table.
 */
class Squareoffs_Comments_List_Table extends Squareoffs_List_Table {

	/**
	 * Default orderby.
	 *
	 * @var string
	 */
	protected $orderby = 'squareoffs_comment_created_at';

	/**
	 * Default order.
	 *
	 * @var string
	 */
	protected $order = 'desc';


	/**
	 * SquareOffs_List_Table constructor.
	 */
	function __construct() {
		parent::__construct( array(
			'singular' => 'comment',
			'plural'   => 'comments',
			'ajax'     => false,
		) );
	}

	/**
	 * Get SquareOffs data
	 *
	 * Returns squareoffs from the api.
	 *
	 * @return void
	 */
	function fetch_items() {

		$squareoffs_api = squareoffs_get_api();

		$meta = array(
			'per_page'    => 10,
			'total_items' => 0,
			'total_pages' => 0,
		);

		if ( empty( $squareoffs_api ) ) {
			return;
		}

		$data = $squareoffs_api->get_comments( null, $this->get_pagenum(), $this->squareoffs_get_status() );

		if ( ! $data || is_wp_error( $data ) ) {
			return;
		}

		$this->items = array_map( array( $this, 'format_item' ), $data->comments );

		$meta['total_items']      = absint( $data->meta->total_count );
		$meta['total_pages']      = absint( $data->meta->total_pages );

		$meta['comments_summary'] = (array) $data->meta->comments_summary;
		set_transient( 'squareoffs_current_comment_status', $meta['comments_summary'] );

		$this->set_pagination_args( $meta );

	}


	/**
	 * Status filter.
	 *
	 * @return array $status_links
	 */
	function get_views() {

		$squareoffs_status    = $this->squareoffs_get_status();
		$status_links = array();
		$numbers      = get_transient( 'squareoffs_current_comment_status' );
		if ( false === $numbers ) {
			$numbers = array();
		}

		// Add active state numbers to approved and remove active from array.
		$numbers['approved'] = $numbers['active'] + $numbers['approved'];
		unset( $numbers['active'] );

		$stati = array(
			/* translators: %s: all comments count. */
			'total' => _nx_noop(
				'All <span class="count">(%s)</span>',
				'All <span class="count">(%s)</span>',
				'comments'
			), // Singular not used.

			/* translators: %s: pending comments count. */
			'pending' => _nx_noop(
				'Pending <span class="count">(%s)</span>',
				'Pending <span class="count">(%s)</span>',
				'comments'
			),

			/* translators: %s: approved comments count. */
			'approved' => _nx_noop(
				'Approved <span class="count">(%s)</span>',
				'Approved <span class="count">(%s)</span>',
				'comments'
			),

			/* translators: %s: removed comments count. */
			'removed' => _nx_noop(
				'Trash <span class="count">(%s)</span>',
				'Trash <span class="count">(%s)</span>',
				'comments'
			),
		);

		$link = admin_url( 'admin.php?page=squareoffs-comments' );
		if ( ! empty( $comment_type ) && 'all' !== $comment_type ) {
			$link = add_query_arg( 'comment_type', $comment_type, $link );
		}

		foreach ( $stati as $status => $label ) {

			$class  = ( $status === $squareoffs_status ) ? ' class="current"' : '';
			$link   = add_query_arg( 'current_status', $status, $link );

			if ( array_key_exists( $status, $numbers ) ) {
				$number = $numbers[ $status ];
			} else {
				$number = 0;
			}

			$status_links[ $status ] = "<a href='$link'$class>" . sprintf( translate_nooped_plural( $label, $number ),
				sprintf( '<span class="%s-count">%s</span>',
					esc_attr( $status ),
					number_format_i18n( $number )
				)
			) . '</a>';
		}

		return $status_links;
	}

	/**
	 * Gets status from query.
	 *
	 * @return string Status.
	 */
	function squareoffs_get_status() {
		$status = isset( $_REQUEST['current_status'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['current_status'] ) ) : 'all';  // WPCS: CSRF ok.

		if ( ! in_array( $status, array( 'all', 'pending', 'approved', 'removed', 'active' ), true ) ) {
			$status = 'all';
		}

		return $status;
	}

	/**
	 * Format & Sanitize raw comment object ready for use in the list table.
	 *
	 * @param  StdClass $item Comment Object.
	 * @return array Comment array.
	 */
	function format_item( $item ) {

		$formatted_item['ID']                    = sanitize_text_field( $item->uuid );
		$formatted_item['uuid']                  = sanitize_text_field( $item->uuid );
		$formatted_item['title']                 = sanitize_text_field( $item->text );

		$formatted_item['squareoffs_status']         = sanitize_text_field( $item->current_status );
		$formatted_item['squareoffs_approved']           = sanitize_text_field( $item->approved );
		$formatted_item['squareoffs_censored_text_flag'] = sanitize_text_field( $item->censored_text_flag );
		$formatted_item['squareoffs_spam_flag']          = sanitize_text_field( $this->squareoffs_check_integer( 'spam_flag', $item ) );
		$formatted_item['squareoffs_comment_user_name']  = sanitize_text_field( $item->user->name );
		$formatted_item['squareoffs_comment_created_at'] = sanitize_text_field( $item->created_at );
		$formatted_item['squareoffs_uuid']               = sanitize_text_field( $item->square_off_uuid );
		$formatted_item['squareoffs_question']           = sanitize_text_field( $item->square_off_question );
		$formatted_item['vote_uuid']             = sanitize_text_field( $item->vote_uuid );

		$formatted_item['squareoffs_title'] = sprintf(
			'<a href="%s">%s<a/>',
			esc_url( admin_url( 'admin.php?page=squareoffs&action=squareoffs-details&id=' . $item->square_off_uuid ) ),
			esc_html( $formatted_item['squareoffs_question'] )
		);

		return $formatted_item;
	}

	/**
	 * Default columns.
	 *
	 * @param object $item column data.
	 * @param string $column_name column name.
	 *
	 * @return string
	 */
	function column_default( $item, $column_name ) {

		switch ( $column_name ) {
			case 'title':
			case 'squareoffs_censored_text_flag':
			case 'squareoffs_spam_flag':
			case 'squareoffs_comment_user_name':
			case 'squareoffs_title':
				return $item[ $column_name ];
			case 'squareoffs_status':
				if ( 'active' === $item['squareoffs_status'] ) {
					return 'Approved';
				} else {
					return ucfirst( $item[ $column_name ] );
				}
				// no break, returns.
			case 'squareoffs_comment_created_at':
				return mysql2date( get_option( 'date_format' ), $item[ $column_name ] );
			default:
				return $item['title'];
		}

	}

	/**
	 * Display title plus row actions.
	 *
	 * Note escaping, because WordPress does not do anything at the point of output to secure this.
	 * Possible status:
	 * "pending" : not approved.
	 * "approved"
	 * "removed"
	 *
	 * @param array $item title data.
	 * @return string
	 */
	function column_title( $item ) {

		$page = isset( $_REQUEST['page'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['page'] ) ) : ''; // WPCS: CSRF ok.

		$base_url = add_query_arg( array(
			'page'    => $page,
			'id'      => $item['ID'],
			'squareoffs_id'   => $item['squareoffs_uuid'],
			'vote_id' => $item['vote_uuid'],
			'nonce'   => wp_create_nonce( 'squareoffs_comment_action' ),
		) );

		$actions = array();

		// Remove comment.
		if ( 'removed' !== $item['squareoffs_status'] ) {
			$actions['squareoffs-comment-remove'] = sprintf(
				'<a href="%s">%s</a>',
				esc_url( add_query_arg( 'action', 'squareoffs-comment-remove', $base_url ) ),
				esc_html__( 'Remove', 'squareoffs' )
			);
		}

		// Approve comment.
		if ( 'approved' !== $item['squareoffs_status'] && 'active' !== $item['squareoffs_status'] ) {
			$actions['squareoffs-comment-approve'] = sprintf(
				'<a href="%s">%s</a>',
				esc_url( add_query_arg( 'action', 'squareoffs-comment-approve', $base_url ) ),
				esc_html__( 'Approve', 'squareoffs' )
			);
		}

		return sprintf(
			'<strong class="row-title" aria-label="%s" tabindex="0">%s</strong> %s',
			// Translators: List table actions aria label.
			esc_html( sprintf( __( 'Show actions for “%s”', 'squareoffs' ), $item['title'] ) ),
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
	 * Define columns.
	 *
	 * @return array
	 */
	function get_columns() {

		return array(
			'cb'                     => '<input type="checkbox" />',
			'title'                  => __( 'Text comment', 'squareoffs' ),
			'squareoffs_title'               => __( 'SquareOff', 'squareoffs' ),
			'squareoffs_status'              => __( 'Status', 'squareoffs' ),
			'squareoffs_censored_text_flag'  => __( 'Censored', 'squareoffs' ),
			'squareoffs_spam_flag'           => __( 'Flag count', 'squareoffs' ),
			'squareoffs_comment_user_name'   => __( 'User', 'squareoffs' ),
			'squareoffs_comment_created_at'  => __( 'Created at', 'squareoffs' ),
		);

	}

	/**
	 * Sortable columns.
	 *
	 * Todo: add sortable columns.
	 *
	 * @return array
	 */
	function get_sortable_columns() {

		return array();

	}

	/**
	 * Get columns to hide.
	 *
	 * @return array Hidden columns.
	 */
	function get_hidden_columns() {

		$columns = array();
		$screen  = get_current_screen();

		if ( 'toplevel_page_squareoffs' === $screen->id && ! empty( $_GET['action'] ) && 'squareoffs-details' === $_GET['action'] ) { // WPCS: CSRF ok.
			$columns[] = 'squareoffs_title';
		}

		return $columns;
	}

}
