var personalToken = {
    index: function () {
        this.getList();
        this.initFormSubmit();
    },
    getList: function () {
        var arrColumns = [
            { data: 'row', sortable: false, className: "text-center" },
            {
                data: 'name', sortable: false,
                mRender: function (data, type, row) {
                    return data ? data : '<span class="text-muted">Token Hệ Thống (Mặc định)</span>';
                }
            },
            {
                data: 'token', sortable: false,
                mRender: function (data, type, row) {
                    return '<div class="input-group input-group-sm" style="max-width: 300px;">' +
                           '<input type="text" class="form-control form-control-sm token-val-' + row.id + '" value="' + data + '" readonly style="background-color: #f5f5f5;">' +
                           '<button class="btn btn-outline-secondary btn-sm" type="button" onclick="personalToken.copyToken(' + row.id + ')"><i class="fas fa-copy"></i></button>' +
                           '</div>';
                }
            },
            {
                className: "text-center",
                data: 'permission', sortable: false,
                mRender: function (data, type, row) {
                    var perm = data ? data : 'write';
                    if (perm === 'write') {
                        return '<span class="badge bg-success">Write</span>';
                    }
                    return '<span class="badge bg-info">Read</span>';
                }
            },
            {
                className: "text-center",
                data: 'expired_at',
                sortable: true,
                mRender: function (data, type, row) {
                    return data ? $.format.date(row.expired_at, "dd/MM/yyyy HH:mm:ss") : 'Không giới hạn';
                }
            },
            {
                className: "text-center",
                data: 'created_at',
                sortable: true,
                mRender: function (data, type, row) {
                    return $.format.date(row.created_at, "dd/MM/yyyy HH:mm:ss");
                }
            },
            {
                data: null,
                className: "text-center",
                sortable: false,
                mRender: function (data, type, row) {
                    if (!row.name) {
                        return '<span class="text-muted">Không thể xoá</span>';
                    }
                    var html = '';
                    html += '<ul class="list-unstyled hstack justify-content-center gap-1 mb-0">';
                    html += '<li><button data-params=\'{"id":' + row.id + '}\' onclick="personalToken.delete(this)" data-reload-datatable="#data-table-list" class="btn btn-sm btn-soft-danger d-flex"><i class="mdi mdi-delete-outline"></i> xoá</button></li>';
                    html += '</ul>';
                    return html;
                }
            },
        ];
        base.getDataTableBasic("#data-table-list", arrColumns)
    },
    initFormSubmit: function () {
        $("#frm-add-token").submit(function (event) {
            event.preventDefault();
            var form = $(this);
            var btnSubmit = form.find('button[type="submit"]');
            var iconButtonSubmit = btnSubmit.find("i");
            var iconButtonOriginal = iconButtonSubmit.attr("class");

            if (form.get(0).checkValidity()) {
                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    url: form.attr('action'),
                    data: form.serialize(),
                    beforeSend: function () {
                        btnSubmit.prop('disabled', true);
                        iconButtonSubmit.attr("class", "fas fa-spinner fa-spin");
                    },
                    success: function (result) {
                        if (result.error_code != 0) {
                            toastr["error"](result.message);
                            return false;
                        }

                        toastr["success"](result.message);
                        $("#data-table-list").DataTable().draw();
                        $(".bs-modal-add").modal('hide');

                        // Show the token value in the show modal
                        $("#created-token-val").val(result.data.user_token.token);
                        $(".bs-modal-show-token").modal({
                            backdrop: 'static',
                            keyboard: false
                        }).modal('show');

                        // Reset form
                        form.trigger('reset');
                    },
                    complete: function () {
                        btnSubmit.prop('disabled', false);
                        iconButtonSubmit.attr("class", iconButtonOriginal);
                    }
                });
            }
        });
    },
    delete: function (ts) {
        Swal.fire({
            title: "Thông báo",
            text: "Bạn có chắc chắn muốn xoá token này không?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#34c38f",
            cancelButtonColor: "#f46a6a",
            confirmButtonText: "Đồng ý",
            cancelButtonText: "Không đồng ý"
        }).then(function (t) {
            if (t.value) {
                base.buttonAjax(urlAjaxPersonalTokenDelete, ts,
                    function (result) {
                        if (result.error_code != 0) {
                            return toastr["error"](result.message)
                        }
                        return toastr["success"](result.message)
                    })
            }
        });
    },
    copyToken: function (id) {
        var text = $('.token-val-' + id).val();
        navigator.clipboard.writeText(text).then(function () {
            toastr["success"]('Copied token: ' + text)
        }, function (err) {
            toastr["error"]("Lỗi copy: " + err)
        });
    },
    copyCreatedToken: function () {
        var text = $('#created-token-val').val();
        navigator.clipboard.writeText(text).then(function () {
            toastr["success"]('Copied token: ' + text)
        }, function (err) {
            toastr["error"]("Lỗi copy: " + err)
        });
    }
};
