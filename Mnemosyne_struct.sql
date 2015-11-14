
-- --------------------------------------------------------

--
-- 表的结构 `academe`
--

DROP TABLE IF EXISTS `academe`;
CREATE TABLE IF NOT EXISTS `academe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `academe_id` char(16) NOT NULL,
  `academe_name` char(32) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `academe_id` (`academe_id`,`academe_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='学院表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `class`
--

DROP TABLE IF EXISTS `class`;
CREATE TABLE IF NOT EXISTS `class` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
  `class_id` char(16) NOT NULL,
  `class_name` char(32) NOT NULL COMMENT '班级名称',
  `class_describe` varchar(256) NOT NULL COMMENT '班级描述',
  PRIMARY KEY (`id`),
  KEY `class_name` (`class_name`),
  KEY `class_id` (`class_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='班级表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `major`
--

DROP TABLE IF EXISTS `major`;
CREATE TABLE IF NOT EXISTS `major` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `major_id` char(16) NOT NULL,
  `major_name` char(32) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `major_id` (`major_id`,`major_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='专业表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `school`
--

DROP TABLE IF EXISTS `school`;
CREATE TABLE IF NOT EXISTS `school` (
  `school_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '学校id',
  `school_name` varchar(32) NOT NULL COMMENT '学校名称',
  PRIMARY KEY (`school_id`),
  KEY `school_name` (`school_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='学校表' AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- 表的结构 `school_class_user_map`
--

DROP TABLE IF EXISTS `school_class_user_map`;
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
-- 表的结构 `student`
--

DROP TABLE IF EXISTS `student`;
CREATE TABLE IF NOT EXISTS `student` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` char(16) NOT NULL,
  `student_name` char(8) NOT NULL,
  `student_class` char(16) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `student_name` (`student_name`,`student_class`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='学生基本信息表' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 表的结构 `user`
--

DROP TABLE IF EXISTS `user`;
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
