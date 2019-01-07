-- 创建卫士实时计算数据库
create database if not exists db_realtime_safe charset utf8;

-- 创建uuid数据库表
create table if not exists  tbl_uuid (
id int(4) not null AUTO_INCREMENT,
uuid char(32) not null UNIQUE,
primary key (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

