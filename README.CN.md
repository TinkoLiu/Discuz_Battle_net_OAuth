# Discuz_Battle_net_OAuth

Discuz! 论坛系统战网对接插件

Tinko @ [地精研究院](https://bbs.islga.org/).

## 用法

1. 将 `ga_bnet` 复制到 `[discuz根目录]/source/plugin`
2. 在“应用中心”安装插件
3. 在 [Blizzard Battle.net Developer Portal](https://develop.battlenet.com.cn/) 申请 `Client ID` 和 `Client Secret` 并填入插件设置页

## 提示

1. 插件安装并启用之后，会在插件设置页自动生成申请暴雪接口所需的回调地址；
2. 请确保 **session机制** 启用 ( 即后台管理设置项 **全局 - 性能优化 - 服务器优化 - 是否关闭session机制** 保持为 **否** )