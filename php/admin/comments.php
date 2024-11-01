<?php
/**
 * SquareOffs Comments Moderation
 *
 * @package squareoffs
 */

$squareoffs_list_comments = new Squareoffs_Comments_List_Table();
$squareoffs_list_comments->fetch_items();
$squareoffs_list_comments->prepare_items();

$page = isset( $_REQUEST['page'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['page'] ) ) : '';  // WPCS: CSRF ok.

?>

<div class="wrap">
	<h1><?php esc_html_e( 'SquareOffs Comments Moderation', 'squareoffs' ); ?></h1>
	<hr class="wp-header-end">
	<h2 class="screen-reader-text"><?php esc_html_e( 'Filter comments list', 'squareoffs' ); ?></h2>

	<?php $squareoffs_list_comments->views(); ?>

	<form id="comments-filter" method="get">
		<input type="hidden" name="page" value="<?php echo esc_attr( $page ); ?>" />
		<?php $squareoffs_list_comments->display(); ?>
	</form>
</div>
