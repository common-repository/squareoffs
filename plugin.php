<?php
/**
 * Plugin Name:     SquareOffs
 * Plugin URI:      http://www.squareoffs.com/
 * Description:     Integrate your SquareOffs microdebates with your WordPress site.
 * Author:          SquareOffs
 * Author URI:      https://www.squareoffs.com/
 * Text Domain:     squareoffs
 * Domain Path:     /languages
 * Version:         2.4.1
 *
 * @package         Squareoffs
 */

define( 'SQUAREOFFS_PLUGIN_VERSION', '2.4.1' );
defined( 'SQUAREOFFS_PLUGIN_PATH' ) || define( 'SQUAREOFFS_PLUGIN_PATH', plugin_dir_path( __FILE__ ) );
defined( 'SQUAREOFFS_PLUGIN_URL' ) || define( 'SQUAREOFFS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
defined( 'SQUAREOFFS_BASENAME' ) || define( 'SQUAREOFFS_BASENAME', plugin_basename( __FILE__ ) );

add_action( 'init', 'squareoffs_init' );

/**
 * Init SquareOffs plugin.
 *
 * @return void
 */
function squareoffs_init() {

	require_once( SQUAREOFFS_PLUGIN_PATH . 'php/class-squareoffs-api.php' );
	require_once( SQUAREOFFS_PLUGIN_PATH . 'php/class-squareoffs-internal-api.php' );
	require_once( SQUAREOFFS_PLUGIN_PATH . 'php/shortcode.php' );
	require_once( SQUAREOFFS_PLUGIN_PATH . 'php/media-button.php' );
	require_once( SQUAREOFFS_PLUGIN_PATH . 'php/sanitization.php' );
	require_once( SQUAREOFFS_PLUGIN_PATH . 'php/categories.php' );
	require_once( SQUAREOFFS_PLUGIN_PATH . 'php/admin/class-squareoffs-messages.php' );
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
	require_once( SQUAREOFFS_PLUGIN_PATH . 'php/admin/class-squareoffs-list-table.php' );
	require_once( SQUAREOFFS_PLUGIN_PATH . 'php/admin/class-squareoffs-squareoffs-list-table.php' );
	require_once( SQUAREOFFS_PLUGIN_PATH . 'php/admin/class-squareoffs-comments-list-table.php' );
	require_once( SQUAREOFFS_PLUGIN_PATH . 'php/admin/admin.php' );
	require_once( SQUAREOFFS_PLUGIN_PATH . 'php/admin/options.php' );
	require_once( SQUAREOFFS_PLUGIN_PATH . 'php/admin/squareoffs.php' );

	$squareoffs_api = squareoffs_get_api();

	if ( $squareoffs_api && ! is_wp_error( $squareoffs_api ) ) {
		$internal_api = new Squareoffs_Internal_Api( $squareoffs_api );
	}

}

$squareoffs_api = null;

/**
 * Connect to API if properly authenticated.
 *
 * @param  int $user_id WordPress User ID.
 * @return Squareoffs_Api
 */
function squareoffs_get_api( $user_id = null ) {

	global $squareoffs_api;

	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}

	if ( ! $user_id ) {
		return false;
	}

	if ( $squareoffs_api && $squareoffs_api instanceof Squareoffs_Api ) {
		return $squareoffs_api;
	}

	// $credentials = get_option( 'squareoffs_user_data_' . $user_id, array() );
    $credentials = so_get_user_api_credentials();

	if ( ! empty( $credentials['email'] ) && ! empty( $credentials['token'] ) && ! empty( $credentials['uuid'] ) ) {
		$squareoffs_api = new Squareoffs_Api( $credentials['email'], $credentials['token'], $credentials['uuid'] );
		return $squareoffs_api;
	}else{
        return false;
    }

}

