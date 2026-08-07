var dataTable = null;
var transaction = {
    index: function () {
        this.getList();
        this.createQrUser();
        this.createQrTransaction();
        this.exportExcel();
    },
    getList: function () {
        var arrColumns = [
            {
                className: "text-center",
                data: 'row',
                sortable: false
            },
            {
                data: 'ref_code',
                sortable: false,
                mRender: function (data, type, row) {
                    var html = data;
                    if (isFullAccess == 1 && row.user_email) {
                        html += '<br/><a href="/user/detail?user_id=' + row.user_id + '"><span class="bg-info badge">' + row.user_email + '</span></a>';
                    }
                    if (isFullAccess == 1) {
                        var nameGate = "Unknown";
                        switch (row.gateway_id) {
                            case 1:
                                nameGate = "PayH";
                                break;
                            case 2:
                                nameGate = "Gpay";
                                break;
                            case 3:
                                nameGate = "Yb";
                                break;
                            default:
                                nameGate = "Unknown";
                                break;
                        }
                        var classColor = 'bg-success badge';
                        html += '<br/><span class="' + classColor + '">' + nameGate + '</span>';
                    }
                    return html;
                }
            },
            {
                data: 'code',
                sortable: false,
                mRender: function (data, type, row) {
                    var html = data;
                    if (row.user_id_qrcode_code) {
                        html += '<br/><span class="bg-info badge">QR: ' + row.user_id_qrcode_code + '</span>';
                    }
                    return html;
                }
            },
            {
                className: "text-right",
                data: 'amount',
                sortable: true,
                mRender: function (data, type, row) {
                    return $.number(row.amount) + "đ";
                }
            },
            {
                className: "text-right",
                data: 'fee',
                sortable: true,
                mRender: function (data, type, row) {
                    return $.number(row.fee) + "đ";
                }
            },
            {
                className: "text-right",
                data: 'amount_after_fee',
                sortable: true,
                mRender: function (data, type, row) {
                    return $.number(row.amount_after_fee) + "đ";
                }
            },
            {
                className: "text-left",
                data: 'content',
                sortable: false,
                mRender: function (data, type, row) {
                    return "<div style='overflow: hidden; height: 40px;overflow-y: auto; '>" + data + "</div>";
                }
            },
            {
                className: "text-left",
                data: 'content',
                sortable: false,
                mRender: function (data, type, row) {
                    return row.bank_account_name + "<br/>" + row.bank_account_number;
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
            // {
            //     className: "text-center",
            //     data: 'received_at',
            //     sortable: true,
            //     mRender: function (data, type, row) {
            //         return $.format.date(row.received_at, "dd/MM/yyyy HH:mm:ss");
            //     }
            // },
            {
                className: "text-center",
                data: 'updated_at',
                sortable: true,
                mRender: function (data, type, row) {
                    return $.format.date(row.updated_at, "dd/MM/yyyy HH:mm:ss");
                }
            },
            // {
            //     data: null,
            //     sortable: false,
            //     mRender: function (data, type, row) {
            //         html = '';
            //         html += '<ul class="list-unstyled hstack gap-1 mb-0">';
            //         // html += '<li data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Sửa"><button  data-params=\'{"query":{"id":' + row.id + '}}\' onclick="userTransactionPayment.detail(this)" class="btn btn-sm btn-soft-info"><i class="mdi mdi-pencil-outline"></i></button></li>';
            //         // html += '<li data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Xoá"> <button  data-params=\'{"id":' + row.id + '}\' onclick="userTransactionPayment.delete(this)" data-bs-toggle="modal" class="btn btn-sm btn-soft-danger"><i class="mdi mdi-delete-outline"></i></button></li>';
            //         html += '</ul>';
            //         return html;
            //     }
            // },
        ];
        dataTable = base.getDataTableBasic("#data-table-list", arrColumns)
    },
    createQrUser: function () {
        $(".btn-create-qr-user").click(function () {
            $(".bs-modal-user-qrcode").modal("show");
        });

        $(".btn-user-qrcode-create").click(function () {
            bt = $(this);
            var originIClass = bt.find("i").prop("class");
            $.ajax({
                type: "POST",
                // dataType: 'json',
                // contentType: "application/json; charset=utf-8",
                url: urlAjaxAccountCreateQrPayment,
                data: {
                    amount: $(".bs-modal-user-qrcode input[name='amount']").val(),
                    remark: $(".bs-modal-user-qrcode input[name='remark']").val()
                },
                beforeSend: function () {
                    bt.find("i").removeClass(originIClass).addClass("fas fa-spinner fa-spin")
                },
                success: function (result) {
                    if (result.error_code != 0) {
                        return toastr["error"](result.message)
                    }

                    $(".bs-modal-user-qrcode .image-qrcode").html('<img src="data:image/png;base64,' + result.data.payment_qrcode_base64 + '"/>');
                    $(".bs-modal-user-qrcode .bank-text-info").html(' <div> <span>Ngân hàng: <span class="text-success fw-bold">' + result.data.bank.short_name + '</span></span> </div> <div> <span>Tên TK: <span class="fw-bold">' + result.data.bank.bank_account_name + '</span></span> </div> <div> <span>Số TK: <span class="text-info fw-bold">' + result.data.bank.bank_account_number + '</span></span>  </div><div> <span>Nội dung CK: <span class="text-info fw-bold">' + result.data.remark + '</span></span>  </div>');



                }, complete: function (result) {
                    bt.find("i").removeClass("fas fa-spinner fa-spin").addClass(originIClass)
                }
            });
        })
    },
    createQrTransaction: function () {
        $(".btn-create-qr-transaction").click(function () {
            $(".bs-modal-transaction-qrcode").modal("show");
        });

        $(".btn-transaction-qrcode-create").click(function () {
            bt = $(this);
            var originIClass = bt.find("i").prop("class");
            $.ajax({
                type: "POST",
                // dataType: 'json',
                // contentType: "application/json; charset=utf-8",
                url: urlAjaxTransactionCreateQrPayment,
                data: {
                    amount: $(".bs-modal-transaction-qrcode input[name='amount']").val(),
                },
                beforeSend: function () {
                    bt.find("i").removeClass(originIClass).addClass("fas fa-spinner fa-spin")
                },
                success: function (result) {
                    if (result.error_code != 0) {
                        return toastr["error"](result.message)
                    }

                    $(".bs-modal-transaction-qrcode .image-qrcode").html('<img src="data:image/png;base64,' + result.data.payment_qrcode_base64 + '"/>');
                    $(".bs-modal-transaction-qrcode .bank-text-info").html(' <div> <span>Ngân hàng: <span class="text-success fw-bold">' + result.data.bank.short_name + '</span></span> </div> <div> <span>Tên TK: <span class="fw-bold">' + result.data.bank.bank_account_name + '</span></span> </div> <div> <span>Số TK: <span class="text-info fw-bold">' + result.data.bank.bank_account_number + '</span></span>  </div><div> <span>Nội dung CK: <span class="text-info fw-bold">' + result.data.remark + '</span></span>  </div>');

                }, complete: function (result) {
                    bt.find("i").removeClass("fas fa-spinner fa-spin").addClass(originIClass)
                }
            });
        })
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
                        url: urlAjaxTransactionExportExcel,
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