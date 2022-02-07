-- ----------------------------
-- 新增菜单 运营额统计
-- ----------------------------
INSERT INTO `admin_menu` (`id`, `name`, `pid`, `path`, `component`, `redirect`, `status`, `alwaysShow`, `meta`, `create_time`, `update_time`, `hidden`, `order`) VALUES (49, '', 47, '/operating/turnover', 'operating:turnover', '', 0, 1, '{\"title\":\"\\u8425\\u4e1a\\u989d\\u7edf\\u8ba1\",\"icon\":null}', '2021-12-10 11:56:17', '2021-12-10 11:56:17', 0, 2);

-- ----------------------------
-- 新增定时任务 清除日志
-- ----------------------------
INSERT INTO `admin_cron` (`id`, `name`, `class`, `function`, `begin_time`, `end_time`, `next_time`, `cron`, `create_time`, `update_time`, `status`) VALUES (8, '定时清除操作日志', 'ActionLog', 'delete', NULL, NULL, '2021-12-14 16:34:00', '0 0 1 * * * ', '2021-12-14 16:08:10', '2021-12-14 16:35:01', 1);

-- ----------------------------
-- 用户留存表新增字段
-- ----------------------------

alter table admin_retained add column box_trading double DEFAULT NULL COMMENT 'box交易额';
alter table admin_retained add column nft_trading double DEFAULT NULL COMMENT 'nft交易额';
alter table admin_retained add column box_sales   double DEFAULT NULL COMMENT 'box 销售额';
ALTER  TABLE  `admin_retained`  ADD  INDEX index_timne (`times`);

-- ----------------------------
-- 日志表新增字段
-- ----------------------------
alter table admin_log add column client varchar(255) DEFAULT NULL COMMENT '客户端系统';