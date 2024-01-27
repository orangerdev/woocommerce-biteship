(function( $ ) {
	'use strict';


	$(document).ready(function(){

    	$('#billing_ordv-edit-billing-kelurahan').selectWoo({
			ajax: {
				url: edit_billing_data.ajax_url,
				type: 'GET',
				delay: 250,
				data: function (params) {
					return {
						search: params.term,
						action: edit_billing_data.edit_area.action,						
						nonce: edit_billing_data.edit_area.nonce
					};
				},
				processResults: function (data, params) {  
					return {
						results: data
					}
				},
				cache: false
			},
				minimumInputLength: 3, // only start searching when the user has input 3 or more characters				
                
		});

	});


	$(document).on('change','#billing_ordv-edit-billing-kelurahan',function(){
			
		var d = $("#billing_ordv-edit-billing-kelurahan").select2('data')[0];
		$('#billing_city').val( d['text'] );
		var e_data = d['text'].split(",");

		$('#billing_postcode').val( e_data[0] );
		$('#billing_ordv-edit-billing-lat').val( d['lat'] );
		$('#billing_ordv-edit-billing-lng').val( d['lng'] );

	});
	

	/**
	 * All of the code for your public-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

})( jQuery );
