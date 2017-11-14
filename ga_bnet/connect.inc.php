<?php
session_start();
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
$referer = dreferer();
if (!isset($_G['cache']['plugin']['ga_bnet'])) {
	loadcache('plugin');
}

class bnetApiUrl {
	private $host = [
		"oauth" => [
			"cn" => "https://www.battlenet.com.cn/oauth",
			"us" => "https://us.battle.net/oauth",
			"eu" => "https://eu.battle.net/oauth",
			"kr" => "https://apac.battle.net/oauth",
			"tw" => "https://apac.battle.net/oauth",
			"sea" => "https://us.battle.net/oauth"],
		"api" => [
			"cn" => "https://api.battlenet.com.cn",
			"us" => "https://us.api.battle.net",
			"eu" => "https://eu.api.battle.net",
			"kr" => "https://kr.api.battle.net",
			"tw" => "https://tw.api.battle.net",
			"sea" => "https://us.api.battle.net"],
	];
	private $services = [
		"oauth" => [
			"auth" => "/authorize?",
			"token" => "/token?",
			"check_token" => "/check_token?",
		],
		"api" => [
			"account" => [
				"user" => "/account/user?",
			],
		],
	];
	function getUrl($region) {
		return [
			"authURL" => $this->host["oauth"][$region] . $this->services["oauth"]["auth"],
			"tokenURL" => $this->host["oauth"][$region] . $this->services["oauth"]["token"],
			"checkTokenURL" => $this->host["oauth"][$region] . $this->services["oauth"]["check_token"],
			"userInfoURL" => $this->host["api"][$region] . $this->services["api"]["account"]["user"],
		];
	}
}
$op = htmlspecialchars(getgpc('op', 'G'));
if (isset($_GET["region"])) {
	$arr = new bnetApiUrl();
	if ($_GET["region"] != "cn" && $_GET["region"] != "us" && $_GET["region"] != "eu" && $_GET["region"] != "kr" && $_GET["region"] != "tw" && $_GET["region"] != "sea") {
		showmessage('ga_bnet:invalid_bnet_region', $_G['siteurl'], [], ['alert' => 'error']);
	}
	$_SESSION["ga_bnet"]["region"] = htmlspecialchars(getgpc('region', 'G'));
	$_SESSION["ga_bnet"]["url"] = (array) $arr->getURL($_SESSION["ga_bnet"]["region"]);
}
if ($op == 'dzconnect') {
	$_GET['state'] != FORMHASH && showmessage('ga_bnet:wrong_formhash', $_G['siteurl'], [], ['alert' => 'info']);

	$oauth_code = htmlspecialchars(getgpc('code', 'G'));
	$_SESSION["ga_bnet"]["tokenInfo"] = get_oauth_token($oauth_code);
	$_SESSION["ga_bnet"]["accInfo"] = get_oauth_identity();

	$forumuid = DB::result_first('SELECT `forumuid` FROM ' . DB::table('ga_bnet') . " WHERE `bnet_id` = " . intval($_SESSION["ga_bnet"]["accInfo"]["id"]));

	if ($forumuid) {
		$userinfo = array('uid' => $forumuid);
		ga_login($userinfo, getcookie('ga_refer'));
	} else {
		$formhash = formhash();
		$navtitle = lang('plugin/ga_bnet', 'bind_bnet');
		$referer = getcookie('ga_refer');
		include template('ga_bnet:bindnew');
	}
} elseif ($op == 'bindconnect') {
	$_GET['state'] != FORMHASH && showmessage('ga_bnet:wrong_formhash', $_G['siteurl'], [], ['alert' => 'info']);

	if (!$_G['uid']) {
		dheader('Location:member.php?mod=logging&action=login');
	}
	$oauth_code = getgpc('code', 'G');
	$_SESSION["ga_bnet"]["tokenInfo"] = get_oauth_token($oauth_code);
	$_SESSION["ga_bnet"]["accInfo"] = get_oauth_identity();
	$forumuid = DB::result_first('SELECT `forumuid` FROM ' . DB::table('ga_bnet') . " WHERE `bnet_id` = " . intval($_SESSION["ga_bnet"]["accInfo"]["id"]));

	if ($forumuid) {
		showmessage('ga_bnet:chn_bnet_user_before_bind');
	}

	$fields = array(
		'forumuid' => $_G['uid'],
		'oauth_token' => htmlspecialchars($_SESSION["ga_bnet"]["tokenInfo"]["token"]),
		'bnet_id' => intval($_SESSION["ga_bnet"]["accInfo"]["id"]),
		'battletag' => diconv(htmlspecialchars($_SESSION["ga_bnet"]["accInfo"]["battletag"]), 'UTF-8'),
		'bindtime' => TIMESTAMP,
		'region' => $_SESSION["ga_bnet"]["region"],
	);

	if (addbindinfo($fields)) {
		showmessage('ga_bnet:bind_success', getcookie('ga_refer'));
	} else {
		showmessage('ga_bnet:failure_bind_bnet_user');
	}

} elseif ($op == 'disconnect') {
	$_GET['state'] != FORMHASH && showmessage('ga_bnet:wrong_formhash', $_G['siteurl'], [], ['alert' => 'info']);

	if ($_G['uid']) {
		$binded = DB::fetch_first('SELECT * FROM ' . DB::table('ga_bnet') . ' WHERE forumuid = ' . $_G['uid']);
		if (!empty($binded)) {
			DB::delete('ga_bnet', " forumuid = '" . $_G['uid'] . "'");
			showmessage('ga_bnet:disbind_success', $referer, array(), array('timeout' => '1', 'alert' => 'right'));
		} else {
			showmessage('ga_bnet:notbinded');
		}

	} else {
		dheader('Location:member.php?mod=logging&action=login');
	}

} elseif ($op == 'bindnew') {
	if (submitcheck('ga_username', 1)) {
		include template('common/header_ajax');
		$style = intval(getgpc('style', 'G'));

		if ($style == '0') {
			if (safecheck()) {
				echo '-1';
			} else {
				$ga_username = addslashes(htmlspecialchars(trim(getgpc('ga_username'))));
				$ga_password = addslashes(htmlspecialchars(trim(getgpc('ga_password'))));
				if (function_exists('fetch_uid_by_username')) {
					$uidfromun = C::t('common_member')->fetch_uid_by_username($ga_username);
				} else {
					$uidfromun = ga_fetch_uid_by_username($ga_username);
				}
				$sql = 'SELECT `forumuid` FROM ' . DB::table('ga_bnet') . " WHERE `bnet_id` = '{$_SESSION["ga_bnet"]["accInfo"]["id"]}' OR `forumuid` = '{$uidfromun}'";
				$rs = DB::fetch_first($sql);
				if (!empty($rs)) {
					echo '3';
				} else {
					$sql = 'SELECT `salt` FROM ' . DB::table('ucenter_members') . " WHERE `username` = '{$ga_username}'";
					$rs = DB::fetch_first($sql);
					$salt = $rs['salt'];
					if (!empty($rs)) {
						$sql = 'SELECT `uid`, `password` FROM ' . DB::table('ucenter_members') . " WHERE `username` = '{$ga_username}'";
						$rs = DB::fetch_first($sql);
						if (md5(md5($ga_password) . $salt) == $rs['password']) {
							$bind_u_info = array(
								'forumuid' => $rs['uid'],
								'oauth_token' => htmlspecialchars($_SESSION["ga_bnet"]["tokenInfo"]["token"]),
								'bnet_id' => intval($_SESSION["ga_bnet"]["accInfo"]["id"]),
								'battletag' => diconv(htmlspecialchars($_SESSION["ga_bnet"]["accInfo"]["battletag"]), 'UTF-8'),
								'bindtime' => TIMESTAMP,
								'region' => $_SESSION["ga_bnet"]["region"],
							);
							$insertid = addbindinfo($bind_u_info);
							if ($insertid) {
								$niuc_uinfo = array('uid' => $rs['uid']);
								connect_login($niuc_uinfo);
								manageaftlogin($niuc_uinfo);
								echo '0';
							} else {
								echo '4';
							}

						} else {
							echo '2';
						}

					} else {
						echo '1';
					}

				}
			}
		} elseif ($style == '1') {
			if (safecheck()) {
				echo '-1';
			} else {
				$newusername = addslashes(htmlspecialchars(trim(getgpc('ga_username'))));
				$newpassword = addslashes(htmlspecialchars(trim(getgpc('ga_password'))));
				$newrepassword = addslashes(htmlspecialchars(trim(getgpc('ga_repassword'))));
				$newemail = strtolower(addslashes(htmlspecialchars(trim(getgpc('ga_email')))));
				if ($newpassword != $newrepassword || $newpassword == '') {
					echo '17';
				} else {
					if (ga_fetch_uid_by_username($newusername)) {
						echo '11';
					} else {
						loaducenter();
						$uid = uc_user_register($newusername, $newpassword, $newemail);
						if ($uid <= 0) {
							if ($uid == -1) {
								echo '12';
							} elseif ($uid == -2) {
								echo '13';
							} elseif ($uid == -3) {
								echo '11';
							} elseif ($uid == -4) {
								echo '14';
							} elseif ($uid == -5) {
								echo '15';
							} elseif ($uid == -6) {
								echo '16';
							}
						} else {
							$sql = "SELECT * FROM " . DB::table('common_usergroup') . ' WHERE groupid = \'' . $_G['cache']['plugin']['ga_bnet']['group'] . '\'';
							$group = DB::fetch_first($sql);
							$newadminid = in_array($group['radminid'], array(1, 2, 3)) ? $group['radminid'] : ($group['type'] == 'special' ? -1 : 0);
							loadcache('fields_register');
							$init_arr = explode(',', $_G['setting']['initcredits']);
							$password = md5(random(10));
							addmember($uid, $newusername, $password, $newemail, $_SERVER['REMOTE_ADDR'], $_G['cache']['plugin']['ga_bnet']['group'], array('credits' => $init_arr), $newadminid);
							if ($_G['cache']['plugin']['ga_bnet']['credit']) {
								$credit_style = $_G['cache']['plugin']['ga_bnet']['credit'];
								$sql = 'SELECT `extcredits' . $credit_style . '` FROM ' . DB::table('common_member_count') . " WHERE uid = '{$uid}'";
								$ucredit = DB::fetch_first($sql);
								$data = array('extcredits' . $credit_style => $ucredit['extcredits' . $credit_style] + $_G['cache']['plugin']['ga_bnet']['credit_quan']);
								DB::update("common_member_count", $data, "uid = '{$uid}'");
							}
							$bind_u_info = array(
								'forumuid' => $uid,
								'oauth_token' => htmlspecialchars($_SESSION["ga_bnet"]["tokenInfo"]["token"]),
								'bnet_id' => intval($_SESSION["ga_bnet"]["accInfo"]["id"]),
								'battletag' => diconv(htmlspecialchars($_SESSION["ga_bnet"]["accInfo"]["battletag"]), 'UTF-8'),
								'bindtime' => TIMESTAMP,
								'region' => $_SESSION["ga_bnet"]["region"],
							);
							$insertid = addbindinfo($bind_u_info);
							if ($insertid) {
								$niuc_uinfo = array('uid' => $uid);
								connect_login($niuc_uinfo, getcookie('baidu_refer'));
								manageaftlogin($niuc_uinfo);
								echo '10';
							} else {
								echo '4';
							}

						}
					}
				}
			}
		}
		include template('common/footer_ajax');
	}
} else {
	$_GET['state'] != FORMHASH && showmessage('ga_bnet:wrong_formhash', $_G['siteurl'], [], ['alert' => 'info']);
	$ops = $op == 'bind' ? 'bindconnect' : 'dzconnect';
	dsetcookie('ga_refer', $referer);
	$redirect_uri = $_G['siteurl'] . "plugin.php?id=ga_bnet:connect&op={$ops}";
	$params = array(
		'response_type' => 'code',
		'client_id' => $_G['cache']['plugin']['ga_bnet']['Bnet_Key'],
		'scope' => 'sc2.profile',
		'state' => FORMHASH,
		'redirect_uri' => $redirect_uri,
	);
	$_SESSION['STATE'] = $params['state'];
	$_SESSION["ga_bnet"]["redirect_uri"] = $redirect_uri;
	$url = $_SESSION["ga_bnet"]["url"]["authURL"] . http_build_query($params);
	header("Location: $url");
}

