var userRevenueReport = {
    index: function () {
        this.getList();
        this.exportExcel();
    },
    getList: function () {
        var arrColumns = [
            { data: 'row', sortable: false },
            {
                className: "text-left",
                data: 'user_id',
                sortable: false,
                mRender: function (data, type, row) {
                    var html = row.user_fullname + '<br/><a href="/user/detail?user_id=' + row.user_id + '"><span class="bg-info badge">' + row.user_email + '</span></a>';
                    return html;
                }
            },
            {
                className: "text-center",
                data: 'total_referal_fee',
                sortable: false,
                mRender: function (data, type, row) {
                    return "<span class=\"" + (data > 0 ? "text-success" : "text-danger") + "\">" + $.number(data) + "<sup>đ</sup></span>";
                }
            },
            {
                className: "text-center",
                data: 'total_gateway_fee',
                sortable: false,
                mRender: function (data, type, row) {
                    return "<span class=\"" + (data > 0 ? "text-success" : "text-danger") + "\">" + $.number(data) + "<sup>đ</sup></span>";
                }
            },
            {
                className: "text-center",
                data: 'total_profit',
                sortable: false,
                mRender: function (data, type, row) {
                    return "<span class=\"" + (data > 0 ? "text-success" : "text-danger") + "\">" + $.number(data) + "<sup>đ</sup></span>";
                }
            },
            {
                className: "text-center",
                data: 'total_transaction_amount',
                sortable: false,
                mRender: function (data, type, row) {
                    return "<span class=\"" + (data > 0 ? "text-success" : "text-danger") + "\">" + $.number(data) + "<sup>đ</sup></span>";
                }
            },
            {
                className: "text-center",
                data: 'total_transaction_fee',
                sortable: false,
                mRender: function (data, type, row) {
                    return "<span class=\"" + (data > 0 ? "text-success" : "text-danger") + "\">" + $.number(data) + "<sup>đ</sup></span>";
                }
            },
            {
                className: "text-center",
                data: 'type_id', sortable: false,
                mRender: function (data, type, row) {
                    switch (row.type_id) {
                        case 1:
                            classColor = 'bg-success badge';
                            break;
                        case 2:
                            classColor = 'bg-warning badge';
                            break;
                        default:
                            classColor = 'bg-warning badge';
                            break;
                    }
                    html = '<span class="' + classColor + '">' + (typeof row.type[row.type_id] != "undefined" ? row.type[row.type_id]["name"] : "Unknown") + '</span>';
                    return html;
                }
            },
            {
                className: "text-center",
                data: 'report_at',
                sortable: true,
                mRender: function (data, type, row) {
                    return $.format.date(row.report_at, "dd/MM/yyyy HH:mm:ss");
                }
            }
        ];

        base.getDataTableBasic("#data-table-user-revenue-report-list", arrColumns)
    },
    exportExcel: function () {
        $(document).on('click', '.btn-export-user-revenue-report', function () {
            var btn = $(this);
            var icon = btn.find('i');
            var originIconClass = icon.length ? icon.attr('class') : '';
            var params = {};
            if ($('#table-filer').length > 0) {
                $('#table-filer').serializeArray().forEach(function (item) {
                    params[item.name] = item.value;
                });
            }
            $.ajax({
                type: "POST",
                dataType: 'json',
                url: urlAjaxUserRevenueReportExport,
                data: { query: params },
                beforeSend: function () {
                    btn.prop('disabled', true);
                    if (icon.length) {
                        icon.attr('class', 'fas fa-spinner fa-spin');
                    }
                },
                success: function (result) {
                    if (result.error_code != 0) {
                        toastr["error"](result.message);
                        return;
                    }
                    if (result.data && result.data.url) {
                        window.location.href = result.data.url;
                        toastr["success"](result.message || "Xuất file thành công.");
                    }
                },
                complete: function () {
                    btn.prop('disabled', false);
                    if (icon.length && originIconClass) {
                        icon.attr('class', originIconClass);
                    }
                }
            });
        });
    }
};