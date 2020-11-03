Que = {
    reloadDisplay: function () {
        var table = $('#tb-display').DataTable();
        table.ajax.reload();
    },
    reloadHold: function () {
        var table = $('#tb-hold').DataTable();
        table.ajax.reload();
    },
    reloadQueWait: function () {
        var table = $('#tb-que-wait').DataTable();
        table.ajax.reload();
    },
    removeRow: function (res) {
        var table = $('#tb-display').DataTable();
        table.row('#' + res.data.caller_ids).remove().draw();
    },
    blink: function (res) { //สั่งกระพริบ
        if (config.que_column_length > 1) {
            $('span.' + res.title + ', .' + res.artist.modelCounterService.counter_service_call_number).modernBlink({
                duration: 1000,
                iterationCount: 7,
                auto: true
            });
        } else {
            $('span.' + res.title).modernBlink({
                duration: 1000,
                iterationCount: 7,
                auto: true
            });
        }
    },
};
//Socket Events
$(function () {
    socket.on('show-display', (res) => { //เรียกคิว
        if (
            jQuery.inArray(res.artist.modelCaller.counter_service_id.toString(), counters) !== -1 &&
            jQuery.inArray(res.artist.modelQue.service_id.toString(), services) !== -1
        ) {
            Que.reloadDisplay();
            setTimeout(function () {
                Que.blink(res); //สั่งกระพริบ
            }, 1000);

            //ถ้าเป็นคิวที่เรียกที่รายการพักคิว
            var table = $('#tb-hold').DataTable();
            table.rows().every(function (rowIdx, tableLoop, rowLoop) {
                var data = this.data();
                if (jQuery.inArray(res.artist.modelQue.que_num.toString(), data.data) !== -1) { //ถ้ามีรายการคิวที่แสดงให้โหลดข้อมูลใหม่
                    Que.reloadHold();
                }
            });
        }
    }).on('hold-payment', (res) => { //พักคิวการเงิน
        if (
            jQuery.inArray(res.modelCaller.counter_service_id.toString(), counters) !== -1 &&
            jQuery.inArray(res.modelQue.service_id.toString(), services) !== -1
        ) {
            Que.reloadHold();
            var table = $('#tb-display').DataTable();
            table.rows().every(function (rowIdx, tableLoop, rowLoop) {
                var data = this.data();
                if (jQuery.inArray(res.modelQue.que_num.toString(), data.data) !== -1) { //ถ้ามีรายการคิวที่แสดงให้โหลดข้อมูลใหม่
                    Que.reloadDisplay();
                }
            });
        }
    }).on('hold-recive-drug', (res) => { //พักคิวรับยา
        if (
            jQuery.inArray(res.modelCaller.counter_service_id.toString(), counters) !== -1 &&
            jQuery.inArray(res.modelQue.service_id.toString(), services) !== -1
        ) {
            Que.reloadHold();
            var table = $('#tb-display').DataTable();
            table.rows().every(function (rowIdx, tableLoop, rowLoop) {
                var data = this.data();
                if (jQuery.inArray(res.modelQue.que_num.toString(), data.data) !== -1) { //ถ้ามีรายการคิวที่แสดงให้โหลดข้อมูลใหม่
                    Que.reloadDisplay();
                }
            });
        }
    }).on('update-display', (res) => { //แก้ไขรายการจอแสดงผล
        if (res.model.display_ids == config.display_ids) {
            location.reload();
        }
    }).on('end-recive-drug', (res) => { //เสร้จสิ้นคิวรับยา
        var table = $('#tb-display').DataTable();
        table.rows().every(function (rowIdx, tableLoop, rowLoop) {
            var data = this.data();
            if (jQuery.inArray(res.modelQue.que_num.toString(), data.data) !== -1) { //ถ้ามีรายการคิวที่แสดงให้โหลดข้อมูลใหม่
                Que.reloadDisplay();
            }
        });
        var tablewait = $('#tb-que-wait').DataTable();
        tablewait.rows().every(function (rowIdx, tableLoop, rowLoop) {
            var data = this.data();
            if (jQuery.inArray(res.modelQue.que_num.toString(), data.data) !== -1) { //ถ้ามีรายการคิวที่แสดงให้โหลดข้อมูลใหม่
                Que.reloadQueWait();
            }
        });
        var tablehold = $('#tb-hold').DataTable();
        tablehold.rows().every(function (rowIdx, tableLoop, rowLoop) {
            var data = this.data();
            if (jQuery.inArray(res.modelQue.que_num.toString(), data.data) !== -1) { //ถ้ามีรายการคิวที่แสดงให้โหลดข้อมูลใหม่
                Que.reloadHold();
            }
        });
    }).on('check-drug-payment', (res) => { //โอนคิวไปชำระเงิน
        var tablewait = $('#tb-que-wait').DataTable();
        tablewait.rows().every(function (rowIdx, tableLoop, rowLoop) {
            var data = this.data();
            if (jQuery.inArray(res.modelQue.que_num.toString(), data.data) !== -1) { //ถ้ามีรายการคิวที่แสดงให้โหลดข้อมูลใหม่
                Que.reloadQueWait();
            }
        });
    }).on('check-drug-not-payment', (res) => { //โอนคิวไปรับยา
        var tablewait = $('#tb-que-wait').DataTable();
        tablewait.rows().every(function (rowIdx, tableLoop, rowLoop) {
            var data = this.data();
            if (jQuery.inArray(res.modelQue.que_num.toString(), data.data) !== -1) { //ถ้ามีรายการคิวที่แสดงให้โหลดข้อมูลใหม่
                Que.reloadQueWait();
            }
        });
    }).on('check-drug-wait-drug', (res) => { //คิวรอยานาน
        Que.reloadQueWait();
    }).on('end-payment', (res) => { //คิวรอยานาน
        var table = $('#tb-display').DataTable();
        table.rows().every(function (rowIdx, tableLoop, rowLoop) {
            var data = this.data();
            if (jQuery.inArray(res.modelQue.que_num.toString(), data.data) !== -1) { //ถ้ามีรายการคิวที่แสดงให้โหลดข้อมูลใหม่
                Que.reloadDisplay();
            }
        });
        var tablehold = $('#tb-hold').DataTable();
        tablehold.rows().every(function (rowIdx, tableLoop, rowLoop) {
            var data = this.data();
            if (jQuery.inArray(res.modelQue.que_num.toString(), data.data) !== -1) { //ถ้ามีรายการคิวที่แสดงให้โหลดข้อมูลใหม่
                Que.reloadHold();
            }
        });
    });
});