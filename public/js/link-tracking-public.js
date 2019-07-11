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
			});
		},
		impressions:function(_that){
			// send an ajax request that tracks the impression
		},
	};
	$(function() {
		LinkTracking.construct();
	});
	
	//
})( jQuery );
