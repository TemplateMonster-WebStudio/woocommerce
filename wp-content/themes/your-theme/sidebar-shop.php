<?php
/**
* The sidebar containing the shop widget area
*/

if ( ! is_active_sidebar( 'sidebar-shop' ) ) {
	return;
}
?>
<aside id="shop-widget-area" class="widget-area" role="complementary">
	<?php dynamic_sidebar( 'sidebar-shop' ); ?>
</aside><!-- #shop-widget-area -->
