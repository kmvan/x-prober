//import sf and http modules
var fs=require("fs");
var http = require("http");
var temp;

//read CPU temperature every second
setInterval(function() {temp = fs.readFileSync("/sys/class/thermal/thermal_zone0/temp");},1000);

//creat http server
var server = http.createServer(function (request, response) {
//write response header
response.writeHead(200, {
//defining document type then charset "utf-8"
'Content-Type': 'application/json;charset="utf-8"',
//CORS
'Access-Control-Allow-Origin': "*"
});

var info = {
a : [{
id : "cpu",
name : "CPU 温度",
celsius : temp/1000,
}]
};

//transform object into string, and output with .end() method
response.end(JSON.stringify(info.a));
});

//listen port and IP address
server.listen(4096,'127.0.0.1');
