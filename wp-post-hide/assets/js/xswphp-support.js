jQuery(document).ready(function ($) {
    "use strict";
    $('#xswphp_name , #xswphp_email , #xswphp_message').on('change', function (e) {
        if (!$(this).val()) {
            $(this).addClass("error");
        } else {
            $(this).removeClass("error");
        }
    });
    $('.xswphp_support_form').on('submit', function (e) {
        e.preventDefault();
        $('.xs-send-email-notice').hide();
        $('.xswphp-mail-spinner').addClass('xswphp_is_active');
        $('#xswphp_name').removeClass("error");
        $('#xswphp_email').removeClass("error");
        $('#xswphp_message').removeClass("error");
        $.ajax({
            url: ajaxurl,
            type: 'post',
            data: { 'action': 'xswphp_send_mail', 'nonce': xswphp.nonce, 'data': $(this).serialize() },
            beforeSend: function () {
                if (!$('#xswphp_name').val()) {
                    $('#xswphp_name').addClass("error");
                    $('.xs-send-email-notice').removeClass('notice-success');
                    $('.xs-send-email-notice').addClass('notice');
                    $('.xs-send-email-notice').addClass('error');
                    $('.xs-send-email-notice').addClass('is-dismissible');
                    $('.xs-send-email-notice p').html('Please fill all the fields');
                    $('.xs-send-email-notice').show();
                    window.scrollTo(0, 0);
                    $('.xswphp-mail-spinner').removeClass('xswphp_is_active');
                    return false;
                }
                if (!$('#xswphp_email').val()) {
                    $('#xswphp_email').addClass("error");
                    $('.xs-send-email-notice').removeClass('notice-success');
                    $('.xs-send-email-notice').addClass('notice');
                    $('.xs-send-email-notice').addClass('error');
                    $('.xs-send-email-notice').addClass('is-dismissible');
                    $('.xs-send-email-notice p').html('Please fill all the fields');
                    $('.xs-send-email-notice').show();
                    window.scrollTo(0, 0);
                    $('.xswphp-mail-spinner').removeClass('xswphp_is_active');
                    return false;
                }
                if (!$('#xswphp_message').val()) {
                    $('#xswphp_message').addClass("error");
                    $('.xs-send-email-notice').removeClass('notice-success');
                    $('.xs-send-email-notice').addClass('notice');
                    $('.xs-send-email-notice').addClass('error');
                    $('.xs-send-email-notice').addClass('is-dismissible');
                    $('.xs-send-email-notice p').html('Please fill all the fields');
                    $('.xs-send-email-notice').show();
                    window.scrollTo(0, 0);
                    $('.xswphp-mail-spinner').removeClass('xswphp_is_active');
                    return false;
                }
                $(".xswphp_support_form :input").prop("disabled", true);
                $("#xswphp_message").prop("disabled", true);
                $('.xswphp-send-mail').prop('disabled', true);
            },
            success: function (res) {
                $('.xs-send-email-notice').find('.xs-notice-dismiss').show();
                $('.xswphp-send-mail').prop('disabled', false);
                $(".xswphp_support_form :input").prop("disabled", false);
                $("#xswphp_message").prop("disabled", false);
                if (res.status == true) {
                    $('.xs-send-email-notice').removeClass('error');
                    $('.xs-send-email-notice').addClass('notice');
                    $('.xs-send-email-notice').addClass('notice-success');
                    $('.xs-send-email-notice').addClass('is-dismissible');
                    $('.xs-send-email-notice p').html('Successfully sent');
                    $('.xs-send-email-notice').show();
                    $('.xswphp_support_form')[0].reset();
                } else {
                    $('.xs-send-email-notice').removeClass('notice-success');
                    $('.xs-send-email-notice').addClass('notice');
                    $('.xs-send-email-notice').addClass('error');
                    $('.xs-send-email-notice').addClass('is-dismissible');
                    $('.xs-send-email-notice p').html('Sent Failed');
                    $('.xs-send-email-notice').show();
                }
                $('.xswphp-mail-spinner').removeClass('xswphp_is_active');
            }

        });
    });
    $('.xs-notice-dismiss,.notice-dismiss').on('click', function (e) {
        e.preventDefault();
        $(this).parent().hide();
        $(this).hide();
    });
});