SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 数据库: `mnemosyne`
--

-- --------------------------------------------------------

--
-- 表的结构 `class`
--

CREATE TABLE IF NOT EXISTS `class` (
  `class_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '班级id',
  `class_name` char(32) NOT NULL COMMENT '班级名称',
  `class_describe` varchar(256) NOT NULL COMMENT '班级描述',
  PRIMARY KEY (`class_id`),
  KEY `class_name` (`class_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='班级表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `school`
--

CREATE TABLE IF NOT EXISTS `school` (
  `school_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '学校id',
  `school_name` varchar(32) NOT NULL COMMENT '学校名称',
  PRIMARY KEY (`school_id`),
  KEY `school_name` (`school_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='学校表' AUTO_INCREMENT=2 ;

--
-- 转存表中的数据 `school`
--

INSERT INTO `school` (`school_id`, `school_name`) VALUES
(1, '沈阳工业大学');

-- --------------------------------------------------------

--
-- 表的结构 `school_class_user_map`
--

CREATE TABLE IF NOT EXISTS `school_class_user_map` (
  `map_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '关联表id',
  `school_id` int(11) NOT NULL COMMENT '学校id',
  `class_id` int(11) NOT NULL COMMENT '班级id',
  `user_id` int(11) NOT NULL COMMENT '用户id',
  PRIMARY KEY (`map_id`),
  KEY `class_id` (`class_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='学校班级用户关联表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '用户id',
  `user_name` varchar(32) NOT NULL COMMENT '用户姓名',
  `user_birthday` date NOT NULL COMMENT '用户生日',
  `user_sex` tinyint(4) NOT NULL COMMENT '用户性别',
  `user_password` varchar(256) NOT NULL COMMENT '用户密码',
  `user_mobile` varchar(16) NOT NULL COMMENT '用户联系方式',
  `user_email` varchar(64) NOT NULL COMMENT '用户email',
  `user_sign` varchar(256) NOT NULL COMMENT '用户签名',
  `user_status` tinyint(4) NOT NULL COMMENT '用户状态',
  `user_create_time` date NOT NULL COMMENT '加入时间',
  `user_last_login_time` datetime NOT NULL COMMENT '最后登录时间',
  PRIMARY KEY (`user_id`),
  KEY `user_name` (`user_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户表' AUTO_INCREMENT=1 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
