<?php 
/*
* Shop Top Navigation Bar
*/ ?>
<div class="top-nav">
	<div class="shop-nav alignleft">
		<button class="menu-toggle"><span class="fa fa-2x fa-reorder fa-border"></span></button>
		<?php get_template_part( 'extensions/woocommerce/templates/shop-nav-menu' ); ?>
	</div>
	<div class="cart-link alignright">
		<?php get_template_part( 'extensions/woocommerce/templates/shopping-cart-link' ); ?>
	</div>
	<div class="clear"></div>
</div>
