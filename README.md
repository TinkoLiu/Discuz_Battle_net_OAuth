# Discuz_Battle_net_OAuth

A Battle.net OAuth login plugin for Discuz! X.

Made by Tinko for [Goblin Academy](https://bbs.islga.org/).

## Usage

1. Place `ga_bnet` folder into `[discuz_root]/source/plugin`
2. Install the plugin.
3. Configure it with your Battle.net API Key and Secret from [Battle.net Developer Portal - Blizzard Developer Portal](https://dev.battle.net/)

## Using as captcha

This plugin can works as a captcha plugin and return the Battle.net account binding status. Due to the restriction of Discuz!, you can not turn on it along with the standard captcha.

If you decided using the Battle.net account binding status as the captcha, please notice:

* When checking on posting, only the users who bound the Battle.net account can post and without any further captcha.
* When checking on login, only the users who bounded ahead can login by password. Any user without Battle.net account bound (including the administrator) can not login. The admin console (`admin.php`) won't be influenced by this.
* When checking on registration, nobody can get registered. The only way for  new user is login with Battle.net and create a new account after OAuth authentication flow.
* **When you are heading for disabling or uninstalling the plugin, do remember selecting another kind of captcha in your Discuz! admin console before you do so.**
* **Make sure you can login your forum through the Battle.net before you switch to this captcha.**
