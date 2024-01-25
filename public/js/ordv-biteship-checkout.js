(function( $ ) {
	'use strict';


	$(document).ready(function(){

		$('#billing_postcode').val('');

		var selected_option = $('#ordv-area option:selected').val();
		
		if( selected_option ){

			var get_text = $('#ordv-area option:selected').text();
			var s_data = get_text.split(',');
			
			$('#billing_postcode').val(s_data[0]);
			//var a = $("#ordv-area").val();

			var a = $('#ordv-area option:selected').val();

			$.ajax({
				type: 'POST',
				url: checkout_ajax.ajax_url,
				data:{
					'a' : a,		
					'nonce' : checkout_ajax.get_services_first_time.nonce,
					'action' : checkout_ajax.get_services_first_time.action
				},
				dataType: 'json',
				success: function (data) {
					$('body').trigger('update_checkout');
				}
			});

		}else{

			$.ajax({
				type: 'POST',
				url: checkout_ajax.ajax_url,
				data:{
					'a' : a,		
					'nonce' : checkout_ajax.get_no_select_value.nonce,
					'action' : checkout_ajax.get_no_select_value.action
				},
				dataType: 'json',
				success: function (data) {
					$('body').trigger('update_checkout');
				}
			});

		}

		$('#ordv-area').selectWoo({

			ajax: {
				url: checkout_ajax.ajax_url,
				type: 'GET',
				delay: 250,
				data: function (params) {
					return {
						search: params.term,
						nonce: checkout_ajax.area.nonce,
						action: checkout_ajax.area.action
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

	$(document).on('change','#ordv-area',function(){
			
		var a = $("#ordv-area").val();
		var d = $("#ordv-area").select2('data')[0];

		$('#billing_postcode').val( d['postcode'] );		
		$('#billing_city').val( d['text'] );

		$.ajax({
			type: 'POST',
			url: checkout_ajax.ajax_url,
			data:{
				'a' : a,
				'a_lat' : d['lat'],
				'a_lng' : d['lng'],
				'nonce' : checkout_ajax.get_services.nonce,
				'action' : checkout_ajax.get_services.action
			},
			dataType: 'json',
			success: function (data) {
				$('body').trigger('update_checkout');
			}
		});

	});

	$(document).on('change','.shipping_method',function(){
		var k = $('input[name="shipping_method[0]"]:checked').val();
		
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
