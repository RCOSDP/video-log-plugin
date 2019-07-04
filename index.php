<?php

header('X-FRAME-OPTIONS: SAMEORIGIN');

function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

class UUID {
    public static function v4() {
      return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
          mt_rand(0, 0xffff), mt_rand(0, 0xffff),
          mt_rand(0, 0xffff),
          mt_rand(0, 0x0fff) | 0x4000,
          mt_rand(0, 0x3fff) | 0x8000,
          mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
      );
    }
    public static function is_valid($uuid) {
        return preg_match('/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?'.
                          '[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i', $uuid) === 1;
    }
}
$v4uuid = UUID::v4();



$url_query = $_SERVER["QUERY_STRING"];
parse_str($url_query, $url_query_arr);
if($url_query_arr[id]){
    $file = $url_query_arr[id];
}else{
    die("video not found");
}

require_once(__DIR__.'/config.php'); 

if($USER->id > $authnumber){
    $uid = $USER->id;
}else{
    die("moodle not login");
}

$ref = $_SERVER["HTTP_REFERER"];
$ref_case1 = $CFG->wwwroot . "/course/view.php?";   // Access from Moodle URL(popup/open)
$ref_case2 = $CFG->wwwroot . "/mod/url/view.php?";  // Access from Moodle URL(embed)
$ref_case3 = $CFG->wwwroot . "/pluginfile.php/";    // Access form CHiLO Book registered in Moodle Resource

if(strstr($ref,$ref_case1)){
    $debug = true;
    $ref_query = str_replace($ref_case1, '', $ref);
    parse_str($ref_query, $ref_query_arr);
    if($ref_query_arr['id']){
        $courseid = $ref_query_arr['id'];
    }
    if($url_query_arr['title']){
        $title = $url_query_arr['title'];
    }
}else if(strstr($ref,$ref_case2)){
    $debug = true;
    $ref_query = str_replace($ref_case2, '', $ref);
    parse_str($ref_query, $ref_query_arr);
    $instanceid = $ref_query_arr['id'];
    if($instanceid){
        $courseid = $DB->get_field_select('course_modules', 'course', "id = $instanceid");
    }
    $embed = true;
}else if($url_query_arr[chilo]){
    $ref = $url_query_arr[chilo];
    if(strstr($ref,$ref_case3)){
        $ref_query = str_replace($ref_case3, '', $ref);
        $contextid = substr($ref_query, 0, strcspn($ref_query,'/'));
        $instanceid = $DB->get_field_select('context', 'instanceid', "id = $contextid");
        if($instanceid){
            $courseid = $DB->get_field_select('course_modules', 'course', "id = $instanceid");
        }
    }
}

if($courseid){
    if(!is_siteadmin($uid = null)){
        $status = $DB->get_field_select('enrol', 'status', "enrol = 'guest' and courseid = $courseid");
        if($status !== "0"){
            if(!is_enrolled(context_course::instance($courseid), $uid)){
                die("course not enroll");
            }
        }
    }
}else{
    die("unknown course");
}

$customparameter = $v4uuid;
$hashstr = hash('sha512', $contentpath.'?'.$wowzasecret.'&'.$wowzatoken.'customparameter='.$customparameter.'&'.$wowzatoken.'endtime='.$wowzaend.'&'.$wowzatoken.'starttime='.$wowzastart.'', true);
$usableHash= strtr(base64_encode($hashstr), '+/', '-_');
$wowzaquery = $wowzatoken."customparameter=".$customparameter."&".$wowzatoken."endtime=".$wowzaend."&".$wowzatoken."starttime=".$wowzastart."&".$wowzatoken."hash=".$usableHash."";
$link = $contenturl."?".$wowzaquery;

?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <?php if($title){echo '<title>'.h($title).'</title>'."\n";}?>
  <link rel="shortcut icon" href="./static/ico/favicon.ico" />
  <script src="./static/js/lib/jquery.min.js"></script>
  <script src="./static/js/lib/mobile-detect.min.js"></script>
  <script src="./static/js/check.js"></script>
  <script src="./static/js/lib/video.min.js"></script>
  <script src="./static/js/lib/videojs-seek-buttons.min.js"></script>
  <script src="./static/js/lib/videojs.persistvolume.js"></script>
  <script>var ref = '<?= h($ref); ?>';var file = '<?= h($file); ?>';var query = '<?= $wowzaquery; ?>';</script>
  <link href="./static/css/lib/video-js.min.css" rel="stylesheet" />
  <link href="./static/css/lib/videojs-seek-buttons.css" rel="stylesheet" />
  <link type="text/css" rel="stylesheet" href="./static/css/common.css" />
  <link type="text/css" rel="stylesheet" href="./static/css/chilo.css" />
  <?php if($embed) {echo '<style><!--body{max-width: 700px;}--></style>';}?>
</head>
<body>
  <video playsinline id="video_player" class="video-js vjs-default-skin vjs-big-play-centered" 
    data-setup='{"controls": true, "autoplay": true, "preload": "auto","fluid": true,"playbackRates": [0.25, 0.5, 0.75, 1, 1.25, 1.5, 1.75, 2],"nativeControlsForTouch": false}' >
    <source src="<?= h($link); ?>" type="application/x-mpegURL">
    <?php
    include 'lang.php';
    foreach ($lang as $v) {
        $extension = substr($file, strrpos($file, '.'));
        $webvtt = './track/' . str_replace($extension,'_' . $v[code] . '.vtt',$file);
        if(file_exists($webvtt)) {
            echo ' <track label="'.$v[name].'" kind="subtitles" srclang="'.$v[code].'" src="'.$webvtt.'" type="text/vtt" >';
        }
    }
    ?>
  </video>
  <?php if($title){echo ' <div class="navbar-area"><h1>'.h($title).'</h1></div>'."\n";}?>
  <script src="./static/js/video-log.js"></script>
</body>
</html>