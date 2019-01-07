#!/bin/bash

#$1 year->2018
#$2 month->11
#$3 day->08

echo $1
echo $2
echo $3

node recvlog.js $1 $2 $3

node unpack.js  $1 $2 $3

echo 'finish!'

