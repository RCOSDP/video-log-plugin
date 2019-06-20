var myPlayer = videojs('video_player');

/* 早送り/巻き戻し ボタン追加、秒数指定 */
myPlayer.seekButtons({
    forward: 15,
    back: 15
});

/* 音量位置記憶 */
myPlayer.persistvolume({
    namespace: "Virality-Is-Reality"
});

/* ログ記録 */
function log_post(event,detail) {
    $.ajax({
        type: 'POST',
        url: 'log.php',
        data: {
            event : event,
            detail : detail,
            file : file,
            query : query,
            current : myPlayer.currentTime(),
            ref : ref
        },
    });
}

/* スライダー操作時間計算 */
var previousTime = 0;
var currentTime = 0;
var seekStart = null;
myPlayer.on('timeupdate', function() {
    previousTime = currentTime;
    currentTime = myPlayer.currentTime();
});
myPlayer.on('seeking', function() {
    if(seekStart === null) {
        seekStart = previousTime;
    }
});
myPlayer.on('seeked', function() {
    log_post('seeked',seekStart);
    seekStart = null;
});

/* 字幕情報取得 */
var timeout;
myPlayer.textTracks().on("change", function action(event) {
    clearTimeout(timeout)
    var showing = this.tracks_.filter(function (track) {
        if (track.kind === "subtitles" && track.mode === "showing") {
            srclang = track.language;
            return true;
        }else{
            return false;
        }
    })[0]
    timeout = setTimeout(function () {
        myPlayer.trigger("subtitleChanged", showing)
    }, 10);
})
myPlayer.on("subtitleChanged", function (event, track) {
    if(track){
        log_post("trackchange",track.language);
    }else{
        log_post("trackchange","off");
    }
})

myPlayer.on('firstplay', function() {
    log_post('firstplay')
});
myPlayer.on('play', function() {
    log_post('play')
});
myPlayer.on('pause', function() {
    log_post('pause')
});
myPlayer.on('ratechange', function() {
    log_post('ratechange',myPlayer.playbackRate());
});
myPlayer.on('ended', function() {
    log_post('ended');
});
window.addEventListener("beforeunload", function (event) {
    log_post('beforeunload-ended');
});
window.addEventListener("pagehide", function (event) {
    log_post('pagehide-ended');
});
window.addEventListener("unload", function (event) {
    log_post('unload-ended');
});
document.addEventListener("visibilitychange", function() {
    if (document.visibilityState == "hidden") { 
        log_post('hidden-ended'); 
    }
});
$(function(){
    setInterval(function(){
        log_post('current-time'); 
    },10000);
});
