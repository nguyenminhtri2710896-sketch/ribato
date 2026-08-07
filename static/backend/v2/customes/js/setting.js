var dataTable = null;
var setting = {
    bankTopup: function () {
        this.getListUserBank();
        $(".js-data-select2").each(function (n, t) {
            base.getDataSelect2(t);
        })
        this.add();
    },
    bankWithDraw: function () {
        this.getListUserBank();
        $(".js-data-select2").each(function (n, t) {
            base.getDataSelect2(t);
        })
        this.add();
    },
    bank: function () {
        this.getListBank();
        $(".js-data-select2").each(function (n, t) {
            base.getDataSelect2(t);
        })
    },
    getListUserBank: function () {
        var arrColumns = [
            {
                className: "text-center",
                data: 'row',
                sortable: false
            },
            {
                data: 'name',
                sortable: false,
                mRender: function (data, type, row) {
                    return data;
                }
            },
            {
                data: 'bank_short_name',
                className: "text-center",
                sortable: false,
                mRender: function (data, type, row) {
                    return data;
                }
            },
            {
                className: "text-center",
                data: null, sortable: false,
                mRender: function (data, type, row) {
                    return '<span class="bg-success badge">ổn định</span>'
                }
            },
            {
                className: "text-right",
                data: 'balance',
                sortable: true,
                mRender: function (data, type, row) {
                    return $.number(data);
                }
            },
            {
                data: null,
                sortable: false,
                mRender: function (data, type, row) {
                    return '<strong>' + row.bank_account_name + '</strong><br/>' + row.bank_account_id;
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
            }
        ];
        dataTable = base.getDataTableBasic("#data-table-list", arrColumns, function (result) { })
    },
    getListBank: function () {
        var arrColumns = [
            {
                className: "text-center",
                data: 'row',
                sortable: false
            },
            {
                data: 'napas_code',
                className: "text-center",
                sortable: false,
                mRender: function (data, type, row) {
                    return data;
                }
            },
            {
                data: 'name',
                sortable: false,
                mRender: function (data, type, row) {
                    return data;
                }
            },
            {
                data: 'short_name',
                sortable: false,
                mRender: function (data, type, row) {
                    return data;
                }
            },
            {
                data: 'short_code',
                sortable: false,
                mRender: function (data, type, row) {
                    return data;
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
            }
        ];
        dataTable = base.getDataTableBasic("#data-table-list", arrColumns, function (result) { })
    }, add: function () {
        base.submitForm(".form-add-bank", function (result) {
            if (result.error_code != 0) {
                return toastr["error"](result.message)
            }
            if (dataTable !== null) {
                dataTable.ajax.reload(null, false)
            }
            $(".bs-modal-add").modal("hide")
            return toastr["success"](result.message)
        });
    }
};