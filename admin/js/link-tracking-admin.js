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
	var LinkTrackingAdmin = {
		construct:function(){
			$(function() {
				// Run initButton when the media button is clicked.
				$( '.link-tracking-media-button' ).each(function( index ) {
					LinkTrackingAdmin.initMediaButton($(this));
			});
			});
		},
		initMediaButton:function(_that){
			_that.click(function(e){
		// Instantiates the variable that holds the media library frame.
		var metaImageFrame;
				 // Get the btn
			var btn = e.target;
	
			// Check if it's the upload button
			if ( !btn || !$( 'input[name="link-tracking_url"]' ) ) return;
	
			// Get the field target
			var field = $( 'input[name="link-tracking_url"]' );
	
			// Prevents the default action from occuring.
			e.preventDefault();
	
			// Sets up the media library frame
			metaImageFrame = wp.media.frames.metaImageFrame = wp.media({
				title: meta_image.title,
				button: { text:  'Use this file' },
			});
	
			// Runs when an image is selected.
			metaImageFrame.on('select', function() {
	
				// Grabs the attachment selection and creates a JSON representation of the model.
				var media_attachment = metaImageFrame.state().get('selection').first().toJSON();
	
				// Sends the attachment URL to our custom image input field.
				$( field ).val(media_attachment.url);
	
			});
	
			// Opens the media library frame.
			metaImageFrame.open();
			});
	},
		getQueryVariable:function(variableName) {
			var query = window.location.search.substring(1);
			var vars = query.split("&");
			for (var i=0;i<vars.length;i++) {
							var pair = vars[i].split("=");
							if(pair[0] == variableName){return pair[1];}
			}
			return(false);
		}
	}
	LinkTrackingAdmin.construct();
})( jQuery );