function sogutenInit() {
    // if(is_admin() && !squareoffs_get_api())return;
    // automatically load dependencies and version
    $asset_file = include( plugin_dir_path( __FILE__ ) . 'build/index.asset.php');
 	wp_enqueue_style( 'squareoffs-iframe', SQUAREOFFS_PLUGIN_URL . 'css/iframe.css' );
    wp_enqueue_style( 'squareoffs-cropper', SQUAREOFFS_PLUGIN_URL . 'css/cropper.css' );
    wp_register_script(
        'soguten',
        plugins_url( 'build/index.js', __FILE__ ),
        $asset_file['dependencies'],
        $asset_file['version']
    );

    if(is_admin() && is_edit_page()){
        $squareoffs_api = squareoffs_get_api();
        if(!$squareoffs_api) $api_connected = false;
        else $api_connected = $squareoffs_api->get_user_profile();
        if(is_wp_error($api_connected)) $api_connected = false;
        else{
            $api_connected = true;
        }
        wp_localize_script( 'soguten', 'soVars', array("soURL" => SQUAREOFFS_PLUGIN_URL, 'api_connected' => $api_connected, "soMargins" => get_user_meta(get_current_user_id(),'soMargins', true)));
    }

    if(function_exists('register_block_type')){
        register_block_type( 'squareoffs/blocks', array(
            'editor_script' => 'soguten',
            'render_callback' => 'squareoffs_render_callback',
            'attributes' => [
                'soID' => [
                    'type' => 'integer',
                    'default' => 0
                ],
                'soSize' => [
                    'type' => 'string',
                    'default' => "wide"
                ],
                'soAlignment' => [
                    'type' => 'string',
                    'default' => "left"
                ],
            ]
        ) );
    } 
}
add_action( 'init', 'sogutenInit' );

function is_edit_page($new_edit = null){
    global $pagenow;
    //make sure we are on the backend
    if (!is_admin()) return false;

    
    if($new_edit == "edit")
        return in_array( $pagenow, array( 'post.php',  ) );
    elseif($new_edit == "new") //check for new post page
        return in_array( $pagenow, array( 'post-new.php' ) );
    else //check for either new or edit
        return in_array( $pagenow, array( 'post.php', 'post-new.php' ) );
}

function squareoffs_render_callback($atts,$content=null){
    if($content) return $content;
    elseif($atts["soID"]) return squareoffs_iframe_shortcode(array("id" => $atts["soID"], "size" => $atts["soSize"], "align" => $atts["soAlignment"]));
    else return "";
}

function soGetCategories(){
	global $squareoffs_api;
    
	$squareoffs_api = squareoffs_get_api();

	if ( $squareoffs_api && ! is_wp_error( $squareoffs_api ) ) {
		$response = $squareoffs_api->get_categories( $atts );
		echo json_encode($response);
	}else{
		echo "[]";
	}
	die();
}
add_action('wp_ajax_soGetCategories', 'soGetCategories');

function soUpdateMargins($soMargins = null, $ret = false){
    //print_r($_POST);
    $margins = ($soMargins?$soMargins:$_POST['soMargins']);
    $soMargins = json_decode(stripslashes($margins), true);
    update_user_meta(get_current_user_id(),'soMargins', array("top"=>$soMargins["top"],"left"=>$soMargins["left"],"bottom"=>$soMargins["bottom"],"right"=>$soMargins["right"]));
    if(!$ret) echo json_encode($soMargins);
    else return $soMargins;
    die();
}
add_action('wp_ajax_soUpdateMargins', 'soUpdateMargins');

function soCreateSquareOffs(){
    global $squareoffs_api;
    $atts = wp_unslash($_POST);
    
    $squareoffs_api = squareoffs_get_api();

    if ( $squareoffs_api && ! is_wp_error( $squareoffs_api ) ) {
        $response = $squareoffs_api->create_squareoff( $atts );
        soUpdateMargins($atts["soMargins"], true);
        echo json_encode($response);
    }else{
        echo "[]";
    }
    die();
}
add_action('wp_ajax_soCreateSquareOffs', 'soCreateSquareOffs');

