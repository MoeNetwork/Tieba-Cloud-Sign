# Tieba-Cloud-Sign-Plugins

## 插件列表
### 云封禁
吧务专用，可循环封禁指定账号

**更新1.3版相关**

此版本需要用到 `STOKEN`，由于云签本体 `v4.98` 并不强制要求 `STOKEN`，请注意及时更新自己绑定的百度帐号的 `BDUSS` 以及 `STOKEN`

**更新到1.1版或更高版本请执行**
```sql
ALTER TABLE `tc_ver4_ban_list` CONVERT TO CHARACTER SET `utf8mb4` COLLATE `utf8mb4_general_ci`;
ALTER TABLE `tc_ver4_ban_userset` CONVERT TO CHARACTER SET `utf8mb4` COLLATE `utf8mb4_general_ci`;
ALTER TABLE `tc_ver4_ban_userset` CHANGE `c` `c` TEXT CHARACTER SET `utf8mb4` COLLATE `utf8mb4_general_ci`; 
ALTER TABLE `tc_ver4_ban_list`
  CHANGE `name` `name` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  CHANGE `tieba` `tieba` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  CHANGE `log` `log` LONGTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL,
  ADD `name_show` TEXT NULL AFTER `name`,
  ADD `portrait` TEXT NULL AFTER `name_show`;
UPDATE `tc_ver4_ban_list` SET `log` = REPLACE(`log`, "<br>", "<br>\n");
```
其中 `tc_` 是默认表名前缀，如有过修改请自行修改表名前缀

### 云知道抽奖
自动完成知道抽奖，每日

### 名人堂
每日自动助攻贴吧名人堂

**更新1.2版本相关**

已不再使用 `tc_ver4_rank_list` 这个表，故不需要执行1.1部分（下面几行）的内容；（对网站管理者）基于同样的理由请务必前往主程序的**计划任务**面板删除掉名为 `ver4_rank_daily` 的任务，否则计划任务会因需要的文件不存在而执行出错

**更新1.1版后请执行**
```sql
ALTER TABLE `tc_ver4_rank_list`
  CHANGE `nid` `nid` varchar(15) COLLATE 'utf8mb4_general_ci' NOT NULL AFTER `fid`,
  CHANGE `name` `name` varchar(255) COLLATE 'utf8mb4_general_ci' NOT NULL AFTER `nid`,
  CHANGE `tieba` `tieba` varchar(255) COLLATE 'utf8mb4_general_ci' NOT NULL AFTER `name`;
```
其中 `tc_` 是默认表名前缀，如有过修改请自行修改表名前缀

### 自动刷新贴吧列表
完全自动每日刷新贴吧列表

### 贴吧云审查
吧务专用，审查贴吧内不符合规范的帖子

### 公告栏
在 首页 显示公告栏

### FlatUI
提供Metro风格云签到

### 云签AmazeUI
云签的一款UI产品

### Gzip插件
将页面直接 GZip 压缩后传输给用户，可大幅减少流量使用，增加加载速度

### 每日用户签到结果qq推送
每日用户签到结果qq推送,目前支持go-cqhttp的正向WebSocket和HTTP API,基于D丶L和quericy的版本重写

### 每日数据库备份qq推送
每日数据库备份qq推送，目前支持go-cqhttp的正向WebSocket和HTTP API，基于D丶L的版本重写

