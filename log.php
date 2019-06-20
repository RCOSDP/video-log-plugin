<?php

function e($str) {
    if(empty($str) && $str !== "0"){
        return "-";
    }else{
        return $str;
    }
}

require_once(__DIR__.'/config.php'); 

if($USER->id > $authnumber){
    if (isset($_POST['event'])) {

        date_default_timezone_set('Asia/Tokyo');

        $date = date("Y-m-d");
        $time = date("H:i:s");
        $tz = date("T");
        $event = $_POST['event'];
        $detail = $_POST['detail'];
        $file = $_POST['file'];
        $query = $_POST['query'];
        $current = $_POST['current'];
        $ip = $_SERVER['REMOTE_ADDR'];
        $ua = $_SERVER['HTTP_USER_AGENT'];
        $ref = $_POST['ref'];
        $uid = $USER->id;

        $filePath = '/var/log/nii/videojs/videojs.'.$date.'.log';

        $logdata = $date . "	" . $time . "	" . $tz .  "	";
        for ($i = 1; $i <= 35; $i++) {
            // Wowza‚Ìo—ÍƒƒO—ñ‚Ì•ª‚¸‚ç‚µ‚Ä‚é
            $logdata .= "-	";
        }
        $logdata .= $event . "	" . e($detail) . "	" . e($file) . "	" . e($query) . "	" . e($current) . "	" . e($ip) . "	" . e($ua) . "	" . e($ref) . "	" . $uid . "\n";
        
        file_put_contents($filePath, $logdata, FILE_APPEND | LOCK_EX);
    }
}

?>