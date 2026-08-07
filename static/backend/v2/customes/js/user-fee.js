var userFee = {
    index: function () {
        this.getList();
    },
    getList: function () {
        var arrColumns = [
            { data: 'row', sortable: false },
            { data: 'name', sortable: false },
            { data: 'fee', sortable: false },
            {
                className: "text-center",
                data: 'balance', sortable: false,
                mRender: function (data, type, row) {

                    return '<span class="text-success">' + $.number(row.min_fee) + '<sup>đ</sup></span>'
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
                data: null,
                className: "text-center",
                sortable: false,
                mRender: function (data, type, row) {
                    html = '';
                    html += '<ul class="list-unstyled hstack d-inline-flex gap-1 mb-0">';
                    html += '<li data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Sửa"><button  data-params=\'{"query":{"id":' + row.id + '}}\' onclick="userFee.detail(this)" class="btn btn-sm btn-soft-info  d-flex"  data-reload-datatable="#data-table-list"><i class="mdi mdi-pencil-outline"></i> sửa</button></li>';
                    html += '</ul>';
                    return html;
                }
            },
        ];
        base.getDataTableBasic("#data-table-user-fee-list", arrColumns)
    }, detail: function (ts) {
        base.buttonAjax(urlAjaxUserFeeDetail, ts,
            function (result) {
                if (result.error_code != 0) {
                    return toastr["error"](result.message)
                }

                var modal = $(".bs-modal-update-user-fee");
                $.each(result.data.user_fee, function (k, i) {
                    modal.find('[name="' + k + '"]').val(i).change();
                    modal.find('[data-for="input[name=\'' + k + '\']"]').val(i).change();
                });
                modal.modal("show", { backdrop: 'static', keyboard: false });
            });
    }
};