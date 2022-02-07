SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for admin_cron
-- ----------------------------
DROP TABLE IF EXISTS `admin_cron`;
CREATE TABLE `admin_cron`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '定时器名称',
  `class` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '类名',
  `function` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '方法名',
  `begin_time` datetime NULL DEFAULT NULL COMMENT '开始时间',
  `end_time` datetime NULL DEFAULT NULL COMMENT '结束时间',
  `next_time` datetime NULL DEFAULT NULL COMMENT '下次执行时间',
  `cron` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '定时器执行规则',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '更新时间',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '状态  0开启 1关闭',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 8 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin_cron
-- ----------------------------
INSERT INTO `admin_cron` VALUES (7, '统计留存数据', 'Retained', 'main', NULL, NULL, '2021-12-08 11:30:00', '0 */10 * * * *', '2021-10-20 17:12:43', '2021-12-08 11:20:00', 0);

-- ----------------------------
-- Table structure for admin_cron_log
-- ----------------------------
DROP TABLE IF EXISTS `admin_cron_log`;
CREATE TABLE `admin_cron_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `code` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0成功 1失败',
  `msg` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '失败或者成功信息',
  `time` datetime NOT NULL COMMENT '执行时间',
  `cron_id` int(11) NOT NULL COMMENT '定时器id',
  `time_len` int(11) NULL DEFAULT NULL COMMENT '执行时长',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 13 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Table structure for admin_log
-- ----------------------------
DROP TABLE IF EXISTS `admin_log`;
CREATE TABLE `admin_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `api` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'api接口',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '操作名称',
  `time` datetime NOT NULL COMMENT '操作时间',
  `ip` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '客户端ip',
  `uid` int(11) NOT NULL COMMENT '操作人员',
  `status` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0成功  1 失败',
  `info` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '详细信息',
  `request` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '请求信息',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 146 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for admin_menu
-- ----------------------------
DROP TABLE IF EXISTS `admin_menu`;
CREATE TABLE `admin_menu`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '名称',
  `pid` int(11) NOT NULL COMMENT '父级id',
  `path` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '路由地址',
  `component` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '组件',
  `redirect` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '重定向地址',
  `status` tinyint(1) NOT NULL COMMENT '状态 0 开启 1关闭',
  `alwaysShow` tinyint(1) NOT NULL COMMENT '是否固定显示',
  `meta` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '菜单属性 json  title:名称  icon：图标',
  `create_time` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '修改时间',
  `hidden` int(1) NOT NULL DEFAULT 0 COMMENT '隐藏显示',
  `order` int(11) NULL DEFAULT 0 COMMENT '排序',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 49 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of admin_menu
