<?php

// Moodle config file
require(__DIR__.'/../../config.php');

/* Moodleログインの有無で処理を止める
 * 0 : ゲストユーザーを含めた認証済みユーザーは全て許可
 * 1 : ゲストユーザーを除く認証済みユーザーは全て許可
 */
$authnumber  = 0;

// Wowza config
$contenturl  = 'https:// your wowza domain /vod/_definst_/'.$file.'/playlist.m3u8';
$contentpath = 'vod/_definst_/'.$file;
$wowzasecret = '';
$wowzatoken  = '';
$wowzastart = '0';
$wowzaend = strtotime(date('d-m-Y H:i:s')) + 3600;
