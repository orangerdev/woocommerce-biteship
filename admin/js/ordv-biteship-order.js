(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
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

	$(document).ready(function(){
		
	});

	$(document).on('click','.open-dialog',function(e){
		e.preventDefault();
		var order_id = $(this).attr('data_order_id');

		$("#my-content-id-x").dialog({
			width: "480px",
			maxWidth: "480px",
			closeText : '',
			open: function(event, ui) {
				
				$.ajax({
					type: 'POST',
					url: oso_vars.ajax_url,
					data:{
						'o_id' : order_id,
						'action': oso_vars.get_time_slots.action,
						'nonce': oso_vars.get_time_slots.nonce
					},
					//dataType: 'json',
					beforeSend: function() {
						// setting a timeout							
						$('#div-inside').html('<span style="color:red;">sedang memuat data...</span>');
					},
					success: function (data) {
						$('#div-inside').html(data);
					}
				});
				
			}
		});

	});
	
	$(document).on('click','.update-order-status',function(e){

		e.preventDefault();
		var order_id = $(this).attr('data_order_id');

		$.ajax({
			type: 'POST',
			url: oso_vars.ajax_url,
			data:{
				'o' : order_id,
				'action' : oso_vars.update_status.action,
				'nonce' : oso_vars.update_status.nonce,
			},
			dataType: 'json',
			beforeSend: function() {				
				$('tr#post-'+order_id).block({ message: null });
			},
			success: function (data) {
				if( data.order_code == 1170 || data.order_code == 1180 || data.order_code == 1190 || data.order_code == 2000 ){
					$('tr#post-'+order_id).load(' tr#post-'+order_id+' > *' );
				}else{					
					$('tr#post-'+order_id).load(' tr#post-'+order_id+' > *' );
				}                    
			}
		});

	});

	$(document).on('click','.btn-create-order-biteship', function(e){

		e.preventDefault();
		var order_id = $(this).attr('data_order_id');

		
		
		$.ajax({
			type: 'POST',
			url: oso_vars.ajax_url,
			data: {
				'i' : order_id,
				'action': oso_vars.create_order.action,
				'nonce': oso_vars.create_order.nonce
			},
			dataType: 'json',
			beforeSend: function() {					
				$('tr#post-'+order_id).block({ message: null });
			},
			success: function (data) {
				$('tr#post-'+order_id).load(' tr#post-'+order_id+' > *' );
			}
		});

	});

	$(document).on('submit','#set-pickup-time',function(e){
		e.preventDefault();

		var data = $(this).serialize();
		var order_id = $('#order_id').val();

		$.ajax({
			type: 'POST',
			url: oso_vars.ajax_url,
			data:{
				'data' : data,
				'action' : oso_vars.set_pickup_time.action,
				'nonce' : oso_vars.set_pickup_time.nonce
			},
			dataType: 'json',
			beforeSend: function() {	
				$("#my-content-id-x" ).dialog( "close" );				
				$('tr#post-'+order_id).block({ message: null });
			},
			success: function (data) {				
				$('tr#post-'+order_id).load(' tr#post-'+order_id+' > *' );
			}
		});
	
	});


})( jQuery );
