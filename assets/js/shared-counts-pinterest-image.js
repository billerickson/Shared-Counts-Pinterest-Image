;(function($) {

	var media_modal = false;

	/**
	 * Bind upload/select image button.
	 */
	$( document ).on( 'click', '.shared-counts-pinterest-image-setting button', function( event ) {
		event.preventDefault();
		imageUploadModal( $( this ) );
		toggleLinks( $( this ) );
	});

	$( document ).on( 'click', '.shared-counts-pinterest-image-setting a', function( event ) {
		event.preventDefault();
		removeImage( $( this ) );
		toggleLinks( $( this ) );
	});


	/**
	 * Image upload modal window.
	 */
	function imageUploadModal( el ) {

		 if ( media_modal ) {
			 media_modal.open();
			 return;
		 }

		 var $setting = $( el ).closest( '.shared-counts-pinterest-image-setting' );

		 media_modal = wp.media.frames.shared_counts_pinterest_media_frame = wp.media({
			 className: 'media-frame shared-counts-pinterest-media-frame',
			 frame: 'select',
			 multiple: false,
			 title: 'Upload or Choose Your Image',
			 library: {
				 type: 'image'
			 },
			 button: {
				 text: 'Use Image'
			 }
		 });

		 media_modal.on( 'select', function(){
			 // Grab our attachment selection and construct a JSON representation of the model.
			 var media_attachment = media_modal.state().get( 'selection' ).first().toJSON();

			 // Send the attachment URL to our custom input field via jQuery.
			 $setting.find( 'input.sc-pinterest-image' ).val( media_attachment.url );
			 $setting.find( 'img' ).remove();
			 $setting.prepend( '<img src="'+media_attachment.url+'" style="max-width: 100%; height: auto;">' );
		 });

		 // Now that everything has been set, let's open up the frame.
		 media_modal.open();
	};

	/**
	 * Remove Image
	 */
	function removeImage( el ) {
		var $setting = $( el ).closest( '.shared-counts-pinterest-image-setting' );
		 $setting.find( 'input.sc-pinterest-image' ).val( '' );
		 $setting.find( 'img' ).remove();
	}

	/**
	 * Toggle links
	 */
	function toggleLinks( el ) {
		var $setting = $( el ).closest( '.shared-counts-pinterest-image-setting' );
		$setting.find( 'button' ).toggle();
		$setting.find( 'a' ).toggle();
	};

})( jQuery );
