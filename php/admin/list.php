<?php
/**
 * Squareoffs list, edit or delete templates.
 *
 * @package squareoffs
 */

/**
 * SquareOffs edit or delete existing SquareOff.
 */
if ( isset( $_GET['id'] ) && isset( $_GET['action'] ) ) { // WPCS: CSRF ok.

	if ( 'squareoffs-edit' === $_GET['action'] ) { // WPCS: CSRF ok.
		require_once( SQUAREOFFS_PLUGIN_PATH . 'php/admin/edit.php' );
	}

	if ( 'squareoffs-details' === $_GET['action'] ) { // WPCS: CSRF ok.
		require_once( SQUAREOFFS_PLUGIN_PATH . 'php/admin/details.php' );
	}

	return;

}
?>


<div class="wrap">

	<h1><?php esc_html_e( 'SquareOffs', 'squareoffs' ); ?></h1>
	<table class="squareoffs" param-id="<?php echo wp_create_nonce( 'squareoffs_squareoffs_action' ); ?>">
        <thead>
            <tr>
                <th>Question</th>
                <th>Side 1</th>
                <th>Side 2</th>
                <th>User</th>
                <th>Category</th>
                <th>Comments</th>
                <th>SquareOff ID</th>
                <th>Created</th>
            </tr>
        </thead>
        <tfoot>
             <tr>
                <th>Question</th>
                <th>Side 1</th>
                <th>Side 2</th>
                <th>User</th>
                <th>Category</th>
                <th>Comments</th>
                <th>SquareOff ID</th>
                <th>Created</th>
            </tr>
        </tfoot>
    </table>

</div>
