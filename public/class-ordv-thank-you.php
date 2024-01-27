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
class Ordv_Biteship_Thankyou{

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

		$this->plugin_name          = $plugin_name;
		$this->version              = $version;
	}

	/**
	 * Register new user when checkout if customer doesn't log in
	 * Hooked via	action woocommerce_thankyou
	 * @since		1.0.0
	 * @param		$order_id
	 * @return		void
	 */
	public function ordv_biteship_wc_register_guests( $order_id ){
		
		// get all the order data
		$order = new WC_Order($order_id);

		//get the user email from the order
		$order_email = $order->get_billing_email();

		// check if there are any users with the billing email as user or email
		$email	= email_exists( $order_email );  
		$user	= username_exists( $order_email );

		 // if the UID is null, then it's a guest checkout
		if( $user == false && $email == false ){
			// perform guest user actions here

			$random_password = wp_generate_password();
			$user_id = wp_create_user( $order_email, $random_password, $order_email );

			update_user_meta( $user_id, 'first_name', $order->get_billing_first_name() );
            update_user_meta( $user_id, 'last_name', $order->get_billing_last_name() );

			//user's billing data
			update_user_meta( $user_id, 'billing_address_1', $order->get_billing_address_1() );
			update_user_meta( $user_id, 'billing_address_2', $order->get_billing_address_2() );
			update_user_meta( $user_id, 'billing_city', $order->get_billing_city() );
			update_user_meta( $user_id, 'billing_company', $order->get_billing_company() );
			update_user_meta( $user_id, 'billing_country', $order->get_billing_country() );
			update_user_meta( $user_id, 'billing_email', $order->get_billing_email() );
			update_user_meta( $user_id, 'billing_first_name', $order->get_billing_first_name() );
			update_user_meta( $user_id, 'billing_last_name', $order->get_billing_last_name() );
			update_user_meta( $user_id, 'billing_phone', $order->get_billing_phone() );
			update_user_meta( $user_id, 'billing_postcode', $order->get_billing_postcode() );
			update_user_meta( $user_id, 'billing_state', $order->get_billing_state() );
		 
			// user's shipping data
			update_user_meta( $user_id, 'shipping_address_1', $order->get_shipping_address_1() );
			update_user_meta( $user_id, 'shipping_address_2', $order->get_shipping_address_2() );
			update_user_meta( $user_id, 'shipping_city', $order->get_shipping_city() );
			update_user_meta( $user_id, 'shipping_company', $order->get_shipping_company() );
			update_user_meta( $user_id, 'shipping_country', $order->get_shipping_country() );
			update_user_meta( $user_id, 'shipping_first_name', $order->get_shipping_first_name() );
			update_user_meta( $user_id, 'shipping_last_name', $order->get_shipping_last_name() );
			update_user_meta( $user_id, 'shipping_method', $order->get_shipping_method() );
			update_user_meta( $user_id, 'shipping_postcode', $order->get_shipping_postcode() );
			update_user_meta( $user_id, 'shipping_state', $order->get_shipping_state() );

			$data_area = WC()->session->get( 'data_area' );

			update_user_meta( $user_id, 'user_order_area_id',	$data_area['id'] );
			update_user_meta( $user_id, 'user_order_area_text',	$order->get_billing_city() );
			update_user_meta( $user_id, 'user_order_area_lat',	$data_area['lat'] );
			update_user_meta( $user_id, 'user_order_area_lng',	$data_area['lng'] );
			
			WC()->session->__unset( 'data_area');

			// link past orders to this newly created customer
			wc_update_new_customer_past_orders( $user_id );
			wc_set_customer_auth_cookie( $user_id );

		}else{
			// if user has registered and doesn't have meta for area_id & text
			$user = get_user_by( 'email', $order_email );
			$user_id = $user->ID;

			$data_area = WC()->session->get( 'data_area' );

			update_user_meta( $user_id, 'user_order_area_id',	$data_area['id'] );
			update_user_meta( $user_id, 'user_order_area_text',	$order->get_billing_city() );
			update_user_meta( $user_id, 'user_order_area_lat',	$data_area['lat'] );
			update_user_meta( $user_id, 'user_order_area_lng',	$data_area['lng'] );

			WC()->session->__unset( 'data_area');

		}

	}


}
