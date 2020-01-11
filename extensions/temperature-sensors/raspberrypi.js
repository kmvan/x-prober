/**
 * Raspberry PI Series (Linux) Temperature CPU
 *
 * @usage `node ./raspberrypi.js`
 */

//import sf and http modules
const fs = require('fs')
const http = require('http')
let temp

//read CPU temperature every second
setInterval(() => {
  temp = fs.readFileSync('/sys/class/thermal/thermal_zone0/temp')
}, 1000)

//creat http server
const server = http.createServer((request, response) => {
  //write response header
  response.writeHead(200, {
    //defining document type then charset "utf-8"
    'Content-Type': 'application/json;charset="utf-8"',
    //CORS
    'Access-Control-Allow-Origin': '*',
  })
  const items = [
    {
      id: 'cpu',
      name: 'CPU',
      celsius: temp / 1000,
    },
  ]
  //transform object into string, and output with .end() method
  response.end(JSON.stringify(items))
})
//listen port and IP address
server.listen(4096, '127.0.0.1')
