jQuery(document).ready(function ($) {
    let googleUser = {};
    ({ajaxUrl, adminUrl, panelUrl, gLoginClientID} = googleLogin);

    gapi.load('auth2', function() {
        auth2 = gapi.auth2.init({
            client_id: gLoginClientID,
            cookiepolicy: 'single_host_origin',
        });

        document.querySelectorAll('.wpap-g-login-btn').forEach(i => {
            auth2.attachClickHandler(i, {}, function(googleUser) {
                let user = googleUser.getBasicProfile();
                $('.wpap-login-loading-msg').css('display', 'flex');

                $.ajax({
                    type: 'post',
                    url: ajaxUrl,
                    data : {
                        action: 'google_login',
                        email: user.getEmail(),
                        first_name: user.getGivenName(),
                        last_name: user.getFamilyName(),
                        display_name: user.getName(),
                        image_url: user.getImageUrl()
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            $('.wpap-login-loading-msg').remove();
                            $('.wpap-g-login-success').css('display', 'flex');

                            if (response.action === 'login' && response.cap) {
                                window.location.href = `${adminUrl}`;
                            } else {
                                window.location.href = `${panelUrl}`;
                            }
                        }
                    },
                    error: function () {}
                })
            },
            function(error) {
                // alert(JSON.stringify(error, undefined, 2));
            });
        })
    });
})
