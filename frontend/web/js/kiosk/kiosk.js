$('a.activity-print').on('click', function (e) {
    e.preventDefault();
    var url = $(this).attr('href'),
        title = $(this).attr('title');
    var table = $('#tb-que-list').DataTable();
    swal({
        title: 'พิมพ์บัตรคิว?',
        text: title,
        type: 'warning',
        showCancelButton: true,
        confirmButtonText: 'พิมพ์',
        cancelButtonText: "ยกเลิก",
        allowEscapeKey: false,
        allowOutsideClick: false,
        showLoaderOnConfirm: true,
        preConfirm: function () {
            return new Promise(function (resolve, reject) {
                $.ajax({
                    type: 'POST',
                    url: url,
                    success: function (response, textStatus, jqXHR) {
                        window.open(response.url, "myPrint", "width=800, height=600");
                        table.ajax.reload();
                        socket.emit('register', response); //sending data
                        resolve();
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        swal({
                            type: "error",
                            title: textStatus,
                            text: errorThrown,
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
            swal.close();
        }
    });
});

//Socket Events
$(function () {
    socket.on('register', (res) => { //ออกบัตรคิว
        var table = $('#tb-que-list').DataTable();
        table.ajax.reload();
        toastr.warning('#' + res.modelQue.que_num, 'คิวใหม่!', {
            "timeOut": 7000,
            "positionClass": "toast-top-right",
            "progressBar": true,
            "closeButton": true,
        });
    });
});