<?php
/*
Plugin Name: UOM Multi
Version: 1.0.1
Description: UOM for multisite
Author: OnlineVagyok
Author URI: https://onlinevagyok.hu
*/

if ( ! defined( 'ABSPATH' ) ) {
	header( 'Status: 403 Forbidden' );
	header( 'HTTP/1.1 403 Forbidden' );
	exit;
}


add_action( 'woocommerce_product_options_general_product_data', 'uommulti_woo_custom_fields' );
/**
* Add a select Field at the bottom
*/
function uommulti_woo_custom_fields() {
  $field = array(
    'id' => 'uommulti_custom_field',
    'label' => __( 'Mennyiségi egység:', 'textdomain' ),

  );
  woocommerce_wp_text_input( $field );
}


add_action( 'woocommerce_process_product_meta', 'uommulti_save_custom_field' );

function uommulti_save_custom_field( $post_id ) {
  // Tertiary operator
  // kérdés ? igaz : hamis
  $custom_field_value = isset( $_POST['uommulti_custom_field'] ) ? $_POST['uommulti_custom_field'] : '';

  update_post_meta($post_id, 'uommulti_custom_field', $custom_field_value);

}

add_filter( 'woocommerce_get_price_html', 'uommulti_render_output', 10, 2 );

function uommulti_render_output( $price ) {
    global $post;
    // Check if uom text exists.
    $uommulti_output = get_post_meta( $post->ID, 'uommulti_custom_field', true );
    // Check if variable OR UOM text exists.
    if ( $uommulti_output ) :
        $price = $price . '<span class="uommulti"> / ' . esc_attr( $uommulti_output, 'uommulti_custom_field' ) . '</span>';
        return $price;
    else :
        return $price;
    endif;
}

add_filter( 'woocommerce_cart_item_price', 'uommulti_product_price_cart', 10, 3 );

function uommulti_product_price_cart( $price, $cart_item, $cart_item_key ) {

	$unit_price = get_post_meta( $cart_item['product_id'], 'uommulti_custom_field', true );

	if ( ! empty( $unit_price ) ) {
		$price .= ' / ' . $unit_price;	
	}

	return $price;
}	