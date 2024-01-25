<?php

/**
 * Get detail data tracking from biteship
 * @since   1.0.0
 * @uses    Ordv_Biteship_Check_Awb::ordv_biteship_cek_resi_data
 * @uses    Ordv_Biteship_Check_Awb::ordv_biteship_get_resi_detail
 * @uses    Ordv_Biteship_View_Order::ordv_biteship_add_delivery_details
 * @param   int $order_biteship_id
 * @return  mixed
 */
// id17 done - need test 3 - get tracking order
function ordv_biteship_fn_detail_data_tracking( $order_biteship_id ){
    $endpoint_get_data_biteship      = 'v1/orders/'.$order_biteship_id;
    $endpoint_url_get_data_biteship  = ordv_biteship_get_url_api().''.$endpoint_get_data_biteship;

    $api_key = carbon_get_theme_option('w_biteship_api_key');

    $args = array(
        'headers' => array(
            'Content-Type' => 'application/json',
            'authorization' => $api_key
        )           
    );

    $request = wp_remote_get( 
        $endpoint_url_get_data_biteship,
        $args
    );

    $body       = wp_remote_retrieve_body( $request );
    $data_api   = json_decode($body);
    $data       = $data_api;

    return $data;

}

/**
 * Update order status when reload view order page
 * @uses    Ordv_Biteship_View_Order::ordv_biteship_add_delivery_details
 * @since   1.0.0
 * @param   int     $order_id
 * @param   mixed   $detail_data
 * @return  mixed
 */
function ordv_biteship_fn_update_data_tracking( $order_id, $detail_data){

    $order              = wc_get_order( $order_id );
    $no_resi            = $detail_data->awb_number;

    $data_tracking      = $detail_data->trackings;
    $n_data             = count($data_tracking);
    $latest_data_n      = ($n_data - 1);

    $pickup_code        = $detail_data->pickup_code;

    $latest_code        = $data_tracking[$latest_data_n]->biteship_status->code;
    $latest_status      = $data_tracking[$latest_data_n]->biteship_status->description;


    update_post_meta( $order_id, 'no_resi',         $no_resi );
    update_post_meta( $order_id, 'pickup_code',     $pickup_code );
    update_post_meta( $order_id, 'status_code',     $latest_code );
    update_post_meta( $order_id, 'status_tracking', $latest_status );

    if( 1190 === $latest_code || 1180 === $latest_code || 1170 === $latest_code || 1160 === $latest_code ){

        $order->update_status('wc-in-shipping');
        $order->save();
    }

    if( 2000 === $latest_code ){

        $order->update_status('wc-completed');
        $order->save();
    }

    

}