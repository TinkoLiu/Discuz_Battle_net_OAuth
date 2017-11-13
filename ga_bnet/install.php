<?php

if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

$sql = <<<EOF
CREATE TABLE IF NOT EXISTS `cdb_ga_bnet` (
  `forumuid` mediumint(8) unsigned NOT NULL,
  `oauth_token` char(64) NOT NULL,
  `bnet_id` char(20) NOT NULL DEFAULT '-',
  `battletag` varchar(50) NOT NULL DEFAULT '',
  `region` char(5) NOT NULL DEFAULT '',
  `bindtime` int(10) unsigned NOT NULL DEFAULT '0',
  UNIQUE KEY `forumuid` (`forumuid`),
  KEY `bnet_id` (`bnet_id`)
) ENGINE=MyISAM ;
EOF;

runquery($sql);

$finish = true;

?>
