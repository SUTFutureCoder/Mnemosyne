
-- --------------------------------------------------------

--
-- 表的结构 `academe`
--

-- DROP TABLE IF EXISTS `academe`;
CREATE TABLE IF NOT EXISTS `academe` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '学院唯一id',
  `academe_id` char(16) NOT NULL,
  `academe_name` char(32) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `academe_name` (`academe_name`),
  KEY `academe_id` (`academe_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='学院表' ;

-- --------------------------------------------------------

--
-- 表的结构 `class`
--

-- DROP TABLE IF EXISTS `class`;
CREATE TABLE IF NOT EXISTS `class` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '班级唯一id',
  `school_id` char(16) NOT NULL COMMENT '学校id',
  `class_id` char(16) NOT NULL,
  `class_name` char(32) NOT NULL COMMENT '班级名称',
  `class_describe` varchar(256) NOT NULL COMMENT '班级描述',
  PRIMARY KEY (`id`),
  KEY `school_id` (`school_id`),
  KEY `class_name` (`class_name`),
  KEY `class_id` (`class_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='班级表';

-- --------------------------------------------------------

--
-- 表的结构 `major`
--

-- DROP TABLE IF EXISTS `major`;
CREATE TABLE IF NOT EXISTS `major` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '专业唯一id',
  `major_id` char(16) NOT NULL,
  `major_name` char(32) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `major_name` (`major_name`),
  KEY `major_id` (`major_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='专业表';

-- --------------------------------------------------------

--
-- 表的结构 `school`
--

-- DROP TABLE IF EXISTS `school`;
CREATE TABLE IF NOT EXISTS `school` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '学院唯一id',
  `school_id` char(16) NOT NULL COMMENT '学校id',
  `school_name` varchar(32) NOT NULL COMMENT '学校名称',
  PRIMARY KEY (`id`),
  KEY `school_name` (`school_id`,`school_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='学校表';

-- --------------------------------------------------------

--
-- 表的结构 `school_class_user_map`
--

-- DROP TABLE IF EXISTS `school_class_user_map`;
CREATE TABLE IF NOT EXISTS `school_class_user_map` (
  `map_id` int(11) NOT NULL AUTO_INCREMENT COMMENT '关联表id',
  `school_unique_id` bigint(20) NOT NULL COMMENT '学校唯一id',
  `class_unique_id` bigint(20) NOT NULL COMMENT '班级唯一id',
  `user_unique_id` bigint(20) NOT NULL COMMENT '用户唯一id',
  `student_id` char(16) NOT NULL COMMENT '在教务处学号',
  PRIMARY KEY (`map_id`),
  KEY `class_id` (`class_unique_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='学校班级用户关联表';

-- --------------------------------------------------------

--
-- 表的结构 `student`
--

-- DROP TABLE IF EXISTS `student`;
CREATE TABLE IF NOT EXISTS `student` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '学生唯一id',
  `school_id` char(16) NOT NULL,
  `student_id` char(16) NOT NULL,
  `student_name` char(8) NOT NULL,
  `student_class` char(16) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `school_id` (`school_id`),
  KEY `student_id` (`student_id`),
  KEY `student_name` (`student_name`),
  KEY `student_class` (`student_class`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='学生基本信息表' ;

-- --------------------------------------------------------

--
-- 表的结构 `user`
--

-- DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `user_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '用户唯一id',
  `user_name` varchar(32) NOT NULL COMMENT '用户姓名',
  `user_birthday` date NOT NULL COMMENT '用户生日',
  `user_sex` tinyint(4) NOT NULL COMMENT '用户性别',
  `user_password` varchar(256) NOT NULL COMMENT '用户密码',
  `user_mobile` varchar(16) NOT NULL COMMENT '用户联系方式',
  `user_email` varchar(64) NOT NULL COMMENT '用户email',
  `user_sign` varchar(256) NOT NULL COMMENT '用户签名',
  `user_status` tinyint(4) NOT NULL COMMENT '用户状态',
  `user_create_time` int(11) unsigned NOT NULL COMMENT '加入时间',
  `user_last_login_time` int(11) unsigned NOT NULL COMMENT '最后登录时间',
  PRIMARY KEY (`user_id`),
  KEY `user_name` (`user_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户表';


-- --------------------------------------------------------

--
-- 表的结构 `group`
--

-- DROP TABLE IF EXISTS `group`;
CREATE TABLE IF NOT EXISTS `group` (
  `group_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '群组id',
  `group_name` char(32) NOT NULL COMMENT '群组名',
  `img` varchar(256) NOT NULL DEFAULT '' COMMENT '群组图片logo',
  `description` varchar(256) NOT NULL COMMENT '群备注',
  `creater_id` int(11) NOT NULL COMMENT '创建者用户id',
  `group_type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '群组类型',
  `disable` enum('0','1') NOT NULL DEFAULT '0' COMMENT '是否禁用：0，不禁用；1、 禁用',
  `addtime` int(11) unsigned NOT NULL COMMENT '添加时间',
  `modtime` int(11) unsigned NOT NULL COMMENT '修改时间',
  PRIMARY KEY (`group_id`),
  KEY `IDX_group_name` (`group_name`),
  KEY `IDX_creater_id` (`creater_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 表的结构 `user_group_map`
--

-- DROP TABLE IF EXISTS `user_group_map`;
CREATE TABLE IF NOT EXISTS `user_group_map` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '群组id',
  `user_id` bigint(20) NOT NULL COMMENT '用户id',
  `group_id` bigint(20) NOT NULL COMMENT '群组id',
  `privilage` tinyint(4) NOT NULL DEFAULT '0' COMMENT '权限',
  `addtime` int(11) unsigned NOT NULL COMMENT '添加时间',
  `modtime` int(11) unsigned NOT NULL COMMENT '修改时间',
  `adminid` bigint(20) NOT NULL COMMENT '修改用户id',
  PRIMARY KEY (`id`),
  KEY `IDX_group_id` (`group_id`),
  KEY `IDX_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


