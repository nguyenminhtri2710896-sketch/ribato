var profileInfo = null;
var account = {
    index: function () {
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
                $(".account-avatar").attr('src', result.data.sub_user.image_avatar);
                $(".account-cover").attr('src', result.data.sub_user.image_cover);
                $(".account-fullname").html(result.data.sub_user.fullname);
                $(".account-email").html(result.data.sub_user.email);
                $(".account-phone").html(result.data.sub_user.phone);
                $(".account-address").html(result.data.sub_user.address);
                $(".account-address").html(result.data.sub_user.address);

                $.each(result.data.sub_user, function (n, i) {
                    $(".form-account-update-profile input[name='" + n + "']").val(i);
                })

            }, complete: function (result) {

            }
        });

    }
};