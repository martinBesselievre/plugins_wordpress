jQuery( function ( $ ) {
	
	function GameBox($el) {
		this.$el = $el;
		
		this.init();
		this.setupEvents();
	};
	GameBox.prototype.init = function() {
		this.$gameSearch = this.$el.find('.-js-game-search');
		this.$gamePlatform = this.$el.find('.-js-game-platform');
		this.$gameId = this.$el.find('.-js-game-id');
		this.$gameEditionId = this.$el.find('.-js-game-edition-id');
		
		this.$gameSearchBox = this.$el.find('.-js-game-search-box');
		this.$gameBoxItem = this.$el.find('.-js-game-box-item');
	};
	GameBox.prototype.setupEvents = function() {
		// Autocompletion
		this.$gameSearch.autocomplete({
			source: $.proxy(function(request, response) {
				$.getJSON(
					ajaxurl,
					$.extend({}, request, {
						action   : 'gp_search_game',
						platform : this.$gamePlatform.val()
					}),
					response
				);
			}, this),
			minLength: 2,
			select: $.proxy(this.onSelectGame, this)
		}).autocomplete( "instance" )._renderItem = GameBox.renderAutocompleteGame;
		
		this.$gamePlatform.change($.proxy(this.updateGameItem, this));
		
		this.$el.on('click', '.-js-game-remove', $.proxy(this.onSelectGame, this));
	};
	GameBox.renderAutocompleteGame = function( ul, item ) {
		return $( "<li>" )
		.append( "<a>" + item.name + ((item.edition) ? ' - <em>' + item.edition + '</em>' : '') + "</a>" )
		.appendTo( ul );
	};
	GameBox.prototype.onSelectGame = function(event, ui) {
		event.preventDefault();
		
		this.$gameId.val( ((ui && ui.item) ? ui.item.gameMasterId : '') );
		this.$gameEditionId.val( ((ui && ui.item && ui.item.editionId) ? ui.item.editionId : '') );
		this.updateGameItem();
	};
	GameBox.prototype.updateGameItem = function() {
		this.$gameBoxItem.html('');
		
		if(this.$gameId.val()) {
			this.$gameSearchBox.hide();
			
			this.$gameBoxItem.load( ajaxurl + '?' + $.param({
				'action' : 'gp_display_game_item',
				'game_id': this.$gameId.val(),
				'game_platform' : this.$gamePlatform.val(),
				'game_edition_id' : this.$gameEditionId.val()
			}), $.proxy(function() {
				if(this.$gameBoxItem.is(':empty')) {
					this.$gameSearchBox.show();
				} else {
					this.$gameSearchBox.hide();
				}
			}, this));
		} else {
			this.$gameSearchBox.show();
		}
	};
	
	new GameBox($('#gp-game-box'));
});