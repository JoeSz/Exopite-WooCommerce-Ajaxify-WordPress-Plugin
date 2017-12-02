<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://joe.szalai.org
 * @since      1.0.0
 *
 * @package    Exopite_Wc_Ajaxify
 * @subpackage Exopite_Wc_Ajaxify/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Exopite_Wc_Ajaxify
 * @subpackage Exopite_Wc_Ajaxify/includes
 * @author     Joe Szalai <joe@szalai.org>
 */
class Exopite_Wc_Ajaxify {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Exopite_Wc_Ajaxify_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'PLUGIN_NAME_VERSION' ) ) {
			$this->version = PLUGIN_NAME_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'exopite-wc-ajaxify';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Exopite_Wc_Ajaxify_Loader. Orchestrates the hooks of the plugin.
	 * - Exopite_Wc_Ajaxify_i18n. Defines internationalization functionality.
	 * - Exopite_Wc_Ajaxify_Admin. Defines all hooks for the admin area.
	 * - Exopite_Wc_Ajaxify_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-exopite-wc-ajaxify-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-exopite-wc-ajaxify-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-exopite-wc-ajaxify-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-exopite-wc-ajaxify-public.php';

		$this->loader = new Exopite_Wc_Ajaxify_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Exopite_Wc_Ajaxify_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Exopite_Wc_Ajaxify_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Exopite_Wc_Ajaxify_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Exopite_Wc_Ajaxify_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts', 99 );

        /*
        Plugin Name: Woocommerce Add to cart Ajax for variable products
        Plugin URI: http://www.rcreators.com/woocommerce-ajax-add-to-cart-variable-products
        Description: Ajax based add to cart for varialbe products in woocommerce.
        Author: Rishi Mehta - Rcreators Websolutions
        Version: 1.2.9
        Author URI: http://rcreators.com
        */
        $this->loader->add_filter( 'woocommerce_get_sections_products', $plugin_public, 'add_to_cart_variable_add_section' );
        $this->loader->add_filter( 'woocommerce_get_settings_products', $plugin_public, 'add_to_cart_variable_all_settings', 10, 2 );
        $this->loader->add_filter( 'wp_ajax_woocommerce_add_to_cart_variable_rc', $plugin_public, 'add_to_cart_variable_rc_callback' );
        $this->loader->add_filter( 'wp_ajax_nopriv_woocommerce_add_to_cart_variable_rc', $plugin_public, 'add_to_cart_variable_rc_callback' );

        /**
         * WooCommerce - Show quantity inputs for simple products within loops.
         *
         * @link https://gist.github.com/mikejolley/2793710
         */
        $this->loader->add_filter( 'woocommerce_loop_add_to_cart_link', $plugin_public, 'quantity_inputs_for_woocommerce_loop_add_to_cart_link', 10, 2 );

        /*
         * Custom field
         *
         * Tutorial:
         * https://www.proy.info/how-to-add-woocommerce-custom-fields/
         * http://www.remicorson.com/mastering-woocommerce-products-custom-fields/
         */
        $this->loader->add_filter( 'woocommerce_product_options_advanced', $plugin_public, 'add_custom_general_fields' );
        $this->loader->add_filter( 'woocommerce_process_product_meta', $plugin_public, 'add_custom_general_fields_save' );

        //$this->loader->add_filter( 'woocommerce_loop_add_to_cart_link', $plugin_public, 'woo_archive_page_cart_button_custom_class', 99, 2 );
        $this->loader->add_filter( 'woocommerce_product_add_to_cart_text', $plugin_public, 'woo_archive_page_cart_button_custom_text', 99 );



	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Exopite_Wc_Ajaxify_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
