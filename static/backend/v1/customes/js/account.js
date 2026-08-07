var profileInfo = null;
var account = {
    index: function () {
        this.getListSignLog();
        this.getProfile();
        this.settingSecret();
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
                $(".api-token").html(result.data.user_token.token);
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