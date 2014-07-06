-- 百度云签到数据库信息卸载： MySQL 语句模板
--
-- 手工卸载方法：
-- 直接粘贴下面的语句到 工具箱 -- 运行SQL语句
-- 注意：若有分表，请手动删除


DROP TABLE IF EXISTS `{VAR-PREFIX}cron`;
DROP TABLE IF EXISTS `{VAR-PREFIX}options`;
DROP TABLE IF EXISTS `{VAR-PREFIX}users`;
DROP TABLE IF EXISTS `{VAR-PREFIX}tieba`;
DROP TABLE IF EXISTS `{VAR-PREFIX}baiduid`;