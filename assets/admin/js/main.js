jQuery(document).ready(function ($) {
    $(document).on('click', '#wpap-settings-show-sidebar', function (e) {
        $('#wpap-settings-sidebar').fadeIn(200);
    });

    $(document).on('click', '#wpap-settings-hide-sidebar', function (e) {
        $('#wpap-settings-sidebar').fadeOut(200);
    });

    $(document).on('click', '#wpap-settings-nav .wpap-menu .wpap-menu-item.wpap-has-sub > a', function(e) {
        e.preventDefault();

        const clickedItem = $(this).parent(); // li
        const submenu = clickedItem.children('ul.wpap-menu');

        if (clickedItem.hasClass('wpap-open')) {
            clickedItem.removeClass('wpap-open');
            submenu.css('display', 'block').slideUp(200);
        } else {
            clickedItem.addClass('wpap-open');
            submenu.css('display', 'none').slideDown(200);
        }

        e.stopPropagation();
    });

    $(document).on('click', '.wpap-supporter', function (e) {
        $(this).next('.wpap-popup-form-wrap').fadeIn(200);
    });

    $(document).on('click', '.wpap-popup-form-wrap', function (e) {
        $(this).fadeOut(200);
    });

    $(document).on('click', '.wpap-popup-form', function (e) {
        e.stopPropagation();
    });

    $(document).on('click', '.wpap-close-popup', function (e) {
        e.preventDefault();
        $('.wpap-popup-form-wrap').fadeOut(200);
    });

    var { ticketStatusPlaceholder, ticketDepPlaceholder, deleteText } = wpapMain;

    $(document).on('click', '#wpap-add-status', function (e) {
        e.preventDefault();

        $('#wpap-ticket-status-input-wrap').append(`
            <div class="wpap-ticket-settings-status">
                <input id="wpap-ticket-status" type="text" name="ticket_status_name[]" placeholder="${ticketStatusPlaceholder}"/>
                
                <div class="wpap-status-color-input-wrap">
                    <label>
                        <i class="ri-square-line"></i>
                        <input type="color" name="ticket_status_color[]"/>
                    </label>
                    
                    <label>
                        <i class="ri-text"></i>
                        <input type="color" name="ticket_status_text_color[]"/>
                    </label>
                </div>
                
                <a class="wpap-delete-status" href=""><i class="bx bx-trash"></i></a>
            </div>        
`       );
    });

    $(document).on('click', '#wpap-add-department', function (e) {
        e.preventDefault();

        $('#wpap-dep-input-wrap').append(`
            <div>
                <input id="wpap-ticket-department" type="text" name="departments[]" placeholder="${ticketDepPlaceholder}"/>
                <a class="wpap-delete-department" href=""><i class="ri-delete-bin-7-line"></i></i></a>
            </div>        
`       );
    });

    $(document).on('click', '.wpap-delete-department, .wpap-delete-status', function (e) {
        e.preventDefault();
        $(this).parent().remove();
    });

    $(document).on('input', '.wpap-attachment-field', function () {
        var parent = $(this).parents('.wpap-upload-wrap');
        var fileName = $(this).prop('files')[0].name;
        parent.find('.wpap-upload-preview').text(fileName);
    });

    $(document).on('change', '#wpap-sms-providers', function (e) {
        $('.wpap-sms-settings-form').not(`#wpap-${$(this).val()}`).hide(0);
        $(`#wpap-${$(this).val()}`).show(0);
    });

    $(document).on('click', '.wpap-ajax-field ul li', function () {
        var parent = $(this).parents('.wpap-ajax-field');
        parent.find('input').val($(this).attr('data-name'));
        parent.find('> div').hide();
    });
});