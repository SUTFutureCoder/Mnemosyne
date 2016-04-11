-- phpMyAdmin SQL Dump
-- version 4.0.10deb1
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2016-04-10 11:50:57
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
  `access_key` varchar(1024) NOT NULL COMMENT '读取key',
  `secret_key` varchar(1024) NOT NULL COMMENT '修改key',
  `bucket_root` varchar(1024) NOT NULL COMMENT '文件root目录',
  `enable_host_list` varchar(10240) NOT NULL COMMENT '允许的访问域名，反盗链，使用json存储，空则允许全部域',
  `enable_null_referer` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否允许空来源',
  `is_public` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否公有',
  `key_need` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否特别key验证，如不需要则使用用户key',
  `ctime` bigint(20) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`,`bucket_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='bucket' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `bos_object`
--

CREATE TABLE IF NOT EXISTS `bos_object` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `object_id` bigint(20) NOT NULL COMMENT '存储对象id',
  `object_index` varchar(1024) NOT NULL COMMENT '获取存储对象的key，用于http访问',
  `name` varchar(1024) NOT NULL COMMENT '存储对象源文件名',
  `mine` varchar(1024) NOT NULL COMMENT '文件mine信息',
  `sign` varchar(1024) NOT NULL COMMENT '文件签名',
  `user` bigint(20) NOT NULL COMMENT '用户uuid',
  `bucket` bigint(20) NOT NULL COMMENT '存放bucket id',
  `is_public` tinyint(4) NOT NULL COMMENT '是否为公有',
  `ctime` bigint(20) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`),
  KEY `object_index` (`object_index`(255)),
  KEY `bucket` (`bucket`),
  KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='BOS服务存储对象表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `bos_user`
--

CREATE TABLE IF NOT EXISTS `bos_user` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '自增id',
  `user_id` bigint(20) NOT NULL COMMENT '用户id',
  `access_key` varchar(1024) NOT NULL COMMENT '访问私有资源key',
  `secret_key` varchar(1024) NOT NULL COMMENT '操作bucket、文件key',
  `is_delete` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`id`,`user_id`),
  KEY `key_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户表，存储key等' AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
