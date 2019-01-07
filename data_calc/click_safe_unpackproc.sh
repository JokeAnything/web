#!/bin/bash

#$1 目标路径
#$2 文件名称(未加后缀)
#$3 服务器目录
#$4 年份
#$5 月份
#$6 天
cd $1

fullextend='.tar.xz'
partextend='.tar'

#tar.xz->tar
fullpath=${2}${fullextend}
xz -d  $fullpath

#tar->*.txt
subdir='/home/share/safe_calc/log/unpackdata/'${4}'/'${5}'/'${6}'/'${3}
mkdir -p $subdir
cd $subdir
partpath=${2}${partextend}
tar -xf $partpath

#del tar file.
#rm -rf $partpath

