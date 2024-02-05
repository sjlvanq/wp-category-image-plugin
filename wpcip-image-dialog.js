/*
 * Attaches the image uploader to the input field
 */
jQuery(document).ready(function($){
 
    // Instantiates the variable that holds the media library frame.
    var wpcip_image_frame;
 
    // Runs when the image button is clicked.
    $('#wpcip-image-button').click(function(e){

        // Prevents the default action from occuring.
        e.preventDefault();
 
        // If the frame already exists, re-open it.
        if ( wpcip_image_frame ) {
            wpcip_image_frame.open();
            return;
        }
 
        // Sets up the media library frame
        wpcip_image_frame = wp.media.frames.media_image_frame = wp.media({
            title: meta_image.title,
            button: { text:  meta_image.button },
            library: { type: 'image' }
        });
 
        // Runs when an image is selected.
        wpcip_image_frame.on('select', function(){

            // Grabs the attachment selection and creates a JSON representation of the model.
            var media_attachment = wpcip_image_frame.state().get('selection').first().toJSON();
			// Sends attachment id to hidden field and src to img preview
			$('#wpcip_image_attachment_id').val(media_attachment.id);
        });
 
        // Opens the media library frame.
        wpcip_image_frame.open();
    });
});
