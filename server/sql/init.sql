-- ----------------------------
-- 数据库初始化
-- ----------------------------
truncate table admin_cron_log;
truncate table admin_log;
truncate table admin_retained;
delete from admin_roles where id > 1;
truncate table admin_permissions;
delete from admin_user where id > 1;