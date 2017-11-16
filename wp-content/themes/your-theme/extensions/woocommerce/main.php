<?php
/**
* Woocomerce wrapper class
*/

namespace extensions\woocommerce;

class Main{

	private static $_instance;

	const PREFIX = 'woo-wrapper';
	const DIR = 'extensions/woocommerce/';
	const DIR_ASSETS = self::DIR . 'assets/';
	const DIR_ASSETS_CSS = self::DIR_ASSETS . 'css/';
	const DIR_ASSETS_JS = self::DIR_ASSETS . 'js/';

	public function __construct(){

		locate_template( self::DIR . 'inc/class-tgm-plugin-activation.php', true, true );
		add_action( 'tgmpa_register',  array( $this, 'tgmpa_register' ) );

		if( ! function_exists('WC') ){
			return;
		}

		remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper' );

		remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end' );

		/** Basic Woocommerce support */
		add_action( 'after_setup_theme', array( $this, 'theme_setup' ) );

		/** Adding Customizer settings */
		add_action( 'customize_register', array( $this, 'customize_register' ), 12 );

		add_action( 'woocommerce_before_main_content', array( $this, 'output_content_wrapper' ) );

		add_action( 'woocommerce_after_main_content', array( $this, 'output_content_wrapper_end' ) );

		add_action( 'woocommerce_sidebar', array( $this, 'after_sidebar_wrapper_close' ), 11 );

		add_action( 'woocommerce_before_template_part', array( $this, 'loop_shop_wrap_start' ), 10, 4 );

		add_action( 'woocommerce_after_template_part', array( $this, 'loop_shop_wrap_end' ), 999, 4 );

		/** Adding shop sidebar */
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );

		/** Quering assets */
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		/** Updating templates include quene */
		add_filter( 'theme_page_templates', array( $this, 'theme_page_templates' ), 10, 4 );

		add_filter( 'page_template_hierarchy', array( $this, 'page_template_hierarchy' ) );

		add_filter( 'template_include', array( $this, 'theme_templates' ), 20 );

		add_filter( 'body_class', array( $this, 'body_class' ), 10, 2 );

		/** Modifing woocommerce templates path */
		add_filter( 'woocommerce_template_path', array( $this, 'woocommerce_template_path' ), 20 );

		add_filter( 'loop_shop_per_page', array( $this, 'loop_per_page' ) );

		add_filter( 'loop_shop_columns', array( $this, 'loop_columns' ) );
		
		add_filter( 'woocommerce_output_related_products_args', array( $this, 'related_products_args' ) );

