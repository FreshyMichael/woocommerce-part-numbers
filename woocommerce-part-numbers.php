<?php
/**
* Plugin Name: WooCommerce Part Numbers
* Plugin URI: https://github.com/FreshyMichael/woocommerce-part-numbers
* Description: Add a Custom Part Number Field for WooCommerce Products, and include that part number with notification emails
* Version: 1.0.0
* Author: FreshySites
* Author URI: https://freshysites.com/
* License: GNU v3.0
*/

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

/* PluginName Start */
//______________________________________________________________________________

// Display Fields
add_action('woocommerce_product_options_general_product_data', 'woocommerce_product_part_number_field');
// Save Fields
add_action('woocommerce_process_product_meta', 'woocommerce_product_part_number_save');
function woocommerce_product_part_number_field()
{
    global $woocommerce, $post;
    echo '<div class="part_number_field">';
    // Custom Product Text Field
    woocommerce_wp_text_input(
        array(
            'id' => 'part_number',
            'placeholder' => 'Part Number',
            'label' => __('Part Number', 'woocommerce'),
            'desc_tip' => 'true'
        )
    );

    echo '</div>';
}

//Save the Part Number field in the DB

function woocommerce_product_part_number_save($post_id)
{
    // Custom Product Text Field
    $woocommerce_product_part_number_field = $_POST['part_number'];
    if (!empty($woocommerce_product_part_number_field))
        update_post_meta($post_id, 'part_number', esc_attr($woocommerce_product_part_number_field));
}

//Add the Part Number Field to the emails


add_action( 'woocommerce_order_item_meta_start', 'email_confirmation_display_order_items', 10, 3 );
function email_confirmation_display_order_items( $item_id, $item, $order ) {
    // On email notifications for line items
    if ( ! is_wc_endpoint_url() && $item->is_type('line_item') ) {
        $part_number = get_post_meta( $item->get_product_id(), 'part_number', true );

        if ( ! empty($part_number) ) {
            printf( '<div>' . __("Part Number: %s", "woocommerce") . '</div>', $part_number );
        }
    }
}

//______________________________________________________________________________
// All About Updates

//  Begin Version Control | Auto Update Checker
require 'plugin-update-checker/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
// ***IMPORTANT*** Update this path to New Github Repository Master Branch Path
	'https://github.com/FreshyMichael/woocommerce-part-numbers',
	__FILE__,
// ***IMPORTANT*** Update this to New Repository Master Branch Path
	'woocommerce-part-numbers'
);
//Enable Releases
$myUpdateChecker->getVcsApi()->enableReleaseAssets();
//Optional: If you're using a private repository, specify the access token like this:
//
//
//Future Update Note: Comment in these sections and add token and branch information once private git established
//
//
//$myUpdateChecker->setAuthentication('your-token-here');
//Optional: Set the branch that contains the stable release.
//$myUpdateChecker->setBranch('stable-branch-name');

//______________________________________________________________________________
/* PluginName End */

//-------------Single Product----------------//
//------------------------------------------//

// Display Fields
/*
add_action('woocommerce_product_options_general_product_data', 'woocommerce_product_custom_fields');
// Save Fields
add_action('woocommerce_process_product_meta', 'woocommerce_product_custom_fields_save');
function woocommerce_product_custom_fields()
{
    global $woocommerce, $post;
    echo '<div class="part_number_field">';
    // Custom Product Text Field
    woocommerce_wp_text_input(
        array(
            'id' => 'part_number',
            'placeholder' => 'Part Number',
            'label' => __('Part Number', 'woocommerce'),
            'desc_tip' => 'true'
        )
    );

    echo '</div>';
}

//Save the Part Number field in the DB

function woocommerce_product_custom_fields_save($post_id)
{
    // Custom Product Text Field
    $woocommerce_custom_product_text_field = $_POST['part_number'];
    if (!empty($woocommerce_custom_product_text_field))
        update_post_meta($post_id, 'part_number', sanitize_text_field($woocommerce_custom_product_text_field));
}



//-----------------Variable Product--------------//

// Source: https://www.businessbloomer.com/woocommerce-add-custom-field-product-variation/

// 1. Add Part Number input @ Product Data > Variations > Single Variation

add_action( 'woocommerce_variation_options_pricing', 'fs_add_variation_part_number_to_variations', 10, 3 );

function fs_add_variation_part_number_to_variations( $loop, $variation_data, $variation ) {
   woocommerce_wp_text_input( array(
	'id' => 'variation_part_number[' . $loop . ']',
	'class' => 'short',
	'label' => __( 'Variation Part Number:', 'woocommerce' ),
	'value' => get_post_meta( $variation->ID, 'variation_part_number', true )
   ) );
}

// -----------------------------------------
// 2. Save Part Number on product variation save

add_action( 'woocommerce_save_product_variation', 'fs_save_variation_part_number_variations', 10, 2 );

function fs_save_variation_part_number_variations( $variation_id, $i ) {
   $variation_part_number = $_POST['variation_part_number'][$i];
   if ( isset( $variation_part_number ) ) update_post_meta( $variation_id, 'variation_part_number', esc_attr( $variation_part_number ) );
}


// 3. Store Part Number value into variation data

add_filter( 'woocommerce_available_variation', 'fs_add_variation_part_number_variation_data' );

function fs_add_variation_part_number_variation_data( $variations ) {
   $variations['variation_part_number'] = '<div class="woocommerce_variation_part_number">Part Number: <span>' . get_post_meta( $variations[ 'variation_id' ], 'variation_part_number', true ) . '</span></div>';
   return $variations;
}

//-------------- Emails --------------//

//Add the Part Number Field to the emails

add_action( 'woocommerce_order_item_meta_start', 'email_confirmation_display_order_items', 10, 3 );
function email_confirmation_display_order_items( $item_id, $item, $order ) {
	
	//-------------- Variable Products --------------//
	global $product;
	
	if ( $product->is_type( 'variable' ) ) {
		
		$variation = wc_get_product($item['variation_id']);
		// $variation_attributes = $variation->get_variation_attributes();
		
		if ( ! is_wc_endpoint_url() && $item->is_type('line_item') ) {
		
       	 $part_number = get_post_meta( $item->/*get_product_id()*//*$variation, 'variation_part_number', true );

        	if ( ! empty($part_number) ) {
            	printf( '<div>' . __("Part Number: %s", "woocommerce") . '</div>', $part_number );
        	}
    	}
		
	}		
    
//-------------- All Other Products --------------//
	else{		
		
	// On email notifications for line items
    	if ( ! is_wc_endpoint_url() && $item->is_type('line_item') ) {
		
       	 $part_number = get_post_meta( $item->get_product_id(), 'part_number', true );

        	if ( ! empty($part_number) ) {
            	printf( '<div>' . __("Part Number: %s", "woocommerce") . '</div>', $part_number );
        	}
    	}
	}

}
*/

?>
