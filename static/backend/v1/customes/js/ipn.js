var ipnHelper = {
    formatPayload: function (value) {
        if (!value) {
            return '';
        }

        if (typeof value === "object") {
            return JSON.stringify(value, null, 2);
        }

        try {
            return JSON.stringify(JSON.parse(value), null, 2);
        } catch (error) {
            return value;
        }
    },
    renderStatusBadge: function (statusMap, statusId) {
        var badgeClass = 'bg-warning';
        if (statusId == 2) {
            badgeClass = 'bg-success';
        } else if (statusId == 3) {
            badgeClass = 'bg-danger';
        }

        var label = (statusMap && statusMap[statusId]) ? statusMap[statusId]["name"] : "Không xác định";
        return '<span class="badge ' + badgeClass + '">' + label + '</span>';
    },
    formatNumber: function (value) {
        if (value === null || typeof value === "undefined" || value === '') {
            return '';
        }
        return $.number(value) + ' đ';
    }
};

var ipnCollection = {
    index: function () {
        this.getList();
    },
    getList: function () {
        var arrColumns = [
            { data: 'row', sortable: false, className: 'text-center' },
            {
                data: 'transaction_code', sortable: false, className: 'text-left',
                mRender: function (data, type, row) {
                    var html = data ? '<strong>' + data + '</strong>' : '---';
                    if (row.transaction_user_email) {
                        html += '<div><small class="badge bg-info">' + row.transaction_user_email + '</small></div>';
                    }
                    if (row.transaction_id) {
                        html += '<div><small>ID: ' + row.transaction_id + '</small></div>';
                    }
                    return html;
                }
            },
            {
                data: 'transaction_ref_code', sortable: false, className: 'text-left',
                mRender: function (data) {
                    return data || '---';
                }
            },
            {
                data: 'message', sortable: false, className: 'text-left',
                mRender: function (data, type, row) {
                    var html = data || '---';
                    if (row.header_code) {
                        html += '<div><small class="text-muted">Header: ' + row.header_code + '</small></div>';
                    }
                    return html;
                }
            },
            {
                data: 'callback_status_id', sortable: false, className: 'text-center',
                mRender: function (data, type, row) {
                    var html = ipnHelper.renderStatusBadge(row.status, data);
                    html += '<div><small>Retry: ' + (row.callback_total_retry ?? 0) + '</small></div>';
                    return html;
                }
            },
            {
                data: 'created_at', sortable: true, className: 'text-center',
                mRender: function (data, type, row) {
                    if (!row.created_at) {
                        return '---';
                    }
                    return $.format.date(row.created_at, "dd/MM/yyyy HH:mm:ss");
                }
            },
            {
                data: null, sortable: false, className: 'text-center',
                mRender: function (data, type, row) {
                    var html = '<ul class="list-unstyled hstack d-inline-flex gap-1 mb-0">';
                    html += '<li><button type="button" class="btn btn-sm btn-info" data-params=\'{"id":' + row.id + '}\' onclick="ipnCollection.viewDetail(this)"><i class="far fa-eye"></i> Chi tiết</button></li>';
                    if (row.transaction_id) {
                        html += '<li><button type="button" class="btn btn-sm btn-warning" data-reload-datatable="#data-table-ipn-collection" data-params=\'{"transaction_id":' + row.transaction_id + '}\' onclick="ipnCollection.resend(this)"><i class="fas fa-redo"></i> Gửi lại</button></li>';
                    }
                    html += '</ul>';
                    return html;
                }
            }
        ];
        base.getDataTableBasic("#data-table-ipn-collection", arrColumns);
    },
    viewDetail: function (ts) {
        base.buttonAjax(urlAjaxIpnCollectionDetail, ts, function (result) {
            if (result.error_code != 0) {
                return toastr["error"](result.message);
            }
            var modal = $(".bs-modal-ipn-collection-detail");
            var callback = result.data.callback;
            modal.find('[name="transaction_code"]').val(callback.transaction_code || '');
            modal.find('[name="transaction_ref_code"]').val(callback.transaction_ref_code || '');
            modal.find('[name="transaction_user_email"]').val(callback.transaction_user_email || callback.user_email || '');
            modal.find('[name="callback_status_text"]').val((result.data.status[callback.callback_status_id] || {}).name || '');
            modal.find('[name="callback_total_retry"]').val(callback.callback_total_retry ?? 0);
            modal.find('[name="message"]').val(callback.message || '');
            modal.find('[name="param_request"]').val(ipnHelper.formatPayload(callback.param_request));
            modal.find('[name="param_response"]').val(ipnHelper.formatPayload(callback.param_response));
            modal.modal("show", { backdrop: 'static', keyboard: false });
        });
    },
    resend: function (ts) {
        Swal.fire({
            html: "Bạn có chắc muốn gửi lại IPN này?<br/><strong>Số lần retry sẽ được đặt về 0.</strong>",
            showCancelButton: !0,
            confirmButtonText: "Gửi lại",
            cancelButtonText: "Đóng",
            showLoaderOnConfirm: !0,
            confirmButtonColor: "#556ee6",
            cancelButtonColor: "#f46a6a",
            confirmButtonClass: "btn btn-success mt-2 btn-swal-lg-custom",
            cancelButtonClass: "btn btn-danger ms-2 mt-2 btn-swal-lg-custom",
            allowOutsideClick: !1
        }).then(function (t) {
            if (t.isConfirmed) {
                base.buttonAjax(urlAjaxIpnCollectionResend, ts, function (result) {
                    if (result.error_code != 0) {
                        return toastr["error"](result.message);
                    }
                    toastr["success"](result.message);
                });
            }
        });
    }
};

