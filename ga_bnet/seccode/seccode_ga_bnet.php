<?php

if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class seccode_ga_bnet {

	var $version = '1.0';
	var $name = 'bnet_seccode_name';
	var $description = "bnet_seccode_description";
	var $copyright = "bnet_seccode_copyright";

	function check() {
		global $_G;
		if ($_G['uid'] == 0) {
			return false;
		}
		$sql = 'SELECT * FROM ' . DB::table('ga_bnet') . " WHERE`forumuid` = '{$_G['uid']}'";
		$rs = DB::fetch_first($sql);
		if (!empty($rs)) {
			return true;
		}
		return false;
	}

	function make() {
		echo lang('plugin/ga_bnet', 'bnet_seccode_echo_frontend');
	}

	function image() {}

}

?>
