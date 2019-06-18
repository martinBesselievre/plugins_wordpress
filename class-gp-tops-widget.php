<?php

/**
 * GamerPrices Game class
 *
 * Most Viewed Games Widget
 *
 * @package GamerPrices
 * @since 1.0
 */
class GP_Tops_Widget extends WP_Widget {
	
	const WIDGET_ID = 'gp_tops_widget';
	
	function __construct() {
		parent::__construct ( self::WIDGET_ID, __ ( 'GamerPrices Widget', 'gamerprices' ), array (
				'description' => __ ( 'Show the most viewed games', 'gamerprices' ) 
		) );
		
		if (is_active_widget ( false, false, $this->id_base )) {
			add_action ( 'wp_enqueue_scripts', array (
					'GP_Tops_Widget',
					'load_resources' 
			) );
		}
	}
	
	function load_resources() {
		wp_register_style ( 'w-tops-games', GAMERPRICES__PLUGIN_URL . '_inc/w-tops-games.css', array (), GAMERPRICES_VERSION );
		wp_enqueue_style ( 'w-tops-games' );
	}
	
	function form($instance) {
		if ($instance) {
			$platforms = $instance ['platforms'];
		} else {
			$platforms = array();
		}
		
		
		// Title
		
?>
<ul>
<?php
		// Platforms
		foreach ( GamerPrices::$platforms_available as $platform ) {
?>
<li id="platform-<?php echo $platform; ?>">
	<label class="selectit">
		<input value="<?php echo $platform; ?>" 
			type="checkbox" 
			name="<?php echo $this->get_field_name( 'platforms' ); ?>[]" 
			id="in-category-<?php echo $platform; ?>" 
			<?php echo ((in_array($platform, $platforms, TRUE)) ? 'checked="checked"' : '') ?>
			/> <?php echo $platform; ?>
	</label>
</li>
<?php
		}
?>
</ul>
<?php
	}
	
	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance ['platforms'] = $new_instance['platforms'];
		return $instance;
	}
	
	function widget($args, $instance) {
		$platforms = [ 'pc', 'ps4', 'xboxone', 'wiiu' ];
		
		if (! empty ( $instance ['platforms'] )) {
			$platforms = $instance ['platforms'];
		}
		
		$games_by_platform = GamerPrices::api()->tops( $platforms, 5 );
		
		if(empty ( $games_by_platform ) ) {
			return;
		}
			
		echo $args['before_widget'];
			
		GamerPrices::view ( 'widget', 'tops-games', compact ( 'platforms', 'games_by_platform' ) );
		
		echo $args['after_widget'];
	}
}