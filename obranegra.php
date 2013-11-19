<?
/*
    Plugin Name: Obra Negra
    Plugin URI: http://www.buildingon.ru/ins
    Description: States
    Author: Marcel Miranda
    Version: 1.66666666
    Author URI: http://www.reaktivo.com
*/

/*/////////////////////////////////////////////////////////////////////
// obranegra_admin is called when the obranegra admin page is opened //
/////////////////////////////////////////////////////////////////////*/

function obranegra_admin() {
  global $wpdb;
  $text = $_POST['obranegra_css'];
  if ($text) {
    $wpdb->insert(on_table(), array('text' => $text));
    echo '<div class="updated"><p><strong>New state saved.</strong></p></div>';
    obranegra_save_states();
  }

  $states = on_states();

  $latest_css = '';
  if($states) {
    $latest_css = end($states);
    reset($states);
  }
  $states_count = count($states);
  $foreground = obranegra_int2hex($states_count);
  $background = obranegra_int2hex(255 - $states_count);

  include('views/obranegra-admin.php');
}


/*///////////////////////////////////////////////////////////////
// obranegra_save_states is called after saving new state to a //
// file to be accesed by the client.                           //
///////////////////////////////////////////////////////////////*/

function obranegra_save_states() {
  global $wpdb;

  $states = on_states();
  $states = array_map('obranegra_map_states', $states);

  $str = "window.OBRANEGRA_STATES = ";
  $str .= json_encode($states);

  file_put_contents(dirname(__FILE__).'/states/obranegra.js', $str);
}


/*/////////////////////////////////////////////////////////////////////
// obranegra_admin_assets setups js and css assets on the admin page //
/////////////////////////////////////////////////////////////////////*/

function obranegra_admin_assets($hook) {
  global $obranegra_hook;
  if ($hook != $obranegra_hook) return;
  wp_enqueue_style('codemirror', plugins_url('js/lib/codemirror-3.19/lib/codemirror.css', __FILE__));
  wp_enqueue_script('codemirror', plugins_url('js/lib/codemirror-3.19/lib/codemirror.js', __FILE__));
  wp_enqueue_script('codemirrorcss', plugins_url('js/lib/codemirror-3.19/mode/css/css.js', __FILE__));
  wp_enqueue_style('obranegra-admin', plugins_url('css/obranegra-admin.css', __FILE__));
  wp_enqueue_script('obranegra-admin', plugins_url('js/obranegra-admin.js', __FILE__));
}
add_action( 'admin_enqueue_scripts', 'obranegra_admin_assets' );


/*///////////////////////////////////////////////////////////////////////
// obranegra_client_assets setups js and css assets on the client page //
///////////////////////////////////////////////////////////////////////*/

function obranegra_client_assets() {
  global $wpdb;
  $count = $wpdb->get_var( "SELECT COUNT(*) FROM " . on_table() );
  wp_enqueue_script('obranegra-states', plugins_url('states/obranegra.js', __FILE__), false, $count);
  wp_enqueue_script('ba-throttle-debounce', plugins_url('js/lib/jquery.ba-throttle-debounce.js', __FILE__), array('jquery'));
  wp_enqueue_script('obranegra-client', plugins_url('js/obranegra-client.js', __FILE__), array('jquery', 'ba-throttle-debounce'));
}
add_action( 'wp_enqueue_scripts', 'obranegra_client_assets' );


/*/////////////////////////////////////////////////////////////////
// obranegra_admin_actions sets up a submenu on Wordpress' admin //
/////////////////////////////////////////////////////////////////*/

function obranegra_admin_actions() {
  global $obranegra_hook;
  $title = "Obra Negra";
  $obranegra_hook = add_theme_page($title, $title, 1, 'obranegra', "obranegra_admin");
}
add_action('admin_menu', 'obranegra_admin_actions');


/*/////////////////////////////////////////////////////////////////
// obranegra_install is run when activating the obranegra plugin //
/////////////////////////////////////////////////////////////////*/

function obranegra_install() {
   global $wpdb;

   $table_name = on_table();

   $sql = "CREATE TABLE $table_name (
  id mediumint(9) NOT NULL AUTO_INCREMENT,
  time timestamp DEFAULT CURRENT_TIMESTAMP NOT NULL,
  text text NOT NULL,
  UNIQUE KEY id (id)
    );";

   require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
   dbDelta( $sql );
}
register_activation_hook( __FILE__, 'obranegra_install' );


/*//////////////////
// helper methods //
//////////////////*/

function on_table() {
  global $wpdb;
  return $wpdb->prefix . "obranegra";
}

function on_states() {
  global $wpdb;
  return $wpdb->get_results("SELECT * FROM " . on_table() . " ORDER BY time ASC");
}

function obranegra_map_states($obj) {
  return $obj->text;
}

function obranegra_int2hex($int) {
  $str = strtoupper(dechex($int));
  if (strlen($str) == 1) $str = "0" . $str;
  return "#" . $str . $str . $str;

}
