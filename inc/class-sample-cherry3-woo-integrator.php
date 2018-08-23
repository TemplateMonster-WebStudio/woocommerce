<?php 

namespace extensions\woocommerce\inc {

	class SampleCherry3WooIntegrator extends AbstractWooIntegrator{
		
		public function __construct(){
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 999 );

			/**
			 * Titles format override
			 */
			/*remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
			add_action( 'woocommerce_shop_loop_item_title', array( $this, 'woocommerce_template_loop_product_title' ), 10 );

			remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
			add_action( 'woocommerce_single_product_summary', array( $this, 'woocommerce_template_single_title'), 5 );*/
		}

		public function output_content_wrapper_start(){ ?>
			<div class="motopress-wrapper content-holder clearfix">
				<div class="container">
					<div class="row">
						<div class="<?php echo cherry_get_layout_class( 'full_width_content' ); ?>" data-motopress-wrapper-file="page.php" data-motopress-wrapper-type="content">
							<div class="row">
								<div class="<?php echo cherry_get_layout_class( 'full_width_content' ); ?>" data-motopress-type="static" data-motopress-static-file="static/static-title.php">
									<?php get_template_part("static/static-title"); ?>
								</div>
							</div>
							<div class="row">
								<div class="<?php echo cherry_get_layout_class( 'content' ); ?> <?php echo of_get_option('blog_sidebar_pos') ?>" id="content">
		<?php }

		public function output_content_wrapper_end(){ ?>
								</div>
								<div class="<?php echo cherry_get_layout_class( 'sidebar' ); ?> sidebar" id="sidebar">
		<?php }

		public function after_sidebar_wrapper_close(){ ?>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php }

		public function enqueue_scripts(){
			/* Removes Conflict in Cherry3-base vs Wishlist plugin */
			wp_dequeue_style( 'bootstrap-grid' );
		}

		/**
		 * Title format in products loop
		 * @return [type] [description]
		 */
		public function woocommerce_template_loop_product_title(){
			\the_title( '<h4 class="woocommerce-loop-product__title">', '</h4>' );
		}

		/**
		 * Title format in single product page
		 * @return [type] [description]
		 */
		public function woocommerce_template_single_title(){
			\the_title( '<h3 class="product_title entry-title">', '</h3>' );
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
