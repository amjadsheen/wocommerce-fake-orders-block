<?php
/*
Plugin Name: Block Fake Orders
Plugin URI: 
Description:  Block Fake Orders By IP ADDRESS (Dont allow multiple order by ip within 24 hours)
Version: 1.0
Author: amjadalisheen
Author URI: 
Text Domain: 
*/




add_action('woocommerce_checkout_process', 'wh_DisableCheckout');

function wh_DisableCheckout() {
    

   

        $error_msg_text = "Your already Placed order in  <strong>Last 24 Hours</strong>, please try again Later.";

        $ip_address = method_exists( 'WC_Geolocation', 'get_ip_address' ) ? WC_Geolocation::get_ip_address() : wmfo_get_ip_address();
        $args = array(
            'post_type' => 'shop_order',
            'posts_per_page' => -1,
            'post_status' => 'any',
            'date_query' => array(
                array(
                      'after' => '24 hours ago'
                      )
                ),
            'meta_query' => array(
                array(
                    'key' => '_customer_ip_address',
                    'value' => $ip_address,
                    'compare' => '='
    
                )
                ),
            );
       $orderFound = 0;
       $customer_orders = new WP_Query( $args );
       $orderFound = $customer_orders->found_posts;
       if($orderFound > 0){
        wc_add_notice(__($error_msg_text ), 'error');
       }
    
}

/**
 *
 * In case woo commerce changes the function name to get IP address,
 */
function wmfo_get_ip_address() {
	if ( isset( $_SERVER['HTTP_X_REAL_IP'] ) ) { // WPCS: input var ok, CSRF ok.
		return sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_REAL_IP'] ) ); // WPCS: input var ok, CSRF ok.
	} elseif ( isset( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) { // WPCS: input var ok, CSRF ok.
		// Proxy servers can send through this header like this: X-Forwarded-For: client1, proxy1, proxy2
		// Make sure we always only send through the first IP in the list which should always be the client IP.
		return (string) rest_is_ip_address( trim( current( preg_split( '/[,:]/', sanitize_text_field( wp_unslash( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) ) ) ) ); // WPCS: input var ok, CSRF ok.
	} elseif ( isset( $_SERVER['REMOTE_ADDR'] ) ) {
		return sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
	}

	return '';
}
