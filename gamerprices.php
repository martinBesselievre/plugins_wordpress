<?php
/*
 * Plugin Name: GamerPrices
 * Plugin URI: https://www.gamerprices.com
 * Description: Description <strong>GamerPrices</strong>
 * Version: 1.0
 * Author: GamerPrices
 * Text Domain: gamerprices
 * Domain Path : /languages
 */
if (! function_exists ( 'add_action' )) {
	exit ();
}

define ( 'GAMERPRICES_VERSION', '1.0' );
define ( 'GAMERPRICES__MINIMUM_WP_VERSION', '3.0' );
define ( 'GAMERPRICES__PLUGIN_URL', plugin_dir_url ( __FILE__ ) );
define ( 'GAMERPRICES__PLUGIN_DIR', plugin_dir_path ( __FILE__ ) );

require_once (GAMERPRICES__PLUGIN_DIR . 'class-gp.php');

register_activation_hook ( __FILE__, array (
	'GamerPrices',
	'plugin_activation' 
) );
register_deactivation_hook ( __FILE__, array (
	'GamerPrices',
	'plugin_deactivation' 
) );

// add_action( 'init', array( 'GamerPrices', 'init' ) );

require_once (GAMERPRICES__PLUGIN_DIR . 'class-gp-api.php');

// Widgets 
require_once (GAMERPRICES__PLUGIN_DIR . 'class-gp-game-widget.php');
// require_once (GAMERPRICES__PLUGIN_DIR . 'class-gp-tops-widget.php');

function gamerprices_register_widgets() {
	register_widget ( 'GP_Game_Widget' );
// 	register_widget ( 'GP_Tops_Widget' );
}

add_action ( 'widgets_init', 'gamerprices_register_widgets' );

if (is_admin ()) {
	require_once (GAMERPRICES__PLUGIN_DIR . 'class-gp-admin.php');
	add_action ( 'init', array (
		'GP_Admin',
		'init'
	) );
}

require_once (GAMERPRICES__PLUGIN_DIR . 'class-gp-editor.php');
add_action ( 'init', array (
	'GP_Editor',
	'init'
) );
