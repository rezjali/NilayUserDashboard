jQuery(document).ready(function ($) {
    $('body').on('keyup', 'input[name=user_pass], input[name=password2]', function (event) {
        wdmChkPwdStrength(
            $('input[name=user_pass]'),
            $('input[name=password2]'),
            $('#wpap-password-strength'),
            $('input[type=submit], button[type=submit]'),
            ['admin', 'happy', 'hello', '1234']
        );
    });

    function wdmChkPwdStrength($pwd, $confirmPwd, $strengthStatus, $submitBtn, blacklistedWords) {
        let pwd = $pwd.val();
        let confirmPwd = $confirmPwd.val();
        blacklistedWords = blacklistedWords.concat(wp.passwordStrength.userInputDisallowedList());
        $submitBtn.attr('disabled', 'disabled');
        $strengthStatus.removeClass('wpap-pass-short wpap-pass-bad wpap-pass-good wpap-pass-strong wpap-pass-mismatch');
        let pwdStrength = wp.passwordStrength.meter(pwd, blacklistedWords, confirmPwd);

        switch (pwdStrength) {
            case 2:
                $strengthStatus.addClass('wpap-pass-bad').html(pwsL10n.bad);
                break;
            case 3:
                $strengthStatus.addClass('wpap-pass-good').html(pwsL10n.good);
                break;
            case 4:
                $strengthStatus.addClass('wpap-pass-strong').html(pwsL10n.strong);
                break;
            case 5:
                $strengthStatus.addClass('wpap-pass-mismatch').html(pwsL10n.mismatch);
                break;
            default:
                $strengthStatus.addClass('wpap-pass-short').html(pwsL10n.short);
        }

        if (4 === pwdStrength && '' !== confirmPwd.trim()) {
            $submitBtn.removeAttr('disabled');
        }

        return pwdStrength;
    }
});