var dataTable = null;
var categoryPost = {
    index: function () {
        this.getList();
        this.add();
        this.update();
        this.loadEditer("#description-add");
        this.loadEditer("#description-update");
    },
    getList: function () {
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
                className: "text-center",
                data: 'status_id', sortable: false,
                mRender: function (data, type, row) {
                    switch (row.status_id) {
                        case 1:
                            classColor = 'text-info';
                            break;
                        case 2:
                            classColor = 'text-success';
                            break;
                        case 3:
                            classColor = 'text-danger';
                            break;
                        case 4:
                            classColor = 'text-secondary';
                            break;
                        default:
                            classColor = 'text-warning';
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
            {
                className: "text-center",
                data: null,
                sortable: false,
                mRender: function (data, type, row) {

                    html = '';
                    html += '<ul class="list-unstyled hstack gap-1 mb-0">';
                    html += '<li data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Sửa"><button  data-id="' + row.id + '" onclick="categoryPost.detail(this)" class="btn btn-sm btn-soft-info"><i class="mdi mdi-pencil-outline"></i></button></li>';
                    html += '<li data-bs-toggle="tooltip" data-bs-placement="top" aria-label="Xoá"> <button data-id="' + row.id + '" onclick="categoryPost.delete(this)" data-bs-toggle="modal" class="btn btn-sm btn-soft-danger"><i class="mdi mdi-delete-outline"></i></button></li>';
                    html += '</ul>';
                    return html;
                }
            },
            { className: "text-center", data: 'id', sortable: false },
        ];
        dataTable = base.getDataTableBasic("#data-table-list", arrColumns, function (result) { })
    }, add: function () {
        base.submitForm(".form-add-category-post", function (result) {
            if (result.error_code != 0) {
                return toastr["error"](result.message)
            }
            if (dataTable !== null) {
                dataTable.ajax.reload(null, false)
            }
            return toastr["success"](result.message)
        });
    }, detail: function (ts) {
        /**
         * Dùng ajax lấy data
         */
        bt = $(ts);
        var id = bt.data("id");
        var originIClass = bt.find("i").prop("class");
        base.ajax(strUrlApiCategoryPostGetDetail, { query: { id: id } },
            function () {
                bt.find("i").removeClass(originIClass).addClass("fas fa-spinner fa-spin")
            },
            function (result) {
                if (result.error_code != 0) {
                    return toastr["error"](result.message)
                }
                var modal = $(".bs-modal-update");
                $.each(result.data.category_post, function (k, i) {
                    modal.find('input[name="' + k + '"]').val(i);
                    modal.find('textarea[name="' + k + '"]').val(i);
                    modal.find('select[name="' + k + '"]').val(i).change();
                    if (k == 'description') {
                        tinymce.get("description-update").setContent(i ?? "");
                    }
                })
                modal.modal("show", { backdrop: 'static', keyboard: false });
            },
            function (result) {
                bt.find("i").removeClass("fas fa-spinner fa-spin").addClass(originIClass)
            })

    }, update: function (ts) {
        base.submitForm(".form-update-category-post", function (result) {
            if (result.error_code != 0) {
                return toastr["error"](result.message)
            }
            if (dataTable !== null) {
                dataTable.ajax.reload(null, false)
            }
            $(".bs-modal-update").modal("hide")
            return toastr["success"](result.message)
        });
    }, delete: function (ts) {
        bt = $(ts);
        var id = bt.data("id");
        var originIClass = bt.find("i").prop("class");

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
                base.ajax(strUrlApiCategoryPostDelete, { id: id },
                    function () {
                        bt.find("i").removeClass(originIClass).addClass("fas fa-spinner fa-spin")
                    },
                    function (result) {
                        if (result.error_code != 0) {
                            return toastr["error"](result.message)
                        }
                        if (dataTable !== null) {
                            dataTable.ajax.reload(null, false)
                        }
                        return toastr["success"](result.message)
                    },
                    function (result) {
                        bt.find("i").removeClass("fas fa-spinner fa-spin").addClass(originIClass)
                    })
            }
        })

    }, loadEditer: function (tag) {
        $(document).ready(function () {
            0 < $(tag).length && tinymce.init({
                selector: "textarea" + tag,
                setup: function (editor) {
                    editor.on('change', function (e) {
                        editor.save();
                    });
                },
                // images_upload_url: 'postAcceptor.php',
                images_upload_handler: base.tinymceUploadImageHandler,
                height: 300,
                plugins: ["advlist autolink link image lists charmap print preview hr anchor pagebreak spellchecker", "searchreplace wordcount visualblocks visualchars code fullscreen insertdatetime media nonbreaking", "save table contextmenu directionality emoticons template paste textcolor"],
                toolbar: "insertfile undo redo | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media fullpage | forecolor backcolor emoticons",

            })
        });
    }
};