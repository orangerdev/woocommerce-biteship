<?php

use Carbon_Fields\Container;
use Carbon_Fields\Field;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://ridwan-arifandi.com
 * @since      1.0.0
 *
 * @package    Ordv_Biteship
 * @subpackage Ordv_Biteship/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ordv_Biteship
 * @subpackage Ordv_Biteship/admin
 * @author     Ridwan Arifandi <orangerdigiart@gmail.com>
 */
class Ordv_Biteship_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Enqueue scripts
	 * Hooked via	action admin_enqueue_scripts, priority 10
	 * @since 		1.0.0
	 * @return 		void
	 */
	public function ordv_biteship_enqueue_scripts() {

		$screen = get_current_screen();

		if ( $screen->base === 'term' ) :
			wp_enqueue_script( $this->plugin_name.'-blockui', ORDV_BITESHIP_URI.'admin/js/jquery.blockUI.js', ['jquery'], $this->version, true );
			wp_enqueue_script( $this->plugin_name.'-select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js', ['jquery'], $this->version, true );
			wp_enqueue_script( $this->plugin_name, ORDV_BITESHIP_URI.'admin/js/ordv-biteship-admin.js', ['jquery',$this->plugin_name.'-select2'], $this->version, true );
			wp_localize_script( $this->plugin_name, 'osa_vars',[
				'get_locations_nonce' => wp_create_nonce('get-locations-by-ajax' )
			] );
		endif;

		if( 'edit' === $screen->base && 'shop_order' === $screen->post_type  ):

			wp_enqueue_script( $this->plugin_name, ORDV_BITESHIP_URI.'admin/js/ordv-biteship-order.js', array( 'jquery', 'jquery-ui-dialog' ), $this->version, true );

			$settings = array(
                'ajax_url'  => admin_url( 'admin-ajax.php' ),
                'update_status'      => [
                    'action'    => 'get_data_status',
                    'nonce'     => wp_create_nonce( 'ajax-nonce' )
				],
				'get_time_slots'	=> [
					'action'	=> 'get_time_slots',
					'nonce'		=> wp_create_nonce( 'ajax-nonce' )
				],
				'create_order' => [
					'action'	=> 'biteship_create_order',
					'nonce'		=> wp_create_nonce( 'ajax-nonce' )
				],
				'set_pickup_time' => [
					'action'	=> 'set_pickup_time',
					'nonce'		=> wp_create_nonce( 'ajax-nonce' )
				]
            );

            wp_localize_script( $this->plugin_name, 'oso_vars', $settings);

		endif;


	}

	/**
	 * Enqueue styles
	 * Hooked via	action admin_enqueue_scripts, priority 10
	 * @since		1.0.0
	 * @return		void
	 */
	public function ordv_biteship_enqueue_styles() {

		$screen = get_current_screen();

		if ( $screen->base === 'term' ) :

			wp_enqueue_style( $this->plugin_name.'-select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css' );
			wp_enqueue_style( $this->plugin_name, ORDV_BITESHIP_URI.'admin/css/ordv-biteship-admin.css' );

		endif;

		if ( 'post' === $screen->base && 'shop_order' === $screen->id ):			
			wp_enqueue_style( $this->plugin_name.'admin-base-table', ORDV_BITESHIP_URI.'admin/css/ordv-biteship-admin-base-table.css' );			
			wp_enqueue_style( $this->plugin_name, ORDV_BITESHIP_URI.'admin/css/bulma-timeline.min.css' );
			wp_enqueue_style( $this->plugin_name.'admin-check-awb', ORDV_BITESHIP_URI.'admin/css/ordv-biteship-admin-check-awb.css' );
		endif;

	}

	/**
	 * Load carbon field library
	 * @uses	add_action after_setup_theme priority 10
	 * @since	1.0.0
	 * @return 	void
	 */
	public function ordv_biteship_load_carbon_fields() {

		\Carbon_Fields\Carbon_Fields::boot();

	}

	/**
	 * Get attribute term options
	 * @since 	1.0.0
	 * @return 	array
	 */
	public function get_location_term_options() {

		$options = array();

		foreach( wc_get_attribute_taxonomies() as $id => $taxo ) :
			$options[$taxo->attribute_name] = $taxo->attribute_label;
		endforeach;

		return $options;
	}

	public function get_default_location_options() {

		$options = array();

		$taxonomy = 'pa_lokasi';
		$terms = get_terms($taxonomy);
		foreach ($terms as $key => $term) :
			$options[$term->slug] = $term->name;
		endforeach;

		return $options;

	}

	/**
	 * Add plugin options
	 * Hooked via	action carbon_fields_register_fields, priority 10
	 * @since 		1.0.0
	 * @return 		void
	 */
	public function ordv_biteship_add_plugin_options() {

		Container::make( "theme_options", __("Biteship", "ordv-biteship"))
			->add_fields([
				Field::make( "checkbox", "biteship_demo", __("Demo Site", "ordv-biteship"))
					->set_help_text( __("If activated, it will use static cost field, not from biteship.com", "ordv-biteship")),

				Field::make( "select",	 "biteship_location_term", __("Produk Attribute", "ordv-biteship"))
					->add_options(array($this, "get_location_term_options"))
					->set_help_text( __("Select product attribute that defines location", "ordv-biteship")),

				Field::make( "text", "w_biteship_api_key", __("API Key", "ordv-biteship"))
					->set_default_value( 'biteship_live.eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJuYW1lIjoid29vYml0ZXNoaXAiLCJ1c2VySWQiOiI2NTlmNDY1MWY3YTg5YjVkNDk5ZWVjZTEiLCJpYXQiOjE3MDQ5MzcyMDd9.KCbW_sc6A4_oPefE-aislszILy1I76PT-vuO6It_PUc' ),

				Field::make( "select",	 "biteship_location_default", __("Default Location", "ordv-biteship"))
					->add_options(array($this, "get_default_location_options"))
					->set_help_text( __("First, please set the Product Attributes above and save, then select the default location", "ordv-biteship")),
			]);

	}

	/**
	 * Add location options based on product attribute selected on plugin options
	 * Hooekd via	action carbon_fields_register_fields, priority 10
	 * @since 		1.0.0
	 * @return 		void
	 */
	public function ordv_biteship_add_location_options() {

		$biteship_courier_origin_area = '<h3>Origin - Area</h3>';
		$biteship_courier_origin_area .= '<select class="origin-area" name="origin_area">';

		$area_text = '';
		if ( isset( $_GET['tag_ID'] ) ) :
			$area_id   = get_term_meta( $_GET['tag_ID'], '_origin_area_id', true);
			$area_text = get_term_meta( $_GET['tag_ID'], '_origin_area_text', true);
			if ( $area_id && $area_text ) :
				$biteship_courier_origin_area .= '<option value="'.$area_id.'" selected>'.$area_text.'</option>';
			endif;
		endif;

		$biteship_courier_origin_area .= '</select>';
		$biteship_courier_origin_area .= '<input type="hidden" id="origin_area_text" name="origin_area_text" value="'.$area_text.'">';

		Container::make( "term_meta", __("Location Setup", "ordv-biteship"))
			->where( "term_taxonomy", "=", "pa_" . carbon_get_theme_option("biteship_location_term") )
			->add_fields([
				Field::make( "html", "biteship_courier_origin_area", __("Origin - Area", "ordv-biteship"))
					->set_html( $biteship_courier_origin_area ),
				Field::make( "text", "biteship_courier_origin_lat", __("Origin - Lat", "ordv-biteship")),
				Field::make( "text", "biteship_courier_origin_lng", __("Origin - Lng", "ordv-biteship")),
				Field::make( "text", "biteship_courier_cost", __("Courier Cost", "ordv-biteship"))
					->set_attribute( "type", "number")
					->set_default_value(0)
					->set_help_text( __("Only used if demo site is activated in plugin options", "ordv-biteship"))
			]);

	}

	/**
	 * Add custom shipping method
	 * Hooked via	filter	woocommerce_shipping_methods
	 * @since 		1.0.0
	 * @param  		array 	$methods
	 * @return 		array
	 */
	public function ordv_biteship_modify_shipping_methods( $methods ) {

		require_once( plugin_dir_path( dirname( __FILE__ )) . "includes/class-ordv-shipping-method.php");

		$methods["ordv-biteship"] = "Ordv_Biteship_Shipping_Method";

		return $methods;
	}

	/**
	 * get_locations_by_ajax
	 * hooked via	action	wp_ajax_get-locations, priority 10
	 * @return		json
	 */
	public function ordv_biteship_get_locations_by_ajax() {

		if ( isset( $_GET['nonce'] ) &&
			wp_verify_nonce($_GET['nonce'],'get-locations-by-ajax' ) ) :

			$data = [];

			$_request = wp_parse_args($_GET,[
				'search' => '',
			] );

			if ( $_request['search'] ) :

				$locations = ordv_biteship_get_locations( $_request['search'] );

				foreach ( $locations as $key => $location ) :

					if ( isset( $location->id ) ) :

						$data[] = [
							'id' 	=> $location->id,
							'text' 	=> $location->name,
						];

					endif;

				endforeach;

			endif;

			wp_send_json( $data );

		endif;

	}

	/**
	 * save custom term meta
	 * hooked via	action carbon_fields_term_meta_container_saved, priority 10
	 * @return		void
	 */
	public function ordv_biteship_save_custom_term_meta_area() {

		if ( isset( $_POST['origin_area'] ) ) :

			update_term_meta( $_POST['tag_ID'],'_origin_area_id', $_POST['origin_area'] );
			update_term_meta( $_POST['tag_ID'],'_origin_area_text', $_POST['origin_area_text'] );

		endif;

	}

	/**
	 * Add additional column in list order in woocommerce > orders ( Admin dashboard )
	 * Hooked via	filter manage_edit-shop_order_columns 
	 * @since		1.0.0
	 * @param		$columns
	 * @return		void
	 */
	public function ordv_biteship_custom_shop_order_column($columns)
	{
		$reordered_columns = array();

		// Inserting columns to a specific location
		foreach( $columns as $key => $column){
			$reordered_columns[$key] = $column;
			if( $key ==  'wc_actions' ){
				// Inserting after "Status" column
				$reordered_columns['biteship'] = __( 'Biteship','plugin_domain');
			}
		}
		return $reordered_columns;
	}

	/**
	 * Show new column in the end of order table in woocommerce > orders
	 * hooked via	action manage_shop_order_posts_custom_column, priority 20
	 * @since		1.0.0
	 * @param		$column
	 * @param		$post_id
	 * @return		void
	 */
	public function ordv_biteship_custom_orders_list_column_content( $column, $post_id )
	{
		switch ( $column )
		{
			case 'biteship' :

				ob_start();
				include ORDV_BITESHIP_PATH.'admin/partials/order/order-column.php';
				echo ob_get_clean();

				break;
		}
	}

	/**
	 * Create order biteship.com
	 * Hooked via	action wp_ajax_biteship_create_order
	 * @since		1.0.0
	 * @return		void
	 */
	public function ordv_biteship_action_biteship_create_order(){

		if ( ! wp_verify_nonce( $_POST['nonce'], 'ajax-nonce' ) ) {
            die( 'Close The Door!');
        }
		$order_id = $_POST['i'];

		$data_order_biteship = ordv_biteship_fn_create_order_biteship( $order_id );

		$order = wc_get_order( $order_id );
		$order_biteship_id = $data_order_biteship->order_id;

		// save to meta data order id
		update_post_meta( $order_id, 'order_biteship_id', $order_biteship_id );
		update_post_meta( $order_id, 'is_activate', 0 );

		// If order is "processing" update status to "waiting for delivery"
		if( $order->has_status( 'processing' ) ) {
			$order->update_status('wc-waiting-delivery');
			$order->save();
		}

		//$result = 'ok';
		$result = array(
			'success'	=> true,
			'data'		=> 'ok'
		);

		wp_send_json( $result );
		wp_die();


	}


	/**
	 * Show modal pickup time option
	 * Hooked via	filter admin_footer-edit.php
	 * @since		1.0.0
	 * @return		void
	 */
	public function ordv_biteship_set_pickup_time_form(){

		$currentScreen = get_current_screen();
		if( 'woocommerce' === $currentScreen->parent_base && 'shop_order' === $currentScreen->post_type  ){
			?>

				<div id="my-content-id-x" title="Pilih Waktu Penjemputan" style="display:none">
					<div id="div-inside"></div>
				</div>

			<?php

		}else{
			// do nothing
		}

	}

	/**
	 * Set pickup time after choose "date & time" options
	 * Hooked via	action wp_ajax_set_pickup_time
	 * @since		1.0.0
	 * @return		void
	 */
	public function ordv_biteship_action_set_pickup_time(){

		if(isset($_POST['data']))
		{
			parse_str($_POST['data'], $data);

			$order_id = $data['order_id'];
			$data_time = $data['pickup_time'];

			if( $data_time ){

				$id_biteship_order = get_post_meta($order_id, 'order_biteship_id', true);

				$data = explode("|" , $data_time );
				$date_start = $data[0];
				$date_end	= $data[1];

				$get_pickup_data = ordv_biteship_fn_do_pickup_order( $id_biteship_order, $date_start, $date_end );

				// save pickup data
				update_post_meta( $order_id, 'pickup_code', $get_pickup_data->pickup_code );
				update_post_meta( $order_id, 'is_activate', $get_pickup_data->is_activate );
				update_post_meta( $order_id, 'pickup_time', $get_pickup_data->pickup_time );
				

			}else{

				// do nothing

			}

		}else{

			// do nothing

		}

		$result = array(
			'success' => true,
			'message' => 'ok'
		);

		wp_send_json( $result );
		wp_die();

	}

	/**
	 * Get Latest biteship order status
	 * Hooked via	action wp_ajax_get_data_status
	 * @since		1.0.0
	 * @return		void
	 */
	public function ordv_biteship_get_data_status(){

		if ( ! wp_verify_nonce( $_POST['nonce'], 'ajax-nonce' ) ) {
            die( 'Close The Door!');
        }

		$order_id = $_POST['o'];
		$data_status = ordv_biteship_fn_get_updated_status( $order_id );

		$data_order_code = $data_status['latest_code'];
		$data_order_status = $data_status['latest_status'];

		update_post_meta( $order_id, 'status_code', $data_order_code );
		update_post_meta( $order_id, 'status_tracking', $data_order_status );

		$order = wc_get_order( $order_id );

		if( 1190 === $data_order_code || 1180 === $data_order_code || 1170 === $data_order_code || 1160 === $data_order_code ){

			$order->update_status('wc-in-shipping');
        	$order->save();

		}

		if( 2000 === $data_order_code ){

			$order->update_status('wc-completed');
        	$order->save();

		}

		$arr_data = array(
			'order_code' => $data_order_code,
			'order_status' => $data_order_status
		);

		wp_send_json( $arr_data );
        wp_die();

	}

	/**
	 * Get list of time slot for picking order 
	 * Hooked via	action wp_ajax_get_time_slots
	 * @since		1.0.0 
	 * @return		void
	 */
	public function ordv_biteship_get_time_slots(){

		if ( ! wp_verify_nonce( $_POST['nonce'], 'ajax-nonce' ) ) {
            die( 'Close The Door!');
        }

		$order_id = $_POST['o_id'];

		ob_start();
		include ORDV_BITESHIP_PATH.'admin/partials/order/time-slots.php';
		echo ob_get_clean();

		wp_die();

	}

	/**
	 * Add woocommerce custom order status : waiting for delivery & in shipping
	 * Hooked via	action init
	 * @since		1.0.0
	 * @return		void
	 */
	public function ordv_biteship_register_custom_shipping_status(){
		register_post_status(
			'wc-waiting-delivery',
			array(
				'label'		=> 'Waiting for delivery',
				'public'	=> true,
				'show_in_admin_status_list' => true,
				'show_in_admin_all_list'    => true,
				'exclude_from_search'       => false,
				'label_count'	=> _n_noop( 'Waiting for delivery (%s)', 'Waiting for delivery (%s)' )
			)
		);

		register_post_status(
			'wc-in-shipping',
			array(
				'label'		=> 'In Shipping',
				'public'	=> true,
				'show_in_admin_status_list' => true,
				'show_in_admin_all_list'    => true,
				'exclude_from_search'       => false,
				'label_count'	=> _n_noop( 'In Shipping (%s)', 'In Shipping (%s)' )
			)
		);

	}

	/**
	 * Add status "waiting delivery" & "in shipping" in label above order table
	 * Hooked via	filter wc_order_statuses
	 * @since		1.0.0
	 * @param		$order_statuses
	 * @return		void
	 */
	public function ordv_biteship_biteship_add_status_to_list( $order_statuses ){

		$new = array();

		foreach ( $order_statuses as $id => $label ) {

			if ( 'wc-completed' === $id ) { // before "Completed" status
				$new[ 'wc-waiting-delivery' ]	= 'Waiting for delivery';
				$new[ 'wc-in-shipping' ] 		= 'In Shipping';
			}

			$new[ $id ] = $label;

		}

		return $new;

	}

	/**
	 * Add detail tracking in order detail page dashboard
	 * Hooked via	action carbon_fields_register_fields, priority 30
	 * @since 		1.0.0
	 * @return 		void
	 */
	public function ordv_biteship_add_tracking_order_detail(){

		
		if( $_REQUEST ):

			if( isset( $_REQUEST['post'] ) && $_REQUEST['action'] ):

				$post_id 		= ( $_REQUEST['post'] ) ? $_REQUEST['post'] : 0;
				$post_action	= ( $_REQUEST['action'] ) ? $_REQUEST['action'] : '';

				if( 0 !== $post_id && 'edit' == $post_action ):

					$set_data = orvd_admin_delivery_tracking( $post_id );

					Container::make( 'post_meta', __('Delivery Status', 'ordv-biteship'))		
						->where( 'post_type', '=', 'shop_order' )
						->set_priority( 'low' )
						->add_fields( array(
							Field::make( 'html', 'crb_information_text' )
							->set_html( $set_data )
						)
					);

				else:
					// do nothing
				endif;

			else:
				// do nothing
			endif;			
		
		else:
			// do nothing		
		endif;

	}

}
