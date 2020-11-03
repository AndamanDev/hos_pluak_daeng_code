$(document).ready(function () {

});

var jPlayerid = "#jplayer";
var jp_container = "#jp_container";
var i = 0;
var myPlayer = $(jPlayerid),
    myPlayerData,
    fixFlash_mp4, // Flag: The m4a and m4v Flash player gives some old currentTime values when changed.
    fixFlash_mp4_id, // Timeout ID used with fixFlash_mp4
    ignore_timeupdate, // Flag used with fixFlash_mp4
    myControl = {
        progress: $(jp_container + " .jp-progress-slider"),
        volume: $(jp_container + " .jp-volume-slider")
    };
var myPlaylist = new jPlayerPlaylist({
    jPlayer: jPlayerid,
    cssSelectorAncestor: jp_container
}, [

], {
    playlistOptions: {
        autoPlay: true,
        enableRemoveControls: true,
    },
    ready: function (event) {
        // Hide the volume slider on mobile browsers. ie., They have no effect.
        if (event.jPlayer.status.noVolume) {
            // Add a class and then CSS rules deal with it.
            $(".jp-gui").addClass("jp-no-volume");
        }
        // Determine if Flash is being used and the mp4 media type is supplied. BTW, Supplying both mp3 and mp4 is pointless.
        fixFlash_mp4 = event.jPlayer.flash.used && /m4a|m4v/.test(event.jPlayer.options.supplied);
    },
    timeupdate: function (event) {
        if (!ignore_timeupdate) {
            myControl.progress.slider("value", event.jPlayer.status.currentPercentAbsolute);
        }
    },
    volumechange: function (event) {
        if (event.jPlayer.options.muted) {
            myControl.volume.slider("value", 0);
        } else {
            myControl.volume.slider("value", event.jPlayer.options.volume);
        }
    },
    playing: function (event) {
        var current = myPlaylist.current;
        var data = myPlaylist.playlist[current];
        if (data.wav.indexOf("please.wav") >= 0) {
            socket.emit('show-display', data); //sending data
            toastr.success('#' + data.title, 'Calling!', {
                "timeOut": 5000,
                "positionClass": "toast-top-right",
                "progressBar": true,
                "closeButton": true,
            });
        }
        if (data.wav.indexOf("Prompt1_Sir.wav") >= 0 || data.wav.indexOf("Prompt2_Sir.wav") >= 0) {
            Que.updateStatus(data.artist.modelCaller.caller_ids); //update tb_caller status = callend
        }
        if ((current + 1) === myPlaylist.playlist.length) {
            myPlaylist.remove(); //reset q
        }
    },
    loadstart: function (event) {
        //console.log(myPlaylist.playlist);
    },
    ended: function (event) {

    },
    error: function (event) {
        console.log(event);
    },
    loop: false,
    swfPath: "/vendor",
    supplied: "m4a, oga, mp3, wav",
    cssSelectorAncestor: jp_container,
    wmode: "window",
    keyEnabled: true,
    volume: 1,
    audioFullScreen: true,
    preload: 'auto'
});

myPlayerData = $(jPlayerid).data("jPlayer");

myControl.progress.slider({
    animate: "fast",
    max: 100,
    range: "min",
    step: 0.1,
    value: 0,
    slide: function (event, ui) {
        var sp = myPlayerData.status.seekPercent;
        if (sp > 0) {
            // Apply a fix to mp4 formats when the Flash is used.
            if (fixFlash_mp4) {
                ignore_timeupdate = true;
                clearTimeout(fixFlash_mp4_id);
                fixFlash_mp4_id = setTimeout(function () {
                    ignore_timeupdate = false;
                }, 1000);
            }
            // Move the play-head to the value and factor in the seek percent.
            $(jPlayerid).jPlayer("playHead", ui.value * (100 / sp));
        } else {
            // Create a timeout to reset this slider to zero.
            setTimeout(function () {
                myControl.progress.slider("value", 0);
            }, 0);
        }
    }
});

// Create the volume slider control
myControl.volume.slider({
    animate: "fast",
    max: 1,
    range: "min",
    step: 0.01,
    value: myPlaylist.options.volume,
    slide: function (event, ui) {
        $(jPlayerid).jPlayer("option", "muted", false);
        $(jPlayerid).jPlayer("option", "volume", ui.value);
    }
});

$("#jplayer_inspector").jPlayerInspector({
    jPlayer: $(jPlayerid)
});

Que = {
    addMedia: function (res) {
        $.each(res.media_files, function (index, sound) {
            myPlaylist.add({
                title: res.data.que_num,
                artist: res,
                wav: sound
            });
        });
        $(jPlayerid).jPlayer("play");
    },
    updateStatus: function (caller_ids) {
        $.ajax({
            method: "GET",
            url: baseUrl + "/app/calling/update-status-callend",
            data: {
                caller_ids: caller_ids
            },
            dataType: "json",
            success: function (res) {
                toastr.success('Update Status Completed!', 'Success!', {
                    "timeOut": 5000,
                    "positionClass": "toast-top-right",
                    "progressBar": true,
                    "closeButton": true,
                });
            },
            error: function (jqXHR, textStatus, errorThrown) {
                swal({
                    type: 'error',
                    title: errorThrown,
                    showConfirmButton: false,
                    timer: 1500
                });
            }
        });
    },
    autoloadMedia: function(){
        var self = this;
        
    },
    init: function(){
        var self = this;
        self.autoloadMedia();
    }
};

//Socket Events
$(function () {
    socket.on('call-payment', (res) => { //เรียกคิวการเงิน tb wait
        if (jQuery.inArray(res.modelCaller.counter_service_id, model.counter_service_id) !== -1) {
            Que.addMedia(res);
        }
    }).on('recall-payment', (res) => { //เรียกคิวการเงิน tb calling
        if (jQuery.inArray(res.modelCaller.counter_service_id.toString(), model.counter_service_id) !== -1) {
            Que.addMedia(res);
        }
    }).on('callhold-payment', (res) => { //เรียกคิวการเงิน tb hold
        if (jQuery.inArray(res.modelCaller.counter_service_id.toString(), model.counter_service_id) !== -1) {
            Que.addMedia(res);
        }
    }).on('call-recive-drug', (res) => { //เรียกคิวรับยา tb wait
        if (jQuery.inArray(res.modelCaller.counter_service_id.toString(), model.counter_service_id) !== -1) {
            Que.addMedia(res);
        }
    }).on('recall-recive-drug', (res) => { //เรียกคิวรับยา tb calling
        if (jQuery.inArray(res.modelCaller.counter_service_id.toString(), model.counter_service_id) !== -1) {
            Que.addMedia(res);
        }
    }).on('callhold-recive-drug', (res) => { //เรียกคิวรับยา tb hold
        if (jQuery.inArray(res.modelCaller.counter_service_id.toString(), model.counter_service_id) !== -1) {
            Que.addMedia(res);
        }
    });
});