function ga_login($userinfo, $referer) {
	global $_G;
	connect_login($userinfo);
	$ucsynlogin = manageaftlogin($userinfo);
	loadcache('usergroups');
	$usergroups = $_G['cache']['usergroups'][$_G['groupid']]['grouptitle'];
	$param = array('username' => $_G['member']['username'], 'usergroup' => $_G['group']['grouptitle']);
	showmessage('login_succeed', $referer ? $referer : './', $param, array('extrajs' => $ucsynlogin, 'showdialog' => 1, 'locationtime' => true));
}

function addbindinfo($fields) {
	global $_G;
	return DB::insert('ga_bnet', $fields);
}

function manageaftlogin($userinfo) {
	global $_G;
	DB::update(('common_member_status'), array('lastip' => $_G['clientip'], 'lastvisit' => TIMESTAMP, 'lastactivity' => TIMESTAMP), 'uid=\'' . $userinfo['uid'] . '\'');
	if ($_G['setting']['allowsynlogin']) {
		loaducenter();
		return uc_user_synlogin($_G['uid']);
	}
}

function connect_login($connect_member) {
	global $_G;
	$member = DB::fetch_first('SELECT * FROM ' . DB::table('common_member') . " WHERE `uid` = '$connect_member[uid]'");
	if (!($member = getuserbyuid($connect_member['uid'], 1))) {
		return false;
	} else {
		if (isset($member['_inarchive'])) {
			C::t('common_member_archive')->move_to_master($member['uid']);
		}
	}
	require_once libfile('function/member');
	$cookietime = 2592000;
	setloginstatus($member, $cookietime);
	return true;
}