function soGetSquareOff(){
    global $squareoffs_api;
    $atts = wp_unslash($_POST);
    
    $squareoffs_api = squareoffs_get_api();

    if ( $squareoffs_api && ! is_wp_error( $squareoffs_api ) ) {
        $response = $squareoffs_api->get_squareoff( $atts["soID"] );
        echo json_encode($response);
    }else{
        echo "[]";
    }
    die();
}
add_action('wp_ajax_soGetSquareOff', 'soGetSquareOff');

function soConnect(){
    $data = squareoffs_options_sanitize_connect_account( wp_unslash( array("email"=>$_POST['soEmail'], "password"=>$_POST["soPassword"] ))); // WPCS: sanitization ok.
    if(squareoffs_options_connect_account( $data ) === true) echo json_encode(array('success' => 1));
    else echo json_encode(array('success' => 0));
    die();
}
add_action('wp_ajax_soConnect', 'soConnect');

function soIframe(){
    // global $squareoffs_api;
    $data = $_POST;
    // $squareoffs_api = squareoffs_get_api();

    // if ( $squareoffs_api && ! is_wp_error( $squareoffs_api ) ) {
    //     $so_squareoff = $squareoffs_api->get_squareoff($data["soID"]);
    //     echo json_encode(array("so"=>$so_squareoff,"api"=>$squareoffs_api));
        echo json_encode(convertIFrameToJSON(squareoffs_render_callback($data)));
        // echo squareoffs_render_callback($data);
    // }
    die();
}
add_action('wp_ajax_soIframe', 'soIframe');

function soIframe2(){
    global $squareoffs_api;
    $atts = wp_unslash($_POST);
    
    $squareoffs_api = squareoffs_get_api();

    if ( $squareoffs_api && ! is_wp_error( $squareoffs_api ) ) {
        $response = $squareoffs_api->get_squareoff( $atts["soID"] );
        echo json_encode($response);
    }else{
        echo "[]";
    }
    die();
}
add_action('wp_ajax_soIframe2', 'soIframe2');

function convertIFrameToJSON($iframe){
    $iframe = str_replace("<iframe", "", $iframe);
    $iframe = str_replace("</iframe>", "", $iframe);
    $iframe = str_replace(">", "", $iframe);
    $el = explode('" ', $iframe);
    $atts = array();
    foreach($el as $i=>$att){
        $k = explode('="', $att);
        if(trim($k[0])){
            if(trim($k[0]) == "style"){
                $s = explode(";", $k[1]);
                foreach ($s as $e => $v) {
                    $p = explode(":", $v);
                    if(trim($p[0]) != "") $atts["style"][trim($p[0])] = trim($p[1]);
                }
            }
            elseif(trim($k[0]) == "class") $atts["className"] = $k[1];
            elseif(trim($k[0]) == "frameborder") $atts["frameBorder"] = $k[1];
            else $atts[trim($k[0])] = $k[1]; 
        }
    }
    return $atts;
}

function add_so_scripts() {
  wp_enqueue_script( 'quareoffs-js', '//squareoffs.com/assets/embed.js', array(), SQUAREOFFS_PLUGIN_VERSION );
}
add_action( 'wp_enqueue_scripts', 'add_so_scripts' );



function soGetSquareOffs(){
    global $squareoffs_api;
    $atts = wp_unslash($_POST);
    
    if (!$squareoffs_api) $squareoffs_api = squareoffs_get_api();

    if ( $squareoffs_api && ! is_wp_error( $squareoffs_api ) ) {
        $squareoffs_list_table = new Squareoffs_SquareOffs_List_Table();
        $data = $squareoffs_list_table->fetch_paged_items($_GET['page'], $_GET['per_page']);
        echo json_encode($data);
    }else{
        echo "[]";
    }
    die();
}
add_action('wp_ajax_soGetSquareOffs', 'soGetSquareOffs');