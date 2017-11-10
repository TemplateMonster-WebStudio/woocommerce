<?php
	if( ! function_exists('WC') ){ return; }

	$cart = WC()->cart;
	$items_count = $cart->get_cart_contents_count();

	/*
	$shipping = $cart->get_cart_shipping_total();
	$tax = $cart->get_cart_tax();
	$total = $cart->get_total();
	$discount = $cart->get_total_discount();
	*/

	$text_before = '<span class="button-text-wrap"><i class="fa fa-shopping-cart"></i> ';
	$text_after = " <i class=\"count\">{$items_count}</i></span>";

	$button_text = sprintf( __( '%sCart%s', 'woo-wrapper' ), $text_before, $text_after );
?>
<div class="cart-navbar-wrapper right">
	<a class="navbar-toggle" data-toggle="cart"><?php echo $button_text ?></a>
	<div class="cart-content-wrapper navbar-content widget woocommerce widget_shopping_cart" data-panel="cart">
		<div class="widget_shopping_cart_content">
			<?php wc_get_template( 'cart/mini-cart.php' ); ?>
		</div>
	</div>
</div>