		add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'woocommerce_add_to_cart_fragments' ) );
	}

	public function tgmpa_register(){
		$plugins = array();

		$plugins[] = array(
			'name'    => 'Woocommerce',
			'slug'    => 'woocommerce',
		);

		$plugins[] = array(
			'name'    => 'TM WooCommerce Package',
			'slug'    => 'tm-woocommerce-package',
		);
		
		$plugins[] = array(
			'name'    => 'TM WooCommerce Compare & Wishlist',
			'slug'    => 'tm-woocommerce-compare-wishlist',
		);

		$config = array(
			'id'           => 'tgmpa',
			'default_path' => '',
			'menu'         => 'tgmpa-install-plugins',
			'parent_slug'  => 'themes.php',
			'capability'   => 'edit_theme_options',
			'has_notices'  => true,
			'dismissable'  => true,
			'dismiss_msg'  => '',
			'is_automatic' => true,
			'message'      => '',
		);

		villagio_tgmpa( $plugins, $config );
	}

	public function theme_setup(){
		/* Declaring woocommerce support */
		add_theme_support( 'woocommerce' );

		/* Enabling single product page woocommerce features */
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );

		$shop_menu_slug = 'shop-menu';
		register_nav_menu( $shop_menu_slug, __( 'Shop menu', 'woo-wrapper' ) );
		add_filter( "wp_nav_menu_{$shop_menu_slug}_items", array( $this, 'shop_menu_filter' ), 10, 2 );

		if( 'yes' !== get_theme_mod( 'shop_display_breadcrumbs', 'yes' ) ){
			remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
		}
	}

	public function shop_menu_filter( $items, $args ){

		global $wp;

		$redirect = home_url( $wp->request );

		$items .= '<li class="menu-item">' . wp_loginout( $redirect, false ) . '</li>';

		if( ! is_user_logged_in() ){
			$items .= wp_register( '<li class="menu-item">', '</li>', false );
		}

		return $items;
	}

	public function customize_register( $wp_customize ){
		$wp_customize->add_section( 'shop_section', array(
			'id'=> 'shop_section',
			'title'=> 'Shop Section',
		) );

		$wp_customize->add_setting( 'shop_products_per_page', array(
			'default'           => '12',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new \WP_Customize_Control( $wp_customize, 'shop_products_per_page', array(
			'label'   => esc_html__( 'Products per_page', 'woo-wrapper' ),
			'section' => 'shop_section',
		) ) );

		$wp_customize->add_setting( 'shop_page_columns', array(
			'default'           => '4',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new \WP_Customize_Control( $wp_customize, 'shop_page_columns', array(
			'label'   => esc_html__( 'Products grid columns', 'woo-wrapper' ),
			'section' => 'shop_section',
		) ) );

		$wp_customize->add_setting( 'shop_display_breadcrumbs', array(
			'default'           => 'yes',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new \WP_Customize_Control( $wp_customize, 'shop_display_breadcrumbs', array(
			'label'   => esc_html__( 'Display Breadcrumbs', 'woo-wrapper' ),
			'section' => 'shop_section',
			'type' => 'checkbox',
		) ) );
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

	public function enqueue_scripts(){
		
		$depends = array();
		
		if( is_child_theme() ){
			 $depends = array(
				wp_get_theme()->stylesheet . '-style',
			);
		}
		$depends = apply_filters( self::PREFIX . '-styles', $depends );
		
		wp_register_style(
			self::PREFIX . '-styles',
			self::_dir( 'styles.css', 'css' ),
			$depends );

		$depends = apply_filters( self::PREFIX . '-scripts', array( 'jquery' ) );

		wp_register_script(
			self::PREFIX . '-scripts',
			self::_dir( 'scripts.js', 'js' ),
			$depends );

		wp_enqueue_style( self::PREFIX . '-styles' );
		wp_enqueue_script( self::PREFIX . '-scripts' );
	}

	public function body_class( $classes, $class ){

		return array_merge( $classes, array( 'woocommerce' ) );
	}
	
	/**
	* Hooked to woocommerce_before_main_content, 10
	*/
	public function output_content_wrapper(){
		?>
		<!-- YOUR THEME's CONTENT WRAPPERS BEFORE MAIN CONTENT -->
		<?php
	}

	/**
	* Hooked to woocommerce_after_main_content, 10
	*/
	public function output_content_wrapper_end(){
		?>
		<!-- YOUR THEME's CONTENT WRAPPERS AFTER CONTENT -->
		<?php
	}

	/**
	* Hooked to woocommerce_sidebar, 11
	*/
	public function after_sidebar_wrapper_close(){
		?>
		<!-- YOUR THEME's SIDEBAR WRAPPER END -->
		<?php	
	}

	public function loop_columns( $columns ){

		$columns = get_theme_mod( 'shop_page_columns', $columns );

		return $columns;
	}

	public function loop_per_page( $products ){

		$products = get_theme_mod( 'shop_products_per_page', $products );

		return $products;
	}

	public function related_products_args( $args ){

		$args['columns']        = apply_filters( 'loop_shop_columns', $args['columns'] );
		$args['posts_per_page'] = $args['columns'];

		return $args;
	}

	public function loop_shop_wrap_start( $template_name, $template_path, $located, $args ){

		if( 'loop/loop-start.php' === $template_name ){

			$classes = apply_filters( self::PREFIX . '-loop-wrapper-classes', array( 
				'woocommerce',
				'columns-' . apply_filters( 'loop_shop_columns', 4 ),
			) );

			$attr_class = '';
			if( !empty( $classes ) ){
				$attr_class = sprintf( ' class="%s"', join( array_unique( $classes ), ' ' ) );
			}

			printf( '<div%s>', $attr_class );
		}
	}

	public function loop_shop_wrap_end( $template_name, $template_path, $located, $args ){
		global $woocommerce_loop;

		if( 'loop/loop-end.php' === $template_name ){
			echo '</div>';
		}
	}
	
	public function theme_templates( $template ){

		$file = basename( $template );
		$templates = array();

		$templates[] =  self::_dir( "templates/{$file}" );
		$templates[] =  "templates/{$file}";
		$templates[] =  $file;

		if( $template_override = locate_template( $templates ) ){
			return $template_override;
		}

		return $template;
	}

	public function page_template_hierarchy( $templates ){

		return $templates;
	}

	public function theme_page_templates( $post_templates, $theme, $post, $post_type ){
		
		$template = self::_dir('templates/template-shop.php');
		$post_templates[$template] = __( 'Shop Page Template', 'woo-wrapper' );
		
		return $post_templates;
	}

	public function woocommerce_template_path( $path ){

		return self::DIR . 'templates/';
	}

	public function woocommerce_add_to_cart_fragments( $fragments ){

		$items_count = WC()->cart->get_cart_contents_count();
		$fragments['.cart-navbar-wrapper i.count'] = "<i class=\"count\">{$items_count}</i>";

		return $fragments;
	}

	public static function instance(){
		if( null === self::$_instance ){
			self::$_instance = new self;
		}

		return self::$_instance;
	}

	private static function _dir( $file, $context='' ){

		switch ( $context ){
			case 'assets':
				return get_stylesheet_directory_uri() . '/' . self::DIR_ASSETS . $file;
			break;
			case 'css':
				return get_stylesheet_directory_uri() . '/' . self::DIR_ASSETS_CSS . $file;
			break;
			case 'js':
				return get_stylesheet_directory_uri() . '/' . self::DIR_ASSETS_JS . $file;
			break;
			default:
				return self::DIR . $file;
			break;
		}
	}
}

function woo_wrapper(){
	return Main::instance();
}

$GLOBALS['woo_wrapper'] = woo_wrapper();
