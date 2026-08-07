var gateway = {
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
                className: "text-center",
                data: 'status_id', sortable: false,
                mRender: function (data, type, row) {
                    switch (row.status_id) {
                        case 1:
                            classColor = 'bg-info badge';
                            break;
                        case 2:
                            classColor = 'bg-success badge';
                            break;
                        case 3:
                            classColor = 'bg-danger badge';
                            break;
                        case 4:
                            classColor = 'bg-secondary badge';
                            break;
                        default:
                            classColor = 'bg-warning badge';
                            break;
                    }

                    return '<span class="' + classColor + '">' + (typeof row.status[row.status_id] != "undefined" ? row.status[row.status_id]["name"] : "Unknown") + '</span>'
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
                className: "text-center",
                data: 'updated_at',
                sortable: true,
                mRender: function (data, type, row) {
                    return $.format.date(row.updated_at, "dd/MM/yyyy HH:mm:ss");
                }
            },
            {
                data: null,
                className: "text-center",
                sortable: false,
                mRender: function (data, type, row) {
                    html = '';
                    html += '<ul class="list-unstyled hstack d-inline-flex gap-1 mb-0">';
                    html += '<li data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Chi tiết"><a href="/gateway-account/index?gateway_id=' + row.id + '" class="btn btn-sm btn-soft-primary d-flex"><i class="mdi mdi-eye-outline"></i> chi tiết</a></li>';
                    html += '<li data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Sửa"><a href="javascript:void(0);" onclick="gateway.openEditModal(\'' + row.id + '\')" class="btn btn-sm btn-soft-info d-flex"><i class="mdi mdi-pencil-outline"></i> sửa</a></li>';
                    html += '</ul>';
                    return html;
                }
            }
        ];

        base.getDataTableBasic("#data-table-list", arrColumns)
    },
    add: function () {
        var data = {
            name: $('#modal-add input[name="name"]').val(),
        };
        $.ajax({
            url: '/gateway/ajax-add',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function (res) {
                if (res.error_code == 0) {
                    $('#modal-add').modal('hide');
                    $('#data-table-list').DataTable().draw();
                    toastr["success"](res.message || 'Thêm mới thành công');
                } else {
                    if (res.errors && res.errors.length > 0) {
                        toastr["error"](res.errors[0][0]);
                    } else {
                        toastr["error"](res.message || 'Thêm mới thất bại');
                    }
                }
            },
            error: function () {
                toastr["error"]('Có lỗi xảy ra');
            }
        });
    },
    update: function () {
        var data = {
            id: $('#modal-edit input[name="id"]').val(),
            name: $('#modal-edit input[name="name"]').val(),
        };
        $.ajax({
            url: '/gateway/ajax-update',
            type: 'POST',
            data: data,
            dataType: 'json',
            success: function (res) {
                if (res.error_code == 0) {
                    $('#modal-edit').modal('hide');
                    $('#data-table-list').DataTable().draw();
                    toastr["success"](res.message || 'Cập nhật thành công');
                } else {
                    if (res.errors && res.errors.length > 0) {
                        toastr["error"](res.errors[0][0]);
                    } else {
                        toastr["error"](res.message || 'Cập nhật thất bại');
                    }
                }
            },
            error: function () {
                toastr["error"]('Có lỗi xảy ra');
            }
        });
    },
    openEditModal: function (id) {
        $.ajax({
            url: '/gateway/ajax-get-detail',
            type: 'POST',
            data: { query: { id: id } },
            dataType: 'json',
            success: function (res) {
                if (res.error_code == 0 && res.data) {
                    var data = res.data;
                    $('#edit-id').val(data.id);
                    $('#edit-name').val(data.name);
                    $('#modal-edit').modal('show');
                } else {
                    toastr["error"](res.message || 'Không lấy được thông tin chi tiết');
                }
            },
            error: function () {
                toastr["error"]('Có lỗi xảy ra');
            }
        });
    }
};