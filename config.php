<h2 class="gp-header">
	<span style="color: #000080;">
		<span style="color: #000080;">
			<strong><span style="color: #333333;">Gamer</span></strong>
			<span style="color: #ffc000;"><strong>Prices</strong></span>
		</span>
	</span>
</h2>

<div>
	<p><?php esc_html_e('To Activate GamerPrices widgets enter a valid API key', 'gamerprices'); ?></p>

	<?php if($success_api_key) { ?>
	<p>
	<?php esc_html_e('Your API Key is incorrect', 'gamerprices'); ?>
	</p>
	<?php } ?>

	<?php if($success_api_key) { ?>
	<p>
	<?php esc_html_e('Your API Key is successful changed', 'gamerprices'); ?>
	</p>
	<?php } ?>
	
	<?php if(!empty($roles)) { ?>
	<p>
	<?php esc_html_e('Your account is active', 'gamerprices');  ?>
	</p>
	<?php } ?>

	<div class="activate-highlight secondary activate-option">
		<div class="option-description">
			<strong><?php esc_html_e('Enter your API key', 'gamerprices'); ?></strong>
		</div>
		<form action="<?php echo esc_url( GP_Admin::get_page_url() ); ?>"
			method="POST" id="gamerprices-enter-api-key" class="right">
			<input id="key" name="key" type="text" size="15"
				value="<?php echo esc_attr( GamerPrices::get_api_key() ); ?>"
				class="regular-text code" />
			<input type="hidden" name="action"
				value="enter-key" />
			<?php wp_nonce_field( GP_Admin::NONCE ); ?>
		
			<input type="submit" name="submit" id="submit"
				class="button button-secondary"
				value="<?php esc_attr_e('Use this key', 'gamerprices');?>" />
		</form>
	</div>
</div>