var tool = {
    index: function () {
        $(".btn-create-sign").click(function () {
            tool.createSign();
        })
    },
    createSign: function () {
        btnSubmit = $(".btn-create-sign");
        var iconButtonSubmit = btnSubmit.find("i");
        var thisForm =  $(".form-create-sign");
        $.ajax({
            type: "POST",
            dataType: 'json',
            url: urlAjaxCreateSign,
            data: thisForm.serialize(),
            beforeSend: function () {
                btnSubmit.prop('disabled', true);
                iconButtonSubmit.attr("class", "fas fa-spinner fa-spin");
            },
            success: function (result) {
                if (result.error_code != 0) {
                    toastr["error"](result.message)
                    return false
                }

                toastr["success"](result.message)
                $("#sign").val(result.data.sign);
                return false;
            }, complete: function () {
                btnSubmit.prop('disabled', false);
                iconButtonSubmit.attr("class", iconButtonOriginal);
            }
        });
    }
};