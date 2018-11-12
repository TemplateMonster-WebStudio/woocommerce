# woocomerce integrator
Basis for __Wordpress__ theme extension, adds __Woocommerce__ plugin support to any Wordpress theme.

__Notice!__
<br><em>Expected you've cloned this into __extensions/__ directory inside your theme directory</em>
<br><em>This package is not "ready from the box". Thus you will need to perform some edits, before it starts to work properly.<br>I recommend to apply all changes to child-theme.</em>

## Edits to apply:

+ In __functions.php__ include base wrapper class.
	```php
    locate_template( 'extensions/woocommerce/main.php', true );
    ```

+ In __extensions/woocommerce/inc/__ folder create "integrator" class file using folowing naming pattern:
	<br>__class-%theme-name%-woo-integrator.php__ - for a file;
	<br>Where %theme-name% - is basically your theme's directory name
	<br>And the Class name - __%ThemeName%WooIntegrator__.
	<br>You can copy a __class-sample-woo-integrator.php__
	<br>The new Class should extend __AbstractWooIntegrator__ and will be autoloaded
	
	You can gain access to instance of this class through __$woo_wrapper__ global variable and its method __get_integrator()__ method.
	
	
+ There are basic wrapping methods inside sample class:
	* __Wrapper start__
		```php
		/**
		* Hooked to woocommerce_before_main_content, 10
		*/
		public function output_content_wrapper(){
			?>
			<!-- YOUR THEME's CONTENT WRAPPERS BEFORE MAIN CONTENT -->
			<?php
		}
		```
	* __Wrapper end__
		```php
		/**
		* Hooked to woocommerce_after_main_content, 10
		*/
		public function output_content_wrapper_end(){
			?>
			<!-- YOUR THEME's CONTENT WRAPPERS AFTER CONTENT -->
			<?php
		}
		```
	* __After Sidebar wrapper close__
		```php
		/**
		* Hooked to woocommerce_sidebar, 11
		*/
		public function after_sidebar_wrapper_close(){
			?>
			<!-- YOUR THEME's SIDEBAR WRAPPER END -->
			<?php	
		}
		```

+ Duplicate __header.php__ and __footer.php__ from original theme and rename them to __header-shop.php__ and __footer-shop.php__. You already have __sidebar-shop.php__ included.
	You may omit this step if you don't need the shop header and footer to differ from original site header and footer. Although you will still need copies of header and footer to integrate 'Shop menu'(see further).

+ Open your __page.php__(static page template), from original theme. Copy "page content code"(typically all between __get_header()__ and __get_footer()__). Replace code inside __extensions/woocommerce/templates/template-shop.php__ with copied "page content code".
	
    If __get_sidebar()__ is present inside "page content code" - replace it with __get_sidebar('shop')__.
	Alternatively you can replace __template-shop.php__ with renamed __page.php__. In this case, replace all occurrences of __get_header__, __get_footer__ and __get_sidebar__ calls whith their "shop" versions.
	
	This will provide you with static page template named 'Shop Page Template'. Available for selection in admin section of the site. On 'Edit Page' page, inside 'Page Attributes' meta-box.
	Main purpose of this template - output 'Shop Sidebar' on desired, non related to shop, pages.

+ Considering all performed well. So inside __Admin area__, you should see notice asking to perform requiered plugins instalation. Do install and activate plugins.

+ Install Woocommerce 'dummy data'(wp-content/plugins/woocommerce/dummy-data/dummy-data.xml or wp-content/plugins/woocommerce/dummy-data/dummy-data.csv) if needed.

+ You will need to create static pages __Compare__ and __Wishlist__ for __TM WooCommerce Compare & Wishlist__, if they
 aren't created already. And select them on __Woocommerce__ settings page.

+ Inside Admin area -> Appearance->Menus section, there will be added 'Shop menu' location. Create and attach new menu to it.

+ Now you can include __shop-nav-menu.php__-template
	```php
	get_template_part( 'extensions/woocommerce/templates/shop-nav-menu' );
	```

+ Add minicart navbar
	```php
	get_template_part( 'extensions/woocommerce/templates/shopping-cart-link' );
	```

+ Inside __extensions/woocommerce/assets/scss/__ you will find __styles.scss__ - adjust colors and rules to to suffice your needs. You may compile scss with 
```
npm run sass-compile
```
or 
```
npm run sass-watch
```
commands, this requires you to have __node.js__ and __node-sass__ installed. Optionaly you can edit __extensions/woocommerce/assets/css/styles.css__.

## Check for bugs and gliches
+ See [Wiki](https://github.com/Tolumba/woocommerce/wiki) for this repository.
+ If you have Cherry3-theme - use [this repository](https://github.com/Tolumba/woocommerce/tree/cherry3)
+ If you have Cherry4-theme - use [this repository](https://github.com/Tolumba/woocommerce/tree/cherry4)
+ If you have Cherry5-theme - use [this repository](#)
