<script type="text/javascript">
function gpChangePlatform(platform) {
  var filterByPlatform = '[data-platform=' + platform + ']';
  jQuery('.w-most-viewed .w-games li').hide().filter(filterByPlatform).show();
  jQuery('.w-most-viewed .w-filters .w-filter').removeClass('active').filter(filterByPlatform).addClass('active');
}
</script>


<div class="w-most-viewed">
	<div class="w-title">jeux <span>les plus consultés</span></div>
	<div class="w-filters">
		<ul class="w-platform-filters">
		<?php
		foreach ( $platforms as $index => $platform ) {
		?>
			<li
				class="w-filter <?php echo ($index === 0) ? 'active' : '' ?>"
				data-platform="<?php echo $platform; ?>" data-toggle="tooltip"
				data-placement="top" title=""
				data-original-title="<?php echo strtoupper($platform); ?>">
				<a href="#"
					onclick="gpChangePlatform('<?php echo $platform; ?>');return false;">
						<?php echo strtoupper($platform); ?>
					</a>
			</li>
		<?php
		}
		?>
		</ul>
	</div>

	<ol class="w-games">
	<?php
	foreach ( $games_by_platform as $platform => $games ) {
		foreach ( $games as $game ) {
	?>
		<li data-platform="<?php echo $platform; ?>" style="<?php echo ($platform !== $platforms[0]) ? 'display:none;' : ''?>">
			<a href="<?php echo GamerPrices::get_site_url( $game->platforms->$platform->url ); ?>"
				target="_blank" title="Acheter <?php echo $game->alt ?>">
				<div class="w-games-thumb">
					<img
						src="<?php echo GamerPrices::get_static_images_url( $game->header ); ?>"
						alt="<?php echo $game->alt ?>" />
				</div>
				<div class="w-games-infos">
					<span class="game-name"><?php echo $game->name ?> <?php echo empty($game->edition) ? "" : $game->edition; ?></span>
					<span class="price"><?php echo $game->platforms->$platform->price . $game->platforms->$platform->currency; ?></span>
				</div>
			</a>
		</li>
		<?php
				}
			}
			?>
		<li>
	</ol>
</div>