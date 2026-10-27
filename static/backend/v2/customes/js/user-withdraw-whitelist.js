var userWithdrawWhitelist = {
    index: function () {
        this.getList();
    },
    getList: function () {
        var arrColumns = [
            { data: 'row', sortable: false },
            {
                className: "text-left",
                data: 'fullname',
                sortable: false,
                mRender: function (data, type, row) {
                    return row.user_fullname + "<br/><span class=\"bg-info badge\">" + row.user_email + "</span>";
                }
            },
            {
                className: "text-left",
                data: 'bank_name',
                sortable: false,
                mRender: function (data, type, row) {
                    return "(" + row.bank_short_code + ") " + row.bank_name;
                }
            },
            { className: "text-left", data: 'bank_account_number', sortable: false },
            { className: "text-center", data: 'bank_account_name', sortable: false },
            { className: "text-left", data: 'note', sortable: false, mRender: function (d) { return $('<div>').text(d || '').html(); } },
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
                    html = '';
                    html += '<ul class="list-unstyled hstack d-inline-flex gap-1 mb-0">';
                    html += '<li data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Sửa"><button  data-params=\'{"query":{"id":' + row.id + '}}\' onclick="userWithdrawWhitelist.getDetail(this)" class="btn btn-sm btn-soft-info d-flex"  data-reload-datatable="#data-table-user-withdraw-whitelist-list"><i class="mdi mdi-pencil-outline"></i> sửa</button></li>';
                    html += '<li data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Xoá"> <button  data-params=\'{"id":' + row.id + '}\' onclick="userWithdrawWhitelist.delete(this)" data-reload-datatable="#data-table-user-withdraw-whitelist-list" class="btn btn-sm btn-soft-danger d-flex"><i class="mdi mdi-delete-outline"></i> xoá</button></li>';
                    html += '</ul>';
                    return html;
                }
            },
        ];

        base.getDataTableBasic("#data-table-user-withdraw-whitelist-list", arrColumns)
    }, getDetail: function (ts) {
        base.buttonAjax(urlAjaxUserWithdrawWhitelistDetail, ts,
            function (result) {
                if (result.error_code != 0) {
                    return toastr["error"](result.message)
                }

                var modal = $(".bs-modal-user-withdraw-whitelist-update");
                $.each(result.data.user_withdraw_whitelist, function (k, i) {
                    modal.find('[name="' + k + '"]').val(i).change();
                });
                /**
                 * Set select input
                 */
                if (result.data.user) {
                    modal.find('select[name="user_id"]').html('');
                    dataSelect = new Option(result.data.user.fullname + " " + result.data.user.email, result.data.user.id, true);
                    modal.find('select[name="user_id"]').append(dataSelect).trigger('change');
                }

                if (result.data.bank) {
                    modal.find('select[name="bank_id"]').html('');
                    dataSelect = new Option(result.data.bank.name, result.data.bank.id, true);
                    modal.find('select[name="bank_id"]').append(dataSelect).trigger('change');
                }
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
                base.buttonAjax(urlAjaxUserWithdrawWhitelistDelete, ts,
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