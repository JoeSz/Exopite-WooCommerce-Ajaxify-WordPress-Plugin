<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://joe.szalai.org
 * @since      1.0.0
 *
 * @package    Exopite_Wc_Ajaxify
 * @subpackage Exopite_Wc_Ajaxify/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Exopite_Wc_Ajaxify
 * @subpackage Exopite_Wc_Ajaxify/public
 * @author     Joe Szalai <joe@szalai.org>
 */
class Exopite_Wc_Ajaxify_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Exopite_Wc_Ajaxify_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Exopite_Wc_Ajaxify_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/exopite-wc-ajaxify-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Exopite_Wc_Ajaxify_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Exopite_Wc_Ajaxify_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/exopite-wc-ajaxify-public.js', array( 'jquery' ), $this->version, false );

	}

    public function add_to_cart_variable_add_section( $sections ) {

        $sections['wc_ajax_add_to_cart_variable'] = __( 'WC Ajax for Variable Products', 'text-domain' );
        return $sections;

    }

    public function add_to_cart_variable_all_settings( $settings, $current_section ) {

        /**
         * Check the current section is what we want
         **/

        if ( $current_section == 'wc_ajax_add_to_cart_variable' ) {

            $settings_slider = array();

            // Add Title to the Settings
            $settings_slider[] = array( 'name' => __( 'WC Ajax for Variable Products Settings', 'text-domain' ), 'type' => 'title', 'desc' => __( 'The following options are used to configure WC Ajax for Variable Products', 'text-domain' ), 'id' => 'wc_ajax_add_to_cart_variable' );

            // Add first checkbox option
            $settings_slider[] = array(

                'name'     => __( 'Add Selection option to Category Page', 'text-domain' ),
                'desc_tip' => __( 'This will automatically insert variable selection options on product Category Archive Page', 'text-domain' ),
                'id'       => 'wc_ajax_add_to_cart_variable_category_page',
                'type'     => 'checkbox',
                'css'      => 'min-width:300px;',
                'desc'     => __( 'Enable Varition select option on Category Archive page', 'text-domain' ),

            );

            $settings_slider[] = array( 'type' => 'sectionend', 'id' => 'wc_ajax_add_to_cart_variable' );

            return $settings_slider;

        /**
         * If not, return the standard settings
         **/

        } else {

            return $settings;

        }

    }

    public function add_to_cart_variable_rc_callback() {

        ob_start();

        $product_id = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST['product_id'] ) );
        $quantity = empty( $_POST['quantity'] ) ? 1 : apply_filters( 'woocommerce_stock_amount', $_POST['quantity'] );
        $variation_id = $_POST['variation_id'];
        $variation  = $_POST['variation'];
        $passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );

        if ( $passed_validation && WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation  ) ) {

            do_action( 'woocommerce_ajax_added_to_cart', $product_id );

            if ( get_option( 'woocommerce_cart_redirect_after_add' ) == 'yes' ) {

                wc_add_to_cart_message( $product_id );

            }

            // Return fragments
            WC_AJAX::get_refreshed_fragments();

        } else {

            $this->json_headers();

            // If there was an error adding to the cart, redirect to the product page to show any errors
            $data = array(
                'error' => true,
                'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id )
                );
            echo json_encode( $data );

        }

        die();

    }

    public function quantity_inputs_for_woocommerce_loop_add_to_cart_link( $html, $product ) {

        if ( $product && $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() && ! $product->is_sold_individually() ) {

            $amount_disabled = get_post_meta( $product->id, '_single_without_amount', true );
            if ( $amount_disabled === 'yes' ) return $html;
            $html = '<form action="' . esc_url( $product->add_to_cart_url() ) . '" class="cart test" method="post" enctype="multipart/form-data">';
            $html .= woocommerce_quantity_input( array(), $product, false );
            $html .= '<button type="submit" data-quantity="5" data-product_id="'. $product->id . '" class="button alt ajax_add_to_cart add_to_cart_button product_type_simple">' . esc_html( $product->add_to_cart_text() ) . '</button>1';
            $html .= '</form>';

        }

        return $html;
    }


}
