(function( $ ) {
	'use strict';



    $(document).on('submit','#check-resi',function(e){
        e.preventDefault();

        $('#notices-area').removeClass();
        $('#notices-area').html('');
        $('#hasil-data').html('');
		$('#detail-paket').html('');

        var data = $(this).serialize();

        $.ajax({
            type: 'POST',
            url: cek_resi_ajax.ajax_url,
            data:{
                'data' : data,
                'action' : cek_resi_ajax.cek_resi.action
            },
            dataType: 'json',
			beforeSend: function() {
				$('#hasil-data').html('<span style="color:red;">Checking data...</span>');
			},
            success: function (data) {
				$('#hasil-data').html('');
                $('#notices-area').addClass(data.class).html(data.notice);
                if(data.status == 1){
                    $('#hasil-data').html(data.content);
                }else{
                    // do nothing
                }
            }
        });

    });

	$(document).on('click','.detail-resi',function(e){
		e.preventDefault();
		
		$('#detail-paket').html('');
		var order_id = $(this).attr('data_order_id');

		$.ajax({
			type: 'POST',
			url: cek_resi_ajax.ajax_url,
			data: {
				'i' : order_id,
				'action' : cek_resi_ajax.get_detail.action,
				'nonce' : cek_resi_ajax.get_detail.nonce

			},
			dataType: 'json',
			beforeSend: function() {
				$('#detail-paket').html('<span style="color:red;">Loading data...</span>');
			},
			success: function (data) {
				$('#detail-paket').html('');
				if(data.status == 1){
                    $('#detail-paket').html(data.content);
                }else{
                    // do nothing
                }
			}
		});
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
