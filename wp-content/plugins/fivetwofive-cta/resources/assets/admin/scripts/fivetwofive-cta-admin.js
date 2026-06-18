(function ($) {
    const colorPickerInit = function() {
        const colorPickerFields = $('.js-ftf-cta-colorpicker');
        if (colorPickerFields.length >= 1) {
            colorPickerFields.iris({
                palettes: false,
                hide: false,
            });
        }
    }

    const mediaUploadInit = function() {
        const mediaUploadSections = $('.js-ftf-media-uploader-section');
        let mediaUploader;

        if ( ! mediaUploadSections.length ) {
            return;
        }

        mediaUploadSections.each(function() {
            const setMediaBtn = $(this).find('.js-ftf-media-uploader-set-trigger');
            const deleteMediaBtn = $(this).find('.js-ftf-media-uploader-delete-trigger');
            const imgContainer = $(this).find('.js-ftf-media-uploader-preview');
            const imgIdInput = $(this).find('.js-ftf-media-uploader-field');

            setMediaBtn.on('click', function(e) {
                e.preventDefault();

                if ( mediaUploader ) {
                    mediaUploader.open();
                    return;
                }

                mediaUploader = wp.media.frames.file_frame = wp.media({
                    title: 'Choose media',
                    button: {
                        text: 'Choose picture',
                    },
                    multiple: false
                });

                // When an image is selected in the media frame...
                mediaUploader.on( 'select', function() {

                    // Get media attachment details from the frame state
                    const attachment = mediaUploader.state().get('selection').first().toJSON();

                    // Send the attachment URL to our custom image input field.
                    imgContainer.html( '' ).append( '<img src="'+attachment.url+'" alt="" style="width:100%;max-width:300px;"/>' );

                    imgIdInput.val( attachment.id );
                });

                // Finally, open the modal on click
                mediaUploader.open();
            });

            deleteMediaBtn.on('click', function(e) {
                e.preventDefault();

                imgContainer.html( '' );

                imgIdInput.val( '' );
            });
        });
    }

	const adminTabsInit = function() {
		$("#ftfCtaTabs").tabs({
			beforeActivate: function( event, ui ) {
				ui.oldTab.find('.nav-tab').removeClass('nav-tab-active');
				ui.newTab.find('.nav-tab').addClass('nav-tab-active');
			}
		});
	}

    $(function(){
        colorPickerInit();
        mediaUploadInit();
		adminTabsInit();
    });
})(jQuery);