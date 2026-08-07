var userIdQrcode = {
    index: function () {
        this.getList();
    },
    getList: function () {
        var arrColumns = [
            { data: 'row', sortable: false },
            {
                className: "text-left",
                data: 'name',
                sortable: false,
                mRender: function (data, type, row) {
                    return row.name;
                }
            },
            {
                className: "text-left",
                data: null,
                sortable: false,
                mRender: function (data, type, row) {
                    var html = '';
                    html += 'Tên TK: ' + row.bank_account_name + '<br/>';
                    html += 'Số TK: ' + row.bank_account_number + '<br/>';
                    html += 'Mã: ' + row.code + '<br/>';
                    return html;
                }
            },
            {
                className: "text-left",
                data: null,
                sortable: false,
                mRender: function (data, type, row) {
                    var html = '<img width="115" src="' + assetUrl + row.path_qrcode + '"/>';
                    return html;
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
                className: "text-left",
                data: null,
                sortable: false,
                mRender: function (data, type, row) {
                    var html = '';
                    html += '<ul class="list-unstyled hstack d-inline-flex gap-1 mb-0">';
                    html += '<li data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Xoá"> <button  data-params=\'{"id":' + row.id + '}\' onclick="userIdQrcode.delete(this)" data-reload-datatable="#data-table-list" class="btn btn-sm btn-soft-danger d-flex"><i class="mdi mdi-delete-outline"></i> xoá</button></li>';
                    html += '</ul>';
                    return html;
                }
            },
        ];

        base.getDataTableBasic("#data-table-user-id-qrcode-list", arrColumns)
    }, delete: function (ts) {
        Swal.fire({
            title: "Thông báo",
            text: "Bạn có chắc chắn muốn xoá không?",
            icon: "warning",
            showCancelButton: !0,
            confirmButtonColor: "#34c38f",
            cancelButtonColor: "#f46a6a",
            confirmButtonText: "Đồng ý",
            cancelButtonText: "Không đồng ý"
        }).then(function (t) {
            if (t.value) {
                base.buttonAjax(urlAjaxUserIdQrcodeDelete, ts,
                    function (result) {
                        if (result.error_code != 0) {
                            return toastr["error"](result.message)
                        }
                        $("#data-table-user-id-qrcode-list").DataTable().draw();
                        return toastr["success"](result.message)
                    })
            }
        });
    }
};