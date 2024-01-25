<?php

/**
 * Get list area from biteship
 * @uses    Ordv_Biteship_Checkout::ordv_biteship_get_data_area
 * @uses    Ordv_Biteship_Edit_Address_Billing::ordv_biteship_get_edit_data_area
 * @since   1.0.0
 * @param   string  $search
 * @return  mixed
 */
// id17 done 1 - get locations
function ordv_biteship_fn_get_list_area( $search = '' ){

    $data = [];

    $api_url = add_query_arg( array(
        'countries' => 'ID',
        'input'   => $search,
        'type'  => 'single'
    ), ordv_biteship_get_url_api().'/v1/maps/areas' );
    $api_key = carbon_get_theme_option('w_biteship_api_key');

    $args = array(
        'headers' => array(
            'Content-Type' => 'application/json',
            'authorization'    => $api_key
        )
    );

    $response = wp_remote_get(
        $api_url,
        $args
    );

    $responseBody = wp_remote_retrieve_body( $response );
    $result = json_decode( $responseBody );

    if ( ! is_wp_error( $result ) ) :

        $data = $result->areas;

    endif;

    return $data;

}

/**
 * Get detail Package order data
 * @uses    Ordv_Biteship_Checkout::ordv_biteship_get_data_services
 * @uses    Ordv_Biteship_Checkout::ordv_biteship_custom_shipping_package_name
 * @since   1.0.0
 * @return  void
 */
function ordv_biteship_fn_get_packages_data(){    

    $origin_id = 0;
    $origin_text = '';
    $total_length = 0;
    $total_height = 0;
    $total_width = 0;
    $total_weight = WC()->cart->get_cart_contents_weight();

    $items_data = [];

    $items = WC()->cart->get_cart(); 

    foreach($items as $item => $values) : 

        $product = $values['data'];
        $item_attribute = $product->get_attribute( 'pa_lokasi' );
        $item_term      = get_term_by( 'name', $item_attribute, 'pa_lokasi' );
        $area_id        = get_term_meta( $item_term->term_taxonomy_id, '_origin_area_id', true);
        $area_text      = get_term_meta( $item_term->term_taxonomy_id, '_origin_area_text', true);
        $origin_id	    = $area_id;
        $origin_text    = strval( $area_text );

        $quantity = $values['quantity'];

        $items_data[] = [
            'name'          => $product->get_name(),
            'description'   => $product->get_short_description(),
            'sku'           => $product->get_sku(),
            'value'         => $product->get_price(),
            'quantity'      => $quantity,
            'weight'        => $product->get_weight(),
            'height'        => $product->get_height(),
            'length'        => $product->get_length(),
            'width'         => $product->get_width()
        ];

        $total_length += floatval($product->get_length());
        $total_height += floatval($product->get_height());
        $total_width += floatval($product->get_width());

        if ( $total_weight < $product->get_weight() ) :
            $total_weight = $product->get_weight();
        endif;

    endforeach;

    $data = [
        'weight' => $total_weight,
        'height' => $total_height,
        'width' => $total_width,
        'length' => $total_length,
        'origin_id' => $origin_id,
        'origin_text' => $origin_text,
        'items' => $items_data
    ];

    return $data;
}

/**
 * Get data list kurir 
 * @since   1.0.0
 * @uses    Ordv_Biteship_Checkout::ordv_biteship_get_data_services
 * @param   int     $api_d_area_id
 * @param   int     $area_id_lat
 * @param   int     $area_id_lng
 * @param   array   $data_packages
 * @return  void
 */
// id17 done 4 - get couriers rate
function ordv_biteship_fn_get_data_list_kurir( $api_d_area_id, $area_id_lat, $area_id_lng, $data_packages ){

    $instance_settings = [];
    $delivery_zones = \WC_Shipping_Zones::get_zones();
    foreach ($delivery_zones as $zone) :
        foreach( $zone['shipping_methods'] as $shipping_method ) :
            if( 'ordv-biteship' === $shipping_method->id && 'yes' === $shipping_method->enabled ) :
                $instance_settings = $shipping_method->instance_settings;
                break;
            endif;
        endforeach;
    endforeach;

    $enable_kurir = [];
    if ( isset( $instance_settings['logistic']['enabled'] ) ) :
        $enable_kurir = $instance_settings['logistic']['enabled'];
    endif;

    $order_kurir = [];
    if ( isset( $instance_settings['logistic']['order'] ) ) :
        $order_kurir = $instance_settings['logistic']['order'];
    endif;

    $clean_enable_kurir = [];
    foreach ( $enable_kurir as $key => $value ) :
        $val_arr = explode('__',$value);
        $clean_enable_kurir[] = $val_arr[0];
    endforeach;
    $courier_codes = array_unique($clean_enable_kurir);
    $courier_codes_str = implode(',',$courier_codes);

    $endpoint_kurir = '/v1/rates/couriers';
    $endpoint_url   = ordv_biteship_get_url_api().''.$endpoint_kurir;  
    $api_key        = carbon_get_theme_option('w_biteship_api_key');  

    $items = [];
    if ( isset( $data_packages['items'] ) ) :
        $items = $data_packages['items'];
    endif;

    $origin_id = 0;
    if ( isset( $data_packages['origin_id'] ) ) :
        $origin_id = $data_packages['origin_id'];
    endif;

    $body = array(
        'origin_area_id'        => $origin_id,
        'destination_area_id'   => $api_d_area_id,
        'couriers'              => $courier_codes_str,
        'items'                 => $items
    );

    $body = wp_json_encode( $body );

    $args = array(
        'headers' => array(
            'Content-Type' => 'application/json',
            'authorization' => $api_key
        ),
        'body' => $body            
    );

    $request = wp_remote_post(
        $endpoint_url,
        $args
    );
    $body     = wp_remote_retrieve_body( $request );
    $data_api = json_decode( $body );
    $data_list_kurir = $data_api->pricing;

    $list_available_kurir = [];

    foreach ( $data_list_kurir as $key => $kurir ) :

        $rate_id = $kurir->courier_code.'__'.$kurir->courier_service_code;
        
        $list_available_kurir[$rate_id] = [
            'logistic_id'   => $kurir->courier_code,
            'logistic_code' => $kurir->courier_service_code,
            'logistic_name' => $kurir->courier_name,
            'rate_id'       => $rate_id,
            'rate_name'     => $kurir->courier_name.' '.$kurir->courier_service_name.' ('.$kurir->duration.')',
            'final_price'   => $kurir->price
        ];

    endforeach;

    $new_list_available_kurir = [];
    foreach ( $order_kurir as $key => $value ) :
        if ( in_array( $value, $enable_kurir ) ) :
            if ( isset( $list_available_kurir[$value] ) ) :
                $new_list_available_kurir[] = $list_available_kurir;
            endif;
        endif;
    endforeach;

    return $new_list_available_kurir;

}

