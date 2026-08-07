var userDebit = {
    index: function () {
        this.getList();
    },
    getList: function () {
        var arrColumns = [
            { data: 'row', sortable: false },
            {
                className: "text-left",
                data: 'user_fullname',
                sortable: false,
                mRender: function (data, type, row) {
                    return row.user_fullname;
                }
            },
            {
                className: "text-right",
                data: 'amount',
                sortable: true,
                mRender: function (data, type, row) {
                    if (row.amount < 0) {
                        return "<span class='text-danger'>" + $.number(row.amount, 0) + "<sup>đ</sup></span>";
                    }
                    return "<span class='text-success'>" + $.number(row.amount, 0) + "<sup>đ</sup></span>";
                }
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
                sortable: true,
                mRender: function (data, type, row) {
                    return row.note;
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
                data: 'debit_at',
                sortable: true,
                mRender: function (data, type, row) {
                    return $.format.date(row.debit_at, "dd/MM/yyyy HH:mm:ss");
                }
            },
            {
                className: "text-center",
                data: null,
                sortable: false,
                mRender: function (data, type, row) {
                    var html = '';
                    html += '<ul class="list-unstyled hstack d-inline-flex gap-1 mb-0">';
                    if (isFullAccess == 1) {
                        html += '<li data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Xoá"> <button  data-params=\'{"id":' + row.id + '}\' onclick="userDebit.delete(this)" data-reload-datatable="#data-table-list" class="btn btn-sm btn-soft-danger d-flex"><i class="mdi mdi-delete-outline"></i> xoá</button></li>';
                    }
                    html += '</ul>';
                    return html;
                }
            }
        ];
        var dataTable = base.getDataTableBasic("#data-table-list", arrColumns);
        // dataTable.on('draw', function (data) {
        //     var data = dataTable.data().toArray();
        //     console.log('Rendered rows:', data);
        // });
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
                base.buttonAjax(urlAjaxUserDebitDelete, ts,
                    function (result) {
                        if (result.error_code != 0) {
                            return toastr["error"](result.message)
                        }
                        $("#data-table-list").DataTable().draw();
                        return toastr["success"](result.message)
                    })
            }
        });
    }
};