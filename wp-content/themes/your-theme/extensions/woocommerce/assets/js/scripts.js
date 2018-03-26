'use strict';


/** Quantity field wrapper */
!(function($, doc, window){

	function setupQuantityControls(){
		var $quantity = $('form .quantity');

		$quantity.each(function(){
			var $this = $(this),
				$input = $('.input-text.qty', $this),
				$plus = !!$('.tm-qty-plus', $this)[0]? $('.tm-qty-plus', $this):$('<span/>', { 'class':'tm-qty-plus'}),
				$minus = !!$('.tm-qty-minus', $this)[0]? $('.tm-qty-minus', $this):$('<span/>', { 'class':'tm-qty-minus'});

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

			$plus.off('click');
			$minus.off('click');

			$plus.on('click', $.proxy( clickCallback, $input ));
			$minus.on('click', $.proxy( clickCallback, $input ));

			$this.append($plus);
			$this.append($minus);
		});
	}

	$(doc).ready(setupQuantityControls);
	$(doc).on('wc_fragments_refreshed', 'body', setupQuantityControls);
})(jQuery, document, window);


/** Navbar toggle plugin */
!(function($, doc, win) {
	
	function setupToggle( target, commonParent ){

		var _toggle = $(target),
			_parent = $(commonParent)[0]? $(commonParent): _toggle.parent(),
			_panel_selector = _toggle.data('toggle'),
			_panel;

		_panel = $('[data-panel="' + _panel_selector + '"]', _parent).eq(0);

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
})(jQuery, document, window);


/** Plugin to switch layout classes depending on window resolution */
function WooLayoutSwitch( options ){
	var options = options||{};

	this.settings = this.extend( this.defaults, options );
	this.elem = document.querySelector( this.settings.selector );

	if( this.elem ){
		this.init();
	}
}

WooLayoutSwitch.prototype = {
	name: 'WooLayoutSwitch',
	elem: null,
	settings: {},
	defaults: {
		switchPoints : {
			0:{ items:1 },
			480:{ items:2 },
			992:{ items:3 },
			1200:{ items:4 },
		},
		timeout: 50,
		selector: 'div.woocommerce[class*="columns-"]',
		prefix: 'columns-',
	},
	extend: function(...rest) {
		var result = {};

		for( var index in rest ){
			var props = rest[index];

			for ( var prop in props ){
				result[prop] = props[prop];
			}
		}

		return result;
	},
	addListeners: function(){
		window.addEventListener( 'resize', this.onResize.bind(this) );
	},
	removeListeners: function(){
		window.removeEventListener( 'resize', this.onResize.bind(this) );
	},
	onResize: function( e ){
		if( this.timeoutID ){
			clearTimeout( this.timeoutID );
		}
		this.timeoutID = setTimeout( this.doSwitchLayout.bind( this, e ), this.settings.timeout );
	},
	doSwitchLayout: function( e ){
		var self = this,
		winw = window.outerWidth,
		switchPoint;

		for( var resolution in this.settings.switchPoints ){
			if( winw >= parseInt( resolution ) ){
				switchPoint = this.settings.switchPoints[resolution];
			}
		}

		if( 'items' in switchPoint ){
			
			var classes = self.elem.classList;

			classes.forEach( function( _class, index ){
				if ( _class.search( RegExp(self.settings.prefix) ) >= 0 ){
					var new_class = self.settings.prefix + switchPoint.items;
					classes.replace( _class, new_class );
				}
			} );

			self.elem.querySelectorAll( '.last' )
			.forEach( function( el, index ){
				el.classList.remove( 'last' );
			});
			self.elem.querySelectorAll( '.first' )
			.forEach( function( el, index ){
				el.classList.remove( 'first' );
			});

			self.elem.querySelectorAll( 'li.product:nth-child(' + switchPoint.items + 'n)' )
			.forEach( function(el, index){
				el.classList.add( 'last' );
			});
			
			self.elem.querySelectorAll( 'li.product:nth-child(' + switchPoint.items + 'n+1)' )
			.forEach( function(el, index){
				el.classList.add( 'first' );
			});
		}
	},
	init: function() {
		this.removeListeners();
		this.addListeners();
	},
}

var opts = {};
/** Make sure that the related products section follows the same layout rules */
if( document.body.classList.contains( 'single-product' ) ){
	opts = {
		switchPoints : {
			0:{ items:1 },
			480:{ items:2 },
			992:{ items:4 },
			1200:{ items:5 },
		},
		timeout: 50,
		selector: 'div.woocommerce[class*="columns-"]',
		prefix: 'columns-',
	}
}
var wooLayoutSwitch = new WooLayoutSwitch( opts );
window.dispatchEvent( new Event( 'resize' ) );
