<?php
/**
 * Comments List Table.
 *
 * @package squareoffs
 */

/**
 * SquareOffs List Table.
 */
class Squareoffs_List_Table extends WP_List_Table {

	/**
	 * Default orderby.
	 *
	 * @var string
	 */
	protected $orderby = 'created_at';

	/**
	 * Default order.
	 *
	 * @var string
	 */
	protected $order = 'desc';

	/**
	 * Constructor.
	 *
	 * @param array $args Args array.
	 */
	function __construct( $args = array() ) {

		$allowed_orderby = array_keys( $this->get_sortable_columns() );
		$allowed_order   = array( 'asc', 'desc' );

		$order   = isset( $_GET['order'] ) ? sanitize_text_field( wp_unslash( $_GET['order'] ) ) : '';  // WPCS: CSRF ok.
		$orderby = isset( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : '';  // WPCS: CSRF ok.

		if ( in_array( $orderby, $allowed_orderby, true ) ) {
			$this->orderby = $orderby;
		}

		if ( in_array( $order, $allowed_order, true ) ) {
			$this->order = $order;
		}

		parent::__construct( $args );

	}

	/**
	 * Check if field exists and is a valid integer
	 *
	 * @param string $api_label name of field.
	 * @param object $item column data.
	 *
	 * @return int
	 */
	function squareoffs_check_integer( $api_label, $item ) {

		if ( ! isset( $item->$api_label ) ) {
			return 0;
		}

		return absint( $item->$api_label );

	}

	/**
	 * Set items using raw API data.
	 * Maps each item to a formatted version.
	 * If you already have formatted items, you can just set it directly.
	 *
	 * @param array $items Raw item data.
	 */
	function set_items( $items ) {
		$this->items = array_map( array( $this, 'format_item' ), $items );
	}

	/**
	 * Get columns to hide.
	 *
	 * @return array Hidden columns.
	 */
	function get_hidden_columns() {
		return array();
	}

	/**
	 * Prepare items for display
	 *
	 * @uses $this->_column_headers
	 * @uses $this->items
	 * @uses $this->get_columns()
	 * @uses $this->get_sortable_columns()
	 * @uses $this->get_pagenum()
	 * @uses $this->set_pagination_args()
	 */
	function prepare_items() {

		$columns  = $this->get_columns();
		$hidden   = $this->get_hidden_columns();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );
		if ( $this->items ) {
			usort( $this->items, array( $this, 'item_sort_callback' ) );
		}

	}

	/**
	 * Sort callback for columns
	 *
	 * @param array $a to sort.
	 * @param array $b sorted.
	 *
	 * @return int
	 */
	public function item_sort_callback( $a, $b ) {
		$result  = strcmp( $a[ $this->orderby ], $b[ $this->orderby ] );
		return ( 'asc' === $this->order ) ? $result : -$result;
	}

}
