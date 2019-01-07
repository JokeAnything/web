"use strict";
const fs = require('fs');
const path = require('path');
const mysql = require('mysql'); //调用MySQL模块

// 调用命令
//node parse_calc_log_mysql.js F:\calc_data\box 2018-09-04

let mysqlConnObj = null;
let newTableName = null;

let totalRows = 0;
let insertedRows = 0;
function connectMysql(tableName) {
    //创建一个connection 
    mysqlConnObj = mysql.createConnection({
        host: 'localhost',    //host
        user: 'root',        // mysql user
        password: 'root',    //mysql password
        database: 'db_realtime_safe', // target database
        port: '3306'          //mysql default password.
    });

    //创建一个connection 
    mysqlConnObj.connect(function (err) {
        if (err) {
            console.log('[connect] - :' + err);
            return;
        }
        console.log('connect mysql successfully.');
        // create table.

        if (tableName.length <= 0) {
            console.log('table name is invalid');
            return;
        }

        // create database table.
        let sqlNewTbl = `create table if not exists ${tableName} (
        id int(4) not null AUTO_INCREMENT,
        uuid char(32) not null UNIQUE,
        primary key (id)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;`;

        //mysqlConnObj.querySy
        mysqlConnObj.query(sqlNewTbl, function (err, result) {
            if (err) {
                throw err;
            } else {
                console.log(`create table ${tableName} successfully.`);
            }
        });

        // empty table data.
        let sqlDel = `delete from ${tableName}`;
        mysqlConnObj.query(sqlDel, function (err, result) {
            if (err) {
                throw err;
            } else {
                console.log(`delete table ${tableName}.`);
            }
        });
    }
    );
}

function parseFile(filePath) {
    // 读取文件
    fs.readFile(filePath, (errr, data) => {
        if (errr) {
            console.error(errr);
        } else {
            // 获取一行文本
            var result = [];
            var contentstr = data.toString();
            var array = contentstr.split("\n");
            totalRows += array.length;
            array.forEach(dataEle => {
                // json解析的内容
                dataEle.trim();
                if (dataEle.length <= 0) {
                    insertedRows++;
                    return;
                }
                let content = JSON.parse(dataEle);

                let boxid = content["a1"];
                let jsonItems = ["a1", "a2", "a3", "b1", "b2", "b3", "b4", "b5", "b6", "b7", "c", "ip"];
                let value = [];
                jsonItems.forEach(element => {
                    let str = content[element].toString();
                    value.push(str);
                });

                let date = new Date(content["timestamp"] * 1000);
                let datestr = date.toLocaleDateString() + " " + date.toLocaleTimeString();
                value.push(datestr);
                let sqlStat = `INSERT INTO ${newTableName} (boxid, mid, root, cpu, cpunum, cpuusage, network, harddisk,version,hour,c,ip,recvtime) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ? ,? ,? )`
                mysqlConnObj.query(sqlStat, value, function (err, result) {
                    if (err) {
                        console.log('insert encounter one error.');
                    }
                    insertedRows++;
                });
            });
        }
    })
}

// go starting from this point.
let rootdir = "";
let year = "";
let month = "";
let day = "";

if (process.argv.length >= 5) {
    rootdir = process.argv[1];
    year = process.argv[2];
    month = process.argv[3];
    day = process.argv[4];

    console.log(`parameter:${rootdir},${year},${month},${day}`);
}
else {
    console.log("missing root dir or date argument");
    process.exit(0);
}


let newTblName = `tbl_uuid_${year}${month}${day}`;

// connect database and create new table for current date.
try {
    connectMysql(newTblName);
}
catch (err) {
    console.log(err);
}

// get all files.
let fileslist = fs.readdirSync(rootdir);
if (fileslist.length <= 0) {
    console.log("file list is empty");
    process.exit(0);
}


console.log("total files:" + fileslist.length);

let timerPending = false;
let filesCount = fileslist.length;
let curFileIdx = 0;

function postFileTask() {

    // 此处防止内存崩溃溢出
    let isFeed = totalRows - insertedRows;
    // 控制在可用的1000以下
    if (isFeed < 1000) {
        // 继续投喂数据
        if (curFileIdx < filesCount) {
            parseFile(fileslist[curFileIdx]);
            curFileIdx++;
        }
    }
    timerPending = false;
}

setInterval(() => {
    if (!timerPending) {
        setTimeout(postFileTask, 300);
        timerPending = true;
    }
    console.log("status:" + insertedRows + "/" + totalRows);
    if ((insertedRows === totalRows) && 
        (curFileIdx === filesCount)) {
        mysqlConnObj.end();
        process.exit(1);
    }
}, 300);

