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
        
         public function get_cart_volume(){
                // Initializing variables
                $volume = $rate = 0;
            
                // Get the dimetion unit set in Woocommerce
                $dimension_unit = get_option( 'woocommerce_dimension_unit' );
            
                // Calculate the rate to be applied for volume in m3
                if ( $dimension_unit == 'mm' ) {
                    $rate = pow(10, 9);
                } elseif ( $dimension_unit == 'cm' ) {
                    $rate = pow(10, 6);
                } elseif ( $dimension_unit == 'm' ) {
                    $rate = 1;
                }
            
                if( $rate == 0 ) return false; // Exit
            
                // Loop through cart items
                foreach(WC()->cart->get_cart() as $cart_item) { 
                    // Get an instance of the WC_Product object and cart quantity
                    $product = $cart_item['data'];
                    $qty     = $cart_item['quantity'];
            
                    // Get product dimensions  
                    $length = $product->get_length();
                    $width  = $product->get_width();
                    $height = $product->get_height();
            
                    // Calculations a item level
                    $volume += $length * $width * $height * $qty;
                } 
                return $volume / $rate;
            }
            
            $volume = get_cart_volume();
            

        public function calculate_shipping( $package = array() ) {
            
		// Check if valid to be calculeted.
		if ( '' === $package['destination']['postcode'] || 'BR' !== $package['destination']['country'] ) {
			return;
		}
                        
		$postcode = $package['destination']['postcode'];
		$state = $package['destination']['state'];
		
		$postcode = str_replace('-', '', $postcode);
		$postcode = intval($postcode);
		$cost = 0;
            
		if ($postcode >= 75960000 && $postcode <= 75969999 && $state == 'GO')
		{
			$cost = 17;
			$titleShipping = 'Test X:';
		}
		
		elseif  ($volume > 100)
		{
			$cost = 100;
			$titleShipping = 'Test Y:';
		}
            
            if ($cost == 0){
		    $titleShipping = 'Test Z';
	    }
               

            $rate = array(
                'id' => $this->id,
                'label' => $titleShipping,
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
