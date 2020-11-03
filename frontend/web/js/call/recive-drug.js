keySelected = [];

Que = {
    handleClick: function () {
        var self = this;
        //รายการคิวรอเรียก
        $('#tb-que-waiting tbody').on('click', 'tr td a', function (event) {
            event.preventDefault();
            var tr = $(this).closest("tr"),
                url = $(this).attr("data-url"),
                table = $('#tb-que-waiting').DataTable();
            if (tr.hasClass("child") && typeof table.row(tr).data() === "undefined") {
                tr = $(this).closest("tr").prev();
            }
            var key = tr.data("key"); //que_ids
            var data = table.row(tr).data();
            var counter_name = self.getCounterName();
            if($(this).hasClass('activity-call') && counter_name !== 'จุดบริการ'){
                swal({
                    title: 'ยืนยันเรียกคิว ' + data.que_num + ' ?',
                    text: counter_name,
                    html: '<small class="text-danger" style="font-size: 13px;">กด Enter เพื่อยืนยัน / กด Esc เพื่อยกเลิก</small><p>' + counter_name + '</p>',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'เรียกคิว',
                    cancelButtonText: 'ยกเลิก',
                    allowOutsideClick: false,
                    showLoaderOnConfirm: true,
                    preConfirm: function () {
                        return new Promise(function (resolve, reject) {
                            $.ajax({
                                method: "POST",
                                url: baseUrl + url,
                                dataType: "json",
                                data: {
                                    data: data, //Data in column Datatable
                                    modelProfile: modelProfile,
                                    formData: formData,
                                },
                                success: function (response) {
                                    $('li#tabs-1, div#tab-1').removeClass('active');
                                    $('li#tabs-2, div#tab-2').addClass('active');
                                    self.reloadTableWaiting();
                                    self.reloadTableCalling();
                                    socket.emit('call-recive-drug', response); //sending data
                                    resolve();
                                },
                                error: function (jqXHR, textStatus, errorThrown) {
                                    self.ajaxAlertError(textStatus, errorThrown);
                                }
                            });
                        });
                    },
                }).then((result) => {
                    if (result.value) { //Confirm
                        swal.close();
                    }
                });
            }else if ($(this).hasClass('activity-recheck') && counter_name !== 'จุดบริการ') {
                swal({
                    title: 'Recheck ' + data.que_num + ' ?',
                    text: '',
                    html: '<small class="text-danger" style="font-size: 13px;">กด Enter เพื่อยืนยัน / กด Esc เพื่อยกเลิก</small>',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'ยืนยัน',
                    cancelButtonText: 'ยกเลิก',
                    allowOutsideClick: false,
                    showLoaderOnConfirm: true,
                    preConfirm: function () {
                        return new Promise(function (resolve, reject) {
                            $.ajax({
                                method: "POST",
                                url: baseUrl + url,
                                dataType: "json",
                                success: function (response) {
                                    self.reloadTableWaiting();
                                    socket.emit('recheck', response); //sending data
                                    resolve();
                                },
                                error: function (jqXHR, textStatus, errorThrown) {
                                    self.ajaxAlertError(textStatus, errorThrown);
                                }
                            });
                        });
                    },
                }).then((result) => {
                    if (result.value) { //Confirm
                        swal.close();
                    }
                });
            } else if($(this).hasClass('activity-end') && counter_name !== 'จุดบริการ'){
                swal({
                    title: 'เสร็จสิ้น คิว ' + data.que_num + ' ?',
                    text: '',
                    html: '<small class="text-danger" style="font-size: 13px;">กด Enter เพื่อยืนยัน / กด Esc เพื่อยกเลิก</small>',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'ยืนยัน',
                    cancelButtonText: 'ยกเลิก',
                    allowOutsideClick: false,
                    showLoaderOnConfirm: true,
                    preConfirm: function () {
                        return new Promise(function (resolve, reject) {
                            $.ajax({
                                method: "POST",
                                url: baseUrl + url,
                                dataType: "json",
                                data: {
                                    data: data, //Data in column Datatable
                                    modelProfile: modelProfile,
                                    formData: formData,
                                },
                                success: function (response) {
                                    self.reloadTableWaiting();
                                    socket.emit('end-recive-drug', response); //sending data
                                    resolve();
                                },
                                error: function (jqXHR, textStatus, errorThrown) {
                                    self.ajaxAlertError(textStatus, errorThrown);
                                }
                            });
                        });
                    },
                }).then((result) => {
                    if (result.value) { //Confirm
                        swal.close();
                    }
                });
            } else if(counter_name == 'จุดบริการ') {
                swal({
                    type: 'warning',
                    title: 'Oops...',
                    text: 'กรุณาเลือกจุดบริการ!',
                });
            }
        });

        //รายการคิวกำลังเรียก
        $('#tb-que-calling tbody').on('click', 'tr td a', function (event) {
            event.preventDefault();
            var tr = $(this).closest("tr"),
                url = $(this).attr("data-url"),
                table = $('#tb-que-calling').DataTable();
            if (tr.hasClass("child") && typeof table.row(tr).data() === "undefined") {
                tr = $(this).closest("tr").prev();
            }
            var key = tr.data("key"); //que_ids
            var data = table.row(tr).data();
            var counter_name = self.getCounterName();
            if ($(this).hasClass('activity-recall')) {
                swal({
                    title: 'ยืนยันเรียกคิว ' + data.que_num + ' ?',
                    text: '',
                    html: '<small class="text-danger" style="font-size: 13px;">กด Enter เพื่อยืนยัน / กด Esc เพื่อยกเลิก</small><p>' + counter_name + '</p>',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'เรียกคิว',
                    cancelButtonText: 'ยกเลิก',
                    allowOutsideClick: false,
                    showLoaderOnConfirm: true,
                    preConfirm: function () {
                        return new Promise(function (resolve, reject) {
                            $.ajax({
                                method: "POST",
                                url: baseUrl + url,
                                dataType: "json",
                                data: {
                                    data: data, //Data in column Datatable
                                    modelProfile: modelProfile,
                                    formData: formData,
                                },
                                success: function (response) {
                                    socket.emit('recall-recive-drug', response); //sending data
                                    resolve();
                                },
                                error: function (jqXHR, textStatus, errorThrown) {
                                    self.ajaxAlertError(textStatus, errorThrown);
                                }
                            });
                        });
                    },
                }).then((result) => {
                    if (result.value) { //Confirm
                        swal.close();
                    }
                });
            }

            //พักคิว
            if ($(this).hasClass('activity-hold')) {
                swal({
                    title: 'ยืนยันพักคิว ' + data.que_num + ' ?',
                    text: '',
                    html: '<small class="text-danger" style="font-size: 13px;">กด Enter เพื่อยืนยัน / กด Esc เพื่อยกเลิก</small>',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'พักคิว',
                    cancelButtonText: 'ยกเลิก',
                    allowOutsideClick: false,
                    showLoaderOnConfirm: true,
                    preConfirm: function () {
                        return new Promise(function (resolve, reject) {
                            $.ajax({
                                method: "POST",
                                url: baseUrl + url,
                                dataType: "json",
                                data: {
                                    data: data, //Data in column Datatable
                                    modelProfile: modelProfile,
                                    formData: formData,
                                },
                                success: function (response) {
                                    self.reloadTableCalling();
                                    self.reloadTableHold();
                                    socket.emit('hold-recive-drug', response); //sending data
                                    resolve();
                                },
                                error: function (jqXHR, textStatus, errorThrown) {
                                    self.ajaxAlertError(textStatus, errorThrown);
                                }
                            });
                        });
                    },
                }).then((result) => {
                    if (result.value) { //Confirm
                        swal.close();
                    }
                });
            }

            //เสร็จสิ้น
            if ($(this).hasClass('activity-end')) {
                swal({
                    title: 'เสร็จสิ้น คิว ' + data.que_num + ' ?',
                    text: '',
                    html: '<small class="text-danger" style="font-size: 13px;">กด Enter เพื่อยืนยัน / กด Esc เพื่อยกเลิก</small>',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'ยืนยัน',
                    cancelButtonText: 'ยกเลิก',
                    allowOutsideClick: false,
                    showLoaderOnConfirm: true,
                    preConfirm: function () {
                        return new Promise(function (resolve, reject) {
                            $.ajax({
                                method: "POST",
                                url: baseUrl + url,
                                dataType: "json",
                                data: {
                                    data: data, //Data in column Datatable
                                    modelProfile: modelProfile,
                                    formData: formData,
                                },
                                success: function (response) {
                                    self.reloadTableCalling();
                                    socket.emit('end-recive-drug', response); //sending data
                                    resolve();
                                },
                                error: function (jqXHR, textStatus, errorThrown) {
                                    self.ajaxAlertError(textStatus, errorThrown);
                                }
                            });
                        });
                    },
                }).then((result) => {
                    if (result.value) { //Confirm
                        swal.close();
                    }
                });
            }
        });

        //รายการพักคิว
        $('#tb-que-hold tbody').on('click', 'tr td .dropdown-action ul li a', function (event) {
            event.preventDefault();
            var tr = $(this).closest("tr"),
                url = $(this).attr("data-url"),
                table = $('#tb-que-hold').DataTable();
            if (tr.hasClass("child") && typeof table.row(tr).data() === "undefined") {
                tr = $(this).closest("tr").prev();
            }
            var key = tr.data("key"); //que_ids
            var data = table.row(tr).data();
            var counter_name = self.getCounterName();

            //เรียกคิวซ้ำ
            if ($(this).hasClass('activity-recall')) {
                swal({
                    title: 'ยืนยันเรียกคิว ' + data.que_num + ' ?',
                    text: '',
                    html: '<small class="text-danger" style="font-size: 13px;">กด Enter เพื่อยืนยัน / กด Esc เพื่อยกเลิก</small><p>' + counter_name + '</p>',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'เรียกคิว',
                    cancelButtonText: 'ยกเลิก',
                    allowOutsideClick: false,
                    showLoaderOnConfirm: true,
                    preConfirm: function () {
                        return new Promise(function (resolve, reject) {
                            $.ajax({
                                method: "POST",
                                url: baseUrl + url,
                                dataType: "json",
                                data: {
                                    data: data, //Data in column Datatable
                                    modelProfile: modelProfile,
                                    formData: formData,
                                },
                                success: function (response) {
                                    self.reloadTableHold();
                                    self.reloadTableCalling();
                                    socket.emit('callhold-recive-drug', response); //sending data
                                    resolve();
                                },
                                error: function (jqXHR, textStatus, errorThrown) {
                                    self.ajaxAlertError(textStatus, errorThrown);
                                }
                            });
                        });
                    },
                }).then((result) => {
                    if (result.value) { //Confirm
                        swal.close();
                    }
                });
            }

            //เสร็จสิ้น
            if ($(this).hasClass('activity-end')) {
                swal({
                    title: 'เสร็จสิ้น คิว ' + data.que_num + ' ?',
                    text: '',
                    html: '<small class="text-danger" style="font-size: 13px;">กด Enter เพื่อยืนยัน / กด Esc เพื่อยกเลิก</small>',
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'เสร็จสิ้น',
                    cancelButtonText: 'ยกเลิก',
                    allowOutsideClick: false,
                    showLoaderOnConfirm: true,
                    preConfirm: function () {
                        return new Promise(function (resolve, reject) {
                            $.ajax({
                                method: "POST",
                                url: baseUrl + url,
                                dataType: "json",
                                data: {
                                    data: data, //Data in column Datatable
                                    modelProfile: modelProfile,
                                    formData: formData,
                                },
                                success: function (response) {
                                    self.reloadTableHold();
                                    socket.emit('end-recive-drug', response); //sending data
                                    resolve();
                                },
                                error: function (jqXHR, textStatus, errorThrown) {
                                    self.ajaxAlertError(textStatus, errorThrown);
                                }
                            });
                        });
                    },
                }).then((result) => {
                    if (result.value) { //Confirm
                        swal.close();
                    }
                });
            }
        });
    },
    reloadTableWaiting: function () {
        var table = $('#tb-que-waiting').DataTable();
        table.ajax.reload();
    },
    reloadTableCalling: function () {
        var table = $('#tb-que-calling').DataTable();
        table.ajax.reload();
    },
    reloadTableHold: function () {
        var table = $('#tb-que-hold').DataTable();
        table.ajax.reload();
    },
    reloadTableQueList: function () {
        var table = $('#tb-que-list').DataTable();
        table.ajax.reload();
    },
    getCounterName: function () {
        var counter_name = '';
        if (undefined != $('#tbserviceprofile-counter_service_id').select2('data')) {
            counter_name = $('#tbserviceprofile-counter_service_id').select2('data')[0]['text'];
        }
        return counter_name;
    },
    ajaxAlertError: function (textStatus, errorThrown) {
        swal({
            type: 'error',
            title: textStatus,
            text: errorThrown,
            showConfirmButton: false,
            timer: 1500
        });
    },
    init: function () {
        var self = this;
        self.handleClick();
    },
};

