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
class Ordv_Biteship_Check_Awb {

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
     * Register Scripts & Styles in check-awb page
	 * Hooked via	action wp_enqueue_scripts
	 * @since 		1.0.0
	 * @return 		void
     */
	public function ordv_biteship_cek_resi_scripts_load(){

		if( ordv_biteship_fn_is_wc_endpoint( 'check-awb') ){

			wp_enqueue_script( 'cek-resi-script', ORDV_BITESHIP_URI.'public/js/ordv-check-awb.js', array( 'jquery' ), ORDV_BITESHIP_VERSION, true );
			$settings = array(
				'ajax_url'  => admin_url( 'admin-ajax.php' ),               
				'cek_resi'      => [
					'action'    => 'cek_resi_data',
				],
				'get_detail'	=> [
					'action'	=> 'get_resi_detail',
					'nonce'     => wp_create_nonce( 'ajax-nonce' )
				]
			);

			wp_localize_script( 'cek-resi-script', 'cek_resi_ajax', $settings);

			wp_enqueue_style( $this->plugin_name.'-bulma', 'https://cdn.jsdelivr.net/npm/bulma@0.9.4/css/bulma.min.css' );
			wp_enqueue_style( $this->plugin_name.'-bulma-timeline', ORDV_BITESHIP_URI.'public/css/bulma-timeline.min.css' );
			wp_enqueue_style( $this->plugin_name.'-check-awb', ORDV_BITESHIP_URI.'public/css/ordv-biteship-check-awb.css' );

		}
	}

	/**
	 * Register New endpoint for check awb page
	 * Hooked via	action init
	 * @since 		1.0.0
	 * @return 		void
	 */
    public function ordv_biteship_register_check_awb_endpoint(){
        add_rewrite_endpoint( 'check-awb', EP_ROOT | EP_PAGES );
    }


	/**
	 * Query vars for check awb page
	 * Hooked via	filter query_vars
	 * @since 		1.0.0
	 * @param 		$vars
	 * @return 		void
	 */
    public function ordv_biteship_check_awb_query_vars( $vars ){
        $vars[] = 'check-awb';
	    return $vars;
    }
	
	/**
	 * Add tab to myaccount page 
	 * Hooked via	filter woocommerce_account_menu_items
	 * @since 		1.0.0
	 * @param 		$items
	 * @return 		void
	 */
    public function ordv_biteship_add_check_awb_tab( $items ){
        $items['check-awb'] = 'Cek Resi';
	    return $items;
    }


	/**
	 * Re-order tab menu in my account page woocommerce
	 * Hooked via	filter woocommerce_account_menu_items 
	 * @since 		1.0.0
	 * @param 		array $items
	 * @return 		void
	 */
    public function ordv_biteship_reorder_account_menu( $items ){
        return array(
	        'dashboard'          => __( 'Dashboard', 'woocommerce' ),
	        'orders'             => __( 'Orders', 'woocommerce' ),
            'check-awb'          => __( 'Cek Resi', 'woocommerce' ),
	        'downloads'          => __( 'Downloads', 'woocommerce' ),
	        'edit-account'       => __( 'Edit Account', 'woocommerce' ),	        
	        'edit-address'       => __( 'Addresses', 'woocommerce' ),
	        'customer-logout'    => __( 'Logout', 'woocommerce' ),
        );
    }

	/**
	 * Template for check awb page
	 * Hooked via	action woocommerce_account_check-awb_endpoint
	 * @since 		1.0.0
	 * @return 		void
	 */
    public function ordv_biteship_add_check_awb_content(){

        ob_start();
        include ORDV_BITESHIP_PATH.'public/partials/ordv-check-awb-public-display.php';
        echo ob_get_clean();        

    }

