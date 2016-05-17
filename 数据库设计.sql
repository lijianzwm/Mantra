CREATE TABLE `gx_admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `username` varchar(20) NOT NULL COMMENT '用户名',
  `realname` varchar(20) NOT NULL COMMENT '真实姓名',
  `password` varchar(20) DEFAULT NULL COMMENT '密码',
  `phone` varchar(20) DEFAULT NULL COMMENT '联系电话',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `gx_user` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '主键id，唯一自增',
  `username` varchar(20) NOT NULL UNIQUE COMMENT '用户名',
  `password` varchar(32) NOT NULL COMMENT '密码',
  `phone` varchar(50) DEFAULT NULL COMMENT '联系电话',
  `realname` varchar(50) DEFAULT NULL COMMENT '真实姓名',
  `dharma` varchar(50) DEFAULT NULL COMMENT '法名',
  `showname` varchar(50) DEFAULT NULL COMMENT '显示名',
  `day_goal` int(10) DEFAULT '0' COMMENT '每日计划持诵数目',
  `goal` bigint(30) DEFAULT '0' COMMENT '发愿持诵总数',
  `total` bigint(20) NOT NULL DEFAULT '0' COMMENT '共修总数',
  PRIMARY KEY (`id`),
  INDEX `index_id` (`id`),
  INDEX `index_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `gx_day_count` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '主键id，唯一自增',
  `userid` varchar(50) NOT NULL COMMENT '用户id',
  `today_date` date NOT NULL COMMENT '日期',
  `num` bigint(20) NOT NULL DEFAULT '0' COMMENT '今日修持数目',
  `update_time` DATETIME NOT NULL COMMENT '提交时间',
  PRIMARY KEY (`id`),
  KEY `index_date` (`today_date`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `gx_day_ranklist` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '主键id，唯一自增',
  `date` date UNIQUE NOT NULL COMMENT '日期',
  `total` bigint(30) NOT NULL COMMENT '总数',
  `ranklist` longtext COMMENT '排行榜',
  PRIMARY KEY (`id`),
  KEY `index_date` (`date`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `gx_month_ranklist` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '主键id，唯一自增',
  `yearmonth` varchar(7) UNIQUE NOT NULL COMMENT '年月',
  `total` bigint(30) NOT NULL COMMENT '总数',
  `ranklist` longtext COMMENT '排行榜',
  PRIMARY KEY (`id`),
  KEY `index_yearmonth` (`yearmonth`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `gx_stage_gx` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '主键id，唯一自增',
  `title` varchar(100) NOT NULL COMMENT '共修主题',
  `num` bigint(30) NOT NULL COMMENT '总数',
  `beg_date` date NOT NULL COMMENT '开始日期',
  `end_date` date NOT NULL COMMENT '结束日期',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;