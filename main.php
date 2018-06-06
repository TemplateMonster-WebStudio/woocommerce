<?php
/**
* Woocomerce wrapper class
*/

namespace extensions\woocommerce;

class Main{

	private static $_instance;
	private $_integrator;

	const PREFIX = 'woo-wrapper';
	const DIR = 'extensions/woocommerce/';
	const DIR_ASSETS = self::DIR . 'assets/';
	const DIR_ASSETS_CSS = self::DIR_ASSETS . 'css/';
	const DIR_ASSETS_JS = self::DIR_ASSETS . 'js/';

	public function __construct(){

		locate_template( self::DIR . 'inc/class-tgm-plugin-activation.php', true, true );
		add_action( 'tgmpa_register',  array( $this, 'tgmpa_register' ) );

		spl_autoload_register( array( $this, '_loader' ) );

		if( ! function_exists('WC') ){
			return;
		}

		remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper' );

		remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end' );

		/** Basic Woocommerce support */
		add_action( 'after_setup_theme', array( $this, 'theme_setup' ) , false);

		/** Adding Customizer settings */
		add_action( 'customize_register', array( $this, 'customize_register' ), 12 );

		/** Layout Setup */
		add_action( 'woocommerce_before_main_content', array( $this->get_integrator(), 'output_content_wrapper_start' ) );

		add_action( 'woocommerce_after_main_content', array( $this->get_integrator(), 'output_content_wrapper_end' ) );

		add_action( 'woocommerce_sidebar', array( $this->get_integrator(), 'after_sidebar_wrapper_close' ), 11 );

		add_action( 'woocommerce_before_template_part', array( $this, 'loop_shop_wrap_start' ), 10, 4 );

		add_action( 'woocommerce_after_template_part', array( $this, 'loop_shop_wrap_end' ), 999, 4 );

		/** Adding shop sidebar */
		add_action( 'widgets_init', array( $this, 'widgets_init' ) );

		/** Quering assets */
		add_filter( 'woocommerce_enqueue_styles', array( $this, 'woocommerce_enqueue_styles' ) );
		
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

	public function get_integrator(){

		if( !empty( $this->_integrator ) ){
			return $this->_integrator;
		}

		$classname = wp_get_theme()->stylesheet;
		$classname = __NAMESPACE__ . '\\inc\\' . preg_replace( '/ /', '', ucwords( preg_replace( '/[-_]/', ' ', $classname ) ) ) . 'WooIntegrator';

		try{
			spl_autoload_call( $classname );
		} catch( \Exception $e ) {
			echo $e->getMessage();
		}

		if( class_exists( $classname ) ){
			$this->_integrator = new $classname;
		}else{
			$this->_integrator = $this;
		}

		return $this->_integrator;
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


		if( (bool) get_theme_mod( 'shop_hide_title', false ) ){
			add_filter( 'woocommerce_show_page_title', '__return_false', 99 );
		}

		if( (bool) get_theme_mod( 'shop_hide_breadcrumbs', false ) ){
			remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
		}
	}

	public function shop_menu_filter( $items, $args ){

		global $wp;

		$redirect = home_url( $wp->request );

		$items .= '<li class="menu-item wp-login-li">' . wp_loginout( $redirect, false ) . '</li>';

		if( ! is_user_logged_in() ){
			$items .= wp_register( '<li class="menu-item wp-register-li">', '</li>', false );
		}

		return $items;
	}

	public function customize_register( $wp_customize ){
		$wp_customize->add_section( 'theme_woo_options', array(
			'id'=> 'theme_woo_options',
			'title'=> 'Theme Options',
			'panel' => 'woocommerce',
		) );

		$wp_customize->add_setting( 'shop_products_per_page', array(
			'default'           => '12',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new \WP_Customize_Control( $wp_customize, 'shop_products_per_page', array(
			'label'   => esc_html__( 'Products per page', 'woo-wrapper' ),
			'section' => 'theme_woo_options',
		) ) );

		$wp_customize->add_setting( 'shop_page_columns', array(
			'default'           => '4',
			'sanitize_callback' => 'sanitize_text_field',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new \WP_Customize_Control( $wp_customize, 'shop_page_columns', array(
			'label'   => esc_html__( 'Products grid columns', 'woo-wrapper' ),
			'section' => 'theme_woo_options',
		) ) );

		$wp_customize->add_setting( 'shop_hide_title', array(
			'default'           => '0',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new \WP_Customize_Control( $wp_customize, 'shop_hide_title', array(
			'label'   => esc_html__( 'Hide Title', 'woo-wrapper' ),
			'section' => 'theme_woo_options',
			'type' => 'checkbox',
		) ) );

		$wp_customize->add_setting( 'shop_hide_breadcrumbs', array(
			'default'           => '0',
			'transport'         => 'refresh',
		) );
		$wp_customize->add_control( new \WP_Customize_Control( $wp_customize, 'shop_hide_breadcrumbs', array(
			'label'   => esc_html__( 'Hide Breadcrumbs', 'woo-wrapper' ),
			'section' => 'theme_woo_options',
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

		$columns = (int) apply_filters( 'loop_shop_columns', 4 );
		$vars = array(
			'layout_settings' => array(
				'0'    => array( 'items' => 1 ),
				'480'  => array( 'items' => 2 ),
				'992'  => array( 'items' => min( 3, $columns ) ),
				'1200'  => array( 'items' => $columns ),
			),
		);
		wp_localize_script( self::PREFIX . '-scripts', 'woo_wrapper', apply_filters( self::PREFIX . '-js-vars', $vars ) );

		wp_enqueue_style( self::PREFIX . '-styles' );
		wp_enqueue_script( self::PREFIX . '-scripts' );
	}

	/**
	* Applying Small Screen styles only on Checkout and Cart pages
	*/
	public function woocommerce_enqueue_styles( $styles=array() ){

		if( array_key_exists( 'woocommerce-smallscreen', $styles ) ){

			unset( $styles['woocommerce-smallscreen'] );
		}

		return $styles;
	}

	public function body_class( $classes, $class ){

		return array_merge( $classes, array( 'woocommerce' ) );
	}
	
	/**
	* Hooked to woocommerce_before_main_content, 10
	*/
	public function output_content_wrapper_start(){
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

	private final function _loader( $classname ){

		$pattern = '/' . preg_quote( __NAMESPACE__ ) . '/';

		if( !preg_match( $pattern, $classname ) ){
			return false;
		}

		$classname = preg_replace( $pattern, '', $classname );

		$pattern = '/\\\/';
		$namespace_arr = preg_split( $pattern, $classname, -1, PREG_SPLIT_NO_EMPTY );

		$pattern = '/([[:upper:]][^[:upper:]]*)/';
		$parts = preg_split(
			$pattern,
			array_pop( $namespace_arr ),
			-1,
			PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE
		);

		array_unshift( $parts, 'class' );
		$name = strtolower( implode( '-', $parts ) . '.php' );

		//array_shift( $namespace_arr );
		$file = __DIR__ . DIRECTORY_SEPARATOR . $name;
		
		//var_dump($file);

		if( file_exists( $file ) ) {

			require_once $file;
			return true;
		} else if( count( $namespace_arr ) ) {

			$subdirs = implode( DIRECTORY_SEPARATOR, $namespace_arr );
			$file = __DIR__ . DIRECTORY_SEPARATOR . $subdirs . DIRECTORY_SEPARATOR . $name;

			//var_dump($file);

			if( file_exists( $file ) ){
				require_once $file;
				return true;
			}
		}

		return false;
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
