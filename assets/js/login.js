/**
 * @copyright This code from "DoLogin Security" WordPress plugin
 * https://wordpress.org/plugins/dologin
 * */

document.addEventListener('DOMContentLoaded', function () {
    jQuery(document).ready(function ($) {
        var teligro_can_submit_user = '';
        var teligro_can_submit_bypass = false;

        function teligro_login_tfa(e) {
            var teligro_user_handler = '#user_login';
            if ($(this).find('#username').length) {
                teligro_user_handler = '#username';
            }

            if (teligro_can_submit_user && teligro_can_submit_user == $(teligro_user_handler).val()) {
                return true;
            }

            if (teligro_can_submit_bypass) {
                return true;
            }

            e.preventDefault();

            $('#teligro-login-process').show();
            $('#teligro-process-msg').attr('class', 'teligro-spinner').html('');

            // Append the submit button for 2nd time submission
            var submit_btn = $(this).find('[type=submit]').first();
            if (!$(this).find('[type="hidden"][name="' + submit_btn.attr('name') + '"]').length) {
                $(this).append('<input type="hidden" name="' + submit_btn.attr('name') + '" value="' + submit_btn.val() + '" />');
            }

            var that = this;

            $.ajax({
                url: teligro_login.login_url,
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function (res) {
                    if (!res._res) {
                        $('#teligro-process-msg').attr('class', 'teligro-err').html(res._msg);
                        $('#teligro-two_factor_code').attr('required', false);
                        $('#teligro-dynamic_code').hide();
                    } else {
                        // If no phone set in profile
                        if ('bypassed' in res) {
                            teligro_can_submit_bypass = true;
                            $(that).submit();
                            return;
                        }
                        $('#teligro-process-msg').attr('class', 'teligro-success').html(res.info);
                        $('#teligro-dynamic_code').show();
                        $('#teligro-two_factor_code').attr('required', true);
                        teligro_can_submit_user = $(teligro_user_handler).val();
                    }
                }
            });
        }

        if ($('#loginform').length > 0)
            $('#loginform').submit(teligro_login_tfa);

        if ($('.woocommerce-form-login').length > 0)
            $('.woocommerce-form-login').submit(teligro_login_tfa);

        // $('.tml-login form[name="loginform"], .tml-login form[name="login"], #wpmem_login form, form#ihc_login_form').submit( teligro_login_tfa );
    });
});