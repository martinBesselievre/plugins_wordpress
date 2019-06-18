<?php

/**
 * Gamer Prices class
 *
 *
 * @package GamerPrices
 * @since 1.0
 */
class GamerPrices {

	const API_KEY = 'gamerprices_api_key';
	public static $platforms_available = array('pc', 'ps4', 'xboxone', 'wiiu', 'ps3', 'xbox360', 'nintendods', 'psvita', 'wii', 'psp', 'linux', 'mac');
	private static $api = NULL;
	
	/**
	 * Activation
	 *
	 * @static
	 *
	 */
	public static function plugin_activation() {
	}
	
	/**
	 * Deactivation
	 *
	 * @static
	 */
	public static function plugin_deactivation() {
		return self::update_api_key ( NULL );
	}
	
	/**
	 * Get the GamerPrices API
	 */
	public static function api() {
		if (! self::has_api_key ()) {
			self::$api = NULL;
		} else if (is_null ( self::$api )) {
			self::$api = new GP_Api ( get_option ( self::API_KEY ) );
		}
		return self::$api;
	}
	
	/**
	 * @return true if API Key existed
	 */
	public static function has_api_key() {
		return self::get_api_key () !== FALSE;
	}
	
	/**
	 * @return the API Key
	 */
	public static function get_api_key() {
		return get_option ( self::API_KEY );
	}
	
	/**
	 * Update API Key
	 * @param string $key
	 */
	public static function update_api_key( $key ) {
		if (is_null ( $key )) {
			delete_option ( self::API_KEY );
		} else {
			update_option ( self::API_KEY, $key );
		}
		self::$api = NULL;
	}
	
	/**
	 * Verify API key
	 * @param string $key
	 */
	public static function verify_api_key( $key ) {
		$result = (new GP_Api ( $key ))->check_api_key ( );
		return (is_array ( $result )) ? in_array ( 'ROLE_USER', $result ) : FALSE;
	}
	
	/**
	 * Include view file
	 * 
	 * @param string $dir
	 * @param string $name
	 * @param array $args
	 */
	public static function view( $dir, $name, array $args = array() ) {
		foreach ( $args as $key => $val ) {
			$$key = $val;
		}
		
		load_plugin_textdomain ( 'gamerprices' );
		
		$file = GAMERPRICES__PLUGIN_DIR . 'views/' . $dir . '/' . $name . '.php';
		
		include $file;
	}
	
	public static function get_static_images_url( $path = '' ) {
		return "https://statics.gamerprices.com/images/" . $path;
	}
	
	public static function get_site_url( $path = '' ) {
		return "https://www.gamerprices.com/" . $path;
	}
	
}

?>