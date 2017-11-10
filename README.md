# wp-theme-woocomerce
Wordpress theme extension base, to add Woocommerce support to the theme.

	__Notice!__
		
	_This package is not "ready from the box".
	Thus you will need to perform some file edits._
		
	_I recommend to add all changes to child-theme._

## Edits to perform:

+ In _functions.php_ include base wrapper class.
	>
    >`locate_template( 'extensions/woocommerce/main.php', true );`
    >

+ Duplicate _header.php_ and _footer.php_ from original theme and rename them to _header-shop.php_ and _footer-shop.php_. You already have _sidebar-shop.php_ included.
	You may omit this step if you don't need the shop header and footer to differ from original site header and footer. Although you will still need copies of header and footer to integrate 'Shop menu'(see further).

+ Open your _index.php_, from original theme. Replace code inside _extensions/woocommerce/templates/template-shop.php_ with "loop code"(typically all between _get_header()_ and _get_footer()_).
	
    If _get_sidebar()_ is present inside "loop code" - replace it with _get_sidebar('shop')_.
	Alternatively you can replace _template-shop.php_ with renamed _index.php_. In this case, replace all occurrences of _get_header_, _get_footer_ and _get_sidebar_ calls whith their "shop" versions.
	
	This will provide you with static page template named 'Shop Template'. Available for selection in admin section of the site. On 'Edit Page' page, inside 'Page Attributes' meta-box.
	Main purpose of this template - output 'Shop Sidebar' on desired, non related to shop, pages.

+ Considering all performed well. Inside Admin area, you should see notice asking to perform requiered plugins instalation. Do install and activate plugins.

+ Install Woocommerce 'dummy data' (wp-content/plugins/woocommerce/dummy-data/dummy-data.xml), if needed.

+ Inside Admin area -> Appearance->Menus section, there will be added 'Shop menu' location. Create and attach new menu to it.

+ Now you can include _shop-nav-menu.php_-template
	>
	>`get_template_part( 'extensions/woocommerce/templates/shop-nav-menu' );`
	>

+ Add minicart navbar
	>
	>`get_template_part( 'extensions/woocommerce/templates/shopping-cart-link' );`
	>

+ Inside _extensions/woocommerce/assets/scss/_ you will find _styles.scss_ - adjust colors and rules to to suffice your needs. You may compile scss with _extensions/woocommerce/sass-watch.bat_ locally, if you have node.js and node-sass installed. Optionaly you can edit _extensions/woocommerce/assets/css/styles.css_.

## Check for bugs and gliches
Should add some tipical 'shit happens' examples here in future

## Files description(respectively to _extensions/woocommerce/_)
	- _main.php_ - main "wrapper" class.
	- _assets\css\styles.css_ - styles.
