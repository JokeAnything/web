#!/bin/bash

#$1 目标路径
#$2 文件名称(未加后缀)
#$3 服务器目录
#$4 年份
#$5 月份
#$6 天
#$7 小时

cd $1

fullextend='.gz'

#tar.xz->tar
fullpath=${2}${fullextend}

#tar->*.txt
subdir='/home/share/safe_calc/log/unpackdata_realtime/'${4}'/'${5}'/'${6}'/'${3}'/'${7}
mkdir -p $subdir
cd $subdir
gzip -d  $fullpath

#calc data

node realtime_safe_log_parse_db.js $subdir $4 $5 $6
