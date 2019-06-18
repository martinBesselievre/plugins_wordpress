<div style="clear:both;">
	<div style="display:inline-block;max-width:300px;">
		<img src="<?php echo GamerPrices::get_static_images_url($game_compare->game->image) ?>" width="100%" />
	</div>
	<div style="display:inline-block;vertical-align:top;">
		<a href="#" class="-js-game-remove">Changer</a>
		<br />
		<u>Nom : </u> <?php echo $game_compare->game->name; ?>
		<br />
		<?php 
      	if(!empty($game_compare->game->edition)) {
      	?>
		<u>Edition : </u> <?php echo $game_compare->game->edition; ?>
		<br />
		<?php 
      	}
      	?>
      	<u>Meilleurs Prix sur : </u> 
      	<ul>
      	<?php
      	foreach($game_compare->prices as $platform => $shopsPrice) {
      	?>
      		<li><?php echo strtoupper($platform); ?> : <?php echo $shopsPrice[0]->price . " " . $shopsPrice[0]->currencySymbol; ?></li>
      	<?php 
      	}
      	?>
      	</ul>
	</div>
</div>