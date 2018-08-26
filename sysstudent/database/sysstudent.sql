--创建sysstudent数据库
create database sysstudent charset utf8;

--使用缺省的数据库
use sysstudent;

--创建数据库表
create table student(
    s_id int not null,
    c_id int not null,
    name varchar(20) not null,
    gender varchar(10) not null,
    age tinyint,
    height smallint,
    primary key(s_id)
)charset utf8;


--插入数据
insert into student values(2018001,1,'A','male',ceil(rand()*20+20),ceil(rand()*20+160));
insert into student values(2018002,2,'B','female',ceil(rand()*20+20),ceil(rand()*20+160));
insert into student values(2018003,3,'C','female',ceil(rand()*20+20),ceil(rand()*20+160));
insert into student values(2018004,1,'D','male',ceil(rand()*20+20),ceil(rand()*20+160));
insert into student values(2018005,1,'E','male',ceil(rand()*20+20),ceil(rand()*20+160));
insert into student values(2018006,2,'F','female',ceil(rand()*20+20),ceil(rand()*20+160));

--创建课程表
create table class(
c_id int not null,
name varchar(30) not null,
primary key(c_id)
)charset utf8;

--插入数据
insert into class values(1,'math');
insert into class values(2,'english');
insert into class values(3,'chinese');

select student.s_id,student.name,student.gender,class.name from student join class on student.c_id = class.c_id;
--+---------+------+--------+---------+
--| s_id    | name | gender | name    |
--+---------+------+--------+---------+
--| 2018001 | A    | male   | math    |
--| 2018002 | B    | female | english |
--| 2018003 | C    | female | chinese |
--| 2018004 | D    | male   | math    |
--| 2018005 | E    | male   | math    |
--| 2018006 | F    | female | english |
--+---------+------+--------+---------+

select student.* from student inner join class on student.c_id = class.c_id;
--+---------+------+------+--------+------+--------+
--| s_id    | c_id | name | gender | age  | height |
--+---------+------+------+--------+------+--------+
--| 2018001 |    1 | A    | male   |   31 |    178 |
--| 2018002 |    2 | B    | female |   36 |    165 |
--| 2018003 |    3 | C    | female |   36 |    166 |
--| 2018004 |    1 | D    | male   |   23 |    173 |
--| 2018005 |    1 | E    | male   |   37 |    165 |
--| 2018006 |    2 | F    | female |   34 |    173 |
--+---------+------+------+--------+------+--------+


select student.*,class.name from student inner join class on student.c_id = class.c_id;
--+---------+------+------+--------+------+--------+---------+
--| s_id    | c_id | name | gender | age  | height | name    |
--+---------+------+------+--------+------+--------+---------+
--| 2018001 |    1 | A    | male   |   31 |    178 | math    |
--| 2018002 |    2 | B    | female |   36 |    165 | english |
--| 2018003 |    3 | C    | female |   36 |    166 | chinese |
--| 2018004 |    1 | D    | male   |   23 |    173 | math    |
--| 2018005 |    1 | E    | male   |   37 |    165 | math    |
--| 2018006 |    2 | F    | female |   34 |    173 | english |
--+---------+------+------+--------+------+--------+---------+

--再次插入数据
insert into student values(2018007,4,'G','female',ceil(rand()*20+20),ceil(rand()*20+160));


--内联表查询

select student.*,class.name from student inner join class on student.c_id = class.c_id;