	/**
	 * Handle query order number by using 'no_resi'
	 * Hooked via	filter woocommerce_order_data_store_cpt_get_orders_query
	 * @since 		1.0.0
	 * @param 		$query
	 * @param 		$query_vars
	 * @return 		void
	 */
    public function ordv_biteship_handle_order_number_custom_query_var( $query, $query_vars ){
        
        if ( ! empty( $query_vars['no_resi'] ) ) {
            $query['meta_query'][] = array(
                'key' => 'no_resi',
                'value' => esc_attr( $query_vars['no_resi'] ),
            );
        }
    
        return $query;
    }

	/**
	 * Check AWB / No Resi data
	 * Hooked via	action wp_ajax_cek_resi_data
	 * @since 		1.0.0
	 * @return 		void
	 */
	public function ordv_biteship_cek_resi_data(){

		if(isset($_POST['data']))
		{
			parse_str($_POST['data'], $data);

			if ( ! wp_verify_nonce( $data['cek_no_resi_nonce'], 'cek_no_resi' ) ) 
			{
				die( 'Close The Door!');
			}
			
			$no_resi = $data['no_resi'];

			if( $no_resi )
			{
				$order 	= wc_get_orders( array( 'no_resi' => $no_resi ) );
				if($order)
				{
					$order_id			= $order[0]->get_id();
					$order_biteship_id	= get_post_meta($order_id, 'order_biteship_id', true);

					// do ajax here
					$detail_data = ordv_biteship_fn_detail_data_tracking( $order_biteship_id );					

					ob_start();
					include ORDV_BITESHIP_PATH.'public/partials/check-awb/show-hasil-data.php';
					$set_data =  ob_get_clean();

					$response = array(
						'status'	=> 1,
						'content'	=> $set_data,
						'notice'	=> 'Data Nomer Resi ditemukan.',
						'class'		=> 'woocommerce-message',
					);

				}
				else
				{
					$response = array(
						'status'	=> 0,					
						'content'	=> '',
						'notice'	=> 'Data Nomer Resi tidak ditemukan.',
						'class'		=> 'woocommerce-error',
					);
				}
			}
			else
			{
				$response = array(
					'status'	=> 0,					
					'content'	=> '',
					'notice'	=> 'Data Nomer Resi tidak ditemukan.',
					'class'		=> 'woocommerce-error',
				);
			}

			wp_send_json($response);
			wp_die();
			
		}
		
	}

	/**
	 * Get detail delivery status for timeline view in check awb page
	 * Hooked via	action wp_ajax_get_resi_detail
	 * @since 		1.0.0
	 * @return 		void
	 */
	public function ordv_biteship_get_resi_detail(){
		
		if ( ! wp_verify_nonce( $_POST['nonce'], 'ajax-nonce' ) ) {
            die( 'Close The Door!');
        }

		if( $_POST['i'] ){

			$order_biteship_id = $_POST['i'];
			$detail_data = ordv_biteship_fn_detail_data_tracking( $order_biteship_id );

			ob_start();
			include ORDV_BITESHIP_PATH.'public/partials/check-awb/show-detail-data.php';
			$set_data =  ob_get_clean();

			$response = array(
				'status'	=> 1,
				'content'	=> $set_data,
				'notice'	=> 'Data Nomer Resi ditemukan.',
				'class'		=> 'woocommerce-message',
			);

		}else{			

			$response = array(
				'status'	=> 0,					
				'content'	=> '',
				'notice'	=> 'Data tidak ditemukan.',
				'class'		=> 'woocommerce-error',
			);

		}

		wp_send_json($response);
		wp_die();

	}

	/**
	 * Register new endpoint API for biteship callback
	 * Hooked via	action rest_api_init, priority 10
	 * @since 		1.0.0
	 * @return		mixed
	 */
	public function ordv_add_callback_url_endpoint(){
		register_rest_route(
			'biteship-webhook/v1', // Namespace
			'update-order', // Endpoint
			array(
				'methods'  => 'POST',
				'callback' => 'ordv_update_order_callback'
			)
		);
	}



}