function get_oauth_token($code) {
	global $_G;
	$params = array(
		'grant_type' => 'authorization_code',
		'client_id' => $_G['cache']['plugin']['ga_bnet']['Bnet_Key'],
		'client_secret' => $_G['cache']['plugin']['ga_bnet']['Bnet_Secret'],
		'scope' => 'sc2.profile',
		'code' => $code,
		'redirect_uri' => $_SESSION["ga_bnet"]["redirect_uri"],
	);
	$url_params = http_build_query($params);
	$url = $_SESSION["ga_bnet"]["url"]["tokenURL"] . $url_params;
	$curl = curl_init();
	curl_setopt($curl, CURLOPT_URL, $url);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($curl, CURLOPT_POST, 1);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
	$result = curl_exec($curl);
	$result_obj = json_decode($result, true);
	$access_token = $result_obj['access_token'];
	$expires_in = $result_obj['expires_in'];
	$expires_at = time() + $expires_in;
	if (!$access_token || !$expires_in) {
		runlog('ga_bnet_error', "GA_Bnet OAuth Failed. method:get_oauth_token API Request URL:" . $_SESSION["ga_bnet"]["url"]["tokenURL"] . " Combined param: code: " . $code . " redirect_uri: " . $_SESSION["ga_bnet"]["redirect_uri"] . " access_token json:" . $result . "SESSION ga_bnet:" . json_encode($_SESSION["ga_bnet"]));
		showmessage('ga_bnet:bnet_invalid_token', $_G['siteurl'], [], ['alert' => 'error']);
	} else {
		return [
			"token" => $access_token,
			"expires_in" => $expires_in,
			"expires_at" => $expires_at,
		];
	}
	showmessage('ga_bnet:bnet_invalid_token', $_G['siteurl'], [], ['alert' => 'error']);
}

