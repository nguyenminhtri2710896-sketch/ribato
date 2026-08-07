$.ajaxSetup({
    ajaxError: function () {
        return toastr["error"]("Có lỗi xảy ra")
    }
});

(function ($) {
    $.fn.formToArray = function () {
        var data = $(this).serializeArray();
        $("form input:checkbox").each(function () {
            data.push({ name: this.name, value: this.checked });
        });
        return data;
    };
})(jQuery);

var base = {
    getParamQueryToUrl: function () {
        url = window.location.href;
        var result = {}, t
        var search = url.split("?")[1] ?? ""
        $.each(search.split("&"), function (i, v) {
            t = v.split("=")
            if (t[0] != "") {
                result[decodeURIComponent(t[0])] = decodeURIComponent(t[1])
            }
        })
        return result
    },
    formatInputNumber: function () {
        $('.decimal-input').on('input', function () {
            // Lấy giá trị và loại bỏ dấu phẩy cũ
            var tagInput = $(this);
            var tagMirror = tagInput.next(tagInput.data('for'));
            let value = tagInput.val().replace(/,/g, '');
            let originValue = value;
            // Chỉ cho phép số và dấu chấm
            value = value.replace(/[^0-9.]/g, '');
            // Đảm bảo chỉ có một dấu chấm
            let parts = value.split('.');
            if (parts.length > 2) {
                value = parts[0] + '.' + parts.slice(1).join('');
            }
            // Định dạng phần số nguyên với dấu phẩy
            if (parts[0]) {
                parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            }
            // Gán lại giá trị
            tagInput.val(parts.join('.'));
            if (tagMirror.length > 0) {
                tagMirror.val(originValue);
            }
        });
    },
    getParamTrigerScript: function () {
        url = window.location.href;
        search = url.split("#")[1] ?? ""
        return search
    },
    ajaxSelect2: function () {
        $(".ajax-select2 .js-data-select2").each(function (i, t) {
            var urlAjax = $(t).data('ajax-url');
            var textPlaceholder = $(t).data('text-placeholder');
            var dataParams = $(t).data('params');
            if (typeof dataParams == 'undefined') {
                dataParams = {};
            }
            console.log(dataParams);

            $(t).select2({
                dropdownParent: $(t).parent(),
                ajax: {
                    url: urlAjax,
                    dataType: 'json',
                    type: "POST",
                    delay: 250,
                    data: function (params) {
                        return {
                            query: Object.assign({ name: params.term }, dataParams),
                            page: params.page
                        };
                    },
                },
                placeholder: textPlaceholder
            });
        });
    },
    baseSelect2: function () {
        $(".base-select2").each(function (i, t) {
            var textPlaceholder = $(t).data('text-placeholder');
            $(t).select2({
                dropdownParent: $(t).parent(),
                placeholder: textPlaceholder
            });
        })
    },
    signOut: function () {
        $.ajax({
            type: "POST",
            dataType: 'json',
            contentType: "application/json; charset=utf-8",
            url: urlAjaxSignOut,
            data: {},
            beforeSend: function () {
            },
            success: function (result) {
                window.location.href = urlDaskboard;
            }, complete: function (result) {

            }
        });
    },
    submitAjaxForm: function () {
        $(".frm-ajax-submit").each(function (i, t) {
            $(t).submit(function (event) {
                thisForm = $(this);
                event.preventDefault();
                var btnSubmit = thisForm.find('button[type="submit"]');
                var urlAjax = thisForm.data('ajax-url');
                var urlRedirect = thisForm.data('redirect-url');
                var iconButtonSubmit = btnSubmit.find("i");
                var iconButtonOriginal = iconButtonSubmit.attr("class")
                var reloadDataTable = thisForm.data('reload-datatable');
                var closeModal = thisForm.data('close-modal');
                if (thisForm.get(0).checkValidity()) {
                    $.ajax({
                        type: "POST",
                        dataType: 'json',
                        url: urlAjax,
                        data: thisForm.serialize(),
                        beforeSend: function () {
                            btnSubmit.prop('disabled', true);
                            iconButtonSubmit.attr("class", "fas fa-spinner fa-spin");
                        },
                        success: function (result) {
                            if (result.error_code != 0) {
                                toastr["error"](result.message)
                                return false
                            }

                            toastr["success"](result.message)
                            if (reloadDataTable) {
                                $(reloadDataTable).DataTable().draw();
                                // dataTable.ajax.reload(null, false)
                            }

                            if (closeModal) {
                                $(closeModal).modal('hide');
                            }

                            if (typeof result?.data?.url_redirect != 'undefined') {
                                setTimeout(() => {
                                    window.location = result.data.url_redirect;
                                }, 1000);
                                return false;
                            }

                            if (urlRedirect) {
                                setTimeout(() => {
                                    window.location = urlRedirect;
                                }, 1000);
                            }
                            return false;
                        }, complete: function () {
                            btnSubmit.prop('disabled', false);
                            iconButtonSubmit.attr("class", iconButtonOriginal);
                        }
                    });
                }
            });
        })
    },
    getDataTableBasic: function (tag, arrColumns) {




        var tagList = $(tag);
        var urlAjax = tagList.data('ajax-url');
        var objKeyList = tagList.data('key');
        var dataParams = $(tag).data('params');
        var tagFilter = $(tag).data('id-filter');
        var objHederView = tagList.data('header-view');

        if (typeof dataParams == 'undefined') {
            dataParams = {};
        }

        if (typeof tagFilter != 'undefined') {
            $('#' + tagFilter).on("submit", function (event) {
                event.preventDefault();
                tagList.DataTable().draw();
            });
            $('.dropdown-menu').on('click', function (e) {
                e.stopPropagation();
            });
        }


        return tagList.DataTable({
            sDom: 'Rfrtlip',
            searching: false,
            serverSide: true,
            processing: true,
            lengthChange: true,
            rowReorder: {
                dataSrc: 'tr',
                selector: 'td.reorder',
            },
            language: {
                emptyTable: "Không có dữ liệu",
                paginate: {
                    previous: "<",
                    next: ">"
                },
                info: "Đang hiển thị _START_ đến _END_ trên tổng _TOTAL_ mục",
                infoEmpty: ""
            },
            columnDefs: [
                {
                    targets: 0,
                    className: 'reorder',
                    createdCell: function (td, cellData, rowData) {
                        var selector = $(td).parents('tr').find('.reorder');
                        selector.addClass('reorder-disabled');
                        selector.removeClass('reorder');
                    }
                }
            ],
            responsive: {
                details: {
                    display: $.fn.dataTable.Responsive.display.childRowImmediate,
                    target: '',
                    type: ''
                }
            },
            ajax: function (data, callback, settings) {
                /**
                 * Define sort
                 */
                var sort = {}
                $.each(data.order, function (n, i) {
                    sort[data.columns[i.column].data] = i.dir;
                });
                /**
                 * Request data
                 */
                var query = dataParams
                $.each(base.getParamQueryToUrl(), function (k, v) {
                    query[k] = v;
                })

                if ($('#' + tagFilter).length > 0) {
                    if ($('#' + tagFilter).serializeArray()) {
                        $.each($('#' + tagFilter).serializeArray(), function (n, i) {
                            query[i.name] = i.value;
                        })
                    }
                }

                $.ajax({
                    type: "POST",
                    dataType: 'json',
                    url: urlAjax,
                    data: {
                        limit: data.length,
                        page: (data.start + data.length) / data.length,
                        sort: sort,
                        query: query
                    },
                    beforeSend: function () {

                    },
                    success: function (result) {
                        if (result.error_code !== 0) {
                            toastr["error"](result.message)
                        } else {
                            var result_data = []
                            var rowData = [];
                            $.each(result.data[objKeyList], function (n, i) {
                                i["row"] = n + 1 + data.start;
                                if (typeof result.data.status != "undefined") {
                                    i["status"] = result.data.status;
                                }

                                if (typeof result.data.type != "undefined") {
                                    i["type"] = result.data.type;
                                }

                                if (typeof result.data.gateway != "undefined") {
                                    i["gateway"] = result.data.gateway;
                                }

                                result_data.push(i)
                                if (typeof i.id != "undefined") {
                                    rowData[i.id] = i;
                                }
                            });
                            if (
                                result.data &&
                                result.data[objHederView] &&
                                typeof result.data[objHederView] === 'object' &&
                                Object.keys(result.data[objHederView]).length > 0
                            ) {
                                $.each(result.data[objHederView], function (n, i) {
                                    if (base.isNumeric(i)) {
                                        $("thead th." + n + " span.val").html($.number(i) + "<sup>đ</sup>");
                                    } else {
                                        $("thead th." + n + " span.val").html(i);
                                    }
                                });
                            }

                            callback({
                                recordsTotal: result.data.records_total,
                                recordsFiltered: result.data.records_total,
                                data: result_data
                            });
                        }
                    }, complete: function () {
                        $('.dataTables_paginate').addClass('pagination-rounded justify-content-end mb-2');
                    }
                });

            },
            order: [
            ],
            columns: arrColumns
        });
    },
    buttonAjax: function (urlAjax, tag, successCallback) {
        bt = $(tag);

        var params = {}
        if (bt.data("params")) {
            params = bt.data("params");
        }
        var reloadDataTable = bt.data('reload-datatable');
        var originIClass = bt.find("i").prop("class");
        $.ajax({
            type: "POST",
            dataType: 'json',
            contentType: "application/json; charset=utf-8",
            url: urlAjax,
            data: JSON.stringify(params),
            beforeSend: function () {
                bt.find("i").removeClass(originIClass).addClass("fas fa-spinner fa-spin")
            },
            success: function (result) {
                if (reloadDataTable) {
                    $(reloadDataTable).DataTable().draw();
                }
                successCallback(result);
            }, complete: function (result) {
                bt.find("i").removeClass("fas fa-spinner fa-spin").addClass(originIClass)
            }
        });
    },
    getBalance: function () {
        if (window.isTabActive == false) {
            setTimeout(() => {
                base.getBalance();
            }, 10000);
            return;
        }
        $.ajax({
            type: "POST",
            url: urlAjaxAccountGetBalance,
            data: {
                'router_name': routerName
            },
            beforeSend: function () {

            },
            success: function (result) {
                if (result.error_code == 0) {
                    $(".txt-header-balance").html($.number(result.data.user_balance.balance) + "<sup>đ</sup>");
                    txtNote = "";
                    $.each(result.data.user_fees, function (n, i) {
                        txtNote += i.note + "<br/>";
                    });

                    $('.header-item-balance')
                        .attr('data-bs-content', txtNote)
                        .popover('dispose')  // Xoá popover cũ
                        .popover({
                            trigger: 'click',
                            html: true
                        });

                   

                }

            }, complete: function () {
                setTimeout(() => {
                    base.getBalance();
                }, 10000);
            }
        });
    }, basicImageUpload: function () {
        if ($(".basic-image-upload").length > 0) {
            $(".basic-image-upload").each(function (i, tag) {
                var btnUpload = $(tag);
                if (btnUpload.length <= 0) {
                    return false;
                }
                var url = btnUpload.data("url");
                var tagLoading = btnUpload.find('.loading');
                var tagInput = btnUpload.find('input.image');
                var tagInputCode = btnUpload.find('input.code');
                var tagErrorMessage = btnUpload.find('.error-message');
                var tagContentSuccess = btnUpload.find('.content-success');
                new ss.SimpleUpload({
                    button: btnUpload,
                    url: url,
                    name: 'file_image',
                    allowedExtensions: ["jpg", "jpeg", "png", "gif"],
                    multipart: true,
                    resize_image: {
                        w: 800
                    },
                    hoverClass: 'hover',
                    focusClass: 'focus',
                    responseType: 'json',
                    startXHR: function () {
                        tagLoading.show();
                    },
                    onExtError: function (name, ex) {
                        tagErrorMessage.html("Định dạng file không hợp lệ");
                        tagErrorMessage.show();
                    },
                    onSubmit: function () {

                    },
                    onComplete: function (filename, response) {
                        tagErrorMessage.html("");
                        tagLoading.hide();
                        if (response.error_code != 0) {
                            tagErrorMessage.html(response.message);
                            return;
                        }
                        tagContentSuccess.find("img").attr('src', 'data:image/png;base64,' + response.data.image_base64);
                        tagContentSuccess.show();
                        tagInput.val(response.data.file_path);
                        tagInputCode.val(response.data.image_code);
                    },
                    onError: function () {
                        tagLoading.hide();
                    }
                });
            })
        }
    }
    , removeVietnameseTones: function (str) {
        return str.normalize("NFD")
            .replace(/[\u0300-\u036f]/g, "")
            .replace(/đ/g, "d").replace(/Đ/g, "D");
    }, isNumeric: function (str) {
        str = String(str); // ép kiểu về chuỗi
        return !isNaN(str) && str.trim() !== '';
    }, copyClipboard: function (classCopy) {
        var text = $('.' + classCopy).val();
        navigator.clipboard.writeText(text).then(function () {
            toastr["success"]('Copied: ' + text)
        }, function (err) {
            toastr["error"]("Lỗi:" + err)
        });
    }
};

