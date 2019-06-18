<div class="gp-game-module">
    <div class="game-thumbnail">
        <a href="<?php echo GamerPrices::get_site_url() ?>" class="icon-gp" target="_blank"></a>
        <img src="<?php echo GamerPrices::get_static_images_url($game_compare->game->image); ?>" alt="<?php echo $game_compare->game->name . " " .$game_platform; ?>"/>
    </div>
    <div class="game-name">
       <?php echo $game_compare->game->name; ?>
       <?php 
       if(!empty($game_compare->game->edition)) {
       	echo " " . $game_compare->game->edition;
       }
       ?>
    </div>
    <div class="selected-platform">
        <i class="icon-platform <?php echo $game_platform; ?>"></i>
    </div>
    <ol class="shop-list">
    <?php 
    foreach($game_compare->prices->$game_platform as $index => $shop_price) {
    ?>
        <li>
            <a href="<?php echo GamerPrices::get_site_url( $game_compare->game->urls->$game_platform ); ?>" target="_blank"
            	 title="<?php echo $shop_price->shop; ?>">
                <div class="logo">
                    <img class="logo-shop" src="<?php echo GamerPrices::get_static_images_url($shop_price->shopImg); ?>" alt="<?php echo $shop_price->shop?>"/>
                </div>
                <div class="price <?php echo (($index === 0) ? 'top-price' : '')?>">
                	<?php 
                		$format_price = number_format($shop_price->price, 2);
                		$prices = explode(".", $format_price);
                	?>
                	<?php echo $prices[0]; ?>.<sup><?php echo $prices[1] . $shop_price->currencySymbol; ?></sup></div>
                <div class="method <?php echo $shop_price->type?> <?php echo ((!empty($shop_price->drm)) ? strtolower($shop_price->drm) : '')?>"></div>
            </a>
        </li>
	<?php
    }
    ?>
    </ol>
<!--     <a class="show-more" href="#">afficher tous les prix</a> -->
	<?php 
	if(count($game_compare->game->platforms) > 1) {
	?>
    <div class="availability">
        <span>Disponible également sur</span>
        <?php 
        foreach($game_compare->game->platforms as $platform) {
        	if($platform == $game_platform) continue;
        ?>
        <a href="<?php echo GamerPrices::get_site_url( $game_compare->game->urls->$platform ); ?>" target="_blank">
        	<i class="icon-platform <?php echo $platform; ?>"></i>
        </a>
        <?php 
        }
        ?>
    </div>
    <?php 
	}
	?>
</div>