//Socket Events
$(function () {
    socket.on('check-drug-not-payment', (res) => {
        toastr.warning('#' + res.modelQue.que_num, 'คิวใหม่!', {
            "timeOut": 7000,
            "positionClass": "toast-top-right",
            "progressBar": true,
            "closeButton": true,
        });
        Que.reloadTableWaiting(); //โหลดข้อมูลคิวรอเรียก
        Que.reloadTableQueList(); //โหลดข้อมูลคิว
    }).on('end-payment', (res) => {
        toastr.warning('#' + res.modelQue.que_num, 'คิวใหม่!', {
            "timeOut": 7000,
            "positionClass": "toast-top-right",
            "progressBar": true,
            "closeButton": true,
        });
        Que.reloadTableWaiting(); //โหลดข้อมูลคิวรอเรียก
        Que.reloadTableQueList(); //โหลดข้อมูลคิว
    }).on('hold-recive-drug', (res) => {
        if ( //ถ้าเป็นเซอร์วิสเดียวกัน
            res.formData.service_profile_id == formData.service_profile_id &&
            res.formData.counter_service_id == formData.counter_service_id) {
            Que.reloadTableCalling(); //โหลดข้อมูลคิวกำลังเรียก
            Que.reloadTableHold(); //โหลดข้อมูล พักคิว
        }
    }).on('end-recive-drug', (res) => {
        if ( //ถ้าเป็นเซอร์วิสเดียวกัน
            res.formData.service_profile_id == formData.service_profile_id &&
            res.formData.counter_service_id == formData.counter_service_id) {
            Que.reloadTableCalling(); //โหลดข้อมูลคิวกำลังเรียก
            Que.reloadTableHold(); //โหลดข้อมูล พักคิว
        }
    }).on('callhold-recive-drug', (res) => {
        if ( //ถ้าเป็นเซอร์วิสเดียวกัน
            res.formData.service_profile_id == formData.service_profile_id &&
            res.formData.counter_service_id == formData.counter_service_id) {
            Que.reloadTableCalling(); //โหลดข้อมูลคิวกำลังเรียก
            Que.reloadTableHold(); //โหลดข้อมูล พักคิว
        }
    }).on('call-recive-drug', (res) => {
        Que.reloadTableWaiting(); //โหลดข้อมูลคิวรอเรียก
        if ( //ถ้าเป็นเซอร์วิสเดียวกัน
            res.formData.service_profile_id == formData.service_profile_id &&
            res.formData.counter_service_id == formData.counter_service_id) {
            Que.reloadTableCalling(); //โหลดข้อมูลคิวกำลังเรียก
        }
    }).on('register', (res) => {
        Que.reloadTableQueList(); //โหลดข้อมูลคิว
    });
});