-- ----------------------------
INSERT INTO `admin_menu` VALUES (1, 'system', 0, '/system', 'Layout', '/', 0, 0, '{\"title\":\"\\u7cfb\\u7edf\\u7ba1\\u7406\",\"icon\":\" el-icon-s-tools\"}', '2021-10-16 11:36:13', '2021-10-16 11:36:13', 0, 9);
INSERT INTO `admin_menu` VALUES (2, 'menu', 1, '/system/menu', 'Menu', '', 0, 1, '{\"title\":\"\\u83dc\\u5355\\u7ba1\\u7406\",\"icon\":\"\"}', '2021-10-14 20:27:34', '2021-10-14 20:27:34', 0, 1);
INSERT INTO `admin_menu` VALUES (3, 'user', 0, '/auth', 'Layout', '', 0, 0, '{\"title\":\"\\u6743\\u9650\\u7ba1\\u7406\",\"icon\":\"user\"}', '2021-10-16 11:36:24', '2021-10-16 11:36:24', 0, 8);
INSERT INTO `admin_menu` VALUES (4, '', 3, 'user/index', 'User', '', 0, 1, '{\"title\":\"\\u7ba1\\u7406\\u5458\\u7ba1\\u7406\",\"icon\":\"\"}', '2021-10-14 20:28:32', '2021-10-14 20:28:32', 0, 1);
INSERT INTO `admin_menu` VALUES (5, '', 3, 'roles/index', 'Roles', '', 0, 1, '{\"title\":\"\\u89d2\\u8272\\u7ba1\\u7406\",\"icon\":\"\"}', '2021-10-14 20:28:36', '2021-10-14 20:28:36', 0, 2);
INSERT INTO `admin_menu` VALUES (13, '', 1, '/system/site', 'Site', '', 0, 1, '{\"title\":\"\\u7ad9\\u70b9\\u7ba1\\u7406\",\"icon\":\"\"}', '2021-10-14 20:27:53', '2021-10-14 20:27:53', 0, 4);
INSERT INTO `admin_menu` VALUES (26, '', 1, '/system/cache', 'Cache', '', 0, 1, '{\"title\":\"\\u7f13\\u5b58\\u7ba1\\u7406\",\"icon\":\"\"}', '2021-10-14 20:27:48', '2021-10-14 20:27:48', 0, 3);
INSERT INTO `admin_menu` VALUES (31, '', 1, '/system/log', 'Log', '', 0, 1, '{\"title\":\"\\u64cd\\u4f5c\\u65e5\\u5fd7\",\"icon\":\"\"}', '2021-12-06 20:15:54', '2021-12-06 20:15:54', 0, 2);
INSERT INTO `admin_menu` VALUES (46, '', 1, '/system/cron', 'system:cron', '', 0, 1, '{\"title\":\"\\u5b9a\\u65f6\\u4efb\\u52a1\",\"icon\":\"\"}', '2021-10-20 16:45:49', '2021-10-20 16:45:49', 0, 5);
INSERT INTO `admin_menu` VALUES (47, '', 0, '/operating', 'Layout', '', 0, 0, '{\"title\":\"\\u8fd0\\u8425\\u7ba1\\u7406\",\"icon\":\"el-icon-s-flag\"}', '2021-12-07 14:45:30', '2021-12-07 14:45:30', 0, 1);
INSERT INTO `admin_menu` VALUES (48, '', 47, '/operating/on', 'operating:on', '', 0, 1, '{\"title\":\"\\u5728\\u7ebf\\u8be6\\u60c5\",\"icon\":\"\"}', '2021-12-07 14:47:20', '2021-12-07 14:47:20', 0, 1);