function get_oauth_identity() {
	global $_G;
	$params = array(
		'access_token' => $_SESSION["ga_bnet"]["tokenInfo"]["token"],
	);
	$url_params = http_build_query($params);
	$url = $_SESSION["ga_bnet"]["url"]["userInfoURL"] . $url_params;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$result = curl_exec($ch);
	$result_obj = json_decode($result, true);
	if (!$result_obj['id']) {
		runlog('ga_bnet_error', "GA_Bnet OAuth Failed. method:get_oauth_identity API Request URL:" . $_SESSION["ga_bnet"]["url"]["userInfoURL"] . " Combined param: access_token: " . $_SESSION["ga_bnet"]["tokenInfo"]["token"] . " result json:" . $result . "SESSION ga_bnet:" . json_encode($_SESSION["ga_bnet"]));
		showmessage('ga_bnet:bnet_user_info_error', $_G['siteurl'], [], ['alert' => 'error']);
	}
	return $result_obj;
}

//safe exam
function safecheck() {
	foreach ($_POST as $key => $value) {
		if (ga_safe($_POST[$key]) == 'ga_Forbidden') {
			return 1;
		}
		//unsafe
	}
	return 0;
}

function ga_safe($string) {
	$pattern = "/select|insert|update|delete|drop|alter|truncate|union|\%|\'|\"|\\\|char\(/i";
	preg_match($pattern, $string, $matches);
	return count($matches) > 0 ? 'ga_Forbidden' : $string;
}

