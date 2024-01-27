<?php

// id17 done 2 - get locations
function ordv_biteship_get_locations( $search = '' ) {

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