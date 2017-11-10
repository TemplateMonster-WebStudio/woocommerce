<?php if ( has_nav_menu( 'shop-menu' ) ) : ?>
<?php wp_nav_menu( array(
	'theme_location'  => 'shop-menu',
	'menu_class'      => 'shop-menu',
	'container_class' => 'shop-menu-container',
	'depth' => 1,
) ); ?>
<?php endif; ?>