var ipnPayout = {
    index: function () {
        this.getList();
    },
    getList: function () {
        var arrColumns = [
            { data: 'row', sortable: false, className: 'text-center' },
            {
                data: 'trans_code', sortable: false, className: 'text-left',
                mRender: function (data, type, row) {
                    var html = data ? '<strong>' + data + '</strong>' : '---';
                    if (row.withdraw_user_email) {
                        html += '<div><small class="badge bg-info">' + row.withdraw_user_email + '</small></div>';
                    }
                    if (row.user_withdraw_id) {
                        html += '<div><small>ID: ' + row.user_withdraw_id + '</small></div>';
                    }
                    return html;
                }
            },
            {
                data: 'withdraw_ref_code', sortable: false, className: 'text-left',
                mRender: function (data) {
                    return data || '---';
                }
            },
            {
                data: 'message', sortable: false, className: 'text-left',
                mRender: function (data, type, row) {
                    var html = data || '---';
                    if (row.header_code) {
                        html += '<div><small class="text-muted">Header: ' + row.header_code + '</small></div>';
                    }
                    return html;
                }
            },
            {
                data: 'callback_status_id', sortable: false, className: 'text-center',
                mRender: function (data, type, row) {
                    var html = ipnHelper.renderStatusBadge(row.status, data);
                    html += '<div><small>Retry: ' + (row.callback_total_retry ?? 0) + '</small></div>';
                    return html;
                }
            },
            {
                data: 'created_at', sortable: true, className: 'text-center',
                mRender: function (data, type, row) {
                    if (!row.created_at) {
                        return '---';
                    }
                    return $.format.date(row.created_at, "dd/MM/yyyy HH:mm:ss");
                }
            },
            {
                data: null, sortable: false, className: 'text-center',
                mRender: function (data, type, row) {
                    var html = '<ul class="list-unstyled hstack d-inline-flex gap-1 mb-0">';
                    html += '<li><button type="button" class="btn btn-sm btn-info" data-params=\'{"id":' + row.id + '}\' onclick="ipnPayout.viewDetail(this)"><i class="far fa-eye"></i> Chi tiết</button></li>';
                    if (row.user_withdraw_id) {
                        html += '<li><button type="button" class="btn btn-sm btn-warning" data-reload-datatable="#data-table-ipn-payout" data-params=\'{"user_withdraw_id":' + row.user_withdraw_id + '}\' onclick="ipnPayout.resend(this)"><i class="fas fa-redo"></i> Gửi lại</button></li>';
                    }
                    html += '</ul>';
                    return html;
                }
            }
        ];
        base.getDataTableBasic("#data-table-ipn-payout", arrColumns);
    },
    viewDetail: function (ts) {
        base.buttonAjax(urlAjaxIpnPayoutDetail, ts, function (result) {
            if (result.error_code != 0) {
                return toastr["error"](result.message);
            }
            var modal = $(".bs-modal-ipn-payout-detail");
            var callback = result.data.callback;
            modal.find('[name="trans_code"]').val(callback.trans_code || '');
            modal.find('[name="withdraw_ref_code"]').val(callback.withdraw_ref_code || '');
            modal.find('[name="withdraw_user_email"]').val(callback.withdraw_user_email || callback.user_email || '');
            modal.find('[name="callback_status_text"]').val((result.data.status[callback.callback_status_id] || {}).name || '');
            modal.find('[name="callback_total_retry"]').val(callback.callback_total_retry ?? 0);
            modal.find('[name="amount"]').val(ipnHelper.formatNumber(callback.amount));
            modal.find('[name="fee"]').val(ipnHelper.formatNumber(callback.fee));
            modal.find('[name="message"]').val(callback.message || '');
            modal.find('[name="param_request"]').val(ipnHelper.formatPayload(callback.param_request));
            modal.find('[name="param_response"]').val(ipnHelper.formatPayload(callback.param_response));
            modal.modal("show", { backdrop: 'static', keyboard: false });
        });
    },
    resend: function (ts) {
        Swal.fire({
            html: "Bạn có chắc muốn gửi lại IPN này?<br/><strong>Số lần retry sẽ được đặt về 0.</strong>",
            showCancelButton: !0,
            confirmButtonText: "Gửi lại",
            cancelButtonText: "Đóng",
            showLoaderOnConfirm: !0,
            confirmButtonColor: "#556ee6",
            cancelButtonColor: "#f46a6a",
            confirmButtonClass: "btn btn-success mt-2 btn-swal-lg-custom",
            cancelButtonClass: "btn btn-danger ms-2 mt-2 btn-swal-lg-custom",
            allowOutsideClick: !1
        }).then(function (t) {
            if (t.isConfirmed) {
                base.buttonAjax(urlAjaxIpnPayoutResend, ts, function (result) {
                    if (result.error_code != 0) {
                        return toastr["error"](result.message);
                    }
                    toastr["success"](result.message);
                });
            }
        });
    }
};


