#!/bin/bash

echo $1
echo $2
echo $3

#node realtime_safe_log_recv.js $1 $2 $3

node realtime_safe_unpack.js  $1 $2 $3

echo 'finish!'

