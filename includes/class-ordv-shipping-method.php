<?php

class Ordv_Biteship_Shipping_Method extends \WC_Shipping_Method {
    /**
     * Constructor
     *
     * @since   1.0.0
     * @return  void
     */
    public function __construct( $instance_id = 0 ) {

        $this->id                 = 'ordv-biteship';
        $this->instance_id        = absint( $instance_id );
        $this->method_title       = __( 'Biteship', 'ordv-biteship' );
        $this->method_description = __( 'Biteship method for WooCommerce', 'ordv-biteship' );

        $this->enabled  = isset( $this->settings['enabled'] ) ? $this->settings['enabled'] : 'yes';
        $this->title    = isset( $this->settings['title'] ) ? $this->settings['title'] : __( 'Biteship', 'ordv-biteship' );
        $this->supports = array (
            "shipping-zones",
            "instance-settings",
            "instance-settings-modal"
        );

        $this->init();
    }

    /**
     * Calculate shipping based on this method
     * @since   1.0.0
     * @param   array   $package
     * @return  void
     */
    public function calculate_shipping( $package = array() ) {

        // $cost       = 0;
        // $location   = null;
        // $title      = $this->title;
        // $location_term = "pa_" . carbon_get_theme_option( "biteship_location_term" );
        // $location_product_attribute = "attribute_" . $location_term;

        // foreach( $package["contents"] as $hash => $item ) :
        //     if(
        //         array_key_exists("variation", $item) &&
        //         array_key_exists($location_product_attribute, $item["variation"])
        //     ) :
        //         $term = get_term_by( "slug", $item["variation"][$location_product_attribute], $location_term );

        //         if( is_a($term, "WP_Term")) :

                    // Later we will get this from biteship
                    // $cost = carbon_get_term_meta( $term->term_id, "biteship_courier_cost");
                    // $title .= " - Dikirim dari " . $term->name;

        //             break;
        //         endif;

        //     endif;
        // endforeach;

        $list_data_kurir = WC()->session->get('data_kurir');

        if( NULL == $list_data_kurir ){
            // show nothing
        }else{

            $i = 0;
            foreach ($list_data_kurir[0] as $detail_kurir_key => $detail_kurir){

                $this->add_rate( array(
                    "id"    => $detail_kurir['rate_id'],
                    "label" => $detail_kurir['rate_name'],
                    "cost"  => $detail_kurir['final_price']
                ));

                $i++;
            }

        }

    }

    private function init() {
		$this->init_form_fields();
	}

	public function init_form_fields() {
		$this->instance_form_fields = array(
			'title'          => array(
				'title'       => __( 'Title', 'ordv-biteship' ),
				'type'        => 'text',
				'description' => '',
				'default'     => $this->method_title,
			),
			'logistic'       => array(
				'type' => 'logistic',
			),
		);
	}

    public function generate_logistic_html() {

        $logistics_results  = ordv_biteship_get_logistics(); 
        $logistics_option   = $this->get_option( 'logistic', array() );
        $logistics_order    = $logistics_option['order'];
        $logistics_enabled  = $logistics_option['enabled'];

        $logistics = [];
        if ( $logistics_order ) :
            foreach ( $logistics_order as $key => $value ) :
                if ( isset( $logistics_results[$value] ) ) :
                    $logistics[$value] = $logistics_results[$value];
                endif;
            endforeach;
            foreach ( $logistics_results as $key => $value) :
                if ( !isset( $logistics[$key] ) ) :
                    $logistics[$key] = $value;
                endif;
            endforeach;
        else:
            $logistics = $logistics_results;
        endif;

		ob_start();
        include ORDV_BITESHIP_PATH.'admin/partials/logistic-options.php';
		return ob_get_clean();

    }

    public function validate_logistic_field( $key ) {

		$logistics = [
            'order'     => [],
            'enabled'   => []
        ];

        if ( isset( $_POST['data']['logistics_order'] ) ) :

            $logistics['order'] = $_POST['data']['logistics_order'];

        endif;
	
        if ( isset( $_POST['data']['logistics_enabled'] ) ) :

            $logistics['enabled'] = $_POST['data']['logistics_enabled'];

        endif;

		return $logistics;

	}

}

add_action( "woocommerce_shipping_init", "Ordv_Biteship_Shipping_Method");
