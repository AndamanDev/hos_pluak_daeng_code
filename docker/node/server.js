var app = require('express')();
var http = require('http').Server(app);
var io = require('socket.io')(http);

app.get('/', function (req, res) {
    res.json({
        welcome: 'nodejs api'
    });
});

io.on('connection', function (socket) {
    //ออกบัตรคิว
    socket.on('register', function (res) {
        socket.broadcast.emit('register', res);
    });

    //ชำระเงิน
    socket.on('check-drug-payment', function (res) {
        socket.broadcast.emit('check-drug-payment', res);
    });

    //ไม่ชำระเงิน
    socket.on('check-drug-not-payment', function (res) {
        socket.broadcast.emit('check-drug-not-payment', res);
    });

    //รอยานาน
    socket.on('check-drug-wait-drug', function (res) {
        socket.broadcast.emit('check-drug-wait-drug', res);
    });

    //เรียกคิว การเงิน
    socket.on('call-payment', function (res) {
        socket.broadcast.emit('call-payment', res);
    });

    //เรียกคิวซ้ำ การเงิน
    socket.on('recall-payment', function (res) {
        socket.broadcast.emit('recall-payment', res);
    });

    //พักคิว การเงิน
    socket.on('hold-payment', function (res) {
        socket.broadcast.emit('hold-payment', res);
    });

    //เสร็จสิ้น การเงิน
    socket.on('end-payment', function (res) {
        socket.broadcast.emit('end-payment', res);
    });

    //เรียกคิว
    socket.on('callhold-payment', function (res) {
        socket.broadcast.emit('callhold-payment', res);
    });

    //เรียกคิวรับยา
    socket.on('call-recive-drug', function (res) {
        socket.broadcast.emit('call-recive-drug', res);
    });

    //เรียกคิวซ้ำ รับยา
    socket.on('recall-recive-drug', function (res) {
        socket.broadcast.emit('recall-recive-drug', res);
    });

    //พักคิว รับยา
    socket.on('hold-recive-drug', function (res) {
        socket.broadcast.emit('hold-recive-drug', res);
    });

    //เรียกคิว
    socket.on('callhold-recive-drug', function (res) {
        socket.broadcast.emit('callhold-recive-drug', res);
    });

    //เสร็จสิ้น รับยา
    socket.on('end-recive-drug', function (res) {
        socket.broadcast.emit('end-recive-drug', res);
    });

    //แสดงคิวเรียก
    socket.on('show-display', function (res) {
        socket.broadcast.emit('show-display', res);
    });

    //แก้ไขข้อมูลจอแสดงผล
    socket.on('update-display', function (res) {
        socket.broadcast.emit('update-display', res);
    });

    socket.on('recheck', function (res) {
        socket.broadcast.emit('recheck', res);
    });

    socket.on('disconnect', function () {
        io.emit('disconnected');
    });
});

http.listen(3000, function () {
    console.log('listening on *:3001');
});