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
class Ordv_Biteship_Checkout {

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
     * Change province name to adjust woocommerce and biteship
     * Hooked via   filter woocommerce_states
     * @since       1.0.0
     * @param       array $states
     * @return      void
     */
    public function ordv_biteship_change_province_name( $states ){
        
        $states['ID']['AC'] = 'Aceh';
        $states['ID']['YO'] = 'DI Yogyakarta';
        
        return $states;
        
    }
	
    /**
     * Remove unecesarry fied in checkout page
     * Hooked via   Filter woocommerce_checkout_fields
     * @since       1.0.0
     * @param       $fields
     * @return      void
     */
    public function ordv_biteship_remove_checkout_field( $fields ){
        
        unset($fields['billing']['billing_company']);           // remove company field
        unset($fields['billing']['billing_address_1']);         // remove billing address 1 field
        unset($fields['billing']['billing_address_2']);         // remove billing address 2 field
        unset($fields['billing']['billing_city']);              // remove billing city field
        unset($fields['billing']['billing_state']);             // remove billing state field
        
        // return field
        return $fields;

    }

    /**
     * Add custom field in checkout page
     * Hooked via   filter woocommerce_checkout_fields priority 15
     * @since       1.0.0
     * @param       $fields
     * @return      void
     */
    public function ordv_biteship_add_checkout_fields( $fields ){

        $fields['billing']['billing_address_1'] = array(
            'type'      => 'textarea',
            'label'     => __('Address', 'woocommerce'),
            'placeholder'   => _x('Nama jalan dan nomor rumah', 'placeholder', 'woocommerce'),
            'required'  => true,
            'class'     => array('form-row-wide'),
            'clear'     => true,
            'priority'  => 35
        );

        if ( is_user_logged_in() ) {

            $user_id = get_current_user_id();

            $user_order_area_id = get_user_meta( $user_id, 'user_order_area_id', true );
            $user_order_area_text = get_user_meta( $user_id, 'user_order_area_text', true );
            $user_order_area_lat = get_user_meta( $user_id, 'user_order_area_lat', true );
            $user_order_area_lng = get_user_meta( $user_id, 'user_order_area_lng', true );
            
            if( $user_order_area_id && $user_order_area_text ){

                $fields['billing']['ordv-area'] = array(
                    'type'      => 'select',
                    'label'     => __('Kelurahan / Desa', 'woocommerce'),
                    'placeholder'   => _x('Pilih Kelurahan / Desa...', 'placeholder', 'woocommerce'),
                    'required'  => true,
                    'class'     => array('form-row-wide'),
                    'clear'     => true,
                    'options'   => array( 
                                    $user_order_area_id => $user_order_area_text
                                ),
                    'priority'  => 82
                );

                $fields['billing']['ordv-area']['data-lat'] = $user_order_area_lat;
                $fields['billing']['ordv-area']['data-lng'] = $user_order_area_lng;
                
            }else{

                $fields['billing']['ordv-area'] = array(
                    'type'      => 'select',
                    'label'     => __('Kelurahan / Desa', 'woocommerce'),
                    'placeholder'   => _x('Pilih Kelurahan / Desa...', 'placeholder', 'woocommerce'),
                    'required'  => true,
                    'class'     => array('form-row-wide'),
                    'clear'     => true,
                    'options'   => array( '' => '' ),
                    'priority'  => 82
                );
            }
            
        } else {

            $fields['billing']['ordv-area'] = array(
                'type'      => 'select',
                'label'     => __('Kelurahan / Desa', 'woocommerce'),
                'placeholder'   => _x('Pilih Kelurahan / Desa...', 'placeholder', 'woocommerce'),
                'required'  => true,
                'class'     => array('form-row-wide'),
                'clear'     => true,
                'options'   => array( '' => '' ),
                'priority'  => 82
            );            
        }
        
        $fields['billing']['billing_city'] = array(
            'type'      => 'hidden',
            'label'     => __('city', 'woocommerce'),
            'placeholder'   => _x('', 'woocommerce'),
            'required'  => true,
            'class'     => array('form-row-wide'),
            'priority'  => 86
        );

        //$fields['billing']['billing_state']['label'] = false;
        $fields['billing']['billing_city']['label'] = false;
    
        return $fields;
    }

    /**
     * Remove required state field
     * Hooked via   filter woocommerce_default_address_fields Priority 999
     * @since       1.0.0
     * @param       $address_fields
     * @return      void
     */
    public function ordv_biteship_override_default_address_fields( $address_fields ) {
        $address_fields['state']['required'] = false;
        return $address_fields;
    }

