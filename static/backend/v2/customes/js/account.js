var profileInfo = null;
var account = {
    index: function () {
        this.getListSignLog();
        this.getProfile();
        this.settingSecret();
        this.bindApiToken();
    },
    maskApiToken: function (token) {
        if (!token) return '--';
        var t = String(token);
        if (t.length <= 10) return t.replace(/.(?=.{2})/g, '•');
        return t.substr(0, 6) + '••••••••••••' + t.substr(-4);
    },
    bindApiToken: function () {
        var self = this;
        $(document).off('click.apiToken').on('click.apiToken', '.api-token-toggle', function () {
            var box = $(this).closest('.api-token-box');
            var token = box.attr('data-token') || '';
            var display = box.find('.api-token-display');
            var icon = $(this).find('i');
            if (box.attr('data-revealed') === '1') {
                display.text(self.maskApiToken(token));
                icon.removeClass('mdi-eye-off-outline').addClass('mdi-eye-outline');
                box.attr('data-revealed', '0');
            } else {
                display.text(token || '--');
                icon.removeClass('mdi-eye-outline').addClass('mdi-eye-off-outline');
                box.attr('data-revealed', '1');
            }
        });

        $(document).on('click.apiTokenCopy', '.api-token-copy', function () {
            var box = $(this).closest('.api-token-box');
            var token = box.attr('data-token') || '';
            if (!token) {
                toastr["warning"]("Chưa có token để sao chép");
                return;
            }
            var btn = $(this);
            var icon = btn.find('i');
            var done = function () {
                icon.removeClass('mdi-content-copy').addClass('mdi-check');
                btn.addClass('is-copied');
                toastr["success"]("Đã sao chép API token");
                setTimeout(function () {
                    icon.removeClass('mdi-check').addClass('mdi-content-copy');
                    btn.removeClass('is-copied');
                }, 1400);
            };
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(token).then(done, function () {
                    self.fallbackCopy(token, done);
                });
            } else {
                self.fallbackCopy(token, done);
            }
        });
    },
    fallbackCopy: function (text, onSuccess) {
        var ta = document.createElement('textarea');
        ta.value = text;
        ta.style.position = 'fixed';
        ta.style.left = '-9999px';
        document.body.appendChild(ta);
        ta.select();
        try {
            document.execCommand('copy');
            if (typeof onSuccess === 'function') onSuccess();
        } catch (e) {
            toastr["error"]("Không thể sao chép");
        }
        document.body.removeChild(ta);
    }, settingSecret: function () {
        $("#SwitchAuthy2factor").click(function () {
            if ($(this).is(":checked")) {
                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    contentType: "application/json; charset=utf-8",
                    url: strUrlAjaxAccountGetAuthy2factor,
                    data: {},
                    beforeSend: function () {
                    },
                    success: function (result) {
                        if (result.error_code != 0) {
                            return toastr["error"](result.message)
                        }

                        $(".bs-modal-authy-2factor .image-qrcode").html('<img src="data:image/png;base64,' + result.data.authy_2factor.qrcode_base64 + '"/>');
                        $(".bs-modal-authy-2factor .auth-code").html(result.data.authy_2factor.secret_key);
                        $(".bs-modal-authy-2factor input.secret_key").val(result.data.authy_2factor.secret_key);
                        $(".bs-modal-authy-2factor").modal("show");

                    }, complete: function (result) {

                    }
                });

            } else {
                $(".bs-modal-cancel-authy-2factor").modal("show");
            }
        });

        $("#SwitchOtpWithdraw").click(function () {
            if ($(this).is(":checked")) {
                $(".bs-modal-otp-withdraw").modal("show");
            }
        });

    },
    getListSignLog: function () {
        var arrColumns = [
            { data: 'row', sortable: false },
            { data: 'user_ip', sortable: false },
            { data: 'user_agent', sortable: false },
            {
                className: isMobile == 1 ? "text-left" : "text-center",
                data: 'created_at',
                sortable: true,
                mRender: function (data, type, row) {
                    return $.format.date(row.created_at, "dd/MM/yyyy HH:mm:ss");
                }
            },
            { data: 'id', sortable: true },
        ];
        base.getDataTableBasic("#data-table-list", arrColumns)
    },
    getProfile: function () {
        $.ajax({
            type: "POST",
            dataType: 'json',
            contentType: "application/json; charset=utf-8",
            url: strUrlAjaxAccountGetInfo,
            data: {},
            beforeSend: function () {
            },
            success: function (result) {
                if (result.error_code != 0) {
                    return toastr["error"](result.message)
                }
                profileInfo = result.data;
                $(".account-avatar").attr('src', result.data.user.image_avatar);
                $(".account-cover").attr('src', result.data.user.image_cover);
                $(".account-fullname").html(result.data.user.fullname);
                $(".account-email").html(result.data.user.email);
                $(".account-phone").html(result.data.user.phone);
                $(".account-address").html(result.data.user.address);
                $(".account-address").html(result.data.user.address);
                var apiToken = result.data.user_token.token || '';
                $(".api-token-box")
                    .attr('data-token', apiToken)
                    .attr('data-revealed', '0')
                    .find('.api-token-display').text(account.maskApiToken(apiToken));
                $(".api-token-box .api-token-toggle i")
                    .removeClass('mdi-eye-off-outline').addClass('mdi-eye-outline');
                $(".api-webhook-payout-url").html(result.data.user_token.webhook_payout_url);
                $(".api-webhook-url").html(result.data.user_token.webhook_url);
                $(".api-public-key textarea").html(result.data.user_token.system_public_key);
                $("input[name='webhook_url']").val(result.data.user_token.webhook_url);
                $("input[name='webhook_payout_url']").val(result.data.user_token.webhook_payout_url);


                $.each(result.data.user, function (n, i) {
                    $(".form-account-update-profile input[name='" + n + "']").val(i);
                })

            }, complete: function (result) {

            }
        });

    }
};