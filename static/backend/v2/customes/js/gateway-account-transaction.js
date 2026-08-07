var gatewayAccountTransaction = {
    index: function () {
        this.getList();
    },
    getList: function () {
        var arrColumns = [
            { data: 'row', sortable: false },
            {
                className: "text-left",
                data: 'trans_code',
                sortable: false
            },
            {
                className: "text-center",
                data: 'type_id', sortable: false,
                mRender: function (data, type, row) {
                    switch (row.type_id) {
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
                    return '<span class="' + classColor + '">' + (typeof row.type[row.type_id] != "undefined" ? row.type[row.type_id]["name"] : "Unknown") + '</span>'
                }
            },
            {
                className: "text-left",
                data: 'note',
                sortable: false
            },
            {
                className: "text-center",
                data: 'amount',
                sortable: false,
                mRender: function (data, type, row) {
                    return $.number(data) + "<sup>đ</sup>";
                }
            },
            {
                className: "text-center",
                data: 'gateway_account_balance',
                sortable: false,
                mRender: function (data, type, row) {
                    return $.number(data) + "<sup>đ</sup>";
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

        base.getDataTableBasic("#data-table-gateway-account-transaction-list", arrColumns)
    },
};