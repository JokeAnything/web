'use strict'
let path = require('path');
let fs = require('fs');
let spawn = require('child_process').spawn;

let svrArray= new Array(
    "172.16.20.156",
    "172.16.20.157",
    "172.16.20.167",
    "172.16.20.177",
    "172.16.20.178",
    "172.16.22.211",
    "172.16.22.220",
    "172.16.22.221",
    "172.16.20.214",
    "172.16.20.215",
    "172.16.22.234"
    );

let hourArray= new Array(
    "00",
    "01",
    "02",
    "03",
    "04",
    "05",
    "06",
    "07",
    "08",
    "09",
    "10",
    "11",
    "12",
    "13",
    "14",
    "15",
    "16",
    "17",
    "18",
    "19",
    "20",
    "21",
    "22",
    "23"
);

// 调用参数 node unpack.js 2018 11 06

let index = 0;
let isGetPending = false;
let chkSuccNum = 0;
let chkFailNum = 0;
let resumeTime = 0;
let filecount = hourArray.length * svrArray.length;

let year = process.argv[2];
let month = process.argv[3];
let day = process.argv[4];

//let logRootDir = "/home/share/safe_calc/log/data/${svr}/${year}/${month}${day}/${year}-${month}-${day}-${hour}.tar.xz";

setInterval(()=>{
    
    if(isGetPending){
        return;
    }

    if(index >= (hourArray.length * svrArray.length)){
        console.log(`unpack log finish,total=${filecount},succnum:${chkSuccNum},fialnum:${chkFailNum},consume-time:${resumeTime/60000}min`);
        process.exit(0);
        return;
    }

    let svrIdx = Math.floor(index / hourArray.length);
    let hourIdx = index % hourArray.length;

    isGetPending = true;
    let fileDir = `/home/share/safe_calc/log/data/${svrArray[svrIdx]}/${year}/${month}${day}`;
    let fileNameNoExt = `/home/share/safe_calc/log/data/${svrArray[svrIdx]}/${year}/${month}${day}/${year}-${month}-${day}-${hourArray[hourIdx]}`;
    unpackLogFile(fileDir,fileNameNoExt,svrArray[svrIdx],year,month,day);
    index++;
    resumeTime = resumeTime + 1000;
    console.log(`next fetch index ${index},progress:${((index-1)/filecount)*100}`);
},1000)

/*
脚本参数：
#$1 目标路径
#$2 文件名称(未加后缀)
#$3 服务器目录
#$4 年份
#$5 月份
#$6 天
脚本名称：unpackproc.sh
*/
function unpackLogFile(fileDir,fileNameNoExt,svrName,year,month,day){
    let free = spawn('/home/share/safe_calc/log/unpackproc.sh', [`${fileDir}`,`${fileNameNoExt}`,`${svrName}`,`${year}`,`${month}`,`${day}`]);
    //let free = spawn('/home/share/safe_calc/log/unpacktest.sh');

    // 捕获标准输出并将其打印到控制台 
    free.stdout.on('data', function (data) {
        console.log('standard output:\n' + data);
    });
    // 捕获标准错误输出并将其打印到控制台 
    free.stderr.on('data', function (data) {
        console.log('standard error output:\n' + data);
    });
    // 注册子进程关闭事件 
    free.on('exit', function (code, signal) {
        isGetPending = false;
    });    
}
