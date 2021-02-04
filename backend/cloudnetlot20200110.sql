/*
 Navicat Premium Data Transfer

 Source Server         : 192.168.33.10
 Source Server Type    : MySQL
 Source Server Version : 50722
 Source Host           : 192.168.33.10:3306
 Source Schema         : cloudnetlot

 Target Server Type    : MySQL
 Target Server Version : 50722
 File Encoding         : 65001

 Date: 10/01/2020 17:16:34
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for cloudnetlot_admin
-- ----------------------------
DROP TABLE IF EXISTS `cloudnetlot_admin`;
CREATE TABLE `cloudnetlot_admin`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '管理员id',
  `username` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '管理员账号',
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '管理员密码',
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '管理员邮箱',
  `phonecode` varchar(255) NOT NULL COMMENT '国家区域码',
  `phone` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '管理员手机号',
  `remember_token` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '账号状态,0:禁用,1:启用',
  `created_at` bigint(13) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` bigint(13) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '管理员表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for cloudnetlot_area
-- ----------------------------
DROP TABLE IF EXISTS `cloudnetlot_area`;
CREATE TABLE `cloudnetlot_area`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '名称key',
  `pid` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '父区域code码',
  `code` int(11) UNSIGNED NOT NULL COMMENT '区域码',
  `mark` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '备注',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `code`(`code`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '区域表码对照表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for cloudnetlot_auth_tpl
-- ----------------------------
DROP TABLE IF EXISTS `cloudnetlot_auth_tpl`;
CREATE TABLE `cloudnetlot_auth_tpl`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name_zh` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '模板中文名',
  `name_en` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '模板英文名',
  `body_zh` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT '模板主题代码-中文',
  `body_en` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT '模板主题代码-英文',
  `thumb` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '缩略图地址',
  `appid` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '模板使用者ID',
  `trade` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '行业key',
  `button` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '按钮',
  `is_del` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除标志位,0:没有删除,1:已经删除',
  `created_at` bigint(13) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` bigint(13) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_auth_tpl_appid`(`appid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '认证模板表' ROW_FORMAT = Compact;


-- ----------------------------
-- Table structure for cloudnetlot_device
-- ----------------------------
DROP TABLE IF EXISTS `cloudnetlot_device`;
CREATE TABLE `cloudnetlot_device`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `dev_mac` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `dev_ip` varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '设备IP',
  `net_ip` varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '设备外网IP',
  `heartbeat` int(11) UNSIGNED NULL DEFAULT NULL COMMENT '心跳时间',
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '设备名称',
  `prtid` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '产品ID',
  `prt_type` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '产品类型,1:网络设备',
  `prt_size` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '产品型号',
  `cltid` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '客户端ID',
  `type` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '设备型号',
  `mode` tinyint(4) UNSIGNED NOT NULL DEFAULT 1 COMMENT '工作模式1:网关,2:中继,3:WISP,4:WDS,5:AP', 
  `version` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '固件版本',
  `up_time` bigint(13) UNSIGNED NOT NULL DEFAULT 0 COMMENT '固件升级时间',
  `pid` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '父设备MAC',
  `area` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '区域码',
  `country` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '国家',
  `province` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '省',
  `city` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '城市',
  `address` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '详细地址',
  `latitude` decimal(10, 7) NULL DEFAULT NULL COMMENT '纬度',
  `longitude` decimal(10, 7) NULL DEFAULT NULL COMMENT '经度',
  `chip` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '芯片',
  `sn` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '设备相对于云台的sn号',
  `notes` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL,
  `group_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '分钟ID',
  `is_ip_location` tinyint(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否需要IP定位0：否，1：是',
  `bind` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '设备绑定码',
  `is_del` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除,0:没有删除,1:已经删除',
  `join_time` bigint(13) UNSIGNED NULL DEFAULT NULL COMMENT '设备接入时间',
  `created_at` bigint(13) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` bigint(13) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_device_user_id`(`user_id`) USING BTREE,
  INDEX `fk_device_dev_mac`(`dev_mac`) USING BTREE,
  INDEX `fk_device_pid`(`pid`) USING BTREE,
  INDEX `fk_device_group_id`(`group_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '设备表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for cloudnetlot_device_params
-- ----------------------------
DROP TABLE IF EXISTS `cloudnetlot_device_params`;
CREATE TABLE `cloudnetlot_device_params`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `dev_mac` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '设备MAC地址',
  `params` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `type` smallint(6) UNSIGNED NOT NULL COMMENT '1 :加密,2:系统 ,3: 网络,4: 无线,5:用户,6:定时重启,7:升级,8:绑定设备',
  `is_del` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除标志位,0:没有删除,1:已经删除',
  `created_at` bigint(13) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` bigint(13) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_device_params_dev_mac`(`dev_mac`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '设备参数表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for cloudnetlot_dict
-- ----------------------------
DROP TABLE IF EXISTS `cloudnetlot_dict`;
CREATE TABLE `cloudnetlot_dict`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `dev_mac` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '设备MAC',
  `infos` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '设备属性定义',
  `is_del` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除标志位,0:没有删除,1:已经删除',
  `created_at` bigint(13) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` bigint(13) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_dict_dev_mac`(`dev_mac`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '设备属性定义表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for cloudnetlot_group
-- ----------------------------
DROP TABLE IF EXISTS `cloudnetlot_group`;
CREATE TABLE `cnl_group` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL COMMENT '用户ID',
  `pid` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '分组父ID',
  `code` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '分组代码',
  `name` varchar(100) DEFAULT NULL COMMENT '分组中文名称',
  `description` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `config_id` varchar(50) NOT NULL DEFAULT '' COMMENT '配置模板ID,空为未配置模板',
  `auto` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0:未应用,1:手动应用,2:自动应用',
  `level` tinyint(4) NOT NULL DEFAULT '0' COMMENT '深度',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '删除标志位,0:没有删除,1:已经删除',
  `created_at` bigint(13) unsigned DEFAULT NULL COMMENT '创建时间',
  `updated_at` bigint(13) unsigned DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `fk_group_code` (`code`) USING BTREE,
  KEY `fk_group_users` (`user_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='分组表';

-- ----------------------------
-- Table structure for cloudnetlot_group
-- ----------------------------
DROP TABLE IF EXISTS `cloudnetlot_user_group`;
CREATE TABLE `cloudnetlot_user_group` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL COMMENT '用户ID',
  `group_id` bigint(20) unsigned NOT NULL COMMENT '组ID',
  `created_at` bigint(13) unsigned DEFAULT NULL COMMENT '创建时间',
  `updated_at` bigint(13) unsigned DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='用户分组表';
-- ----------------------------
-- Table structure for cloudnetlot_package
-- ----------------------------
DROP TABLE IF EXISTS `cloudnetlot_package`;
CREATE TABLE `cloudnetlot_package`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `fid` varchar(50) NOT NULL COMMENT '文件ID',
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '固件名称',
  `url` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '固件地址',
  `version` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '固件版本',
  `user_id` bigint(20) NOT NULL COMMENT '上传固件用户ID',
  `file_md5` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '固件md5值',
  `is_local` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否是本地上传,0:不是,1:是',
  `size` bigint(20) UNSIGNED NOT NULL COMMENT '固件包大小,单位B',
  `downloads` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '下载次数',
  `created_at` bigint(13) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` bigint(13) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '固件升级包表' ROW_FORMAT = Compact;
-- ----------------------------
-- Table structure for cloudnetlot_package_type
-- ----------------------------
DROP TABLE IF EXISTS `cloudnetlot_package_type`;
CREATE TABLE `cloudnetlot_package_type`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `fid` varchar(50) NOT NULL COMMENT '文件ID',
  `type` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '适应型号',
  `created_at` bigint(13) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` bigint(13) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '升级包适配型号' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for cloudnetlot_tpl_info
-- ----------------------------
DROP TABLE IF EXISTS `cloudnetlot_tpl_info`;
CREATE TABLE `cloudnetlot_tpl_info`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `tpl_id` bigint(20) UNSIGNED NOT NULL,
  `appid` bigint(20) UNSIGNED NULL DEFAULT 0,
  `type` tinyint(4) UNSIGNED NOT NULL COMMENT '1:文本,2:图片,3:滚动图',
  `info_zh` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL,
  `info_en` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL,
  `is_del` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除标志位,0:没有删除,1:已经删除',
  `created_at` bigint(13) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` bigint(13) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_tpl_info_tpl_id`(`tpl_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '认证模板详情表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for cloudnetlot_upgrade_order
-- ----------------------------
DROP TABLE IF EXISTS `cloudnetlot_upgrade_order`;
CREATE TABLE `cloudnetlot_upgrade_order`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `orderid` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '升级单号',
  `user_id` bigint(20) UNSIGNED NOT NULL COMMENT '用户id',
  `package_id` bigint(20) UNSIGNED NOT NULL COMMENT '固件包ID',
  `package_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '固件名称',
  `package_md5` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '文件md5值',
  `package_url` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '文件地址',
  `version` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '固件版本',
  `is_del` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除标志位,0:没有删除,1:已经删除',
  `exec_time` bigint(13) UNSIGNED NULL DEFAULT NULL COMMENT '执行时间',
  `status` tinyint(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态0:待升级1:升级中2:失败3:成功',
  `created_at` bigint(13) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` bigint(13) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_up_order_orderid`(`orderid`) USING BTREE,
  INDEX `fk_upgrade_order_package`(`package_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '设备升级单' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for cloudnetlot_upgrade_order_dev
-- ----------------------------
DROP TABLE IF EXISTS `cloudnetlot_upgrade_order_dev`;
CREATE TABLE `cloudnetlot_upgrade_order_dev`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `orderid` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '升级单号',
  `dev_mac` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '设备MAC',
  `dev_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '设备名称',
  `type` varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '设备型号',
  `status` tinyint(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态0:待升级1:升级中2:失败3:成功',
  `is_del` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除标志位,0:没有删除,1:已经删除',
  `created_at` bigint(13) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` bigint(13) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_up_order_dev_orderid`(`orderid`) USING BTREE,
  INDEX `fk_up_order_dev_dev_mac`(`dev_mac`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '升级单对应设备' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for cloudnetlot_users
-- ----------------------------
DROP TABLE IF EXISTS `cloudnetlot_users`;
CREATE TABLE `cloudnetlot_users`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT ' 用户ID',
  `username` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '用户名',
  `password` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '密码',
  `mq_password` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'mq密码',
  `salt` varchar(35) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'mq加密盐值',
  `nickname` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '用户昵称',
  `pid` bigint(20) NOT NULL DEFAULT '0' COMMENT '父账号id',
  `email` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '邮箱',
  `timeZone` varchar(255) NOT NULL DEFAULT '+08:00' COMMENT '时区',
  `isSummerTime` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否启用夏令时',
  `phonecode` varchar(255) NOT NULL COMMENT '国家区域码',
  `phone` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '手机号',
  `level` tinyint(4) UNSIGNED NOT NULL DEFAULT 1 COMMENT '用户级别，1：具备guest角色的子账号,2：具备admin角色的子账号，3：普通账号',
  `area` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '区域码',
  `address` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '详细地址',
  `latitude` decimal(10, 7) NULL DEFAULT NULL COMMENT '纬度',
  `longitude` decimal(10, 7) NULL DEFAULT NULL COMMENT '经度',
  `status` tinyint(4) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态,0:禁用,1:启用',
  `admin_id` int(11) UNSIGNED NOT NULL COMMENT '管理员账号',
  `remember_token` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL,
  `is_del` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除标志位,0:没有删除,1:已经删除',
  `created_at` bigint(13) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` bigint(13) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_users_admin`(`admin_id`) USING BTREE,
  INDEX `fk_users_area`(`area`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '用户表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for cloudnetlot_wechat_bind
-- ----------------------------
DROP TABLE IF EXISTS `cloudnetlot_wechat_bind`;
CREATE TABLE `cloudnetlot_wechat_bind`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) UNSIGNED NOT NULL COMMENT '用户ID',
  `open_id` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '用户微信的openid',
  `is_del` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除标志位,0:没有删除,1:已经删除',
  `created_at` bigint(13) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` bigint(13) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_wechat_bind_users`(`user_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '微信绑定表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for cloudnetlot_wechat_menu
-- ----------------------------
DROP TABLE IF EXISTS `cloudnetlot_wechat_menu`;
CREATE TABLE `cloudnetlot_wechat_menu`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `pid` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '父菜单ID',
  `admin_id` int(11) UNSIGNED NOT NULL COMMENT '管理员ID',
  `type` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '菜单类型',
  `content` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL,
  `is_del` tinyint(1) UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除标志位,0:没有删除,1:已经删除',
  `created_at` bigint(13) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` bigint(13) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `fk_wechat_menu`(`admin_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '微信菜单表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for cloudnetlot_system_license
-- ----------------------------
DROP TABLE IF EXISTS `cloudnetlot_system_license`;
CREATE TABLE `cloudnetlot_system_license`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '公司名称',
  `domain` text NOT NULL COMMENT '服务器地址',
  `license` text NOT NULL COMMENT 'license',
  `expire_in` bigint(20) UNSIGNED NOT NULL COMMENT '过期时间，单位天',
  `created_at` bigint(13) UNSIGNED NULL DEFAULT NULL COMMENT '创建时间',
  `updated_at` bigint(13) UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE KEY `system_license_company_name_unique` (`company_name`)
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '公司license表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for cloudnetlot_command
-- ----------------------------
DROP TABLE IF EXISTS `cloudnetlot_command`;
CREATE TABLE `cloudnetlot_command` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL COMMENT '用户ID',
  `dev_mac` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '设备MAC',
  `comm_id` varchar(25) NOT NULL COMMENT '命令ID',
  `content` text NOT NULL COMMENT '命令内容',
  `describe` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `type` smallint(6) unsigned NOT NULL COMMENT '命令类型',
  `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '状态,1:未发送,2:已发送,3:执行失败,4:执行成功',
  `dev_ip` varchar(25) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '设备IP',
  `dev_name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '设备名称',
  `dev_version` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '固件版本',
  `dev_type` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '设备型号',
  `is_del` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '删除标志位,0:没有删除,1:已经删除',
  `created_at` bigint(13) unsigned DEFAULT NULL COMMENT '创建时间',
  `updated_at` bigint(13) unsigned DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  KEY `fk_command_user_id` (`user_id`) USING BTREE,
  KEY `fk_command_dev_mac` (`dev_mac`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='命令表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for cloudnetlot_country_code
-- ----------------------------
DROP TABLE IF EXISTS `cloudnetlot_country_code`;
CREATE TABLE `cloudnetlot_country_code` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name_en_us` varchar(80) NOT NULL,
  `name_zh_cn` varchar(80) NOT NULL,
  `short2` char(2) NOT NULL,
  `short3` char(3) NOT NULL,
  `num` varchar(255) NOT NULL,
  `phonecode` varchar(255) NOT NULL,
  `created_at` bigint(20) unsigned NOT NULL,
  `updated_at` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for cloudnetlot_message_read
-- ----------------------------
DROP TABLE IF EXISTS `cloudnetlot_message_read`;
CREATE TABLE `cloudnetlot_message_read` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `comm_id` varchar(25) NOT NULL COMMENT '命令表记录ID',
  `created_at` bigint(20) unsigned NOT NULL,
  `updated_at` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT = '已读消息表' ROW_FORMAT = Compact;

-- ----------------------------
-- Table structure for cloudnetlot_develop
-- ----------------------------
DROP TABLE IF EXISTS `cloudnetlot_develop`;
CREATE TABLE `cloudnetlot_develop` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `user_id` bigint(20) unsigned NOT NULL COMMENT '用户ID',
  `salt` varchar(35) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT '加密盐值',
  `appid` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT '开发者id',
  `secret` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT '开发者秘钥', 
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT '姓名',
  `idcard` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT '身份证',
  `enterprise` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT '企业名称',
  `enterprise_des` text DEFAULT NULL COMMENT '企业描述',
  `enterprisecode` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT '全国统一信用码',
  `is_super` tinyint(1) DEFAULT 0 COMMENT '是否是超级用户,0:否,1:是',
  `aud_status` tinyint(3) unsigned DEFAULT 1 COMMENT '状态,1:申请审批中,2:审批未通过,3:审批通过',
  `created_at` bigint(13) UNSIGNED DEFAULT NULL COMMENT '创建时间',
  `updated_at` bigint(13) UNSIGNED DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='开发者信息表';


-- ----------------------------
-- Table structure for cloudnetlot_develop_product
-- ----------------------------
DROP TABLE IF EXISTS `cloudnetlot_develop_product`;
CREATE TABLE `cloudnetlot_develop_product` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '产品ID',
  `prtid` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '产品ID',
  `uid` bigint(20) UNSIGNED NOT NULL COMMENT '用户ID',
  `name` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '产品名称',
  `describe` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL COMMENT '产品描述',
  `type` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '产品类型,1:网络设备',
  `size` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '产品型号',
  `aud_status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '产品状态表1:注册未发布,2:发布审核中,3:发布失败,4:发布成功',
  `created_at` bigint(13) UNSIGNED DEFAULT NULL COMMENT '创建时间',
  `updated_at` bigint(13) UNSIGNED DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='产品表';

-- ----------------------------
-- Table structure for cloudnetlot_develop_client
-- ----------------------------
DROP TABLE IF EXISTS `cloudnetlot_develop_client`;
CREATE TABLE `cloudnetlot_develop_client` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `uid` bigint(20) UNSIGNED NOT NULL COMMENT '用户ID',
  `prtid` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '产品ID',
  `cltid` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '客户端ID',
  `mac` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL COMMENT '设备MAC地址',
  `created_at` bigint(13) UNSIGNED DEFAULT NULL COMMENT '创建时间',
  `updated_at` bigint(13) UNSIGNED DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='客户端表';

-- ----------------------------
-- Table structure for cloudnetlot_develop_acl
-- ----------------------------
DROP TABLE IF EXISTS `cloudnetlot_develop_acl`;
CREATE TABLE `cloudnetlot_develop_acl` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '自增ID',
  `allow` tinyint(1) UNSIGNED DEFAULT 1 COMMENT '0:deny,1:allow',
  `ipaddr` varchar(60) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'IpAddress',
  `username` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'username',
  `clientid` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'clientid',
  `access` tinyint(2) UNSIGNED NOT NULL COMMENT '1:subscribe,2:publish,3:pubsub',
  `topic` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL DEFAULT '' COMMENT 'topic filter',
  `created_at` bigint(13) UNSIGNED DEFAULT NULL COMMENT '创建时间',
  `updated_at` bigint(13) UNSIGNED DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='MQTT acl表';

-- ----------------------------
-- Table structure for cloudnetlot_device_relation
-- ----------------------------
DROP TABLE IF EXISTS `cloudnetlot_device_relation`;
CREATE TABLE `cloudnetlot_device_relation` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) UNSIGNED NOT NULL COMMENT '用户ID',
  `mac` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '设备MAC',
  `pid` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT '0' COMMENT '父设备MAC',
  `content` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '相关信息',
  `created_at` bigint(13) UNSIGNED DEFAULT NULL COMMENT '创建时间',
  `updated_at` bigint(13) UNSIGNED DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`),
  UNIQUE KEY `device_relation_mac_unique` (`mac`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='设备层级关系表';

-- ----------------------------
-- Table structure for cloudnetlot_topgraphy
-- ----------------------------
DROP TABLE IF EXISTS `cloudnetlot_topgraphy`;
CREATE TABLE `cloudnetlot_topgraphy` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) UNSIGNED NOT NULL COMMENT '用户ID',
  `mac` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '设备MAC',
  `pid` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT '0' COMMENT '父设备MAC',
  `content` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '相关信息',
  `is_edit` tinyint(1) UNSIGNED DEFAULT 0 COMMENT '是否编辑过0:未编辑1:已编辑',
  `is_virture` tinyint(1) UNSIGNED DEFAULT 0 COMMENT '是否是虚拟设备1:是0:否',
  `point_x` int(11) NOT NULL DEFAULT '-1' COMMENT '拓扑图上横坐标',
  `point_y` int(11) NOT NULL DEFAULT '-1' COMMENT '拓扑图上纵坐标',
  `group_id` int(11) NULL DEFAULT '0' COMMENT '工作组ID',
  `created_at` bigint(13) UNSIGNED DEFAULT NULL COMMENT '创建时间',
  `updated_at` bigint(13) UNSIGNED DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='拓扑图表';

-- ----------------------------
-- Table structure for cloudnetlot_device_virture
-- ----------------------------
DROP TABLE IF EXISTS `cloudnetlot_device_virture`;
CREATE TABLE `cloudnetlot_device_virture` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) UNSIGNED NOT NULL COMMENT '用户ID',
  `mac` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT '0' COMMENT '虚拟设备ID',
  `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL COMMENT '虚拟设备名称',
  `mode` int(4) UNSIGNED NOT NULL COMMENT '虚拟设备工作模式,100：网络根节点,101:交换机,102:摄像头,103:移动设备,104：PC',
  `status` tinyint(1) UNSIGNED NOT NULL DEFAULT 1 COMMENT '虚拟设备状态0:离线1:在线2:未知',
  `group_id` int(11) NOT NULL DEFAULT '1' COMMENT '工作组ID',
  `created_at` bigint(13) UNSIGNED DEFAULT NULL COMMENT '创建时间',
  `updated_at` bigint(13) UNSIGNED DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='虚拟设备表';

SET FOREIGN_KEY_CHECKS = 1;
