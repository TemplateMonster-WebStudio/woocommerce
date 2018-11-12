<?php 

namespace extensions\woocommerce\inc {

	class Cherry4SampleWooIntegrator extends AbstractWooIntegrator {
		
		public function __construct() {
			add_filter( 'cherry_get_main_sidebar', array( $this,  'switch_sidebar' ) );
			add_filter( 'cherry_breadcrumbs_custom_trail', array( $this, 'get_woo_breadcrumbs' ), 11, 2 );
		}

		public function output_content_wrapper_start() {}
		public function output_content_wrapper_end() {}
		public function after_sidebar_wrapper_close() {}
		
		public function widgets_init() {
			cherry_register_sidebar(
				array(
					'id'          => 'sidebar-shop',
					'name'        => __( 'Shop Sidebar', 'woo-wrapper' ),
					'description' => __( 'Appears on the Shop related pages', 'woo-wrapper' )
				)
			);
		}

		public function get_woo_breadcrumbs( $is_custom, $args ){

			if( is_singular( 'product' ) && class_exists( 'Cherry_Woo_Breadcrumbs' ) ){
				$woo_breadcrums = new \Cherry_Woo_Breadcrumbs( $args );
				$title = $this->maybe_show_title()? '': \get_the_title();
				$items = $this->maybe_show_breadcrumbs()? array(): $woo_breadcrums->items;
				$is_custom = array( 'items' => $items, 'page_title' => $title );
			}

			return $is_custom;
		}

		public function switch_sidebar( $sidebar ){

			if( is_shop_page() ){
				$sidebar = 'sidebar-shop';
			};

			return $sidebar;
		}

		public function maybe_show_title(){
			return \get_theme_mod( 'shop_hide_title', false );
		}

		public function maybe_show_breadcrumbs(){
			return \get_theme_mod( 'shop_hide_breadcrumbs', false );
		}
	}
}

/**
 * Declaring functions in global namespace
 */
namespace {

	if( ! function_exists( 'is_static_shop_template' ) ){
		
		/**
		 * Detects "Shop Page Template" usage
		 *
		 * @return void
		 */
		function is_static_shop_template(){
			return 'extensions/woocommerce/templates/template-shop.php' == get_page_template_slug();
		}
	}

	if( ! function_exists( 'is_shop_page' ) ){

		/**
		 * Detects whenever page is related to Shop
		 *
		 * @return void
		 */
		function is_shop_page(){
			return is_shop() || is_singular( 'product' ) || is_tax( get_object_taxonomies( 'product' ) ) ;
		}
	}
}