function ga_fetch_uid_by_username($username) {
	$sql = 'SELECT uid FROM ' . DB::table('common_member') . ' WHERE username=\'' . $username . '\'';
	$tmp = DB::fetch_first($sql);
	return empty($tmp) ? 0 : $tmp['uid'];
}

function addmember($uid, $username, $password, $email, $ip, $groupid, $extdata, $adminid = 0) {
	$credits = isset($extdata['credits']) ? $extdata['credits'] : array();
	$profile = isset($extdata['profile']) ? $extdata['profile'] : array();
	$base = array(
		'uid' => $uid,
		'username' => (string) $username,
		'password' => (string) $password,
		'email' => (string) $email,
		'adminid' => intval($adminid),
		'groupid' => intval($groupid),
		'regdate' => TIMESTAMP,
		'emailstatus' => intval($extdata['emailstatus']),
		'credits' => intval($credits[0]),
		'timeoffset' => 9999,
	);

	$status = array(
		'uid' => $uid,
		'regip' => (string) $ip,
		'lastip' => (string) $ip,
		'lastvisit' => TIMESTAMP,
		'lastactivity' => TIMESTAMP,
		'lastpost' => 0,
		'lastsendmail' => 0,
	);

	$count = array(
		'uid' => $uid,
		'extcredits1' => intval($credits[1]),
		'extcredits2' => intval($credits[2]),
		'extcredits3' => intval($credits[3]),
		'extcredits4' => intval($credits[4]),
		'extcredits5' => intval($credits[5]),
		'extcredits6' => intval($credits[6]),
		'extcredits7' => intval($credits[7]),
		'extcredits8' => intval($credits[8]),
	);

	$profile['uid'] = $uid;
	$field_forum['uid'] = $uid;
	$field_home['uid'] = $uid;
	DB::insert('common_member', $base, true);
	DB::insert('common_member_status', $status, true);
	DB::insert('common_member_count', $count, true);
	DB::insert('common_member_profile', $profile, true);
	DB::insert('common_member_field_forum', $field_forum, true);
	DB::insert('common_member_field_home', $field_home, true);
	DB::insert('common_setting', array('skey' => 'lastmember', 'svalue' => $username), false, true);
	manyoulog('user', $uid, 'add');
	$totalmembers = DB::result_first("SELECT COUNT(*) FROM " . DB::table('common_member'));
	$userstats = array('totalmembers' => $totalmembers, 'newsetuser' => stripslashes($username));
	save_syscache('userstats', $userstats);
}
