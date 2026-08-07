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
                    if (row.status_id == 2) {
                        // html += '<br/><button type="button" class="btn btn-sm btn-outline-primary mt-1" onclick="userWithdraw.showBill(' + row.id + ')"><i class="fas fa-file-invoice"></i> Xem bill</button>';
                    }
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

        if (isAdmin || isFullAccess) {
            arrColumns.push({
                className: "text-center",
                data: null, sortable: false,
                mRender: function (data, type, row) {
                    html = '';
                    html += '<ul class="list-unstyled hstack d-inline-flex gap-1 mb-0">';
                    if (row.status_id != 3 && row.status_id != 2) {
                        html += '<li data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Sửa"><button  data-params=\'{"query":{"id":' + row.id + '}}\'  onclick="userWithdraw.getDetail(this)"  type="button" class="btn btn-sm btn-warning"><i class="far fa-eye"></i> Chi tiết</button>';
                    }
                    html += '</ul>';
                    return html;
                }
            });
        }

        base.getDataTableBasic("#data-table-user-withdraw-list", arrColumns)
    }, getDetail: function (ts) {
        base.buttonAjax(urlAjaxUserWithdrawDetail, ts,
            function (result) {
                if (result.error_code != 0) {
                    return toastr["error"](result.message)
                }

                console.log(result.data.user_withdraw.status_id);
                if (result.data.user_withdraw.status_id == 3 || result.data.user_withdraw.status_id == 2) {
                    return toastr["error"]("Giao dịch đã xử lý vui lòng không xử lý lại")
                }


                var modal = $(".bs-modal-user-withdraw-detail");
                $.each(result.data.user_withdraw, function (k, i) {
                    modal.find('[name="' + k + '"]').val(i).change();
                });
                $(".info-tranfer .bank").html(result.data.user_withdraw.bank_short_name);
                $(".info-tranfer .bank-account").html(result.data.user_withdraw.bank_account_name);
                $(".info-tranfer .bank-number").html(result.data.user_withdraw.bank_account_number);

                modal.modal("show", { backdrop: 'static', keyboard: false });
            });
    }, addMultibleCheck: function (ts) {
        bt = $(ts);
        var originIClass = bt.find("i").prop("class");
        $.ajax({
            type: "POST",
            url: urlAjaxAddMultibleCheck,
            data: {
                'content': $("textarea[name='content']").val()
            },
            beforeSend: function () {
                bt.find("i").removeClass(originIClass).addClass("fas fa-spinner fa-spin")
            },
            success: function (result) {
                if (result.error_code != 0) {
                    return toastr["error"](result.message)
                }

                $(".txt-content table tbody").html("");
                $.each(result.data, function (n, i) {
                    $(".txt-content table tbody").append("<tr><td>" + (n + 1) + "</td><td>" + i.bank_short_name + "</td><td>" + i.bank_account_number + "</td><td>" + i.bank_account_name + "</td> <td>" + $.number(i.amount, 0) + "</td><td>" + i.remark + "</td></tr>")
                })

            }, complete: function () {
                bt.find("i").removeClass("fas fa-spinner fa-spin").addClass(originIClass)
            }
        });
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
    showBill: function (id) {
        var modal = $(".bs-modal-user-withdraw-bill");
        var img = modal.find(".bill-image");
        var loading = modal.find(".bill-loading");
        var downloadBtn = modal.find(".btn-download-bill");

        img.attr("src", "").addClass("d-none");
        loading.removeClass("d-none");
        modal.modal("show");

        $.ajax({
            type: "GET",
            url: urlAjaxUserWithdrawCreateBill,
            data: { id: id },
            success: function (result) {
                if (result.error_code != 0) {
                    modal.modal("hide");
                    return toastr["error"](result.message);
                }
                if (result.data && result.data.url) {
                    img.attr("src", result.data.url).removeClass("d-none");
                    downloadBtn.attr("href", result.data.url);
                    loading.addClass("d-none");
                } else {
                    modal.modal("hide");
                    toastr["error"]("Không tìm thấy thông tin hóa đơn.");
                }
            },
            error: function () {
                modal.modal("hide");
                toastr["error"]("Đã xảy ra lỗi khi lấy thông tin hóa đơn.");
            }
        });
    },
};