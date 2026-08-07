var gatewayAccount = {
    index: function () {
        this.getList();
    },
    getList: function () {
        var arrColumns = [
            { data: 'row', sortable: false },
            {
                className: "text-left-desktop",
                data: 'gateway_name',
                sortable: false,
                mRender: function (data, type, row) {
                    return row.gateway_name;
                }
            },
            {
                className: "text-left-desktop",
                data: 'name',
                sortable: false,
                mRender: function (data, type, row) {
                    return row.name;
                }
            },
            {
                className: "text-right-desktop",
                data: 'balance',
                sortable: false,
                mRender: function (data, type, row) {
                    return '<span class="text-success">' + $.number(data) + '<sup>đ</sup></span>'
                }
            },
            {
                className: "text-right-desktop",
                data: 'pending_balance',
                sortable: false,
                mRender: function (data, type, row) {
                    return '<span class="text-success">' + $.number(data) + '<sup>đ</sup></span>'
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
                className: "text-center-desktop",
                sortable: false,
                mRender: function (data, type, row) {
                    html = '';
                    html += '<ul class="list-unstyled hstack d-inline-flex gap-1 mb-0">';
                    html += '<li data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Tải Public Key"><a href="/gateway-account/ajax/download-public-key?id=' + row.id + '" target="_blank" class="btn btn-sm btn-soft-success d-flex"><i class="mdi mdi-download"></i> key</a></li>';
                    html += '<li data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Chi tiết"><a href="/gateway-account/detail?gateway_account_id=' + row.id + '" class="btn btn-sm btn-soft-primary d-flex"><i class="mdi mdi-eye-outline"></i> chi tiết</a></li>';
                    html += '<li data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Sửa"><a href="javascript:void(0)" onclick="gatewayAccount.openEditModal(' + row.id + ')" class="btn btn-sm btn-soft-info d-flex"><i class="mdi mdi-pencil-outline"></i> sửa</a></li>';

                    html += '</ul>';
                    return html;
                }
            },
        ];

        base.getDataTableBasic("#data-table-list", arrColumns)
    },
    add: function () {
        var data = {
            gateway_id: $('#modal-add select[name="gateway_id"]').val(),
            name: $('#modal-add input[name="name"]').val(),
            status_id: $('#modal-add select[name="status_id"]').val(),
            username: $('#modal-add input[name="username"]').val(),
            password: $('#modal-add input[name="password"]').val(),
            payout_pin: $('#modal-add input[name="payout_pin"]').val(),
            tenant: $('#modal-add input[name="tenant"]').val(),
            private_key: $('#modal-add textarea[name="private_key"]').val(),
            public_key: $('#modal-add textarea[name="public_key"]').val(),
        };
        $.ajax({
            url: '/gateway-account/ajax/add',
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
    generateKey: function () {
        $.ajax({
            url: '/gateway/ajax-generate-key',
            type: 'GET',
            dataType: 'json',
            success: function (res) {
                if (res.error_code == 0) {
                    $('#modal-add textarea[name="private_key"]').val(res.data.private_key);
                    $('#modal-add textarea[name="public_key"]').val(res.data.public_key);
                    toastr["success"]('Tạo mã thành công');
                } else {
                    toastr["error"]('Tạo mã thất bại');
                }
            }
        });
    },
    update: function () {
        var data = {
            id: $('#edit-id').val(),
            gateway_id: $('#edit-gateway_id').val(),
            name: $('#edit-name').val(),
            status_id: $('#edit-status_id').val(),
            username: $('#edit-username').val(),
            password: $('#edit-password').val(),
            payout_pin: $('#edit-payout_pin').val(),
            tenant: $('#edit-tenant').val(),
            private_key: $('#edit-private_key').val(),
            public_key: $('#edit-public_key').val(),
        };
        $.ajax({
            url: '/gateway-account/ajax/update',
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
            url: '/gateway-account/ajax/get-detail',
            type: 'POST',
            data: { query: { id: id } },
            dataType: 'json',
            success: function (res) {
                if (res.error_code == 0 && res.data) {
                    var data = res.data;
                    $('#edit-id').val(data.id);
                    if ($('#edit-gateway_id').find("option[value='" + data.gateway_id + "']").length) {
                        $('#edit-gateway_id').val(data.gateway_id).trigger('change');
                    } else { 
                        var newOption = new Option('Cổng ' + data.gateway_id, data.gateway_id, true, true);
                        $('#edit-gateway_id').append(newOption).trigger('change');
                    }
                    $('#edit-name').val(data.name);
                    $('#edit-status_id').val(data.status_id);
                    $('#edit-username').val(data.username);
                    $('#edit-password').val('');
                    $('#edit-payout_pin').val('');
                    $('#edit-tenant').val(data.tenant);
                    $('#edit-private_key').val(data.private_key);
                    $('#edit-public_key').val(data.public_key);
                    $('#modal-edit').modal('show');
                } else {
                    toastr["error"](res.message || 'Không lấy được thông tin chi tiết');
                }
            },
            error: function () {
                toastr["error"]('Có lỗi xảy ra');
            }
        });
    },
    generateKeyEdit: function () {
        $.ajax({
            url: '/gateway/ajax-generate-key',
            type: 'GET',
            dataType: 'json',
            success: function (res) {
                if (res.error_code == 0) {
                    $('#edit-private_key').val(res.data.private_key);
                    $('#edit-public_key').val(res.data.public_key);
                    toastr["success"]('Tạo mã thành công');
                } else {
                    toastr["error"]('Tạo mã thất bại');
                }
            }
        });
    }
};