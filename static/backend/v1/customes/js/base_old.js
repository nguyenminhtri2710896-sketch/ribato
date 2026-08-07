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
    getParamTrigerScript: function () {
        url = window.location.href;
        search = url.split("#")[1] ?? ""
        return search
    },
    submitForm: function (tag, functionCallback) {
        $(tag).submit(function (event) {
            thisForm = $(this);
            event.preventDefault();
            var btnSubmit = thisForm.find('button[type="submit"]');
            var urlAjax = thisForm.data('ajax-url');
            var iconButtonSubmit = btnSubmit.find("i");
            var iconButtonOriginal = iconButtonSubmit.attr("class")
            if (thisForm.get(0).checkValidity()) {
                $.ajax({
                    type: "POST",
                    headers: { 'Authorization': "Bearer " + Cookies.get('access_token') },
                    dataType: 'json',
                    url: urlAjax,
                    data: thisForm.serialize(),
                    beforeSend: function () {
                        btnSubmit.prop('disabled', true);
                        iconButtonSubmit.attr("class", "fas fa-spinner fa-spin");
                    },
                    success: function (result) {
                        functionCallback(result);
                    }, complete: function () {
                        btnSubmit.prop('disabled', false);
                        iconButtonSubmit.attr("class", iconButtonOriginal);
                    }
                });
            }
        });
    },
    checkSession: function () {
        $.ajax({
            type: "POST",
            headers: { 'Authorization': "Bearer " + Cookies.get('access_token') },
            dataType: 'json',
            url: strUrlApiAuthCheckToken,
            beforeSend: function () {
            },
            success: function (result) {
                if (result.error_code != "0") {
                    if (window.location.href != urlSignIn) {
                        window.location.href = urlSignIn;
                    }
                    return false;
                }
                if (window.location.href == urlSignIn) {
                    window.location.href = urlDaskboard;
                }

                $("body").show();
                $(".header-profile-user-avatar").attr('src', result.data.user.image_avatar);
                $(".header-profile-user-fullname").html(result.data.user.fullname);
                if (result.data.user_balance) {
                    $(".header-item-balance span").html(result.data.user_balance.balance + "đ");
                }

                $("#sidebar-menu .user").show();
                if (result.data.user_group.full_access) {
                    $("#sidebar-menu .admin").show();
                }

                return true;
            }, complete: function () {
                setTimeout(() => {
                    base.checkSession()
                }, 5000);
            }
        });
    },
    ajax: function (urlAjax, data, functionBeforeSend, functionCallback, functionComplete) {
        $.ajax({
            type: "POST",
            headers: { 'Authorization': "Bearer " + Cookies.get('access_token') },
            dataType: 'json',
            contentType: "application/json; charset=utf-8",
            url: urlAjax,
            data: JSON.stringify(data),
            beforeSend: function () {
                functionBeforeSend;
            },
            success: function (result) {
                functionCallback(result);
            }, complete: function (result) {
                functionComplete(result);
            }
        });
    },
    buttonAjax: function (urlAjax, tag, params, successCallback) {
        bt = $(tag);

        if (bt.data("params")) {
            params = bt.data("params");
        }
        var originIClass = bt.find("i").prop("class");
        $.ajax({
            type: "POST",
            headers: { 'Authorization': "Bearer " + Cookies.get('access_token') },
            dataType: 'json',
            contentType: "application/json; charset=utf-8",
            url: urlAjax,
            data: JSON.stringify(params),
            beforeSend: function () {
                bt.find("i").removeClass(originIClass).addClass("fas fa-spinner fa-spin")
            },
            success: function (result) {
                successCallback(result);
            }, complete: function (result) {
                bt.find("i").removeClass("fas fa-spinner fa-spin").addClass(originIClass)
            }
        });
    },
    signOut: function () {
        base.ajax(strUrlApiAuthSignOut, null, null, function (result) {
            if (result.error_code != 0) {
                return toastr["error"](result.message)
            }
            window.location.href = urlSignIn;
        }, function (result) { })
    },
    getDataTableBasic: function (tag, arrColumns, functionCallback = null) {
        var tagList = $(tag);
        var urlAjax = tagList.data('ajax-url');
        var objKeyList = tagList.data('key');
        var dataParams = $(tag).data('params');
        if (typeof dataParams == 'undefined') {
            dataParams = {};
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
            responsive: false,
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

                if ($('#table-filter').length > 0) {
                    if ($('#table-filter').serializeArray()) {
                        $.each($('#table-filter').serializeArray(), function (n, i) {
                            query[i.name] = i.value;
                        })
                    }
                }

                $.ajax({
                    type: "POST",
                    headers: { 'Authorization': "Bearer " + Cookies.get('access_token') },
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
                                    if (typeof result.data.status != "undefined") {
                                        i["status"] = result.data.status;
                                    }

                                    if (typeof result.data.type != "undefined") {
                                        i["type"] = result.data.type;
                                    }
                                }
                                result_data.push(i)
                                if (typeof i.id != "undefined") {
                                    rowData[i.id] = i;
                                }
                            });

                            functionCallback(rowData);
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
    getDataSelect2: function (tag) {
        if ($(tag).length > 0) {
            var urlAjax = $(tag).data('ajax-url');
            var textPlaceholder = $(tag).data('text-placeholder');
            var dataParams = $(tag).data('params');
            console.log(dataParams);
            if (typeof dataParams == 'undefined') {
                dataParams = {};
            }

            $(tag).select2({
                dropdownParent: $(tag).parent(),
                ajax: {
                    url: urlAjax,
                    headers: { 'Authorization': "Bearer " + Cookies.get('access_token') },
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
        }
    }, tinymceUploadImageHandler: function (blobInfo, success, failure, progress) {
        var xhr, formData;

        xhr = new XMLHttpRequest();
        xhr.withCredentials = false;
        xhr.open('POST', strUrlApiUploadImage);
        xhr.setRequestHeader('Authorization', "Bearer " + Cookies.get('access_token'))


        xhr.upload.onprogress = function (e) {
            progress(e.loaded / e.total * 100);
        };

        xhr.onload = function () {
            var json;

            if (xhr.status === 403) {
                failure('HTTP Error: ' + xhr.status, { remove: true });
                return;
            }

            if (xhr.status < 200 || xhr.status >= 300) {
                failure('HTTP Error: ' + xhr.status);
                return;
            }

            json = JSON.parse(xhr.responseText);
            if (json.error_code != 0) {
                failure('Invalid JSON: ' + json.message);
            }

            success(assetUrl + json.data.file_path);
        };

        xhr.onerror = function () {
            failure('Image upload failed due to a XHR Transport error. Code: ' + xhr.status);
        };

        formData = new FormData();
        formData.append('file_image', blobInfo.blob(), blobInfo.filename());
        xhr.send(formData);
    },
    uploadImage: function (tag) {
        var btnUpload = $(tag);
        if (btnUpload.length <= 0) {
            return false;
        }

        var url = btnUpload.data('url');
        if (typeof url == "undefined") {
            url = strUrlApiUploadImage;
        }
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
            authorization: "Bearer " + Cookies.get('access_token'),
            // resize_image: {
            //     w: 600
            // },
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

    },

};


