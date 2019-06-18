<?php

/**
 * Gamer Prices Admin class
 *
 *
 * @package GamerPrices
 * @since 1.0
 */
class GP_Admin {
	
	const NONCE = 'gamerprices-update-key';
	
	private static $initiated = false;
	
	public static function init() {
		if (! self::$initiated) {
			self::init_hooks ();
		}
	}
	
	public static function init_hooks() {
		self::$initiated = true;
		
		add_action ( 'admin_menu', array (
			'GP_Admin',
			'admin_menu' 
		), 5 );
		
		add_action ( 'admin_notices', array (
			'GP_Admin',
			'display_notice' 
		) );
	}
	
	/**
	 * Add Admin Menu
	 */
	public static function admin_menu() {
		$hook = add_options_page ( 
			__ ( 'GamerPrices', 'gamerprices' ), 
			__ ( 'GamerPrices', 'gamerprices' ), 
			'manage_options', 
			'gamerprices-key-config', 
			array (
				'GP_Admin',
				'display_page' 
			)
		);
		
		if (version_compare ( $GLOBALS ['wp_version'], '3.3', '>=' )) {
			add_action ( "load-$hook", array (
					'GP_Admin',
					'admin_help' 
			) );
		}
	}
	
	/**
	 * Add help to the admin GamerPrices page
	 */
	public static function admin_help() {
		$current_screen = get_current_screen ();
	}
	
	/**
	 * Display notice
	 */
	public static function display_notice() {
		global $hook_suffix;
		
		if ($hook_suffix == 'plugins.php' && ! GamerPrices::has_api_key ()) {
			GamerPrices::view ( 'admin', 'notice');
		}
	}
	
	/**
	 * Display page
	 */
	public static function display_page() {
		self::display_config_page ();
	}
	
	/**
	 * Display Configuration Page
	 */
	public static function display_config_page() {
		if (function_exists ( 'current_user_can' ) && ! current_user_can ( 'manage_options' )) {
			die ( 'Unauthorized' );
		}
		
		$incorrect_api_key = FALSE;
		$success_api_key = FALSE;
		$roles = array();
		
		if (isset ( $_POST ['action'] ) && $_POST ['action'] == 'enter-key' && wp_verify_nonce ( $_POST ['_wpnonce'], self::NONCE )) {
			if (empty ( $_POST ['key'] )) {
				if (GamerPrices::has_api_key ()) {
					GamerPrices::update_api_key ( NULL );
				}
			} elseif ($_POST ['key'] != GamerPrices::get_api_key ()) {
				if (GamerPrices::verify_api_key ( $_POST ['key'] )) {
					GamerPrices::update_api_key ( $_POST ['key'] );
					$success_change_key = TRUE;
				} else {
					$incorrect_api_key = TRUE;
				}
			}
		}

		if (GamerPrices::has_api_key ()) {
			$roles = GamerPrices::api()->check_api_key();
		}
		
		GamerPrices::view ( 'admin', 'config', compact ( 'roles', 'incorrect_api_key', 'success_api_key' ) );
	}
	
	/**
	 * Return the Admin page URL
	 */
	public static function get_page_url( ) {
		return add_query_arg ( array (
			'page' => 'gamerprices-key-config' 
		), admin_url ( 'options-general.php' ) );
	}
	
}