$('#tb-que-waiting tbody').on('change', 'input[type="checkbox"]', function () {
    var tr = $(this).closest("tr");
    var table = $('#tb-que-waiting').DataTable();
    if (this.checked) {
        $(tr).addClass("success");
    } else {
        $(tr).removeClass("success");
    }
    table.$('input[type="checkbox"]').each(function () {
        var value = this.value;
        if (this.checked) {
            if (jQuery.inArray(value, keySelected) !== -1) {

            } else {
                keySelected.push(value);
            }
        } else {
            if (jQuery.inArray(value, keySelected) !== -1) {
                $.each(keySelected, function (index, data) {
                    if (value == data) {
                        keySelected.splice(index, 1);
                    }
                });
            }
        }
    });

    if (keySelected.length > 0) {
        $(".btn-call-select").attr("disabled", false);
    } else {
        $(".btn-call-select").attr("disabled", true);
    }
});

//เรียกคิวที่เลือก
$("button.btn-call-select").on('click', function () {
    var selectedData = [];
    var queNumber = [];
    var table = $('#tb-que-waiting').DataTable();
    var counter_name = Que.getCounterName();
    if(counter_name !== 'จุดบริการ'){
        $.each(keySelected, function (index, value) {
            table.rows().every(function (rowIdx, tableLoop, rowLoop) {
                var data = this.data();
                if (data.que_ids == value) {
                    selectedData.push(data);
                    queNumber.push(data.que_num);
                }
                /* if (jQuery.inArray(data.que_ids, keys) !== -1) {
                    selectedData.push(data);
                    queNumber.push(data.que_num);
                } */
            });
        });
    
        swal({
            title: 'ยืนยันเรียกคิว?',
            text: counter_name,
            html: '<p>'+counter_name+'</p><p>' + queNumber.join(", ") + '</p><small class="text-danger" style="font-size: 13px;">กด Enter เพื่อยืนยัน / กด Esc เพื่อยกเลิก</small>',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'เรียกคิว',
            cancelButtonText: 'ยกเลิก',
            allowOutsideClick: false,
            showLoaderOnConfirm: true,
            preConfirm: function () {
                return new Promise(function (resolve, reject) {
                    $.ajax({
                        method: "POST",
                        url: baseUrl + "/app/calling/call-recive-selected",
                        dataType: "json",
                        data: {
                            data: selectedData, //Data in column Datatable
                            modelProfile: modelProfile,
                            formData: formData,
                        },
                        success: function (response) {
                            $('li#tabs-1, div#tab-1').removeClass('active');
                            $('li#tabs-2, div#tab-2').addClass('active');
                            $(".btn-call-select").attr("disabled", true);
                            Que.reloadTableWaiting();
                            Que.reloadTableCalling();
                            $.each(response, function (index, data) {
                                socket.emit('call-recive-drug', data); //sending data
                            });
                            keySelected = [];
                            resolve();
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            Que.ajaxAlertError(textStatus, errorThrown);
                        }
                    });
                });
            },
        }).then((result) => {
            if (result.value) { //Confirm
                swal.close();
            }
        });
    }else{
        swal({
            type: 'warning',
            title: 'Oops...',
            text: 'กรุณาเลือกจุดบริการ!',
        });
    }
    /* table.$('input[type="checkbox"]').each(function () {
        if (this.checked) {
            keys.push(this.value);
        }
    }); */
});

