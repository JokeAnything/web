/*
var str = "var a = \"3999\"";
var regExp = /\"([0-9])\"/g; //未使用g选项
var res = regExp.exec(str);
console.log(res); //输出[ 'aaa', index: 0, input: 'aaabbbcccaaabbbccc' ] */


function getStockInfo(respond){
    let result = new Object();
    let len = respond.length; 
    if(len == 0){
        return result;
    }
    let start = respond.search("\"");
    let value = respond.slice(start + 1, len - 1);
    let valArray = value.split(",");

    valArray.forEach((item,idx) => {
        let key=`s${idx}`;
        result[key] = item;
    });

    let resultStr = JSON.stringify(result);
    return resultStr;
}

var str = "var hq_str_sz002195=\"二三四五,4.060,4.030,3.990,4.070,3.950,3.980,3.990,64189923,257130266.350,197800,3.980,433052,3.970,815000,3.960,1135516,3.950,486100,3.940,169490,3.990,492934,4.000,485920,4.010,691050,4.020,493950,4.030,2018-10-26,15:05:03,00\"";

var output = getStockInfo(str);

console.log(output);