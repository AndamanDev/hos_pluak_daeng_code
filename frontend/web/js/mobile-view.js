//Socket Events
$(function () {
    socket.on('show-display', (res) => {
        if(res.artist.modelQue.que_ids == modelQue.que_ids){
            swal({
                type: 'warning',
                title: 'ถึงคิวแล้วครับ!',
                text: "เชิญที่ " + res.artist.modelCounterService.counter_service_name,
            });
            $.pjax.reload({container: '#pjax-mobile-view'});
        }else{
            $.pjax.reload({container: '#pjax-mobile-view'});
        }
    });
});