Que = {
    handleClick: function () {
        var self = this;

        //ตารางคิวรอ
        $('#tb-que-waiting tbody').on('click', 'tr td .dropdown-action ul li a', function (event) {
            event.preventDefault();
            var tr = $(this).closest("tr"),
                url = $(this).attr("data-url"),
                table = $('#tb-que-waiting').DataTable();
            if (tr.hasClass("child") && typeof table.row(tr).data() === "undefined") {
                tr = $(this).closest("tr").prev();
            }
            var key = tr.data("key"); //que_ids
            var data = table.row(tr).data();
            //ชำระเงิน
            if ($(this).hasClass("activity-payment")) {
                swal({
                    title: 'ชำระเงิน คิว ' + data.que_num + ' ?',
                    text: data.pt_name,
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
                                    type: "payment"
                                },
                                success: function (response) {
                                    self.reloadTableWaiting();
                                    self.reloadTableWaitingPayment();
                                    socket.emit('check-drug-payment', response); //sending data
                                    resolve();
                                },
                                error: function (jqXHR, textStatus, errorThrown) {
                                    self.ajaxAlertError(textStatus,errorThrown);
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
            //ไม่ชำระเงิน
            if ($(this).hasClass("activity-not-payment")) {
                swal({
                    title: 'ไม่ชำระเงิน คิว ' + data.que_num + ' ?',
                    text: data.pt_name,
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
                                    type: "not-payment"
                                },
                                success: function (response) {
                                    self.reloadTableWaiting();
                                    socket.emit('check-drug-not-payment', response); //sending data
                                    resolve();
                                },
                                error: function (jqXHR, textStatus, errorThrown) {
                                    self.ajaxAlertError(textStatus,errorThrown);
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

            //รอยานาน
            if ($(this).hasClass("activity-waiting-drug")) {
                swal({
                    title: 'รอยานาน คิว ' + data.que_num + ' ?',
                    text: data.pt_name,
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
                                    type: "waiting-drug"
                                },
                                success: function (response) {
                                    self.reloadTableWaiting();
                                    self.reloadTableWaitingDrug();
                                    socket.emit('check-drug-wait-drug', response); //sending data
                                    resolve();
                                },
                                error: function (jqXHR, textStatus, errorThrown) {
                                    self.ajaxAlertError(textStatus,errorThrown);
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

        //ตารางคิวรอยานาน
        $('#tb-que-waiting-drug tbody').on('click', 'tr td .dropdown-action ul li a', function (event) {
            event.preventDefault();
            var tr = $(this).closest("tr"),
                url = $(this).attr("data-url"),
                table = $('#tb-que-waiting-drug').DataTable();
            if (tr.hasClass("child") && typeof table.row(tr).data() === "undefined") {
                tr = $(this).closest("tr").prev();
            }
            var key = tr.data("key"); //que_ids
            var data = table.row(tr).data();

            //ชำระเงิน
            if ($(this).hasClass("activity-payment")) {
                swal({
                    title: 'ชำระเงิน คิว ' + data.que_num + ' ?',
                    text: data.pt_name,
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
                                    type: "payment"
                                },
                                success: function (response) {
                                    self.reloadTableWaitingDrug();
                                    self.reloadTableWaitingPayment();
                                    socket.emit('check-drug-payment', response); //sending data
                                    resolve();
                                },
                                error: function (jqXHR, textStatus, errorThrown) {
                                    self.ajaxAlertError(textStatus,errorThrown);
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
            //ไม่ชำระเงิน
            if ($(this).hasClass("activity-not-payment")) {
                swal({
                    title: 'ไม่ชำระเงิน คิว ' + data.que_num + ' ?',
                    text: data.pt_name,
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
                                    type: "not-payment"
                                },
                                success: function (response) {
                                    self.reloadTableWaitingDrug();
                                    socket.emit('check-drug-not-payment', response); //sending data
                                    resolve();
                                },
                                error: function (jqXHR, textStatus, errorThrown) {
                                    self.ajaxAlertError(textStatus,errorThrown);
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
    reloadTableWaiting: function(){
        var table = $('#tb-que-waiting').DataTable();
        table.ajax.reload();
    },
    reloadTableWaitingDrug: function(){
        var table = $('#tb-que-waiting-drug').DataTable();
        table.ajax.reload();
    },
    reloadTableWaitingPayment: function(){
        var table = $('#tb-que-waiting-payment').DataTable();
        table.ajax.reload();
    },
    reloadTableQueList: function () {
        var table = $('#tb-que-list').DataTable();
        table.ajax.reload();
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
    socket.on('register', (res) => { //ออกบัตรคิว
        toastr.warning('#' + res.modelQue.que_num, 'คิวใหม่!', {
            "timeOut": 7000,
            "positionClass": "toast-top-right",
            "progressBar": true,
            "closeButton": true,
        });
        Que.reloadTableWaiting();
        Que.reloadTableQueList(); //โหลดข้อมูลคิว
    }).on('check-drug-payment', (res) => {//ชำระเงินตรวจสอบยา
        Que.reloadTableWaiting(); //โหลดข้อมูลรายการคิว
        Que.reloadTableWaitingDrug(); //โหลดข้อมูลคิวรอยานาน
        Que.reloadTableWaitingPayment(); //โหลดข้อมูลรอชำระเงิน
    }).on('check-drug-wait-drug', (res) => {//รอยานานตรวจสอบยา
        Que.reloadTableWaiting(); //โหลดข้อมูลรายการคิว
        Que.reloadTableWaitingDrug(); //โหลดข้อมูลรอยานาน
    }).on('check-drug-not-payment', (res) => {//ไม่ชำระเงินตรวจสอบยา
        Que.reloadTableWaiting(); //โหลดข้อมูลรายการคิว
        Que.reloadTableWaitingDrug(); //โหลดข้อมูลรอยานาน
    }).on('call-payment', (res) => {
        Que.reloadTableWaitingPayment(); //โหลดข้อมูลรอชำระเงิน
    }).on('recheck', (res) => {
        toastr.warning('#' + res.modelQue.que_num, 'Recheck!', {
            "timeOut": 7000,
            "positionClass": "toast-top-right",
            "progressBar": true,
            "closeButton": true,
        });
        Que.reloadTableWaiting();
    });
});

Que.init();
