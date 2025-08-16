const $ = jQuery;

export function wpapFloatMessage(text, type = 'info', dismissible = true) {
    $('.wpap-float-msg').remove();

    let icon;
    switch (type) {
        case 'error':
            icon = 'ri-close-circle-line';
            break;
        case 'success':
            icon = 'ri-checkbox-circle-line';
            break;
        default:
            icon = 'ri-information-line';
    }

    let singleMessageHTML = (`
        <div class="wpap-float-msg wpap-${type}-msg">
            <i class="${icon}"></i>
            <span class="wpap-msg-text">${text}</span>
            <i class="ri-close-large-line wpap-dismiss-msg"></i>
        </div>
    `);

    let multiMessageHTML = (`
        <div class="wpap-float-msg wpap-${type}-msg">
            <i class="${icon}"></i>
            <ul></ul>
            <i class="ri-close-large-line wpap-dismiss-msg"></i>
        </div>
    `);

    $(document)
        .find('body')
        .prepend(
            Array.isArray(text) ? multiMessageHTML : singleMessageHTML
        );

    if (Array.isArray(text)) {
        for (let i of text) {
            $('.wpap-float-msg ul').append(`<li>${i}</li>`);
        }
    }

    var msg = $('.wpap-float-msg');
    msg.hide(0).fadeIn(400);

    setTimeout(function () {
        msg.fadeOut(400);
    }, 10000)
}

export function wpapLoading(enable = true) {
    if (enable) {
        $('.wpap-btn-text').hide(0);
        $('.wpap-loading').show(0);
    } else {
        $('.wpap-loading').hide(0);
        $('.wpap-btn-text').show(0);
    }
}
