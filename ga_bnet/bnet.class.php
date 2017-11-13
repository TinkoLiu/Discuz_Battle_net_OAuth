<?php

if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

class plugin_ga_bnet {
	protected $register_bottom_cnstr;
	protected $config, $_G;

	function __construct() {
		global $_G;
		$this->_G = &$_G;
		if (!isset($_G['cache']['plugin']['ga_bnet'])) {
			loadcache('plugin');
		}

		$this->config = $_G['cache']['plugin']['ga_bnet'];
		$this->register_bottom_cnstr = '<style scoped="scoped">.bnet_img {width:50%;}#ga_bnet_region_select{width:50%;}.pt.hm .bnet_img {height:20px;margin-top:-3px;width:auto;width:initial;}.pt.hm br.bnet{display:none;}.pt.hm #ga_bnet_region_select{width:auto;width:initial;}</style><a href="javascript:void(0);" onclick="window.location=\'plugin.php?id=ga_bnet:connect&region=\'+document.getElementById(\'ga_bnet_region_select\').value+\'&state=' . FORMHASH . '\'"><img src="source/plugin/ga_bnet/images/button_120X24.jpg" align="absmiddle" alt="' . lang('plugin/ga_bnet', 'bnet_login') . '" style="cursor:pointer" class="vm bnet_img"></a><br class="bnet"><select name="ga_bnet_region_select" id="ga_bnet_region_select">
	<option value="cn">' . lang('plugin/ga_bnet', 'bnet_region_cn') . '</option>
	<option value="us">' . lang('plugin/ga_bnet', 'bnet_region_us') . '</option>
	<option value="eu">' . lang('plugin/ga_bnet', 'bnet_region_eu') . '</option>
	<option value="tw">' . lang('plugin/ga_bnet', 'bnet_region_tw') . '</option>
	<option value="kr">' . lang('plugin/ga_bnet', 'bnet_region_kr') . '</option>
	<option value="sea">' . lang('plugin/ga_bnet', 'bnet_region_sea') . '</option>
</select>&nbsp;';

	}

	function global_login_extra() {
		if ($this->config['show_login']) {
			return '<div class="fastlg_fm y" style="margin-right: 10px; padding-right: 10px"><p><a title="' . lang('plugin/ga_bnet', 'bnet_login') . '" href="javascript:void(0);" onclick="window.location=\'plugin.php?id=ga_bnet:connect&region=\'+document.getElementById(\'ga_bnet_region_select_login_extra\').value+\'&state=' . FORMHASH . '\'"><img style="height:36px;"  src="source/plugin/ga_bnet/images/button_120X24.jpg" alt="' . lang('plugin/ga_bnet', 'bnet_login') . '"></a></p><!--<p class="hm xg1" style="padding-top: 2px;">' . lang('plugin/ga_bnet', 'ga_bnet_tip') . '</p>--><select style="height:20px;width:100%" name="ga_bnet_region_select_login_extra" id="ga_bnet_region_select_login_extra">
	<option value="cn">' . lang('plugin/ga_bnet', 'bnet_region_cn') . '</option>
	<option value="us">' . lang('plugin/ga_bnet', 'bnet_region_us') . '</option>
	<option value="eu">' . lang('plugin/ga_bnet', 'bnet_region_eu') . '</option>
	<option value="tw">' . lang('plugin/ga_bnet', 'bnet_region_tw') . '</option>
	<option value="kr">' . lang('plugin/ga_bnet', 'bnet_region_kr') . '</option>
	<option value="sea">' . lang('plugin/ga_bnet', 'bnet_region_sea') . '</option>
</select></div>';
		}
	}

	function global_usernav_extra1() {
		$rs = DB::fetch_first("SELECT * FROM " . DB::table("ga_bnet") . " WHERE `forumuid` = " . $this->_G['uid']);
		if (empty($rs) && $this->config['show_bind']) {
			return '<a title="' . lang('plugin/ga_bnet', 'bnet_login_bind') . '" class=""  href="javascript:void(0);" onclick="window.location=\'plugin.php?id=ga_bnet:connect&op=bind&region=\'+document.getElementById(\'ga_bnet_region_select_usernav\').value+\'&state=' . FORMHASH . '\'"><img style="height: 20px;margin-top: -3px;"  src="source/plugin/ga_bnet/images/bind.jpg" alt="' . lang('plugin/ga_bnet', 'bnet_login_bind') . '" class="vm"></a><select name="ga_bnet_region_select_usernav" id="ga_bnet_region_select_usernav">
	<option value="cn">' . lang('plugin/ga_bnet', 'bnet_region_cn') . '</option>
	<option value="us">' . lang('plugin/ga_bnet', 'bnet_region_us') . '</option>
	<option value="eu">' . lang('plugin/ga_bnet', 'bnet_region_eu') . '</option>
	<option value="tw">' . lang('plugin/ga_bnet', 'bnet_region_tw') . '</option>
	<option value="kr">' . lang('plugin/ga_bnet', 'bnet_region_kr') . '</option>
	<option value="sea">' . lang('plugin/ga_bnet', 'bnet_region_sea') . '</option>
</select>';
		}
	}

	function global_login_text() {
		if ($this->config['show_login']) {
			return $this->register_bottom_cnstr;
		}
	}
}

class plugin_ga_bnet_home extends plugin_ga_bnet {
	function logging_method() {
		if ($this->config['show_login']) {
			return $this->register_bottom_cnstr;
		}
	}

}

class plugin_ga_bnet_member extends plugin_ga_bnet {
	function logging_method() {
		if ($this->config['show_login']) {
			return $this->register_bottom_cnstr;
		}
	}

	function register_logging_method() {
		if ($this->config['show_login']) {
			return $this->register_bottom_cnstr;
		}
	}
}
