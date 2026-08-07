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
                    html += '<li data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Sửa"><a href="/gateway-account/detail?gateway_account_id=' + row.id + '" class="btn btn-sm btn-soft-info d-flex"  data-reload-datatable="#data-table-list"><i class="mdi mdi-pencil-outline"></i> sửa</a></li>';
                    html += '</ul>';
                    return html;
                }
            },
        ];

        base.getDataTableBasic("#data-table-list", arrColumns)
    }
};