<?php 

namespace extensions\woocommerce\inc;

class SampleWooIntegrator extends AbstractWooIntegrator{
	
	public function __construct(){

	}

	public function output_content_wrapper_start(){}
	public function output_content_wrapper_end(){}
	public function after_sidebar_wrapper_close(){}
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
