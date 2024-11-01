<?php
/**
 * View SquareOff.
 *
 * @package squareoffs
 */

// Work out the sizes for the votes graph.
$max = max( array(
	absint( $squareoff->side_1_votes_count ),
	absint( $squareoff->side_2_votes_count ),
) );

$graph_height_1 = 0;
$graph_height_2 = 0;

if ( $max ) {
	$graph_height_1 = absint( $squareoff->side_1_votes_count ) / $max * 100;
	$graph_height_2 = absint( $squareoff->side_2_votes_count ) / $max * 100;
}

?>

<h1><?php esc_html_e( 'SquareOff', 'squareoffs' ); ?> <?php echo esc_html( $squareoff->question ); ?></h1>

<h2><?php esc_html_e( 'Results', 'squareoffs' ); ?></h2>

<div class="squareoffs-view">

	<?php if ( ! empty( $squareoff->cover_photo_url ) ) : ?>
		<p class="squareoffs-image-view" id="squareoffs-url-cover-preview">
			<img src="<?php echo esc_url( $squareoff->cover_photo_url ); ?>" alt="<?php esc_html_e( 'SquareOff cover photo', 'squareoffs' ); ?>" />
		</p>
	<?php endif; ?>

	<table class="squareoffs-view-table" >
		<caption class="screen-reader-text"><?php esc_html_e( 'Results' , 'squareoffs' ); ?></caption>
		<thead>
			<tr>
				<th scope="col"><?php esc_html_e( 'Side 1', 'squareoffs' ); ?></th>
				<th scope="col"><?php esc_html_e( 'Side 2', 'squareoffs' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					<h3 class="squareoffs-view-table-title"><?php echo esc_html( $squareoff->side_1_title ); ?></h3>
					<p><?php echo  esc_html( $squareoff->side_1_defense ); ?></p>
				</td>
				<td>
					<h3 class="squareoffs-view-table-title"><?php echo esc_html( $squareoff->side_2_title ); ?></h3>
					<p><?php echo esc_html( $squareoff->side_2_defense ); ?></p>
				</td>
			</tr>
			<tr>
				<td>
					<span class="squareoffs-graph-container">
						<span class="squareoffs-graph" style="height: <?php echo absint( $graph_height_1 ); ?>%"></span>
					</span>
					<span class="squareoffs-graph-label">
						<?php
						// Translators: Number of votes for side 1.
						echo esc_html( sprintf( _n( '%d vote', '%d votes', $squareoff->side_1_votes_count, 'squareoffs' ), $squareoff->side_1_votes_count ) );
						?>
					</span>
				</td>
				<td>
					<span class="squareoffs-graph-container">
						<span class="squareoffs-graph squareoffs-graph-alt" style="height: <?php echo absint( $graph_height_2 ); ?>%"></span>
					</span>
					<span class="squareoffs-graph-label">
						<?php
						// Translators: Number of votes for side 2.
						echo esc_html( sprintf( _n( '%d vote', '%d votes', $squareoff->side_2_votes_count, 'squareoffs' ), $squareoff->side_2_votes_count ) );
						?>
					</span>
				</td>
			</tr>
			<tr>
				<td>
					<?php if ( ! empty( $squareoff->side_1_photo_url ) ) : ?>
						<div class="squareoffs-image-view" id="squareoffs-image-side-1-preview">
							<img src="<?php echo esc_url( $squareoff->side_1_photo_url ); ?>" alt="<?php esc_html_e( 'Side 1 photo', 'squareoffs' ); ?>" />
						</div>
					<?php endif; ?>
				</td>
				<td>
					<?php if ( ! empty( $squareoff->side_2_photo_url ) ) : ?>
						<div class="squareoffs-image-view" id="squareoffs-image-side-2-preview">
							<img src="<?php echo esc_url( $squareoff->side_2_photo_url ); ?>" alt="<?php esc_html_e( 'Side 2 photo', 'squareoffs' ); ?>" />
						</div>
					<?php endif; ?>
				</td>
			</tr>
		</tbody>
	</table>

	<h2><?php esc_html_e( 'Details' , 'squareoffs' ); ?></h2>


	<p><span class="squareoffs-form-row-label"><?php esc_html_e( 'End Time Squareoff' , 'squareoffs' ); ?></span> <?php echo esc_html( squareoffs_render_date( $squareoff->end_date ) ); ?></p>
	<p><span class="squareoffs-form-row-label"><?php esc_html_e( 'Category' , 'squareoffs' ); ?></span> <?php echo esc_html( $squareoff->category->name ); ?></p>

	<?php if ( ! empty( $squareoff->tag_list ) ) : ?>
		<p><span class="squareoffs-form-row-label"><?php esc_html_e( 'Tags' , 'squareoffs' ); ?></span> <?php echo esc_html( $squareoff->tag_list ); ?></p>
	<?php endif; ?>

</div>
