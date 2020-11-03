dtFunc = {
    initConfirm: function (e) {
        $(e + ' tbody').on('click', 'tr td a[data-method="post"]', function (event) {
            event.preventDefault();
            var elm = this;
            var url = $(elm).attr('href');
            var table = $('#' + $(this).closest('table').attr('id')).DataTable();
            var message = $(elm).attr('data-confirm');
            if (typeof url !== typeof undefined && url !== false || typeof url !== typeof undefined && url !== false) {
                swal({
                    title: '' + message + '',
                    text: "",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonText: "ยืนยัน",
                    cancelButtonText: "ยกเลิก",
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    showLoaderOnConfirm: true,
                    preConfirm: function () {
                        return new Promise(function (resolve, reject) {
                            $.ajax({
                                type: $(elm).attr('data-method'),
                                url: $(elm).attr('href'),
                                success: function (data, textStatus, jqXHR) {
                                    table.ajax.reload();
                                    swal({
                                        type: "success",
                                        title: "Deleted!",
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                    resolve();
                                },
                                error: function (jqXHR, textStatus, errorThrown) {
                                    swal({
                                        type: "error",
                                        title: errorThrown,
                                        showConfirmButton: false,
                                        timer: 1500
                                    });
                                },
                                dataType: "json"
                            });
                        });
                    },
                }).then((result) => {
                    if (result.value) {
                        swal({
                            type: "success",
                            title: "Deleted!",
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                });
            }
            return false;
        });
    },
};