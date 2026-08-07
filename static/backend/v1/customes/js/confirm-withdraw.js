var confirmWithDraw = {
    index: function () {
        $(".frm-confirm-withdraw .btn-cancel").click(function () {
            confirmWithDraw.cancel();
        })

        $(".frm-confirm-withdraw .btn-confirm").click(function () {
            confirmWithDraw.confirm();
        })


    },
    cancel: function () {
        btnSubmit = $(".frm-confirm-withdraw .btn-cancel");
        thisForm = $(".frm-confirm-withdraw")
        var iconButtonSubmit = btnSubmit.find("i");
        Swal.fire({
            title: "Thông báo",
            text: "Bạn có chắc muốn huỷ giao dịch? Vui lòng nhập lý do bên dưới",
            input: "text",
            icon: "warning",
            showCancelButton: !0,
            confirmButtonColor: "#34c38f",
            cancelButtonColor: "#f46a6a",
            confirmButtonText: "Đồng ý",
            cancelButtonText: "Không đồng ý"
        }).then(function (t) {
            console.log(t);
            if (t.isConfirmed) {
                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    url: urlAjaxCancelWithDraw,
                    data: thisForm.serialize() + "&partner_transaction_cancel_reason=" + t.value,
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
                        if (typeof result?.data?.url_redirect != 'undefined') {
                            setTimeout(() => {
                                window.location = result.data.url_redirect;
                            }, 1000);
                            return false;
                        }

                        if (urlRedirect) {
                            setTimeout(() => {
                                window.location = urlRedirect;
                            }, 1000);
                        }
                        return false;
                    }, complete: function () {
                        btnSubmit.prop('disabled', false);
                        iconButtonSubmit.attr("class", iconButtonOriginal);
                    }
                });
            }
        });
    },
    confirm: function () {
        thisForm = $(".frm-confirm-withdraw")
        btnSubmit = $(".frm-confirm-withdraw .btn-confirm");
        var iconButtonSubmit = btnSubmit.find("i");
        Swal.fire({
            title: "Thông báo",
            text: "Bạn có chắc muốn xác nhận giao dịch?",
            icon: "warning",
            showCancelButton: !0,
            confirmButtonColor: "#34c38f",
            cancelButtonColor: "#f46a6a",
            confirmButtonText: "Đồng ý",
            cancelButtonText: "Không đồng ý"
        }).then(function (t) {
            if (t.value) {
                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    url: urlAjaxConfirmWithDraw,
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

                        if (typeof result?.data?.url_redirect != 'undefined') {
                            setTimeout(() => {
                                window.location = result.data.url_redirect;
                            }, 1000);
                            return false;
                        }

                        if (urlRedirect) {
                            setTimeout(() => {
                                window.location = urlRedirect;
                            }, 1000);
                        }
                        return false;
                    }, complete: function () {
                        btnSubmit.prop('disabled', false);
                        iconButtonSubmit.attr("class", iconButtonOriginal);
                    }
                });
            }
        });
    }
};