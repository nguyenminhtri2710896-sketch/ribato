var bankAccount = {
    index: function () {
        this.getList();
    },
    getList: function () {
        var arrColumns = [
            { data: 'row', sortable: false },
            {
                className: "text-center",
                data: 'bank_account_name',
                sortable: false
            },
            {
                className: "text-left",
                data: 'bank_account_number',
                sortable: false
            },
            {
                className: "text-center",
                data: 'bank_short_name',
                sortable: false
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
                    html += '<li data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Sửa"><button  data-params=\'{"query":{"id":' + row.id + '}}\' onclick="bankAccount.detail(this)" class="btn btn-sm btn-soft-info  d-flex"  data-reload-datatable="#data-table-list"><i class="mdi mdi-pencil-outline"></i> sửa</button></li>';
                    html += '<li data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Xoá"> <button  data-params=\'{"id":' + row.id + '}\' onclick="bankAccount.delete(this)" data-reload-datatable="#data-table-list" class="btn btn-sm btn-soft-danger d-flex"><i class="mdi mdi-delete-outline"></i> xoá</button></li>';
                    html += '</ul>';
                    return html;
                }
            },
        ];

        base.getDataTableBasic("#data-table-list", arrColumns)
    }, detail: function (ts) {
        base.buttonAjax(urlAjaxBankAccountDetail, ts,
            function (result) {
                if (result.error_code != 0) {
                    return toastr["error"](result.message)
                }

                var modal = $(".bs-modal-update");
                $.each(result.data.bank_account, function (k, i) {
                    modal.find('[name="' + k + '"]').val(i).change();
                });
                /**
                 * Set select input
                 */
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
                base.buttonAjax(urlAjaxBankAccountDelete, ts,
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