-- ----------------------------
-- Table structure for admin_permissions
-- ----------------------------
DROP TABLE IF EXISTS `admin_permissions`;
CREATE TABLE `admin_permissions`  (
  `role_id` int(11) NOT NULL COMMENT '角色id',
  `permissions` int(11) NOT NULL COMMENT '权限id'
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of admin_permissions
-- ----------------------------
INSERT INTO `admin_permissions` VALUES (2, 28);
INSERT INTO `admin_permissions` VALUES (2, 33);
INSERT INTO `admin_permissions` VALUES (2, 30);
INSERT INTO `admin_permissions` VALUES (2, 1);
INSERT INTO `admin_permissions` VALUES (2, 31);
INSERT INTO `admin_permissions` VALUES (3, 1);
INSERT INTO `admin_permissions` VALUES (3, 26);

-- ----------------------------
-- Table structure for admin_retained
-- ----------------------------
DROP TABLE IF EXISTS `admin_retained`;
CREATE TABLE `admin_retained`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `times` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '日期',
  `news` int(11) NULL DEFAULT 0 COMMENT '今日活跃',
  `days1` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '次日留存',
  `days3` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '三日留存',
  `days7` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '' COMMENT '七日留存',
  `daynews` int(11) NULL DEFAULT NULL COMMENT '今日新增',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 113 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '用户留存记录表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of admin_retained
-- ----------------------------
INSERT INTO `admin_retained` VALUES (111, '2021-12-07', 4, '50.00%', '0.00%', '0.00%', 1);
INSERT INTO `admin_retained` VALUES (112, '2021-12-08', 5, '0.00%', '0.00%', '0.00%', 0);

-- ----------------------------
-- Table structure for admin_roles
-- ----------------------------
DROP TABLE IF EXISTS `admin_roles`;
CREATE TABLE `admin_roles`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '角色名称',
  `roles` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '角色',
  `desc` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '描述',
  `create_time` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8 COLLATE = utf8_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of admin_roles
-- ----------------------------
INSERT INTO `admin_roles` VALUES (1, '超级管理员', 'admin', '最高权限', '2021-10-11 18:14:41', '2021-10-11 18:14:44');
INSERT INTO `admin_roles` VALUES (3, '编辑测试', 'edit01', '', '2021-12-06 20:36:29', '2021-12-06 20:40:23');

-- ----------------------------
-- Table structure for admin_system
-- ----------------------------
DROP TABLE IF EXISTS `admin_system`;
CREATE TABLE `admin_system`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `model` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '模块key',
  `key` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '设置key',
  `value` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '设置值value',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 7 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '系统设置表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of admin_system
-- ----------------------------
INSERT INTO `admin_system` VALUES (6, 'system', 'admin', '{\"title\":\"GMTool\",\"login_background\":\"http:\\/\\/upload.game.com\\/images\\/2021\\/12\\/06\\/59e856d16298d1e56c03b96f472538a7.png\",\"logo\":\"https:\\/\\/upload.xiaoguyun.cn\\/2021\\/10\\/02\\/36cc8690dd33a1d9ea1a9f65e472f5b7.png\",\"login_ex_time\":\"100000\",\"password\":\"123456\"}', '2021-12-06 20:00:09', '2021-12-06 20:00:09');

-- ----------------------------
-- Table structure for admin_user
-- ----------------------------
DROP TABLE IF EXISTS `admin_user`;
CREATE TABLE `admin_user`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `roles` int(10) NULL DEFAULT NULL COMMENT '角色',
  `username` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '账号',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '昵称',
  `password` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '密码',
  `avatar` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '头像',
  `phone` varchar(11) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '手机号',
  `token` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT 'token',
  `create_time` datetime NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` datetime NULL DEFAULT NULL COMMENT '修改时间',
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '邮箱',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 4 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '管理员表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of admin_user
-- ----------------------------
INSERT INTO `admin_user` VALUES (1, 1, 'admin', '系统管理员', 'e10adc3949ba59abbe56e057f20f883e', 'http://upload.game.com/images/2021/12/06/0186cb5c2657b745f93c1c1a26fd5881_200x150.gif', '123', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1aWQiOiIxIiwiaXNzIjoiaW1faHR0cCIsImlhdCI6MTYzODkyNzE3MSwiZXhwIjoxNjM4OTI3NzcxLCJuYmYiOjE2Mzg5MjcxNzEsInN1YiI6ImdhbWUuY29tIiwianRpIjoiZmZhYjcyYjg3OTQyNDQ2YTAzZmJiNmEwZmVhMDY0MTQifQ.695aND9tJTyOchrr78B_AQzmcvg_LmSBODWagfKkXE0', '2021-10-11 17:57:09', '2021-10-11 17:57:11', '1');
INSERT INTO `admin_user` VALUES (3, 3, 'sunshaoyi', '孙少毅', 'e10adc3949ba59abbe56e057f20f883e', 'https://www.cmd5.com/images/logo.png', NULL, 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1aWQiOiIzIiwiaXNzIjoiaW1faHR0cCIsImlhdCI6MTYzODc5NDI3NSwiZXhwIjoxNjM4Nzk0ODc1LCJuYmYiOjE2Mzg3OTQyNzUsInN1YiI6ImdhbWUuY29tIiwianRpIjoiOGFiYTBmMDA2ZmM1MDRlMjBjY2QzZDE3MDJjODQwYzIifQ.lbSlE4iHUL4yovJLshTsNjNt_qEgdEpC2o5YwGQxy6A', '2021-12-06 20:37:45', '2021-12-06 20:37:45', '28141178@qq.com');

SET FOREIGN_KEY_CHECKS = 1;