    /**
     * Load scripts only in checkout page
     * Hooked via   action woocommerce_checkout_billing
     * @since       1.0.0
     * @return      void
     */
    public function ordv_biteship_load_checkout_scripts(){

        $style = '#billing_country_field, #shipping_country_field{ display: none !important; }';
        $style .= '#billing_delivery_option_field.radio {display: inline !important; margin-left: 5px;}';
        $style .= '#billing_delivery_option_field input[type="radio"] { margin-left: 0px;}';
        $style .= '#billing_delivery_option_field input[type="radio"] + label { display: inline-block; margin-right:15px; }';
        echo '<style>'.$style.'</style>'."\n";

        if ( is_checkout() ) {

            //WC()->session->set( 'data_kurir', null );
            WC()->session->__unset( 'data_kurir');

            wp_enqueue_script( 'checkout-script', plugin_dir_url( __DIR__ ). 'public/js/ordv-biteship-checkout.js', array( 'jquery', 'selectWoo' ), ORDV_BITESHIP_VERSION, true );
            
            $settings = array(
                'ajax_url'  => admin_url( 'admin-ajax.php' ),               
                'area'      => [
                    'action'    => 'get_data_area',
                    'nonce'     => wp_create_nonce( 'ajax-nonce' )
                ],
                'get_services_first_time' => [
                    'action'    => 'get_data_services_first_time',
                    'nonce'     => wp_create_nonce( 'ajax-nonce' )
                ],
                'get_no_select_value' => [
                    'action'    => 'get_data_no_select_value',
                    'nonce'     => wp_create_nonce( 'ajax-nonce' )
                ],
                'get_services' => [
                    'action'    => 'get_data_services',
                    'nonce'     => wp_create_nonce( 'ajax-nonce' )
                ]
            );

            wp_localize_script( 'checkout-script', 'checkout_ajax', $settings);
        }

    }

    /**
     * Get list "kelurahan" data for checkout page
     * Hooked via   add_action wp_ajax_get_data_area
     * @since       1.0.0
     * @return      void
     */
    public function ordv_biteship_get_data_area(){

        if ( wp_verify_nonce( $_GET['nonce'], 'ajax-nonce' ) ) {

            $data = array();
            $keyword = $_GET['search'];
            
            if( $keyword ){

                $get_data_area = ordv_biteship_fn_get_list_area( $keyword );

                foreach ($get_data_area as $key => $area) {
                    
                    if ( isset( $area->id ) ) :

						$data[] = [
                            'id' 	=> $area->id,
                            'text' 	=> $area->name,
                            'country_name' => $area->country_name,
                            'country_code' => $area->country_code,
                            'level_1_name' => $area->administrative_division_level_1_name,
                            'level_1_type' => $area->administrative_division_level_1_type,
                            'level_2_name' => $area->administrative_division_level_2_name,
                            'level_2_type' => $area->administrative_division_level_2_type,
                            'level_3_name' => $area->administrative_division_level_3_name,
                            'level_3_type' => $area->administrative_division_level_3_type,
                            'lat'  => '',
                            'lng'  => '',
                            'postcode' => $area->postal_code
						];
                        

					endif;
                }
            }

            WC()->session->__unset( 'data_kurir');
            wp_send_json( $data );

        }
    }

    /**
     * Get list "kurir" and cost
     * @uses    Hooked add_action wp_ajax_get_data_services_first_time
     * @uses    Hooked add_action wp_ajax_get_data_services
     * @since   1.0.0
     * @return  void
     */
    public function ordv_biteship_get_data_services(){

        if ( ! wp_verify_nonce( $_POST['nonce'], 'ajax-nonce' ) ) {
            die( 'Close The Door!');
        }

        $get_action  = $_POST['action'];

        if( 'get_data_services_first_time' === $get_action ){

            if ( is_user_logged_in() ) {

                $user_id = get_current_user_id();                                
                $user_order_area_lat = get_user_meta( $user_id, 'user_order_area_lat', true );
                $user_order_area_lng = get_user_meta( $user_id, 'user_order_area_lng', true );

            }

            $api_d_area_id  = $_POST['a'];
            $area_id_lat    = $user_order_area_lat;
            $area_id_lng    = $user_order_area_lng;           

        } else {

            $api_d_area_id  = $_POST['a'];
            $area_id_lat    = $_POST['a_lat'];
            $area_id_lng    = $_POST['a_lng'];

        }

        $data_packages  = ordv_biteship_fn_get_packages_data();

        $data_list_kurir = ordv_biteship_fn_get_data_list_kurir( $api_d_area_id, $area_id_lat, $area_id_lng, $data_packages );

        // set session data for add_rates
        WC()->session->set( 'data_kurir' , $data_list_kurir );

        $data = array(
            'id'    => $api_d_area_id,
            'lat'   => $area_id_lat,
            'lng'   => $area_id_lng
        );

        // save data for session
        WC()->session->set( 'data_area' , $data );

        //$result = 'ok';
        $result = array(
            'success'   => true,
            'message'   => 'ok'
        );
        wp_send_json( $result );
        wp_die();

    }

