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
class Ordv_Biteship_Edit_Address_Billing {

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
     * Add custom css & js in edit-address/billing
     * Hooked via   action wp_enqueue_scripts
     * @since       1.0.0
     * @return      void
     */
    public function ordv_biteship_load_additonal_styles_scripts(){

        global $wp;
        $current_url    = home_url(add_query_arg(array(),$wp->request));

        $current_lang_id = get_locale();

        if( 'en_US' === $current_lang_id ):
            $billing   = home_url('/my-account/edit-address/billing');
        endif;
        
        if( 'id_ID' === $current_lang_id ):
            $billing   = home_url('/my-account/edit-address/penagihan');
        endif;

        if( is_wc_endpoint_url('edit-address') && $current_url === $billing ){
            
            wp_enqueue_script( $this->plugin_name, plugin_dir_url( __DIR__ ). 'public/js/ordv-biteship-edit-address-billing.js', ['jquery', 'selectWoo'], $this->version, true );
            $settings = array(
                'ajax_url'  => admin_url( 'admin-ajax.php' ),               
                'edit_area'      => [
                    'action'    => 'get_edit_data_area',
                    'nonce'     => wp_create_nonce( 'ajax-nonce' )
                ]
            );

            wp_localize_script( $this->plugin_name, 'edit_billing_data', $settings);            
            
            wp_enqueue_style( $this->plugin_name, plugin_dir_url( __DIR__ ). 'public/css/ordv-biteship-edit-address-billing.css' );
        }
    }


    /**
     * Add "kelurahan" field in edit-address/billing
     * Hooked via   filter woocommerce_default_address_fields
     * @since       1.0.0
     * @param       $fields
     * @return      void
     */
    public function ordv_biteship_edit_billing_add_field( $fields ){

        global $wp;
        $current_url    = home_url(add_query_arg(array(),$wp->request));

        $current_lang_id = get_locale();

        if( 'en_US' === $current_lang_id ):
            $billing   = home_url('/my-account/edit-address/billing');
        endif;
        
        if( 'id_ID' === $current_lang_id ):
            $billing   = home_url('/my-account/edit-address/penagihan');
        endif;

        if( is_wc_endpoint_url('edit-address') && $current_url === $billing ){

            $user_id = get_current_user_id();

            $user_order_area_id = get_user_meta( $user_id, 'user_order_area_id', true );
            $user_order_area_text = get_user_meta( $user_id, 'user_order_area_text', true );
            $user_order_area_lat = get_user_meta( $user_id, 'user_order_area_lat', true );
            $user_order_area_lng = get_user_meta( $user_id, 'user_order_area_lng', true );

                
            if( $user_order_area_id && $user_order_area_text ){

                $fields['ordv-edit-billing-kelurahan'] = array(
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

                $fields['ordv-edit-billing-lat'] = array(
                    'type'      => 'text',
                    'label'     => __('Lat', 'woocommerce'),
                    'placeholder'   => _x('lat', 'placeholder', 'woocommerce'),
                    'required'  => true,
                    'class'     => array('form-row-wide'),
                    'clear'     => true,
                    'priority'  => 83,
                    'default'   => $user_order_area_lat
                ); 
    
                $fields['ordv-edit-billing-lng'] = array(
                    'type'      => 'text',
                    'label'     => __('Lng', 'woocommerce'),
                    'placeholder'   => _x('lng', 'placeholder', 'woocommerce'),
                    'required'  => true,
                    'class'     => array('form-row-wide'),
                    'clear'     => true,
                    'priority'  => 84,
                    'default'   => $user_order_area_lng
                ); 



            }else{

                $fields['ordv-edit-billing-kelurahan'] = array(
                    'type'      => 'select',
                    'label'     => __('Kelurahan / Desa', 'woocommerce'),
                    'placeholder'   => _x('Pilih Kelurahan / Desa...', 'placeholder', 'woocommerce'),
                    'required'  => true,
                    'class'     => array('form-row-wide'),
                    'clear'     => true,
                    'options'   => array( '' => '' ),
                    'priority'  => 82
                ); 

                $fields['ordv-edit-billing-lat'] = array(
                    'type'      => 'text',
                    'label'     => __('Lat', 'woocommerce'),
                    'placeholder'   => _x('lat', 'placeholder', 'woocommerce'),
                    'required'  => true,
                    'class'     => array('form-row-wide'),
                    'clear'     => true,
                    'priority'  => 83
                ); 
    
                $fields['ordv-edit-billing-lng'] = array(
                    'type'      => 'text',
                    'label'     => __('Lng', 'woocommerce'),
                    'placeholder'   => _x('lng', 'placeholder', 'woocommerce'),
                    'required'  => true,
                    'class'     => array('form-row-wide'),
                    'clear'     => true,
                    'priority'  => 84
                ); 

            }

        }

        return $fields;

    }


    /**
     * Get list data area for "kelurahan" dropdown option in edit-address/billing
     * Hooked via   action wp_ajax_get_edit_data_area
     * @since       1.0.0
     * @return      mixed
     */
    public function ordv_biteship_get_edit_data_area(){

        if ( wp_verify_nonce( $_GET['nonce'], 'ajax-nonce' ) ) {


            $data = array();
            $keyword = $_GET['search'];
            
            if( $keyword ){

                $get_data_area = ordv_biteship_fn_get_list_area( $keyword );

                foreach ($get_data_area as $key => $area) {
                    
                    if ( isset( $area->adm_level_cur->id ) ) :

						$data[] = [
                            'id' 	=> $area->adm_level_cur->id,
                            'text' 	=> $area->display_txt,
                            'lat' => $area->adm_level_cur->geo_coord->lat,
                            'lng' => $area->adm_level_cur->geo_coord->lng,
                            'postcode' => $area->adm_level_cur->postcode
						];
                        

					endif;
                }
            }


            WC()->session->__unset( 'data_kurir');
            wp_send_json( $data );

        }

    }

    /**
     * Save area_id & area_text from edit-address/billing
     * Hooked via   action woocommerce_customer_save_address
     * @since       1.0.0
     * @param       $user_id
     * @return      void
     */
    
    public function ordv_biteship_save_custom_billing_field_data( $user_id ){

        $new_area_id = $_POST['billing_ordv-edit-billing-kelurahan'];
        $new_area_text = $_POST['billing_city'];

        $new_area_lat = $_POST['billing_ordv-edit-billing-lat'];
        $new_area_lng = $_POST['billing_ordv-edit-billing-lng'];

        update_user_meta( $user_id, 'user_order_area_id', $new_area_id );
        update_user_meta( $user_id, 'user_order_area_text', $new_area_text  );
        update_user_meta( $user_id, 'user_order_area_lat', $new_area_lat );
        update_user_meta( $user_id, 'user_order_area_lng', $new_area_lng );

    }


}
