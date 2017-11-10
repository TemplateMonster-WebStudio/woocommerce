'use strict';

/** Quantity field wrapper */
;(function($, doc, window){

	$(doc).ready(function(){
		var $quantity = $('form .quantity');

		$quantity.each( function(){
			var $this = $(this),
				$input = $('.input-text.qty', $this),
				$plus = $('<span/>', { 'class':'tm-qty-plus'}),
				$minus = $('<span/>', { 'class':'tm-qty-minus'});

			function clickCallback(e){
				var $this = $(this),
					$min = parseInt($this.attr( 'min' )),
					$max = parseInt($this.attr( 'max' )),
					$current = parseInt($this.val());

				$min = isNaN($min)? 1: $min;
				$max = isNaN($max)? '': $max;

				if( $(e.currentTarget).hasClass('tm-qty-minus') ){
					$this.val( Math.max( $current - 1, $min ) );
				}else if( $max != '' ){
					$this.val( Math.min( $current + 1, $max ) );
				}else{
					$this.val( $current + 1 );
				}

				$this.trigger('change');

				return false;
			}

			$plus.on('click', $.proxy( clickCallback, $input ));
			$minus.on('click', $.proxy( clickCallback, $input ));

			$this.append($plus);
			$this.append($minus);
		} );
	});
})(jQuery, document, window)

/** Navbar toggle plugin */
;(function($, doc, win) {
	
	function setupToggle( target ){

		var _toggle = $(target),
			_panel_selector = _toggle.data('toggle'),
			_panel;

		_panel = $('[data-panel="' + _panel_selector + '"]').eq(0);

		if( 0 == _panel.length ){
			return;
		}

		_toggle.click($.proxy(toggleState, _panel));
		_panel.hide();
	};

	function toggleState(e) {
		e.stopPropagation();

		var _this = this;

		if (_this.hasClass('open')) {
			$(document).off('.navbar-toggle');
			_this.stop(true).slideUp();
		} else {
			_this.stop(true).slideDown(400, function() {
				$(document).on('click.navbar-toggle', _this, function(e) {
					if ( e.target != _this[0]
						&& !$.contains(_this[0], e.target) ) {
						toggleState.call(_this, e);
					}
				});
			});
		}
		_this.toggleClass('open');
	};

	win.setToggle = function( target ){
		$(target).each(function(){
			setupToggle(this);
		})
	};

	$(doc).ready(function(){
		setToggle('.navbar-toggle');
	});
})(jQuery, document, window)