/**
 * Get detailed data my account page url endpoint data
 * @uses    Ordv_Biteship_Check_Awb::ordv_biteship_cek_resi_scripts_load
 * @since   1.0.0
 * @param   $endpoint
 * @return  void
 */
function ordv_biteship_fn_is_wc_endpoint($endpoint) {
    // Use the default WC function if the $endpoint is not provided
    if (empty($endpoint)) return is_wc_endpoint_url();
    // Query vars check
    global $wp;
    if (empty($wp->query_vars)) return false;
    $queryVars = $wp->query_vars;
    if (
        !empty($queryVars['pagename'])
        // Check if we are on the Woocommerce my-account page
        && $queryVars['pagename'] == 'my-account'
    ) {
        // Endpoint matched i.e. we are on the endpoint page
        if (isset($queryVars[$endpoint])) return true;
        // Dashboard my-account page special check - check whether the url ends with "my-account"
        if ($endpoint == 'dashboard') {
            $requestParts = explode('/', trim($wp->request, ' \/'));
            if (end($requestParts) == 'my-account') return true;
        }
    }
    return false;
}

/**
 * Get URL API demo or live
 * @since 1.0.0
 * @return void
 */
function ordv_biteship_get_url_api(){
    
    $demo_active = carbon_get_theme_option('biteship_demo');

    $api_url = NULL;

    if( $demo_active === true ){
        $api_url = 'https://api.biteship.com';
    }else{
        $api_url = 'https://api.biteship.com';
    }

    return $api_url;

}

/**
 * Get callback data from biteship
 * @since 1.0.0
 * @return mixed;
 */
function ordv_update_order_callback(){

    $data_webhook = file_get_contents("php://input");
	$events_webhook = json_decode($data_webhook, true);	
	
	// get order ID by tracking ID dan update data
	if( $events_webhook ):
        
        $tracking_id = $events_webhook['order_id'];
        $order 	= wc_get_orders( array( 'order_biteship_id' =>  $tracking_id ) );
	
		if( $order ):
			$order_id	= $order[0]->get_id();

            // $get_biteship_status_tracking	= $events_webhook['external_status']['description'];
			// $get_biteship_status_code	 	= $events_webhook['external_status']['code'];
			$get_biteship_status_tracking	= '';
			$get_biteship_status_code	 	= '';
			$get_biteship_no_resi 			= $events_webhook['courier_waybill_id'];
	
			update_post_meta( $order_id, 'status_code',  $get_biteship_status_code );
			update_post_meta( $order_id, 'status_tracking',  $get_biteship_status_tracking );

			if( ! $get_biteship_no_resi | '' == $get_biteship_no_resi):
            	update_post_meta( $order_id, 'no_resi',  $get_biteship_no_resi );
			endif;
	
			//get data after update
			$order_status_code	= get_post_meta( $order_id, 'status_code', true );	
			$current_order = wc_get_order($order_id);
	
			if( 1190 == $order_status_code || 1180 == $order_status_code || 1170 == $order_status_code || 1160 == $order_status_code ):

			$current_order->update_status('wc-in-shipping');
        	$current_order->save();

		endif;
	
		if( 2000 == $order_status_code ):

			$current_order->update_status('wc-completed');
        	$current_order->save();

		endif;

		else:
			// do nothing
		endif;

    else:
        // do nothing
    endif;
}

function orvd_admin_delivery_tracking( $post_id ){
    
    ob_start();
    include ORDV_BITESHIP_PATH.'admin/partials/order/delivery-tracking.php';
    $set_data =  ob_get_clean();

    return $set_data;

}