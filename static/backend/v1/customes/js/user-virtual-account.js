var userVirtualAccount = {
    index: function () {
        this.getList();
    },
    initEvent: function () {
        $(document).off('change', '.va-status-toggle').on('change', '.va-status-toggle', function () {
            var id = $(this).data('id');
            var isChecked = $(this).is(':checked');
            var statusId = isChecked ? 2 : 1;
            var inputElement = $(this);

            $.ajax({
                url: '/user-virtual-account/ajax/change-status',
                type: 'POST',
                data: {
                    id: id,
                    status_id: statusId
                },
                success: function (response) {
                    if (response.error_code == 0) {
                        toastr.success(response.message || 'Cập nhật trạng thái thành công.');
                        $("#data-table-user-virtual-account-list").DataTable().draw(false);
                    } else {
                        toastr.error(response.message || 'Cập nhật trạng thái thất bại.');
                        inputElement.prop('checked', !isChecked);
                    }
                },
                error: function (xhr) {
                    toastr.error('Đã xảy ra lỗi, vui lòng thử lại.');
                    inputElement.prop('checked', !isChecked);
                }
            });
        });
    },
    getList: function () {
        if (typeof this.isInitEvent == "undefined") {
            this.isInitEvent = true;
            this.initEvent();
        }
        var arrColumns = [
            { data: 'row', sortable: false },
            { data: 'gateway_name', sortable: false },
            { data: 'bank_short_name', sortable: false },
            { data: 'bank_account_name', sortable: false },
            { data: 'bank_account_number', sortable: false },
            {
                className: "text-center",
                data: null,
                sortable: false,
                mRender: function (data, type, row) {
                    var classColor = row.status_id == 2 ? 'bg-success badge' : 'bg-danger badge';
                    var statusName = (typeof row.status != "undefined" && typeof row.status[row.status_id] != "undefined") ? row.status[row.status_id]["name"] : (row.status_id == 2 ? 'Hoạt động' : 'Đang bảo trì');
                    var isChecked = row.status_id == 2 ? 'checked' : '';
                    return '<div class="form-check form-switch d-flex justify-content-center">' +
                        '<input class="form-check-input va-status-toggle" type="checkbox" data-id="' + row.id + '" ' + isChecked + '>' +
                        '</div><span class="' + classColor + '">' + statusName + '</span>';
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
        base.getDataTableBasic("#data-table-user-virtual-account-list", arrColumns)
    }
};