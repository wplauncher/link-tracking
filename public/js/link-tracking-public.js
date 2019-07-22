(function( $ ) {
	'use strict';

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
	var LinkTracking = {
		construct:function(){
			if($('.link_tracking_link')[0]){
				$('.link_tracking_link').each(function( index ) {
					LinkTracking.impressions($( this ));
					LinkTracking.clicks($( this ));
				});
			}
		},
		clicks:function(_that){
			_that.bind('click',function(){
					// send an ajax request that tracks the click
					var post_id = $(this).data('postId');
					console.log(post_id);
					var dataString = "action=link_tracking_clicks&post_id="+encodeURIComponent(post_id)+"&security="+encodeURIComponent(link_tracking_ajax_object.click_nonce);
					console.log(link_tracking_ajax_object);
					$.ajax({
						url: link_tracking_ajax_object.ajax_url,  
						type: "POST",
							data: dataString,
							dataType:'json',
							success: function(data){
							if(data.response == 'success'){
								console.log('click tracked');
								console.log('clicks'+data.clicks);
							} else {
								console.log('click not tracked')
								
							} 
							console.log( data );
							},
						error: function(jqXHR, textStatus, errorThrown) { 
							console.log(jqXHR, textStatus, errorThrown); 
								console.log('click not tracked')
						}
					});
			});
		},
		impressions:function(_that){
			// send an ajax request that tracks the impression
			var post_id = _that.data('postId');
					console.log(post_id);
					var dataString = "action=link_tracking_impressions&post_id="+encodeURIComponent(post_id)+"&security="+encodeURIComponent(link_tracking_ajax_object.impression_nonce);
					console.log(link_tracking_ajax_object);
					$.ajax({
						url: link_tracking_ajax_object.ajax_url,  
						type: "POST",
							data: dataString,
							dataType:'json',
							success: function(data){
							if(data.response == 'success'){
								console.log('impression tracked');
								console.log('impressions'+data.impressions);
							} else {
								console.log('impression not tracked')
								
							} 
							console.log( data );
							},
						error: function(jqXHR, textStatus, errorThrown) { 
							console.log(jqXHR, textStatus, errorThrown); 
								console.log('impression not tracked')
						}
					});
		},
	};
	$(function() {
		LinkTracking.construct();
	});
	
	//
})( jQuery );
