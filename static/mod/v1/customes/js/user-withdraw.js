var userWithdraw = {
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
                sortable: false,
                mRender: function (data, type, row) {
                    var html = data;
                    if (isFullAccess == 1 && row.user_email) {
                        html += '<br/><a href="/user/detail?user_id=' + row.user_id + '"><span class="bg-info badge">' + row.user_email + '</span></a>';
                    }
                    if (isFullAccess == 1) {
                        var classColor = 'bg-success badge';
                        html += '<br/><span class="' + classColor + '">' + (typeof row.gateway[row.gateway_id] != "undefined" ? row.gateway[row.gateway_id]["name"] : "Unknown") + '</span>';
                    }
                    return html;
                }
            },
            {
                className: "text-left",
                data: 'bank_short_name',
                sortable: false
            },
            {
                className: "text-left",
                data: null,
                sortable: false,
                mRender: function (data, type, row) {
                    return row.bank_account_name + "<br/>" + row.bank_account_number + "<br/>Nội dung CK: " + row.remark
                }
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
                data: 'fee',
                sortable: false,
                mRender: function (data, type, row) {
                    return $.number(data) + "<sup>đ</sup>";
                }
            },
            {
                className: "text-center",
                data: 'amount_after_fee',
                sortable: false,
                mRender: function (data, type, row) {
                    return $.number(data) + "<sup>đ</sup>";
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
                    html = '<span class="' + classColor + '">' + (typeof row.status[row.status_id] != "undefined" ? row.status[row.status_id]["name"] : "Unknown") + '</span>';
                    if (row.note) {
                        html += '<div>' + row.note + '</div>';
                    }

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
                    html += '<br/><span class="' + classColor + '">' + (typeof row.type[row.type_id] != "undefined" ? row.type[row.type_id]["name"] : "Unknown") + '</span>';

                    return html;
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
        base.getDataTableBasic("#data-table-user-withdraw-list", arrColumns)
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
                        url: urlAjaxUserWithdrawExportExcel,
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