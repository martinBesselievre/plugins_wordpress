<div id="gp-game-box">
	<input type="hidden" name="gp_game_nonce" value="<?php echo wp_create_nonce( GP_Editor::NONCE ); ?>" />
	
	<input type="hidden" name="gp_game_id" class="-js-game-id" value="<?php echo $post_game_id;?>" />
	<input type="hidden" name="gp_game_edition_id" class="-js-game-edition-id" value="<?php echo $post_game_edition_id;?>"  />
	
	<div class="-js-game-search-box" style="<?php echo ((!empty($post_game_id)) ? 'display:none;' : '') ?>">
		<input type="text" name="game-search" class="-js-game-search"  />
		
		<select name="gp_game_platform" class="-js-game-platform">

		</select>
	</div>
	
	<div class="-js-game-box-item">
	<?php
	GP_Editor::display_game_item($post_game_id, $post_game_platform);
	?>
	</div>
</div>