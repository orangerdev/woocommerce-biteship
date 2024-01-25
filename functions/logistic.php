<?php

// id17 done 3 - get logistics
function ordv_biteship_get_logistics() {

    $data = [];

    $api_url = ordv_biteship_get_url_api().'/v1/couriers';
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

        $logistics = $result->couriers;

        foreach ( $logistics as $key => $logistic ) :

            $code = $logistic->courier_code.'__'.$logistic->courier_service_code;

            $data[$code] = $logistic;

        endforeach;

    endif;

    return $data;

}