$('#tb-que-calling tbody, #tb-que-hold tbody').on('change', 'input[type="checkbox"]', function () {
    var tr = $(this).closest("tr");
    var tableCalling = $('#tb-que-calling').DataTable();
    var tableHold = $('#tb-que-hold').DataTable();
    if (this.checked) {
        $(tr).addClass("success");
    } else {
        $(tr).removeClass("success");
    }
    tableCalling.$('input[type="checkbox"]').each(function () {
        var value = this.value;
        if (this.checked) {
            if (jQuery.inArray(value, keySelected) !== -1) {

            } else {
                keySelected.push(value);
            }
        } else {
            if (jQuery.inArray(value, keySelected) !== -1) {
                $.each(keySelected, function (index, data) {
                    if (value == data) {
                        keySelected.splice(index, 1);
                    }
                });
            }
        }
    });

    tableHold.$('input[type="checkbox"]').each(function () {
        var value = this.value;
        if (this.checked) {
            if (jQuery.inArray(value, keySelected) !== -1) {

            } else {
                keySelected.push(value);
            }
        } else {
            if (jQuery.inArray(value, keySelected) !== -1) {
                $.each(keySelected, function (index, data) {
                    if (value == data) {
                        keySelected.splice(index, 1);
                    }
                });
            }
        }
    });

    if (keySelected.length > 0) {
        $(".btn-clear-select").attr("disabled", false);
    } else {
        $(".btn-clear-select").attr("disabled", true);
    }
});

