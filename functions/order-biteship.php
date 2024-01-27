<?php

add_action("admin_init", function(){
    if(isset($_GET['nolan'])) :
        ?><pre><?php
        print_r(ordv_biteship_fn_create_order_biteship(1357));
        ?></pre><?php
        exit;
    endif;
});

/**
 * Create order biteship by order_id
 * @uses    Ordv_Biteship_Admin::ordv_biteship_action_biteship_create_order
 * @since   1.0.0
 * @param   string  $order_id
 * @return  mixed
 */
// id17 done - need test 1 - create order
function ordv_biteship_fn_create_order_biteship( $order_id ){

    $endpoint_create_order_biteship      = '/v1/orders';
    $endpoint_url_create_order_biteship  = ordv_biteship_get_url_api().''.$endpoint_create_order_biteship;

    $api_key = carbon_get_theme_option('w_biteship_api_key');

    // get data order detail
    $order = wc_get_order( $order_id );
    $order_data = $order->get_data();

    $first_name = $order_data['billing']['first_name'];
    $last_name = $order_data['billing']['last_name'];

    $raw_phone_number = $order_data['billing']['phone'];
    $ptn = "/^0/";  // Regex
    $rpltxt = "62";  // Replacement string

    $phone_number =  preg_replace($ptn, $rpltxt, $raw_phone_number);
    $rate_id = intval( get_post_meta($order_id, 'rate_id', true) );

    // destination
    $d_address = $order_data['billing']['address_1'];
    $d_area_id = intval( get_post_meta($order_id, 'd_area_id', true) );

    $list_product = $order->get_items();

    $items = array();
    foreach( $list_product as $item_id => $item_data ){

        // get data product
        $product        = $item_data->get_product();
        $product_name   = $item_data->get_name();  // Use get_name() to get the product name
        $product_price  = $product->get_price();
        $product_qty    = $item_data->get_quantity();

        // Add product dimensions
        $height = $product->get_height();
        $length = $product->get_length();
        $weight = $product->get_weight();
        $width  = $product->get_width();

        $list = array(
            'name'        => $product_name,
            'description' => $product->get_description(), // Replace with actual description getter
            'value'       => intval($product_price),
            'quantity'    => $product_qty,
            'height'      => $height,
            'length'      => $length,
            'weight'      => $weight,
            'width'       => $width
        );

        $items[] = $list;

    }

    $term           = carbon_get_theme_option("biteship_location_term");
    $item_attribute = $item_data->get_meta("pa_" . $term);    
    //$item_term    = get_term_by( 'name',  $item_attribute, 'pa_' . $term );
    $item_term      = get_term_by( 'slug',  $item_attribute, 'pa_' . $term );

    $area_id        = get_term_meta( $item_term->term_taxonomy_id, '_origin_area_id', true);
    $area_text      = get_term_meta( $item_term->term_taxonomy_id, '_origin_area_text', true);

    $origin_lat     = get_term_meta( $item_term->term_taxonomy_id, '_biteship_courier_origin_lat', true);
    $origin_lng     = get_term_meta( $item_term->term_taxonomy_id, '_biteship_courier_origin_lng', true);
    

    // destination cordinates
    $dest_lat = strval( get_post_meta( $order_id, 'd_lat_area_id', true ));
    $dest_lng = strval( get_post_meta( $order_id, 'd_lng_area_id', true ));

    // origin
    $o_address = $area_text;
    $o_area_id = intval( $area_id );

    $height = 0;
    $length = 0;
    $weight = 0;
    $width  = 0;
    $package_type = 2;
    $price = 0; // total price package

    foreach( $list_product as $i_id => $i_data ){
        $product    = $i_data->get_product();

        $length += $product->get_length();
        $width += $product->get_width();
        $height += $product->get_height();
        $weight += $product->get_weight();
        $price += $product->get_price();

    }

    $weight = floatval( $weight );
    $weight = ( $weight / 1000 );

    $body = array(
        'shipper_contact_name'   => $order_data['billing']['first_name'],
        'shipper_contact_phone'  => $phone_number,
        'shipper_contact_email'  => $order_data['billing']['email'],
        'shipper_organization'   => $order_data['billing']['company'],
        'origin_contact_name'    => $order_data['billing']['first_name'],
        'origin_contact_phone'   => $phone_number,
        'origin_address'         => $o_address,
        'origin_note'            => $order_data['billing']['note'],
        'origin_postal_code'     => $o_area_id,
        'destination_contact_name'=> $order_data['shipping']['first_name'],
        'destination_contact_phone'=> $order_data['shipping']['phone'],
        'destination_contact_email'=> $order_data['shipping']['email'],
        'destination_address'    => $d_address,
        'destination_postal_code'=> $d_area_id,
        'destination_note'       => $order_data['shipping']['note'],
        'courier_company'        => $order_data['billing']['courier_company'],
        'courier_type'           => $order_data['billing']['courier_type'],
        'courier_insurance'      => $order_data['billing']['courier_insurance'],
        'delivery_type'          => $order_data['billing']['delivery_type'],
        'delivery_date'          => $order_data['billing']['delivery_date'],
        'delivery_time'          => $order_data['billing']['delivery_time'],
        'order_note'             => $order_data['billing']['order_note'],
        'metadata'               => $order_data['billing']['metadata'],
        'items'                  => $items,
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
        $endpoint_url_create_order_biteship,
        $args
    );

    $body       = wp_remote_retrieve_body( $request );
    $data_api   = json_decode($body);
    $data       = $data_api->data;

    return $data;

}

