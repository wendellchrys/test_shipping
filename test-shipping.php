<?php
/*
Plugin Name: Test Shipping
Plugin URI: 
Description: 
Version: 1.0.0
Author: Wendell Christian
Author URI: 
*/

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

function Test_Shipping_Br_init() {
if ( ! class_exists( 'Test_Shipping_Br' ) ) {
    class Test_Shipping_Br extends WC_Shipping_Method {

        public function __construct() {
            $this->id                 = 'Test_Shipping_Br'; 
            $this->method_title       = __( 'Test Shipping Brazil' );  
            $this->method_description = __( '' ); 

            $this->enabled            = "yes"; 
            $this->title              = "Test Shipping Brazil"; 

            $this->init();
        }

        function init() {
            $this->init_form_fields();
            $this->init_settings();

            add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
        }

        public function calculate_shipping( $package ) {

            $array = WC_Cart::get_shipping_packages();                  
            $postcode = $array[0]['destination']['postcode'];

            if ($postcode >= 75960000 && $postcode <= 75969999)
            {
                $cost = 17;

            }

            $rate = array(
                'id' => $this->id,
                'label' => $this->title,
                'cost' => round($cost,2),
                'calc_tax' => 'per_order'
            );

            $this->add_rate( $rate );
        }
    }
}
}

add_action( 'woocommerce_shipping_init', 'Test_Shipping_Br_init' );

function add_Test_Shipping_Br( $methods ) {
    $methods[] = 'Test_Shipping_Br';
    return $methods;
}

add_filter( 'woocommerce_shipping_methods', 'add_Test_Shipping_Br' );
}
