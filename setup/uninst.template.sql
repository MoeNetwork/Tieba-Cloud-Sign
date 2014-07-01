-- 百度云签到卸载： MySQL 语句模板
--
-- 手工卸载方法：
-- 替换所有{VAR-PREFIX}为你已安装的云签到的数据表前缀
--
-- 特别注意：如果没有明确给出数据库名称，请直接删除所有的 
-- 如果这是安装向导提示你手动安装的，请无视这些注释！


DROP TABLE IF EXISTS `{VAR-PREFIX}cron`;
DROP TABLE IF EXISTS `{VAR-PREFIX}options`;
DROP TABLE IF EXISTS `{VAR-PREFIX}users`;
DROP TABLE IF EXISTS `{VAR-PREFIX}tieba`;