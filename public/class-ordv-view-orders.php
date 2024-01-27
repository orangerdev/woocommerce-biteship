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
class Ordv_Biteship_View_Order{

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
     * Add custom style for view order in my account
     * Hooked via   woocommerce_view_order 
     * @since       1.0.0
     * @return      void
     */
    public function ordv_biteship_add_style(){
            
        wp_enqueue_style( $this->plugin_name.'-bulma-timeline', ORDV_BITESHIP_URI.'public/css/bulma-timeline.min.css' );
        wp_enqueue_style( $this->plugin_name.'-view-order', ORDV_BITESHIP_URI.'public/css/ordv-biteship-view-order.css' );

    }

    /**     
     * Get & display delivery details data in view order page
     * Hooked via   action woocommerce_after_order_details
     * @since       1.0.0
     * @param       $order
     * @return      void
     */
    public function ordv_biteship_add_delivery_details( $order ){
        
        $order_id           = $order->get_id();
        $order_biteship_id   = get_post_meta( $order_id, 'order_biteship_id', true );        

        if( $order_biteship_id ){

            $detail_data = ordv_biteship_fn_detail_data_tracking( $order_biteship_id );
            $update_data = ordv_biteship_fn_update_data_tracking( $order_id, $detail_data );

			

			ob_start();
            include ORDV_BITESHIP_PATH.'public/partials/view-order/show-order-hasil-data.php';
			include ORDV_BITESHIP_PATH.'public/partials/view-order/show-order-detail-data.php';
			$set_data =  ob_get_clean();

            echo $set_data;

        }else{
            echo '<p>Data belum tersedia</p>';
        }

    }

}
