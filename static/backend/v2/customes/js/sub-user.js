var subUser = {
    index: function () {
        this.getList();
    },
    getList: function () {
        var arrColumns = [
            { data: 'row', sortable: false },
            {
                data: 'fullname', sortable: false,
                mRender: function (data, type, row) {
                    return data;
                }
            },
            { data: 'phone', sortable: false },
            { data: 'email', sortable: false },
            {
                className: "text-center-desktop",
                data: 'actived', sortable: false,
                mRender: function (data, type, row) {
                    if (row.actived == 1) {
                        return '<span class="bg-success badge">kích hoạt</span>'
                    }
                    return '<span class="bg-danger badge">ngừng kích hoạt</span>'
                }
            },
            {
                className: "text-center-desktop",
                data: 'created_at',
                sortable: true,
                mRender: function (data, type, row) {
                    return $.format.date(row.created_at, "dd/MM/yyyy HH:mm:ss");
                }
            },
            {
                className: "text-center-desktop",
                data: 'updated_at',
                sortable: true,
                mRender: function (data, type, row) {
                    return $.format.date(row.updated_at, "dd/MM/yyyy HH:mm:ss");
                }
            },
            {
                data: null,
                className: "text-center-desktop",
                sortable: false,
                mRender: function (data, type, row) {
                    html = '';
                    html += '<ul class="list-unstyled hstack d-inline-flex gap-1 mb-0">';
                    html += '<li data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Sửa"><button  data-params=\'{"sub_user_id":' + row.id + '}\' onclick="subUser.getDetail(this)" class="btn btn-sm btn-soft-info  d-flex"  data-reload-datatable="#data-table-list"><i class="mdi mdi-pencil-outline"></i> sửa</button></li>';
                    html += '<li data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Xoá"> <button  data-params=\'{"sub_user_id":' + row.id + '}\' onclick="subUser.delete(this)" data-reload-datatable="#data-table-list" class="btn btn-sm btn-soft-danger d-flex"><i class="mdi mdi-delete-outline"></i> xoá</button></li>';
                    html += '</ul>';
                    return html;
                }
            },
        ];
        base.getDataTableBasic("#data-table-list", arrColumns)
    }, getDetail: function (ts) {
        base.buttonAjax(urlAjaxSubUserDetail, ts,
            function (result) {
                if (result.error_code != 0) {
                    return toastr["error"](result.message)
                }

                var modal = $(".bs-modal-update");
                $.each(result.data.sub_user, function (k, i) {
                    if (k == "id") {
                        modal.find('[name="sub_user_id"]').val(i).change();
                    } else {
                        modal.find('[name="' + k + '"]').val(i).change();
                    }
                });

                modal.modal("show", { backdrop: 'static', keyboard: false });
            });
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
                base.buttonAjax(urlAjaxSubUserDelete, ts,
                    function (result) {
                        if (result.error_code != 0) {
                            return toastr["error"](result.message)
                        }
                        return toastr["success"](result.message)
                    })
            }
        });
    }
};