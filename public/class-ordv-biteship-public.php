<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://ridwan-arifandi.com
 * @since      1.0.0
 *
 * @package    Ordv_Biteship
 * @subpackage Ordv_Biteship/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Ordv_Biteship
 * @subpackage Ordv_Biteship/public
 * @author     Ridwan Arifandi <orangerdigiart@gmail.com>
 */
class Ordv_Biteship_Public {

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
	 * Check cart, remove any items in if not related to current item location
	 * Hooked via action woocommerce_add_to_cart, priority 1
	 * @since 	1.0.0
	 * @param  	string 		$cart_item_key
	 * @param  	integer 	$product_id
	 * @param  	integer 	$quantity
	 * @param  	integer 	$variation_id
	 * @param  	[type] 		$variation
	 * @param  	array 		$cart_item_data
	 * @return 	void
	 */
	public function ordv_biteship_check_cart( $cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data ) {

		if( 0 !== absint($variation_id) ) :
			$term = carbon_get_theme_option("biteship_location_term");
			//$location = wc_get_product( $variation_id )->get_attribute( "pa_" . $term );
		
			// get attribute slug value, instead of name
			$location = wc_get_product( $variation_id )->get_attributes();
			$location = $location["pa_" . $term];


			foreach( WC()->cart->get_cart() as $cart_item ) :
				if(
					array_key_exists("variation", $cart_item) &&
					array_key_exists("attribute_pa_" . $term, $cart_item["variation"]) &&
					strtoupper(sanitize_url($location)) !== strtoupper(sanitize_url($cart_item["variation"]["attribute_pa_" . $term]))
				) :
					WC()->cart->remove_cart_item( $cart_item["key"] );
				endif;
			endforeach;

		endif;
	}

}
