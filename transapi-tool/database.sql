--创建数据库，数据库名称db_youdaocld_api_cache，默认缺省的字符集为utf8
create database if not exists db_words_api_cache charset utf8;

--选择默认使用的数据库
use db_words_api_cache;

--创建有道存放单词表结构
create table if not exists tb_words_youdao(
    id int unsigned primary key auto_increment,
    query varchar(100) not null unique,
    fetch_count int unsigned,
    us_phonetic varchar(100),
    phonetic varchar(100),
    uk_phonetic varchar(100),
    wfs varchar(255),
    explains varchar(512)
) engine=InnoDB default charset=utf8;

--为tb_words_youdao表query字段增加索引.
alter table tb_words_youdao add index word_key (query);


--创建有道存放单词表结构
create table if not exists tb_words_kingsoft(
    id int unsigned primary key auto_increment,
    query varchar(100) not null unique,
    fetch_count int unsigned,
    us_phonetic varchar(100),
    phonetic varchar(100),
    uk_phonetic varchar(100),
    exchange varchar(255),
    explains varchar(512)
) engine=InnoDB default charset=utf8;

--为tb_words_kingsoft表query字段增加索引.
alter table tb_words_kingsoft add index word_key (query);
