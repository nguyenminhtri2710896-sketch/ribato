var userTransaction = {
    index: function () {
        this.getList();
        this.exportExcel();
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
                data: 'user_balance',
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

        base.getDataTableBasic("#data-table-user-transaction-list", arrColumns)
    },
    exportExcel: function () {
        $(".btn-export-excel").click(function () {
            var tagFilter = "table-filer";
            var query = {};
            if ($('#' + tagFilter).length > 0) {
                if ($('#' + tagFilter).serializeArray()) {
                    $.each($('#' + tagFilter).serializeArray(), function (n, i) {
                        query[i.name] = i.value;
                    })
                }
            }
            var btnSubmit = $(this);
            var originIClass = btnSubmit.find("i").prop("class");
            Swal.fire({
                html: "Bạn có chắc muốn xuất giao dịch?<br/> <strong style='color:red'>Lưu ý</strong> Giao dịch sẽ xuất dựa trên bộ lọc tìm kiếm.",
                showCancelButton: !0,
                confirmButtonText: "Đồng ý xuất",
                cancelButtonText: "Đóng",
                showLoaderOnConfirm: !0,
                confirmButtonColor: "#556ee6",
                cancelButtonColor: "#f46a6a",
                confirmButtonClass: "btn btn-success mt-2 btn-swal-lg-custom ",
                cancelButtonClass: "btn btn-danger ms-2 mt-2 btn-swal-lg-custom ",
                preConfirm: function (n) {
                    console.log("preConfirm");
                },
                allowOutsideClick: !1
            }).then(function (t) {
                if (t.isConfirmed) {
                    /**
                     * Thực hiện ajax
                     */
                    $.ajax({
                        type: "POST",
                        url: urlAjaxUserTransactionExportExcel,
                        data: {
                            query: query
                        },
                        beforeSend: function () {
                            btnSubmit.find("i").removeClass(originIClass).addClass('fa-spin fa-refresh');
                        },
                        success: function (result) {
                            if (result.error_code !== 0) {
                                toastr["error"](result.message)
                                return false;
                            }

                            window.location.href = result.data.url;
                            toastr["success"](result.message)
                            $(".footer-note").html("<center>Nếu file không tự động tải về <a href='" + result.data.url + "'>vui lòng nhấn vào đây</a> để tải!!</center>");
                        }, complete: function () {
                            btnSubmit.find("i").removeClass("fa-spin fa-refresh").addClass(originIClass);
                        }
                    });
                }
            });
        });
    },
};