var gatewayAccount = {
    index: function () {
        this.getList();
        this.initEvents();
    },
    toggleGatewayUI: function (modal, val, isImmediate) {
        if (val == 8 || val == '8') {
            if (isImmediate) {
                modal.find('.gpayv2-fields').show();
            } else {
                modal.find('.gpayv2-fields').slideDown();
            }
            modal.find('input[name="username"]').attr('placeholder', 'Client ID do GPAY cấp');
            modal.find('input[name="tenant"]').attr('placeholder', 'sandbox hoặc production');
            if (modal.attr('id') === 'modal-edit') {
                modal.find('input[name="password"]').attr('placeholder', 'Client Secret mới (bỏ trống nếu không đổi)');
            } else {
                modal.find('input[name="password"]').attr('placeholder', 'Client Secret do GPAY cấp');
            }
        } else {
            if (isImmediate) {
                modal.find('.gpayv2-fields').hide();
            } else {
                modal.find('.gpayv2-fields').slideUp();
            }
            modal.find('input[name="username"]').attr('placeholder', 'Username');
            modal.find('input[name="tenant"]').attr('placeholder', 'Tenant');
            if (modal.attr('id') === 'modal-edit') {
                modal.find('input[name="password"]').attr('placeholder', 'Mật khẩu mới (bỏ trống nếu không đổi)');
            } else {
                modal.find('input[name="password"]').attr('placeholder', 'Mật khẩu');
            }
        }
    },
    initEvents: function () {
        var self = this;
        $(document).on('change select2:select', '#modal-add select[name="gateway_id"], #gateway-id-select, #edit-gateway_id, select[name="gateway_id"]', function () {
            var val = $(this).val();
            var modal = $(this).closest('.modal');
            self.toggleGatewayUI(modal, val, false);
        });

        $('#modal-add').on('show.bs.modal shown.bs.modal', function () {
            var val = $('#modal-add select[name="gateway_id"]').val();
            self.toggleGatewayUI($('#modal-add'), val, true);
        });
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
            merchant_id: $('#modal-add input[name="merchant_id"]').val(),
            payout_pin: $('#modal-add input[name="payout_pin"]').val(),
            tenant: $('#modal-add input[name="tenant"]').val(),
            access_token: $('#modal-add textarea[name="access_token"]').val(),
            private_key: $('#modal-add textarea[name="private_key"]').val(),
            public_key: $('#modal-add textarea[name="public_key"]').val(),
            gateway_public_key: $('#modal-add textarea[name="gateway_public_key"]').val(),
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
                    if (res.data.certificate && $('#modal-add select[name="gateway_id"]').val() == 8) {
                        $('#modal-add textarea[name="access_token"]').val(res.data.certificate);
                    }
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
            merchant_id: $('#edit-merchant_id').val(),
            payout_pin: $('#edit-payout_pin').val(),
            tenant: $('#edit-tenant').val(),
            access_token: $('#edit-access_token').val(),
            private_key: $('#edit-private_key').val(),
            public_key: $('#edit-public_key').val(),
            gateway_public_key: $('#edit-gateway_public_key').val(),
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
        var self = this;
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
                    $('#edit-merchant_id').val(data.merchant_id || '');
                    $('#edit-payout_pin').val('');
                    $('#edit-tenant').val(data.tenant || '');
                    $('#edit-access_token').val(data.access_token || '');
                    $('#edit-private_key').val(data.private_key || '');
                    $('#edit-public_key').val(data.public_key || '');
                    $('#edit-gateway_public_key').val(data.gateway_public_key || '');
                    
                    self.toggleGatewayUI($('#modal-edit'), data.gateway_id, true);
                    
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
                    if (res.data.certificate && $('#edit-gateway_id').val() == 8) {
                        $('#edit-access_token').val(res.data.certificate);
                    }
                    toastr["success"]('Tạo mã thành công');
                } else {
                    toastr["error"]('Tạo mã thất bại');
                }
            }
        });
    },
    openHistoryModal: function (id) {
        $('#history-gateway-account-id').val(id);
        $('.bs-modal-history').modal('show');
        this.getHistory();
    },
    getHistory: function () {
        var arrColumns = [
            { data: 'row', sortable: false },
            {
                className: "text-left-desktop",
                data: 'gateway_account_name',
                sortable: false,
                mRender: function (data, type, row) {
                    return row.gateway_account_name;
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
                className: "text-center",
                data: 'created_at',
                sortable: true,
                mRender: function (data, type, row) {
                    return $.format.date(row.created_at, "dd/MM/yyyy HH:mm:ss");
                }
            }
        ];
        
        if ($.fn.DataTable.isDataTable('#data-table-gateway-account-history')) {
            $('#data-table-gateway-account-history').DataTable().ajax.reload(null, false);
        } else {
            base.getDataTableBasic("#data-table-gateway-account-history", arrColumns);
        }
    }
};