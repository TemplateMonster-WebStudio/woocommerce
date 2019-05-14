<?php 

namespace extensions\woocommerce\inc {

	class NonCherryWooIntegrator extends AbstractWooIntegrator{
		
		public function __construct(){
			add_action( 'before_header', array( $this, 'before_header' ) );
		}

		public function output_content_wrapper_start(){
			do_action('before_loop');
		}

		public function output_content_wrapper_end(){
			do_action('after_loop');
		}

		public function after_sidebar_wrapper_close(){}

		public function before_header(){
			get_template_part( 'extensions/woocommerce/templates/shop-navbar-top' );
		}

		public function widgets_init(){
			register_sidebar( array(
				'name'          => esc_html__( 'Shop Sidebar', 'woo-wrapper' ),
				'id'            => 'sidebar-shop',
				'description'   => esc_html__( 'Appears on the Shop related pages', 'woo-wrapper' ),
				'before_widget' => '<section id="%1$s" class="widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2 class="widget-title">',
				'after_title'   => '</h2>',
			) );
		}
	}
}
/**
 *  Using global namespace
 */
namespace {
	if( !function_exists( 'is_template_shop' ) ){
		/**
		 * Determines whenever template-shop.php is current page template
		 * @return boolean [description]
		 */
		function is_template_shop(){
			return 'template-shop' === basename( get_page_template(), '.php' );
		}
	}
	if( !function_exists( 'is_shop_page' ) ){
		/**
		 * Checks whenever Shop-related page is displayed
		 * @return boolean true if is single product page or is shop page or shows a taxonomy of a product post type
		 */
		function is_shop_page(){
			return is_singular('product') || is_shop() || is_tax( get_object_taxonomies('product') );
		}
	}
}