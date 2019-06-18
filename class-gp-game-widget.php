<?php

/**
 * GamerPrices Game class
 *
 * Game Compare Widget
 *
 * @package GamerPrices
 * @since 1.0
 */
class GP_Game_Widget extends WP_Widget {
	
	const WIDGET_ID = 'gp_game_widget';

	function __construct() {
		parent::__construct ( self::WIDGET_ID, __ ( 'GamerPrices Compare Game Widget', 'gamerprices' ), array (
				'description' => __ ( 'Compare the prices of a game', 'gamerprices' ) 
		) );
		
		if (is_active_widget ( false, false, $this->id_base )) {
			add_action ( 'wp_enqueue_scripts', array (
					'GP_Game_Widget',
					'load_resources' 
			) );
		}
	}

	function load_resources() {
		wp_register_style ( 'w-compare-game', GAMERPRICES__PLUGIN_URL . '_inc/w-compare-game.css', array (), GAMERPRICES_VERSION );
		wp_enqueue_style ( 'w-compare-game' );
	}

	function widget( $args, $instance ) {
		global $post;
		
		if (! empty ( $post )) {
			$game_id = get_post_meta ( $post->ID, GP_Editor::POSTDATA_GAME_ID, true );
			$game_platform = get_post_meta ( $post->ID, GP_Editor::POSTDATA_GAME_PLATFORM, true );
			$game_edition_id = get_post_meta ( $post->ID, GP_Editor::POSTDATA_GAME_EDITION_ID, true );
			
			if (empty ( $game_id ) || empty ( $game_platform )) {
				return;
			}
			
			$game_compare = GamerPrices::api ()->compare ( $game_id, array (
					$game_platform 
			), $game_edition_id );
			
			if(empty ( $game_compare ) || !isset($game_compare->prices->$game_platform) || empty($game_compare->prices->$game_platform)) {
				return;
			}
			
			echo $args['before_widget'];
			
			GamerPrices::view ( 'widget', 'compare-game', compact ( 'game_compare', 'game_platform' ) );

			echo $args['after_widget'];
		}
	}
}
