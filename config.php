<?php

// Moodle config file
require(__DIR__.'/../../config.php');

/* Change process when logged in with or without Moodle login.
 * 0: Allow all users authenticated including guest users.
 * 1: Allow all users authenticated except guest users．
 */
$authnumber  = 0;

// Wowza config
$contenturl  = 'https:// your wowza domain /vod/_definst_/'.$file.'/playlist.m3u8';
$contentpath = 'vod/_definst_/'.$file;
$wowzasecret = '';
$wowzatoken  = '';
$wowzastart = '0';
$wowzaend = strtotime(date('d-m-Y H:i:s')) + 3600;
