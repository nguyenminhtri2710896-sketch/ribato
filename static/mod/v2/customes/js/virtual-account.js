var virtualAccount = {
    index: function () {
        this.getList();
    },
    getList: function () {
        var arrColumns = [
            { data: 'row', sortable: false },
            { data: 'bank_short_name', sortable: false },
            { data: 'bank_account_name', sortable: false },
            { data: 'bank_account_number', sortable: false },
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