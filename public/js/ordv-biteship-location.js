(function( $ ) {
	'use strict';

    function wbSetCookie(name, value) {
        document.cookie = name + "=" + value + "; path=/";
    }

    function wbGetCookie(name) {
        var cookieArray = document.cookie.split(';');
        
        for (var i = 0; i < cookieArray.length; i++) {
            var cookie = cookieArray[i].trim();
            
            if (cookie.indexOf(name + "=") === 0) {
                return cookie.substring(name.length + 1);
            }
        }
    
        return null;
    }

    // $(document).ready(function(){
    //     $("#pa_lokasi").closest("tr").hide();
    // });

	$(document).on('change','#wb-choose-location',function(e){
		e.preventDefault();
		
        var val = $(this).val();

        wbSetCookie('wb_loc',val);

        // var wb_loc = wbGetCookie('wb_loc');
        // console.log('wb_loc: '+wb_loc);
	});

    $(document).on('click','.wb-check-location-store-open-btn',function(e){
		e.preventDefault();
		
        $('.wb-check-location-store-popup').show();

        var size = $('#pa_size').val();
        var product_id = $('#wb_product_id').val();

        $.ajax({
            url: wb_loc_vars.ajax_url+'?action=wb_get_location_store',
            type: 'get',
            data: {
                size: size,
                nonce: wb_loc_vars.ajax_nonce.get_location_store,
                product_id: product_id
            },
            beforeSend: function(){
                $('.wb-popup-content-loading').html('<p>Sedang mengambil data toko ...</p>');
                $('.wb-popup-content-loading').show();
                $('.wb-location-stores').hide();
                $('.wb-location-stores').html('');
            },
            success: function(response){
                console.log(response);

                $('.wb-popup-content-loading').hide();
                $('.wb-location-stores').show();
                var loc_stores_html = response.loc_stores_html;

                if ( loc_stores_html ) {
                    $('.wb-location-stores').html(loc_stores_html);    
                } else {
                    $('.wb-location-stores').hide();
                    $('.wb-popup-content-loading').show();
                    $('.wb-popup-content-loading').html('<p>Tidak ada toko ditemukan untuk size tersebut, silahkan pilih size lainnya</p>');
                }

            }
        });

	});

    $(document).on('click','.wb-check-location-store-close-btn',function(e){
		e.preventDefault();
		
        $('.wb-check-location-store-popup').hide();
	});

    $(document).on('click','.wb-location-store-choose-btn',function(e){
		e.preventDefault();
		
        var loc = $(this).attr('data-loc');
        $('#pa_lokasi').val(loc);
        $('#pa_lokasi').trigger("change");

        $('.wb-check-location-store-popup').hide();
	});

    // $(document).on('change','#pa_size',function(e){
	// 	e.preventDefault();
		
    //     var val = $(this).val();

    //     var loc = wbGetCookie('wb_loc');
    //     $('#pa_lokasi').val(loc);
    //     $('#pa_lokasi').trigger("change");

	// });

})( jQuery );
