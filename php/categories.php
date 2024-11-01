<?php
/**
 * Handling of SO Categories.
 *
 * @package squareoffs
 */

add_action( 'admin_init', 'squareoffs_categories_cron_setup' );
add_action( 'squareoffs_fetch_categories', 'squareoffs_categories_cron_task' );

/**
 * Setup cron.
 */
function squareoffs_categories_cron_setup() {
	if ( ! wp_next_scheduled( 'squareoffs_fetch_categories' ) ) {
		wp_schedule_event( time(), 'daily', 'squareoffs_fetch_categories' );
	}
}

/**
 * Fetch categories cron task.
 *
 * Internally, calls squareoffs_get_categories and updates transient.
 *
 * @return void
 */
function squareoffs_categories_cron_task() {
	squareoffs_get_categories( true );
}

/**
 * Get categories.
 *
 * Whitelisted/Filtered, cached wrapper for the get categories endpoint.
 * Cached in transient for 1 day because these won't change too much.
 * Whitelisted to subset of categories used in create SO form.
 *
 * @param  boolean $force Force update.
 * @return array
 */
function squareoffs_get_categories( $force = false ) {

	$categories = get_transient( 'squareoffs_api_categories' );

	if ( ! $force && ! empty( $categories ) ) {
		return $categories;
	}

	$api = squareoffs_get_api();

	if ( $api && $api->is_authenticated() ) {
		$categories = $api->get_categories();
		if ( ! $categories || is_wp_error( $categories ) ) {
			return array();
		} else {
			$categories = array_filter( $categories, 'squareoffs_is_category_allowed' );
			set_transient( 'squareoffs_api_categories', $categories, WEEK_IN_SECONDS );
			return $categories;
		}
	} else {
		return array();
	}
}

/**
 * Category filter callback.
 * Filter to subset for display in create SO forms.
 *
 * @param  object $category Category object.
 * @return boolean
 */
function squareoffs_is_category_allowed( $category ) {

	$allowed = array(
		'life--2'   => 'e5e6883a-6e9c-4b7e-b247-43629ce4a5db',
		'news--2'   => 'a6840d90-1f50-4e8a-98d0-c1c1c5134a78',
		'sports--2' => '6c806976-e20c-4666-9d33-45896567c155',
	);

	return in_array( $category->uuid, $allowed, true );
}