/**
 * Get detail order biteship data by $order_biteship_id
 * @since   1.0.0
 * @uses    admin/partials/order/order-column.php
 * @param   int     $order_biteship_id
 * @return  void
 */
// id17 done - need test 4 - get order data
function ordv_biteship_fn_get_biteship_order_data( $order_biteship_id ){

    $endpoint_get_data_biteship      = '/v1/orders/'.$order_biteship_id;
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

    $body           = wp_remote_retrieve_body( $request );
    $data_api       = json_decode($body);

    $data_tracking  = $data_api->courier->history;
    $n_data         = count($data_tracking);
    $latest_data_n  = ($n_data - 1);

    $latest_status  = $data_tracking[$latest_data_n]->note;

    $data = array(
        'awb_number'            => $data_api->courier->waybill_id,
        'biteship_order_status' => $data_api->status, // true or false
        'pickup_code'           => $data_api->ticket_status,
        'tracking_status'       => $latest_status
    );


    return $data;

}

/**
 * Get data pickup time from biteship
 * @since   1.0.0
 * @uses    admin/partials/order/time-slots.php
 * @return  mixed
 */
// id17 dev 5 - get pickup time
function ordv_biteship_fn_get_pickup_time(){

    date_default_timezone_set('Asia/Jakarta');

    $date = date('Y-m-d');
    $time = date('H:i:s');

    //$date_time_req = strval($date.'T'.$time.'+07:00');
    //$date_time_req = $date.'T'.$time.'Z';
    $date_time_zone = 'Asia/Jakarta';


    $endpoint_get_time_slot      = '/v3/pickup/timeslot';
    $endpoint_url_get_time_slot  = ordv_biteship_get_url_api().''.$endpoint_get_time_slot;

    $api_url = add_query_arg( array(
        'time_zone'     => urlencode($date_time_zone),
        //'request_time'  => urlencode($date_time_req),
    ), $endpoint_url_get_time_slot );

    $api_key = carbon_get_theme_option('w_biteship_api_key');

    $args = array(
        'headers' => array(
            'Content-Type'  => 'application/json',
            'authorization'     => $api_key
        )
    );

    $request = wp_remote_get(
        $api_url,
        $args
    );

    $body       = wp_remote_retrieve_body( $request );
    $data_api   = json_decode($body);
    $slot_time  = $data_api->data->time_slots;

    return $slot_time;
}

/**
 * Run process pickup order after picked time
 * @since   1.0.0
 * @uses    Ordv_Biteship_Admin::ordv_biteship_action_set_pickup_time
 * @param   string  $id_biteship_order
 * @param   string  $date_start
 * @param   string  date_end
 * @return  mixed
 */
// id17 dev 2 - create pickup order
function ordv_biteship_fn_do_pickup_order( $id_biteship_order, $date_start, $date_end ){

    $endpoint_do_pickup_order      = '/v3/pickup/timeslot';
    $endpoint_url_do_pickup_order  = ordv_biteship_get_url_api().''.$endpoint_do_pickup_order;

    $api_key = carbon_get_theme_option('w_biteship_api_key');

    $body = array(
        'data' => array(
            'order_activation' => array(
                'order_id'  => array(
                    $id_biteship_order
                ),
                'end_time' => $date_end,
                'start_time'=> $date_start,
                'timezone' => 'Asia/Jakarta'
            )
        )

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
        $endpoint_url_do_pickup_order,
        $args
    );

    $body       = wp_remote_retrieve_body( $request );
    $data_api   = json_decode($body);

    $data       = $data_api->data->order_activations[0];

    return $data;

}

/**
 * Get delivery status from biteship
 * @since   1.0.0
 * @uses    Ordv_Biteship_Admin::ordv_biteship_get_data_status
 * @param   int     $order_id
 * @return  void
 */
// id17 done - need test 6 - get updated status
function ordv_biteship_fn_get_updated_status( $order_id ){

    $order_biteship_id = get_post_meta($order_id, 'order_biteship_id', true);

    $endpoint_get_status      = '/v1/orders/'.$order_biteship_id;
    $endpoint_url_get_status  = ordv_biteship_get_url_api().''.$endpoint_get_status;

    $api_key = carbon_get_theme_option('w_biteship_api_key');

    $args = array(
        'headers' => array(
            'Content-Type' => 'application/json',
            'authorization' => $api_key
        )
    );

    $request = wp_remote_get(
        $endpoint_url_get_status,
        $args
    );

    $body       = wp_remote_retrieve_body( $request );
    $data_api   = json_decode($body);
    $data       = $data_api->courier->history;

    $n_data = count($data);
    $latest_data_n = ($n_data - 1);

    $latest_code = $data[$latest_data_n]->status;
    $latest_status = $data[$latest_data_n]->note;

    $arr_data = array(
        'latest_code'   => $latest_code,
        'latest_status' => $latest_status
    );

    //return $latest_status;
    return $arr_data;

}
