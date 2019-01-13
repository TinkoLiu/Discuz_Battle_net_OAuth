<?php
if(!defined('IN_DISCUZ') || !defined('IN_ADMINCP')) {
	exit('Access Denied');
}

$lang = [
  "extinfo" => lang('plugin/ga_bnet', 'extinfo'),
  "redirect_uri" => lang('plugin/ga_bnet', 'redirect_uri'),
  "redirect_uri_desc" => lang('plugin/ga_bnet', 'redirect_uri_desc'),
  "ext_notice" => lang('plugin/ga_bnet', 'ext_notice'),
  "ext_notice_1" => lang('plugin/ga_bnet', 'ext_notice_1'),
  "ext_notice_2" => lang('plugin/ga_bnet', 'ext_notice_2'),
  "ext_notice_3" => lang('plugin/ga_bnet', 'ext_notice_3'),
  "ext_notice_4" => lang('plugin/ga_bnet', 'ext_notice_4'),
];

function get_redirect_uri( $use_forwarded_host = false )
{
    $ssl      = ( ! empty( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' );
    $sp       = strtolower( $_SERVER['SERVER_PROTOCOL'] );
    $protocol = substr( $sp, 0, strpos( $sp, '/' ) ) . ( ( $ssl ) ? 's' : '' );
    $port     = $_SERVER['SERVER_PORT'];
    $port     = ( ( ! $ssl && $port=='80' ) || ( $ssl && $port=='443' ) ) ? '' : ':'.$port;
    $host     = ( $use_forwarded_host && isset( $_SERVER['HTTP_X_FORWARDED_HOST'] ) ) ? $_SERVER['HTTP_X_FORWARDED_HOST'] : ( isset( $_SERVER['HTTP_HOST'] ) ? $_SERVER['HTTP_HOST'] : null );
    $host     = isset( $host ) ? $host : $_SERVER['SERVER_NAME'] . $port;
    return $protocol . '://' . $host . dirname($_SERVER['REQUEST_URI']);
}

$uri = get_redirect_uri();

$html = <<<EOF
<table class="tb tb2"><tbody>
  <tr>
    <th colspan="15" class="partition">{$lang["extinfo"]}</th>
  </tr>
  <tr><td colspan="2" class="td27" s="1">{$lang["redirect_uri"]}</td></tr>
  <tr class="noborder">
    <td class="vtop rowform"><input value=$uri type="text" class="txt" disabled></td>
    <td class="vtop tips2" s="1">{$lang["redirect_uri_desc"]}</td>
  </tr>
  <tr><td colspan="2" class="td27" s="1">{$lang["ext_notice"]}</td></tr>
</tbody></table>
<ol>
<li>{$lang["ext_notice_1"]}</li>
<li>{$lang["ext_notice_2"]}</li>
<li>{$lang["ext_notice_3"]}</li>
<li>{$lang["ext_notice_4"]} <a href="https://github.com/TinkoLiu/Discuz_Battle_net_OAuth" target="_blank" rel="noreferrer noopener nofollow">https://github.com/TinkoLiu/Discuz_Battle_net_OAuth</a></li>
</ol>

EOF;

echo $html;
?>
