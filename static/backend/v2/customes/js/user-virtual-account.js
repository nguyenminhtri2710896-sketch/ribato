var userVirtualAccount = {
    index: function () {
        this.getList();
    },
    initEvent: function () {
        var self = this;
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

        // Xử lý khi chọn tài khoản cổng trong modal tạo VA
        $(document).off('select2:select', '.bs-modal-add-user-virtual-account select[name="gateway_account_id"]').on('select2:select', '.bs-modal-add-user-virtual-account select[name="gateway_account_id"]', function (e) {
            var data = (e && e.params && e.params.data) ? e.params.data : null;
            var gatewayId = (data && typeof data.gateway_id !== 'undefined') ? data.gateway_id : null;
            if (gatewayId) {
                $(this).data('selected-gateway-id', gatewayId);
                self.applyGatewayBankFilter(gatewayId);
            } else {
                var gatewayAccountId = $(this).val();
                if (gatewayAccountId) {
                    self.fetchGatewayDetailAndFilter(gatewayAccountId);
                } else {
                    self.updateBankSelect(['BIDV', 'TCB', 'MSB']);
                }
            }
        });

        $(document).off('change', '.bs-modal-add-user-virtual-account select[name="gateway_account_id"]').on('change', '.bs-modal-add-user-virtual-account select[name="gateway_account_id"]', function (e) {
            var gatewayAccountId = $(this).val();
            if (!gatewayAccountId) {
                $(this).removeData('selected-gateway-id');
                self.updateBankSelect(['BIDV', 'TCB', 'MSB']);
                return;
            }

            var gatewayId = $(this).data('selected-gateway-id');
            if (gatewayId) {
                self.applyGatewayBankFilter(gatewayId);
            } else {
                self.fetchGatewayDetailAndFilter(gatewayAccountId);
            }
        });

        // Khi mở modal tạo VA
        $('.bs-modal-add-user-virtual-account').on('show.bs.modal', function () {
            var selectGateway = $('.bs-modal-add-user-virtual-account select[name="gateway_account_id"]');
            var gatewayAccountId = selectGateway.val();
            var gatewayId = selectGateway.data('selected-gateway-id');
            if (gatewayAccountId && gatewayId) {
                self.applyGatewayBankFilter(gatewayId);
            } else if (gatewayAccountId) {
                self.fetchGatewayDetailAndFilter(gatewayAccountId);
            } else {
                self.updateBankSelect(['BIDV', 'TCB', 'MSB']);
            }
        });
    },
    fetchGatewayDetailAndFilter: function (gatewayAccountId) {
        var self = this;
        $.ajax({
            url: '/gateway-account/ajax/get-detail',
            type: 'POST',
            data: { query: { id: gatewayAccountId } },
            dataType: 'json',
            success: function (res) {
                if (res.error_code == 0 && res.data) {
                    var gid = res.data.gateway_id;
                    $('.bs-modal-add-user-virtual-account select[name="gateway_account_id"]').data('selected-gateway-id', gid);
                    self.applyGatewayBankFilter(gid);
                } else {
                    self.updateBankSelect(['BIDV', 'TCB', 'MSB']);
                }
            },
            error: function () {
                self.updateBankSelect(['BIDV', 'TCB', 'MSB']);
            }
        });
    },
    applyGatewayBankFilter: function (gatewayId) {
        if (gatewayId == 8 || gatewayId == '8') {
            // Cổng GPAY V2: hỗ trợ 6 ngân hàng: BIDV, TCB, MSB, VCCB, VPB, WOO
            this.updateBankSelect(['BIDV','MB',  'TCB', 'MSB', 'VCCB', 'VPB', 'WOO']);
        } else if (gatewayId == 3 || gatewayId == '3') {
            // Cổng Yoobill: hỗ trợ BIDV
            this.updateBankSelect(['BIDV']);
        } else {
            // Cổng GPAY cũ (gateway 2) hoặc các cổng khác: 3 ngân hàng cũ
            this.updateBankSelect(['BIDV', 'TCB', 'MSB']);
        }
    },
    updateBankSelect: function (shortCodes) {
        var bankSelect = $('.bs-modal-add-user-virtual-account select[name="bank_id"]');
        if (bankSelect.length === 0) return;

        var arrCodes = Array.isArray(shortCodes) ? shortCodes : shortCodes.split(',');
        var ajaxUrl = '/bank/ajax/ajax-select2-get-list';
        
        bankSelect.val(null);
        if (bankSelect.hasClass("select2-hidden-accessible")) {
            bankSelect.select2('destroy');
        }
        bankSelect.empty();

        bankSelect.select2({
            dropdownParent: bankSelect.parent(),
            ajax: {
                url: ajaxUrl,
                dataType: 'json',
                type: "POST",
                delay: 250,
                data: function (params) {
                    return {
                        query: { name: params.term },
                        query_in_list: { short_code: arrCodes },
                        page: params.page
                    };
                }
            },
            placeholder: 'Chọn ngân hàng'
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