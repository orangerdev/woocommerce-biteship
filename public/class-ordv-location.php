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
class Ordv_Biteship_Location {

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

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
     * Register Scripts & Styles in check-awb page
	 * Hooked via	action wp_enqueue_scripts
	 * @since 		1.0.0
	 * @return 		void
     */
	public function enqueue_scripts(){

		wp_enqueue_style( $this->plugin_name.'-location', ORDV_BITESHIP_URI.'public/css/ordv-biteship-location.css' );

		wp_enqueue_script( $this->plugin_name.'-location', ORDV_BITESHIP_URI.'public/js/ordv-biteship-location.js', ['jquery'], $this->version, true );
		wp_localize_script( $this->plugin_name.'-location', 'wb_loc_vars', [
			'ajax_url' => admin_url('admin-ajax.php'),
			'ajax_nonce' => [
				'get_location_store' => wp_create_nonce( 'wb_get_location_store' ) 
			]
		] );

	}

	public function display_sticky_choose_location() {

		if ( is_front_page() ) :

			include ORDV_BITESHIP_PATH.'/public/partials/choose-location.php';

		endif;

	}
	
	public function display_check_location_store() {

		include ORDV_BITESHIP_PATH.'/public/partials/check-location-store.php';

	}

	public function ajax_get_location_stores() {

		if ( wp_verify_nonce( $_GET['nonce'], 'wb_get_location_store' ) ) :

			$_request = wp_parse_args( $_GET, [
				'size' => '',
				'product_id' => '',
			] );

			$size = $_request['size'];
			$product = wc_get_product( $_request['product_id'] );

			$loc_stores_html = '';

			if ( $product->is_type('variable') ) :

				$att_terms = wc_get_product_terms($product->get_id(), 'pa_lokasi', array('fields' => 'all'));
				$loc_arr = [];
				foreach ($att_terms as $term) :
					$loc_arr[$term->slug] = $term->description;
				endforeach;

				$variation_ids = $product->get_children();
				foreach ( $variation_ids as $variation_id ) :

					$variation = wc_get_product($variation_id);
					$attributes = $variation->get_variation_attributes();

					if (isset($attributes['attribute_pa_size']) && 
						$attributes['attribute_pa_size'] === $size ) :

						$variation_stock_quantity = $variation->get_stock_quantity();			

						$loc_name = $attributes['attribute_pa_lokasi'];
						$loc_desc = '';
						if ( isset( $loc_arr[$loc_name] ) ) :
							$loc_desc = $loc_arr[$loc_name];
						endif;

						ob_start();
						?>
						<div class="wb-grid wb-location-store">
							<div class="wb-col-70 wb-location-store-name">
								<h4><?php echo $loc_name; ?></h4>
								<p><?php echo $loc_desc; ?></p>
							</div>
							<div class="wb-col-30 wb-location-store-action">
								<button data-loc="<?php echo $loc_name; ?>" class="wb-location-store-choose-btn" type="button"><?php _e('Beli Di Sini','ordv-biteship'); ?></button>
								<p><?php echo $variation_stock_quantity; ?> tersedia</p>
							</div>
						</div> 
						<?php
						$loc_stores_html .= ob_get_clean();

					endif;

				endforeach;

			endif;

			$data = [
				'loc_stores_html' => $loc_stores_html
			];
			wp_send_json( $data );

		endif;

	}

}
