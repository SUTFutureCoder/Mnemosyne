-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2016-04-15 22:37:54
-- 服务器版本: 5.5.47-0ubuntu0.14.04.1
-- PHP 版本: 5.5.9-1ubuntu4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `bos`
--

-- --------------------------------------------------------

--
-- 表的结构 `bos_bucket`
--

CREATE TABLE IF NOT EXISTS `bos_bucket` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `bucket_id` bigint(20) NOT NULL COMMENT 'uuid',
  `bucket_name` varchar(32) NOT NULL COMMENT 'bucket名',
  `user_id` bigint(20) NOT NULL,
  `access_key` varchar(1024) NOT NULL COMMENT '读取key',
  `secret_key` varchar(1024) NOT NULL COMMENT '修改key',
  `bucket_root` varchar(1024) NOT NULL COMMENT '文件root目录, BOSPATH/resroot/user_id/bucket_root',
  `enable_host_list` varchar(10240) NOT NULL COMMENT '允许的访问域名，反盗链，使用json存储，空则允许全部域',
  `enable_null_referer` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否允许空来源',
  `is_public` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否公有',
  `key_need` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否特别key验证，如不需要则使用用户key',
  `ctime` bigint(20) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`,`bucket_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='bucket';

-- --------------------------------------------------------

--
-- 表的结构 `bos_object`
--

CREATE TABLE IF NOT EXISTS `bos_object` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `object_id` bigint(20) NOT NULL COMMENT '存储对象id',
  `object_index` varchar(1024) NOT NULL COMMENT '获取存储对象的key，用于http访问',
  `name` varchar(1024) NOT NULL COMMENT '存储对象源文件名',
  `mime` varchar(1024) NOT NULL COMMENT '文件mime信息',
  `size` bigint(20) NOT NULL COMMENT '文件大小',
  `sign` varchar(1024) NOT NULL COMMENT '文件签名',
  `user` bigint(20) NOT NULL COMMENT '用户uuid',
  `private_share_key` char(4) NOT NULL COMMENT '分享码，由BOS提供',
  `bucket_id` bigint(20) NOT NULL COMMENT '存放bucket id',
  `is_public` tinyint(4) NOT NULL COMMENT '是否为公有',
  `is_delete` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否被删除',
  `ctime` bigint(20) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `object_index` (`object_index`(255)),
  KEY `bucket` (`bucket_id`),
  KEY `user` (`user`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='BOS服务存储对象表';

-- --------------------------------------------------------

--
-- 表的结构 `bos_tip_off`
--

CREATE TABLE IF NOT EXISTS `bos_tip_off` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '自增字段',
  `object_id` bigint(20) NOT NULL COMMENT '举报文件id',
  `user_id` bigint(20) NOT NULL COMMENT '举报人id',
  `description` varchar(1024) NOT NULL COMMENT '原因',
  PRIMARY KEY (`id`),
  KEY `object_id` (`object_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='举报表，水表已拆，不收快递';

-- --------------------------------------------------------

--
-- 表的结构 `bos_user`
--

CREATE TABLE IF NOT EXISTS `bos_user` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `user_id` bigint(20) NOT NULL COMMENT '用户id',
  `user_name` varchar(32) NOT NULL COMMENT '用户名',
  `access_key` varchar(1024) NOT NULL COMMENT '访问私有资源key',
  `secret_key` varchar(1024) NOT NULL COMMENT '操作bucket、文件key',
  `is_delete` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`id`,`user_id`),
  KEY `key_user_id` (`user_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='用户表，存储key等';

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