    /**
     * Display package detail data in sidebar of checkout page
     * Hooked via   filter woocommerce_shipping_package_name Priority 10
     * @since       1.0.0
     * @param       $name
     * @return      void
     */
    public function ordv_biteship_custom_shipping_package_name( $name ){
        
        $name           = 'Pengiriman';
        $packages       = ordv_biteship_fn_get_packages_data();

        $active_weight_unit     = get_option('woocommerce_weight_unit');
        $active_dimension_unit  = get_option('woocommerce_dimension_unit');

        $total_weight   = $packages['weight'];
        $total_height   = $packages['height'];
        $total_width    = $packages['width'];
        $total_length   = $packages['length'];
        $origin_text    = $packages['origin_text'];

        $name       .= '<br/><small>dari '.$origin_text.'</small>';
        $name       .= '<br/><small>berat '.$total_weight.' '.$active_weight_unit;
        $name       .= '</small>';
        $name       .= '<br/><small>ukuran '.$total_length.'x'.$total_width.'x'.$total_height.' '.$active_dimension_unit.'</small>';
                
        return $name;
        
    }
    
    /**
     * Remove Shipping option data in cart page
     * Hooked via   filter woocommerce_cart_needs_shipping
     * @since       1.0.0
     * @param       $needs_shipping
     * @return      void
     */
    public function ordv_biteship_filter_cart_needs_shipping( $needs_shipping ) {
        if ( is_cart() ) {
            $needs_shipping = false;
        }
        return $needs_shipping;
    }

    /**
     * Save order custom field data when create order
     * Hooked via   action woocommerce_checkout_create_order
     * @since       1.0.0
     * @param       $order
     * @param       $data
     * @return      void
     */
    public function ordv_biteship_save_order_custom_meta_data(  $order, $data  ){
                
        if ( isset( $_POST['shipping_method'][0] ) ){
            $order->update_meta_data('rate_id', $_POST['shipping_method'][0] );
        }

        if ( isset( $_POST['ordv-area'] ) ){
            $order->update_meta_data('d_area_id', $_POST['ordv-area'] );
        }

        $data_dest_cord = WC()->session->get( 'dest_cord' );

        if( $data_dest_cord ){
            $order->update_meta_data('d_lat_area_id', $data_dest_cord['lat'] );
            $order->update_meta_data('d_lng_area_id', $data_dest_cord['lng'] );
        }

        WC()->session->__unset( 'dest_cord');

    }

    /**
     * Replace '[receiver_name]' with customer name
     * Hooked via   action ordv_biteship_biteship_additional_detail
     * @since       1.0.0
     * @param       $order
     * @return      void
     */
    public function ordv_biteship_biteship_additional_detail( $order ){

        $status_tracking = get_post_meta( $order->get_id(), 'status_tracking', true );
        
        if( $status_tracking ){
            $nama = $order->get_billing_first_name().' '.$order->get_billing_last_name();
            $status_tracking = str_replace('[receiver_name]', $nama, $status_tracking);

            ?>
                <tr>
                    <th scope="row">Delivery Status:</th>
                    <td><?php echo esc_html( $status_tracking ) ?></td>
                </tr>
		    <?php

        }else{

            ?>
                <tr>
                    <th scope="row">Delivery Status:</th>
                    <td>-</td>
                </tr>
		    <?php

        }

    }

    /**
     * Add shipping method for biteship
     * 
     * Hooked via   action  woocommerce_checkout_before_order_review, priority 10
     * @since       1.0.0
     * @param       mixed   $posted_data
     * @return      mixed
     */
    public function ordv_biteship_add_rates( $posted_data ){
        $post = array();
        $vars = explode('&', $posted_data);

        foreach ($vars as $k => $value){
            $v = explode('=', urldecode($value));
            $post[$v[0]] = $v[1];
        }

        $data_area =  WC()->session->get('data_area');

        if( $data_area ):

            $api_d_area_id  = $data_area['id'];
            $area_id_lat    = $data_area['lat'];
            $area_id_lng    = $data_area['lng'];

            $data_packages  = ordv_biteship_fn_get_packages_data();
            $data_list_kurir = ordv_biteship_fn_get_data_list_kurir( $api_d_area_id, $area_id_lat, $area_id_lng, $data_packages );

            WC()->session->set( 'data_kurir', $data_list_kurir );

        else:
            // do nothing
        endif;

        // refresh cache data method shipping woocommerce
        $packages = WC()->cart->get_shipping_packages();
        foreach ($packages as $package_key => $package) {
            $session_key = 'shipping_for_package_'.$package_key;
            $stored_rates = WC()->session->__unset($session_key);
        }

    }

    /**
     * Remove all session data related biteship if field kelurahan not selected
     * Hooked via   action wp_ajax_get_data_no_select_value
     * @since       1.0.0 
     * @return      mixed
     */
    public function ordv_biteship_get_data_no_select_value(){

        if ( ! wp_verify_nonce( $_POST['nonce'], 'ajax-nonce' ) ) :
            die( 'Close The Door!');
        endif;

        $list_data_kurir    = WC()->session->get('data_kurir');
        $data_area          = WC()->session->get('data_area');

        if( NULL !== $list_data_kurir ):
            WC()->session->set( 'data_kurir', null );
        endif;

        if( NULL !== $data_area ):
            WC()->session->set( 'data_area', null );
        endif;

    }


}
