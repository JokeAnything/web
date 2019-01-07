'use strict'
let PORT = 3399;
let http = require('http');
let url = require('url');
var qrystr = require("querystring");
let fs = require('fs');
let path = require('path');
let g_stockInfo = new Object();
let g_stockIds = new Array();

// web request:http://192.168.230.131:3388/stock?id=sz002195

let server = http.createServer(function (request, response) {
    var urlObj = url.parse(request.url);
    // urlObj:
    /* Url {
        protocol: null,
        slashes: null,
        auth: null,
        host: null,
        port: null,
        hostname: null,
        hash: null,
        search: '?id=sz002195',
        query: 'id=sz002195',
        pathname: '/stock',
        path: '/stock?id=sz002195',
        href: '/stock?id=sz002195' 
    }*/
    response.writeHead(200, {
        'Content-Type': "text/plain"
    });

    // console.log(urlObj.pathname);
    // console.log(urlObj.query);

    if ((urlObj.pathname == "/stock/") ||
        (urlObj.pathname == "/stock")) {
        if ((urlObj.query != null) && (urlObj.query.length != 0)) {
            var res = qrystr.parse(urlObj.query);
            if (res.id.length != 0) {
                g_stockIds.push(res.id);
                let output = getStockInfoById(res.id);
                response.write(output);
                console.log(output);
            }
        }
    }
    response.end();
});

server.listen(PORT);
console.log("Server runing at port: " + PORT + ".");

setInterval(() => {
    g_stockIds.forEach(item => {
        requestStockInfoById(item);
    })
}, 10000);

function requestStockInfoById(id) {
    if (id.length == 0) {
        return;
    }
    let options = {
        host: 'hq.sinajs.cn',
        path: `/list=${id}`,
        method: 'get'
    }
    let recvMsg = "";
    let req = null;
    try{
        req = http.request(options, function (req) {
            try{
                req.on("data", function (chunk) {
                    let tmp = chunk.toString('utf-8');
                    recvMsg += tmp;
                });
                req.on("end", function (d) {
                    let obj = getStockInfo(recvMsg);
                    g_stockInfo[id] = obj;
                });        
            }catch(e){
                console.log(e);
                return;
            }
        });    
    }
    catch(e){
        console.log(e);
        return;
    }
    if(req != null){
        req.end();
    }
}

function getStockInfo(respond) {
    let result = new Object();
    let len = respond.length;
    if (len == 0) {
        return result;
    }
    let start = respond.search("\"");

    let value = respond.slice(start + 1, len - 3);
    let valArray = value.split(",");

    valArray.forEach((item, idx) => {
        let key = `s${idx}`;
        result[key] = item;
    });
    return result;
}

function getStockInfoById(id) {
    if (id.length == 0){
        return "";
    }
    if(g_stockInfo.hasOwnProperty(id)){
        let objInfo = g_stockInfo[id];
        let objRes = JSON.stringify(objInfo);
        return objRes;
    }else{
        return "{}"
    }
}
