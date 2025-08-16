jQuery(document).ready(function ($) {
    $('input.wpap-media-manager').click(function (e) {
        e.preventDefault();

        let image_frame;
        let preview_img = $(this).parent().prev();
        let banner_id_input = $(this).prev();

        if (image_frame) {
            image_frame.open();
        }

        // Define image_frame as wp.media object
        image_frame = wp.media({
            library: {
                type: 'image',
            }
        });

        image_frame.on('close', function () {
            // On close, get selections and save to the hidden input
            // plus other AJAX stuff to refresh the image preview
            let selection = image_frame.state().get('selection');
            let gallery_ids = [];
            let my_index = 0;
            selection.each(function (attachment) {
                gallery_ids[my_index] = attachment['id'];
                my_index++;
            });
            let ids = gallery_ids.join(",");
            banner_id_input.val(ids);
            Refresh_Image(ids, preview_img);
        });

        image_frame.on('open', function () {
            // On open, get the id from the hidden input
            // and select the appropiate images in the media manager
            let selection = image_frame.state().get('selection');
            let ids = banner_id_input.val().split(',');
            ids.forEach(function (id) {
                let attachment = wp.media.attachment(id);
                attachment.fetch();
                selection.add(attachment ? [attachment] : []);
            });
        });

        image_frame.open();
    });

    let uploader;

    $(document).on('click', '#wpap-upload-logo-btn', function (e) {
        if (uploader) {
            uploader.open();
            return;
        }

        uploader = wp.media({
            library: {
                type: 'image',
            }
        });

        uploader.on('select', function () {
            const logo = $('#wpap-logo');
            const logoPreview = $('#wpap-logo-preview');
            let attachment = uploader.state().get('selection').first().toJSON();

            if (logoPreview.length > 0) {
                logoPreview.remove();
            }

            logo.val(attachment.url);
            logo.after(`<img id="wpap-logo-preview" src="${attachment.url}" height="100"/>`);
            $('#wpap-delete-logo-btn').show(0);
        });

        uploader.open();
    })

    $(document).on('click', '#wpap-delete-logo-btn', function (e) {
        $('#wpap-logo').val('');
        $('#wpap-logo-preview').remove();
        $(this).hide(0);
    });
});

// Ajax request to refresh the image preview
function Refresh_Image(the_id, preview_img) {
    let data = {
        action: 'wpap_get_pre_banner',
        id: the_id
    };

    jQuery.get(ajaxurl, data, function (response) {
        if (response.success === true) {
            preview_img.replaceWith(response.data.image);
        }
    });
}