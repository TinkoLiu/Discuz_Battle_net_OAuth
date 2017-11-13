<?php

if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$ga = DB::fetch_first('SELECT `region`,`battletag` FROM ' . DB::table('ga_bnet') . " WHERE `forumuid` = " . intval($_G['uid']));
$binded = empty($ga) ? 0 : 1;
$battletag = $ga['battletag'];
$regioni18n = lang('plugin/ga_bnet', 'bnet_region_' . $ga['region']);
$bindedtip = lang('plugin/ga_bnet', 'nowbinded');
