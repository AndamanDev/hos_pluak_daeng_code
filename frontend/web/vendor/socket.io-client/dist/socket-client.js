var host = 'http://' + window.location.hostname + ':3001'; // 'https://node-que-pmh.herokuapp.com';
var socket = io(host);
$(function () {
    socket.on('connect', () => {
            console.warn('connect: ' + socket.id);
            toastr.success('ID : ' + socket.id, 'socket connected!', {
                "timeOut": 5000,
                "positionClass": "toast-top-right",
                "progressBar": true,
                "closeButton": true,
            });
        })
        .on('connect_error', (error) => {
            console.warn('connect_error: ' + error);
            toastr.error(error, 'socket connect error!', {
                "timeOut": 2000,
                "positionClass": "toast-top-right",
                "progressBar": true,
                "closeButton": true,
            });
        })
        .on('disconnect', (reason) => {
            console.warn('disconnect: ' + reason);
        })
        .on('connect_timeout', (timeout) => {
            console.warn('connect_timeout: ' + timeout);
        })
        .on('reconnect', (attemptNumber) => {
            console.warn('reconnect: ' + attemptNumber);
        }).on('reconnect_error', (error) => {
            console.warn('reconnect_error: ' + error);
        });
});