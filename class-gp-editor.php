<?php

/**
 * GamerPrices Editor class
 *
 * Extends the articles with custom data
 *
 * @package GamerPrices
 * @since 1.0
 */
class GP_Editor {
	
	const NONCE = 'gamerprices-editor';
	const POSTDATA_GAME_ID = 'gp_game_id';
	const POSTDATA_GAME_EDITION_ID = 'gp_game_edition_id';
	const POSTDATA_GAME_PLATFORM = 'gp_game_platform';
	
	private static $initiated = false;
	
	public static function init() {
		if (! self::$initiated) {
			self::init_hooks ();
		}
	}
	
	public static function init_hooks() {
		self::$initiated = true;
		if (GamerPrices::has_api_key ()) {
			add_action ( 'admin_enqueue_scripts', array (
					'GP_Editor',
					'load_resources' 
			) );
			add_action( 'wp_ajax_gp_search_game', array (
					'GP_Editor',
					'search_game' 
			) );
			add_action( 'wp_ajax_gp_display_game_item', array (
					'GP_Editor',
					'display_game_item'
			) );
			
			add_action ( 'add_meta_boxes_post', array (
					'GP_Editor',
					'display_boxs' 
			) );
			add_action ( 'save_post', array (
					'GP_Editor',
					'save_postdata' 
			) );
		}
	}
	
	public static function load_resources() {
		wp_register_script ( 'gamerprices-editor.js', GAMERPRICES__PLUGIN_URL . '_inc/gamerprices-editor.js', array (
				'jquery',
				'jquery-ui-autocomplete' 
		), GAMERPRICES_VERSION );
		wp_enqueue_script ( 'gamerprices-editor.js' );
	}
	
	public static function display_boxs() {
		if (is_active_widget ( false, false, GP_Game_Widget::WIDGET_ID ) && current_user_can( 'edit_page', get_the_ID() )) {
			add_meta_box(
				'gp-game-box',
				__( 'Compare Game', 'gamerprices' ),
				array(
					'GP_Editor',
					'display_game_box'
				)
			);
		}
	}
	
	public function display_game_box() {
		$game_platform = NULL;
		$game_id = NULL;
		
		$post_id = get_the_ID ();
		if ($post_id) {
			$game_platform = get_post_meta ( $post_id, self::POSTDATA_GAME_PLATFORM, true );
			$game_id = get_post_meta ( $post_id, self::POSTDATA_GAME_ID, true );
			$game_edition_id = get_post_meta ( $post_id, self::POSTDATA_GAME_EDITION_ID, true );
		}
		
		GamerPrices::view ( 'editor', 'game-box', array (
				'type' => 'plugin',
				'post_game_id' => $game_id,
				'post_game_edition_id' => $game_edition_id,
				'post_game_platform' => (empty ( $game_platform ) ? GamerPrices::$platforms_available [0] : $game_platform),
				'platforms' => GamerPrices::$platforms_available 
		) );
	}
	
	public static function display_game_item($game_id = NULL, $game_platform = NULL, $game_edition_id = NULL) {
		foreach ( array (
				'game_id',
				'game_platform',
				'game_edition_id'
		) as $parameter ) {
			if (isset ( $_GET [$parameter] )) {
				$$parameter = $_GET [$parameter];
			}
		}
		if (! empty ( $game_id ) && ! empty ( $game_platform )) {
			$game = GamerPrices::api ()->compare ( $game_id, $game_platform );
			if (! empty ( $game )) {
				GamerPrices::view ( 'editor', 'game-box-item', array (
						'game_compare' => GamerPrices::api ()->compare ( $game_id, $game_platform, $game_edition_id ) 
				) );
			}
		}
		
		if (defined ( 'DOING_AJAX' ) && DOING_AJAX) {
			wp_die ();
		}
	}
	
	public static function search_game() {
		$result = array ();
		if (! empty ( $_GET ['term'] ) && ! empty ( $_GET ['platform'] )) {
			$result = GamerPrices::api ()->search ( $_GET ['term'], $_GET ['platform'] );
		}
		
		wp_send_json ( $result );
	}
	
	public static function save_postdata() {
		$post_id = get_the_ID ();

		if (! wp_verify_nonce ( $_POST ['gp_game_nonce'], self::NONCE ) ||
		     ('page' == $_POST ['post_type'] && ! current_user_can ( 'edit_page', $post_id )) || 
		     ! current_user_can ( 'edit_post', $post_id )) {
			return;
		}
		
		foreach ( array (
				self::POSTDATA_GAME_PLATFORM,
				self::POSTDATA_GAME_ID ,
				self::POSTDATA_GAME_EDITION_ID
		) as $post_meta_key ) {
			
			$new_data = (isset ( $_POST [$post_meta_key] ) ? $_POST [$post_meta_key] : NULL);
			$bak_data = get_post_meta ( $post_id, $post_meta_key, true );
			
			if (empty ( $new_data )) {
				if (! empty ( $bak_data )) {
					delete_post_meta ( $post_id, $post_meta_key, $bak_data );
				}
			} else if (empty ( $bak_data )) {
				add_post_meta ( $post_id, $post_meta_key, $new_data, TRUE );
			} else {
				update_post_meta ( $post_id, $post_meta_key, $new_data, $bak_data );
			}
		}
	}
	
}