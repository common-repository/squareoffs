<?php
/**
 * Insert SquareOffs into tinyMCE functionality
 *
 * @package squareoffs
 */

add_action( 'admin_init', 'squareoffs_insert_squareoff_init' );

/**
 * Setup Insert Squareoff functionality
 *
 * @return void
 */
function squareoffs_insert_squareoff_init() {

	$api = squareoffs_get_api();

	// if ( $api && ! is_wp_error( $api ) && $api->is_authenticated() ) {
		add_filter( 'media_buttons', 'squareoffs_add_media_buttons' );
	// }

	add_action( 'print_media_templates', 'squareoffs_media_templates' );
	add_action( 'wp_enqueue_media', 'squareoffs_insert_squareoff_scripts' );

}

/**
 * Load scripts/styles.
 *
 * Hooked in on admin_enqueue_scripts.
 *
 * @return void
 */
function squareoffs_insert_squareoff_scripts() {
	if(is_blockeditor_active()) return;

	wp_enqueue_style( 'squareoffs-admin', SQUAREOFFS_PLUGIN_URL . 'css/admin.css' );
	wp_enqueue_style( 'squareoffs-media-button', trailingslashit( SQUAREOFFS_PLUGIN_URL ) . 'css/modal.css', array( 'squareoffs-datepicker' ) );
	wp_enqueue_script( 'squareoffs-media-button', trailingslashit( SQUAREOFFS_PLUGIN_URL ) . 'js/media-button.js', array( 'jquery', 'backbone', 'squareoffs-upload-image', 'squareoffs-datepicker' ), SQUAREOFFS_PLUGIN_VERSION, true );

	$script_data = array(
		'endpoints' => array(
			'squareoffs' => get_rest_url( null, '/squareoffs/v1/squareoffs' ),
		),
		'strings' => array(
			'buttonTooltip'     => __( 'Add SquareOffs Embed', 'squareoffs' ),
			'windowInsertTitle' => __( 'Insert SquareOffs Embed', 'squareoffs' ),
			'idLabel'           => __( 'SquareOff', 'squareoffs' ),
			'idPlaceholder'     => __( 'Select a SquareOff', 'squareoffs' ),
			'sizeLabel'         => __( 'Size', 'squareoffs' ),
			'sizeLabelSmall'    => __( 'Small', 'squareoffs' ),
			'sizeLabelWide'     => __( 'Wide', 'squareoffs' ),
		),
		'iconSrc'    => trailingslashit( SQUAREOFFS_PLUGIN_URL ) . 'images/squareoffs-icon-preview.png',
		'nonce'      => wp_create_nonce( 'wp_rest' ),
		'categories' => array(),
	);

	// Add available categories.
	foreach ( squareoffs_get_categories() as $category ) {
		$script_data['categories'][] = array(
			'uuid' => $category->uuid,
			'name' => $category->name,
		);
	}

	wp_localize_script( 'editor', 'soData', $script_data );
	wp_localize_script( 'squareoffs-media-button', 'soData', $script_data );

	$css = sprintf(
		'.squareoffs-insert:before { background: transparent url( \'%s\' ) no-repeat center center; background-size: contain; }',
		esc_url( trailingslashit( SQUAREOFFS_PLUGIN_URL ) . 'images/squareoffs-icon-tinymce.png' )
	);

	wp_add_inline_style( 'squareoffs-media-button', $css );

	add_editor_style( trailingslashit( SQUAREOFFS_PLUGIN_URL ) . 'css/editor.css' );
	wp_enqueue_script( 'soguten' );

}

/**
 * Render the new Add SquareOff button
 *
 * @param string $editor_id Editor Id.
 * @return void
 */
function squareoffs_add_media_buttons( $editor_id ) {
	printf(
		'<button type="button" id="%s" class="button squareoffs-insert" data-editor="%s">%s</button>',
		'squareoffs-insert-squareoff',
		esc_attr( $editor_id ),
		esc_html__( 'Add SquareOff', 'squareoffs' )
	);
}

/**
 * Ouptut SquareOff media button templates.
 *
 * @return void
 */
function squareoffs_media_templates() {
	if(is_blockeditor_active()) return;
	$templates = array(
		'modal-insert',
		'modal-insert-content-default',
		'modal-insert-content-new',
		'modal-insert-content-existing',
		'modal-insert-content-disconnected',
		'modal-insert-content-login',
		'tinymce-preview',
	);

	array_walk( $templates, 'squareoffs_render_template' );

}

/**
 * Render a template script.
 *
 * Template directory structure is fixed, and the file name should match the ID.
 *
 * @param  string $template_id Template Id.
 * @return void
 */
function squareoffs_render_template( $template_id ) {

	ob_start();
	require_once( SQUAREOFFS_PLUGIN_PATH . 'js/templates/' . $template_id . '.php' );
	$template = ob_get_clean();

	printf(
		'<script type="text/html" id="%s">%s</script>',
		'tmpl-squareoffs-' . esc_attr( $template_id ),
		//@codingStandardsIgnoreLine
		$template
	);

}

/**
 * Check if Block Editor is active.
 * Must only be used after plugins_loaded action is fired.
 *
 * @return bool
 */
function is_blockeditor_activexx() {
	var_dump(isBlockEditorActive());
	die();
    // Gutenberg plugin is installed and activated.
    $gutenberg = ! ( false === has_filter( 'replace_editor', 'gutenberg_init' ) );

    // Block editor since 5.0.
    $block_editor = version_compare( $GLOBALS['wp_version'], '5.0-beta', '>' );

    if ( ! $gutenberg && ! $block_editor ) {
        return false;
    }

    if ( is_classic_editor_plugin_active() ) {
        $editor_option       = get_option( 'classic-editor-replace' );
        $block_editor_active = array( 'no-replace', 'block' );

        return in_array( $editor_option, $block_editor_active, true );
    }

    return true;
}

function is_blockeditor_active() {
    if ( function_exists( 'is_gutenberg_page' ) &&
            is_gutenberg_page()
    ) {
        // The Gutenberg plugin is on.
        return true;
    }
    $current_screen = get_current_screen();
    if ( method_exists( $current_screen, 'is_block_editor' ) &&
            $current_screen->is_block_editor()
    ) {
        // Gutenberg page on 5+.
        return true;
    }
    return false;
}

/**
 * Check if Classic Editor plugin is active.
 *
 * @return bool
 */
function is_classic_editor_plugin_active() {
    if ( ! function_exists( 'is_plugin_active' ) ) {
        include_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    if ( is_plugin_active( 'classic-editor/classic-editor.php' ) ) {
        return true;
    }

    return false;
}
