var user = {
    index: function () {
        this.getList();
        this.exportExcel();
    },
    getProfile: function (id) {
        $.ajax({
            type: "POST",
            url: urlAjaxUserDetail,
            data: {
                "query[id]": id
            },
            beforeSend: function () {

            },
            success: function (result) {
                if (result.error_code != 0) {
                    return toastr["error"](result.message)
                }
                profileInfo = result.data;
                if ($(".get-profile-info").length > 0) {
                    $.each(result.data.user, function (key, value) {
                        $(".get-profile-info").find("input[name='" + key + "']").val(value);
                        $(".get-profile-info").find("select[name='" + key + "']").val(value).change();
                    });

                    if ($(".get-profile-info").find(".user-name").length > 0) {
                        $(".get-profile-info").find(".user-name").html(result.data.user.first_name + " " + result.data.user.last_name);
                    }
                    if ($(".get-profile-info").find(".user-image-cover").length > 0) {
                        if (result.data.user.image_cover != "") {
                            $(".get-profile-info").find(".user-image-cover").css('background-image', 'url(' + result.data.user.image_cover + ')');
                        }
                    }

                    if ($(".get-profile-info").find(".user-image-avatar").length > 0) {
                        if (result.data.user.image_avatar != "") {
                            $(".get-profile-info").find(".user-image-avatar").attr('src', result.data.user.image_avatar);
                        }
                    }



                    if (result.data.user_group) {
                        if ($(".get-profile-info").find("select[name='user_group_id']").length > 0) {
                            const newOption = new Option(result.data.user_group.name, result.data.user_group.id, false, false);
                            $(".get-profile-info").find("select[name='user_group_id']").append(newOption).trigger('change');
                        }

                        if ($(".get-profile-info").find(".user-group").length > 0) {
                            $(".get-profile-info").find(".user-group").html(result.data.user_group.name);
                        }
                    }


                    if (result.data.user_parent) {
                        if ($(".get-profile-info").find("select[name='user_parent_id']").length > 0) {
                            const newOption = new Option(result.data.user_parent.first_name + " " + result.data.user_parent.email, result.data.user_parent.id, false, false);
                            $(".get-profile-info").find("select[name='user_parent_id']").append(newOption).trigger('change');
                        }
                    }


                    const $select_gateway_withdraw_ids = $(".get-profile-info").find("select[name='gateway_withdraw_ids[]']");
                    if ($select_gateway_withdraw_ids.length > 0 && result.data.gateway_account_withdraws) {
                        $.each(result.data.gateway_account_withdraws, function (i, n) {
                            $select_gateway_withdraw_ids.append(new Option(n.name, n.id, true, true)).trigger('change');
                        });
                        $select_gateway_withdraw_ids.trigger('change.select2');
                    }

                    const $select_gateway_collection_ids = $(".get-profile-info").find("select[name='gateway_collection_ids[]']");
                    if ($select_gateway_collection_ids.length > 0 && result.data.gateway_account_collections) {
                        $.each(result.data.gateway_account_collections, function (i, n) {
                            $select_gateway_collection_ids.append(new Option(n.name, n.id, true, true)).trigger('change');
                        });
                        $select_gateway_collection_ids.trigger('change.select2');
                    }

                    if (result.data.user_withdraw_limit) {
                        var withdrawLimit = result.data.user_withdraw_limit;
                        var $frmLimit = $(".user-form-submit-withdraw-limit");
                        if ($frmLimit.length > 0) {
                            $frmLimit.find("input[name='withdraw_limit_in_day']").val(withdrawLimit.withdraw_limit_in_day || 0);
                            $frmLimit.find("input[name='withdraw_limit_bank_account_in_day']").val(withdrawLimit.withdraw_limit_bank_account_in_day || 0);
                            $frmLimit.find("input[name='lock_withdraw']").prop('checked', parseInt(withdrawLimit.lock_withdraw) === 1);

                            var $selectAllowDay = $frmLimit.find("select[name='allow_withdraw_day[]']");
                            if ($selectAllowDay.length > 0) {
                                var arrAllowDay = [];
                                if (withdrawLimit.allow_withdraw_day) {
                                    arrAllowDay = String(withdrawLimit.allow_withdraw_day).split(',').filter(function (v) { return v !== ''; });
                                }
                                $selectAllowDay.val(arrAllowDay).trigger('change');
                            }
                        }
                    }


                    // if (result.data.gateway_accounts) {
                    //     if ($(".get-profile-info").find("select[name='gateway_withdraw_ids[]']").length > 0) {
                    //         $.each(result.data.gateway_accounts, function (i, n) {
                    //             console.log(n);
                    //             const newOption = new Option(n.name, n.id, false, false);
                    //             $(".get-profile-info").find("select[name='gateway_withdraw_ids[]']").trigger('change');
                    //         });
                    //     }
                    // }
                    // if (result.data.user_fees) {
                    //     if ($("#table-fee-list .fee-list").length > 0) {
                    //         var html = '';
                    //         $.each(result.data.user_fees, function (i, n) {
                    //             var status = n.status_id == 2 ? '<span class="bg-success badge">Hoạt động</span>' : '<span class="bg-danger badge">Đang bảo trì</span>';
                    //             var type = n.type_id == 1 || n.type_id == 2 ? '<span class="bg-success badge">Phí giá trị</span>' : '<span class="bg-warning badge">Phí %</span>';
                    //             var feeCode = n.type_id == 1 || n.type_id == 2 ? '<sup>đ</sup>' : '%';
                    //             html += '<tr><td>' + n.name + '</td><td>' + type + '</td><td>' + n.fee + feeCode + '</td><td>' + $.number(n.min_fee) + '<sup>đ</sup></td><td>' + status + '</td><td><button class="btn btn-sm btn-soft-info d-flex"><i class="mdi mdi-pencil-outline"></i> sửa</button></td></tr>';
                    //         });
                    //         $("#table-fee-list .fee-list").html(html);
                    //     }
                    // }
                }


            }, complete: function () {

            }, error: function () {

            }
        });
    },
    getList: function () {
        var arrColumns = [
            { data: 'row', sortable: false },
            {
                data: 'fullname', sortable: false,
                mRender: function (data, type, row) {
                    var textForControl = "NULL";
                    if (row.for_control_type == 3) {
                        textForControl = "N+0";
                    }

                    if (row.for_control_type == 4) {
                        textForControl = "N+" + row.number_day_for_control + " Chốt 15h30";
                    }

                    if (row.for_control_type == 1) {
                        textForControl = "N+" + row.number_day_for_control + " Chốt 2h";
                    }
                    return data + '<br/><span class="bg-success badge">' + textForControl + '</span>'
                }
            },
            { data: 'phone', sortable: false },
            { data: 'email', sortable: false },
            {
                className: "text-center-desktop",
                data: 'balance', sortable: false,
                mRender: function (data, type, row) {

                    return '<span class="text-success">' + $.number(row.balance) + '<sup>đ</sup></span>'
                }
            },
            {
                className: "text-center-desktop",
                data: 'user_balance_n1', sortable: false,
                mRender: function (data, type, row) {

                    return '<span class="text-success">' + $.number(row.user_balance_n1) + '<sup>đ</sup></span>'
                }
            },
            {
                className: "text-center-desktop",
                data: 'user_balance_n2', sortable: false,
                mRender: function (data, type, row) {

                    return '<span class="text-success">' + $.number(row.user_balance_n2) + '<sup>đ</sup></span>'
                }
            },
            {
                className: "text-center-desktop",
                data: 'user_balance_n3  ', sortable: false,
                mRender: function (data, type, row) {

                    return '<span class="text-success">' + $.number(row.user_balance_n3) + '<sup>đ</sup></span>'
                }
            },
            {
                className: "text-center-desktop",
                data: 'actived', sortable: false,
                mRender: function (data, type, row) {
                    if (row.actived == 1) {
                        return '<span class="bg-success badge">kích hoạt</span>'
                    }
                    return '<span class="bg-danger badge">ngừng kích hoạt</span>'
                }
            },
            {
                className: "text-center-desktop",
                data: 'created_at',
                sortable: true,
                mRender: function (data, type, row) {
                    return $.format.date(row.created_at, "dd/MM/yyyy HH:mm:ss");
                }
            },
            {
                className: "text-center-desktop",
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
                    if (typeof isAccountant !== "undefined" && isAccountant == 0) {
                        html += '<li data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Sửa"><a href="/user/detail?user_id=' + row.id + '" class="btn btn-sm btn-soft-info d-flex"  data-reload-datatable="#data-table-list"><i class="mdi mdi-pencil-outline"></i> sửa</a></li>';
                        html += '<li data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Xoá"> <button  data-params=\'{"id":' + row.id + '}\' onclick="user.delete(this)" data-reload-datatable="#data-table-list" class="btn btn-sm btn-soft-danger d-flex"><i class="mdi mdi-delete-outline"></i> xoá</button></li>';
                        html += '<li data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Xoá"> <a href="/user-bank-account/index?user_id=' + row.id + '" class="btn btn-sm btn-soft-info d-flex"><i class="mdi mdi-card-bulleted"></i> Bank</a></li>';
                    }
                    html += '</ul>';
                    return html;
                }
            },
        ];
        base.getDataTableBasic("#data-table-list", arrColumns)
    }, getdetail: function (ts) {
        base.buttonAjax(urlAjaxUserDetail, ts,
            function (result) {
                if (result.error_code != 0) {
                    return toastr["error"](result.message)
                }

                var modal = $(".bs-modal-update");
                $.each(result.data.user, function (k, i) {
                    modal.find('[name="' + k + '"]').val(i).change();
                });

                if (result.data.user_parent) {
                    modal.find('select[name="user_parent_id"]').html('');
                    dataSelect = new Option(result.data.user_parent.fullname + " " + result.data.user.email, result.data.user_parent.id, true);
                    modal.find('select[name="user_parent_id"]').append(dataSelect).trigger('change');
                }

                if (result.data.user_group) {
                    modal.find('select[name="group_id"]').html('');
                    dataSelect = new Option(result.data.user_group.name, result.data.user_group.id, true);
                    modal.find('select[name="group_id"]').append(dataSelect).trigger('change');
                }

                if (result.data.gateway_accounts) {
                    modal.find('select[name="gateway_withdraw_ids[]"]').html('');
                    dataSelect = new Option(result.data.gateway_accounts.name, result.data.gateway_accounts.id, true);
                    modal.find('select[name="gateway_withdraw_ids[]"]').append(dataSelect).trigger('change');
                }

                modal.modal("show", { backdrop: 'static', keyboard: false });
            });
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
                base.buttonAjax(urlAjaxUserDelete, ts,
                    function (result) {
                        if (result.error_code != 0) {
                            return toastr["error"](result.message)
                        }
                        return toastr["success"](result.message)
                    })
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
                        if (i.name && i.name.endsWith('[]')) {
                            query[i.name.replace("[]", "")] = $('[name="' + i.name + '"]').val();
                        } else {
                            query[i.name] = i.value;
                        }
                    })
                }
            }
            var btnSubmit = $(this);
            var originIClass = btnSubmit.find("i").prop("class");
            Swal.fire({
                html: "Bạn có chắc muốn xuất danh sách người dùng?<br/> <strong style='color:red'>Lưu ý</strong> Dữ liệu sẽ xuất dựa trên bộ lọc tìm kiếm.",
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
                        url: urlAjaxUserExportExcel,
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