'use strict'
var path = require('path');
var fs = require('fs');
var Promise = require('bluebird');
var Client = require('ftp');
var crypto = require('crypto');

var ftpObj = new Client();

var connectionProperties = {
    host: "172.17.19.88",
    user: "zhuzb",
    password: "zZ8r'|h3ixjPYoCm;>"
};

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


let year = process.argv[2];
let month = process.argv[3];
let day = process.argv[4];
let filecount = hourArray.length * svrArray.length;

console.log(`try to pull log date:${year}-${month}-${day}`);

let isConnect = false;
let isGetPending = false;
let logFilesList = new Array();

let logRoot = "/click_txt/safe/data/";

ftpObj.connect(connectionProperties);

ftpObj.on('ready', function () {
    console.log("ready");
    isConnect = true;
})

function getSpecificHourLog(svr,hour){
    // 开始pending状态
    let logTargetFile = "";
    let chkTargetFile = "";
    // 优先创建日志目录
    let logDir = `data/${svr}/${year}/${month}${day}`;
    mkdirsSync(logDir);

    console.log(`${svr}-${hour}`);

    let ftpLogFile = `/click_txt/safe/data/${svr}/${year}/${month}${day}/${year}-${month}-${day}-${hour}.tar.xz`;
    let logObjPt = new Object();
    try{
        ftpObj.get(ftpLogFile,function(err,stream){
            if (err){
                console.log(`error:${ftpLogFile} encountered exception,skip!`);
                return;
                //throwerr;
            }
            stream.once('close', function () {
                logObjPt["data"] = logTargetFile;
            });
            logTargetFile = `data/${svr}/${year}/${month}${day}/${year}-${month}-${day}-${hour}.tar.xz`;
            stream.pipe(fs.createWriteStream(logTargetFile));
            console.log(logTargetFile);
        })
    }catch(err){
        console.log(`error:${ftpLogFile} encountered exception,skip!`);
    }
    let ftpLogChk = `/click_txt/safe/data/${svr}/${year}/${month}${day}/${year}-${month}-${day}-${hour}.md5`;
    try{
        ftpObj.get(ftpLogChk,function(err,stream){
            if (err){
                console.log(`error:${ftpLogChk} encountered exception,skip!`);
                return;
                //throw err;
            } 
            stream.once('close', function () {
                // 停止pending状态
                isGetPending = false;
                logObjPt["md5"] = chkTargetFile;
                logFilesList.push(logObjPt);
            });
            chkTargetFile = `data/${svr}/${year}/${month}${day}/${year}-${month}-${day}-${hour}.md5`;
            stream.pipe(fs.createWriteStream(chkTargetFile));
            console.log(chkTargetFile);
        })
    }catch(err){
        console.log(`error:${ftpLogChk} encountered exception,skip!`);
    }
}

let index = 0;
let chkSuccNum = 0;
let chkFailNum = 0;
let resumeTime = 0;
setInterval(()=>{
    if(!isConnect){
        return ;
    }
    
    if(isGetPending){
        return;
    }

    if(index >= (hourArray.length * svrArray.length))
    {
        ftpObj.end();
        checkFileComplete();
        console.log(`pull log finish,total=${filecount},succnum:${chkSuccNum},fialnum:${chkFailNum},consume-time:${resumeTime / 60000}min`);
        process.exit(0);
        return;
    }

    let svrIdx = Math.floor(index / hourArray.length);
    let hourIdx = index % hourArray.length;

    isGetPending = true;
    getSpecificHourLog(svrArray[svrIdx],hourArray[hourIdx]);
    index++;
    resumeTime = resumeTime + 1000;
    console.log(`next fetch index ${index},progress:${((index-1)/filecount)*100}`);
},1000)

function mkdirsSync(dirname) {
    if (fs.existsSync(dirname)) {
      return true;
    } else {
      if (mkdirsSync(path.dirname(dirname))) {
        fs.mkdirSync(dirname);
        return true;
      }
    }
}

function calcFileMd5(filePath){
    if(filePath == null ||
        filePath == ""){
            return "";
    }
    var data = fs.readFileSync(filePath);
    let hash = crypto.createHash('md5');
    hash.update(data);
    return hash.digest('hex');
}

function getMd5ValueFromFile(filePath){
    if(filePath == null ||
        filePath == ""){
            return "";
    }
    var data=fs.readFileSync(filePath,'utf-8');
    var arr = data.split(" ");
    var element = arr[0];
    element.trim();
    return element;
}

function checkItemObj(item){
    let md5 = getMd5ValueFromFile(item['md5']);
    let data = calcFileMd5(item['data']);
    if(md5 == "" || data == ""){
        return false;
    }
    if(md5 === data){
        return true;
    }else{
        return false;
    }
}

function checkFileComplete(){
    logFilesList.forEach((item)=>{
        if(checkItemObj(item)){
            console.log(`log:${item['data']} check succ!`);
            chkSuccNum++;
        }else{
            console.log(`log:${item['data']} check fail!`);
            chkFailNum++;
        }
    });
}
