<?php 
/**
 * Note! %cherry5-sample% - reffers to theme name
 */
namespace extensions\woocommerce\inc {
	
	class Cherry5SampleWooIntegrator extends AbstractWooIntegrator {
		
		public function __construct() {
			add_filter( '%cherry5-sample%_widget_area_default_settings', array( $this, 'widget_area_default_settings' ) );
		}
		
		public function output_content_wrapper_start() {}
		public function output_content_wrapper_end() {}
		public function after_sidebar_wrapper_close() {}
		public function widgets_init() {}
		
		public function widget_area_default_settings( $sidebars ){
			/**
			 * Note! This settings may differ in your theme!
			 * 'before_widget' 
			 * 'after_widget'  
			 * 'before_title'  
			 * 'after_title'   
			 * 'before_wrapper'
			 * 'after_wrapper' 
			 */
			$sidebars['sidebar-shop'] = array(
				'name'           => esc_html__( 'Shop Sidebar', 'woo-wrapper' ),
				'description'    => esc_html__( 'Appears on the Shop related pages', 'woo-wrapper' ),
				'before_widget'  => '<aside id="%1$s" class="widget %2$s">',
				'after_widget'   => '</aside>',
				'before_title'   => '<h5 class="widget-title">',
				'after_title'    => '</h5>',
				'before_wrapper' => '<div id="%1$s" %2$s role="complementary">',
				'after_wrapper'  => '</div>',
			);

			return $sidebars;
		}
	}					
}

/**
 *  Using global namespace
 */
namespace {

	if( ! function_exists( 'is_template_shop' ) ){
		/**
		 * Determines whenever template-shop.php is current page template
		 * @return boolean [description]
		 */
		function is_template_shop(){
			return 'template-shop' === basename( get_page_template(), '.php' );
		}
	}

	if( ! function_exists( 'is_shop_page' ) ){
		/**
		 * Checks whenever Shop-related page is displayed
		 * @return boolean true if is single product page or is shop page or shows a taxonomy of a product post type
		 */
		function is_shop_page(){
			return is_shop() || is_tax( get_object_taxonomies( 'product' ) ) || is_singular( 'product' );
		}
	}

	if( ! function_exists( 'get_shop_sidebar_position' ) ){

		function get_shop_sidebar_position(){
			$sidebar_position = get_theme_mod( 'sidebar_position' );
			$shop_page_id = wc_get_page_id( 'shop' );
			

			if( ! $shop_page_id ){
				return $sidebar_position;
			}

			if( ( $shop_sidebar_position = get_post_meta( $shop_page_id, '%cherry5-sample%_sidebar_position', true ) )
				&& 'inherit' !== $shop_sidebar_position ){
				return $shop_sidebar_position;
			}
			
			/**
			 * Considering no Sidebar needed on Single Product Page
			 */
			if( is_singular( 'product' ) ){
				$sidebar_position = 'fullwidth';
			}
			
			return $sidebar_position;
		}
	}
}