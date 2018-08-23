<?php 

namespace extensions\woocommerce\inc {

	class SampleCherry3WooIntegrator extends AbstractWooIntegrator{
		
		public function __construct(){
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 999 );
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
}