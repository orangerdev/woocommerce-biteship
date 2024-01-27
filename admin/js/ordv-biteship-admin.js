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

		$('.origin-area').select2({
			width: '100%',
			minimumInputLength: 3,
			placeholder: 'Input Location Keyword [Min: 3 character]',
			ajax: {
				url: ajaxurl,
				type: 'get',
				delay: 250,
				data: function (params) {
					return {
						search: params.term,
						nonce: osa_vars.get_locations_nonce,
						action: 'get-locations'
					};
				},
				processResults: function (data, params) {			
					//console.log(data);
					return {
						results: data
					}
				},
				cache: true
			},
		});
	});

	$(document).on('change','.origin-area',function(){

		$('#origin_area_text').val( $( ".origin-area option:selected" ).text() );

	});

	$(document).on('click','.open-dialog',function(){
		alert('open');
	});

})( jQuery );