//เรียกคิวที่เลือก
$("button.btn-clear-select").on('click', function () {
    var selectedData = [];
    var queNumber = [];
    var tableCalling = $('#tb-que-calling').DataTable();
    var tableHold = $('#tb-que-hold').DataTable();
    var counter_name = Que.getCounterName();
    if(counter_name !== 'จุดบริการ'){
        $.each(keySelected, function (index, value) {
            tableCalling.rows().every(function (rowIdx, tableLoop, rowLoop) {
                var data = this.data();
                if (data.caller_ids == value) {
                    selectedData.push(data);
                    queNumber.push(data.que_num);
                }
            });
            tableHold.rows().every(function (rowIdx, tableLoop, rowLoop) {
                var data = this.data();
                if (data.caller_ids == value) {
                    selectedData.push(data);
                    queNumber.push(data.que_num);
                }
            });
        });
    
        swal({
            title: 'ยืนยันเคลียร์คิว?',
            text: counter_name,
            html: '<p>'+counter_name+'</p><p>' + queNumber.join(", ") + '</p><small class="text-danger" style="font-size: 13px;">กด Enter เพื่อยืนยัน / กด Esc เพื่อยกเลิก</small>',
            type: 'warning',
            showCancelButton: true,
            confirmButtonText: 'เคลียร์คิว',
            cancelButtonText: 'ยกเลิก',
            allowOutsideClick: false,
            showLoaderOnConfirm: true,
            preConfirm: function () {
                return new Promise(function (resolve, reject) {
                    $.ajax({
                        method: "POST",
                        url: baseUrl + "/app/calling/clear-queue-selected",
                        dataType: "json",
                        data: {
                            data: selectedData, //Data in column Datatable
                            modelProfile: modelProfile,
                            formData: formData,
                        },
                        success: function (response) {
                            $(".btn-clear-select").attr("disabled", true);
                            Que.reloadTableHold();
                            Que.reloadTableCalling();
                            $.each(response, function (index, data) {
                                socket.emit('end-recive-drug', data); //sending data
                            });
                            keySelected = [];
                            resolve();
                        },
                        error: function (jqXHR, textStatus, errorThrown) {
                            Que.ajaxAlertError(textStatus, errorThrown);
                        }
                    });
                });
            },
        }).then((result) => {
            if (result.value) { //Confirm
                swal.close();
            }
        });
    }else{
        swal({
            type: 'warning',
            title: 'Oops...',
            text: 'กรุณาเลือกจุดบริการ!',
        });
    }
});

Que.init();