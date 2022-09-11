const http2 = require("http2");
const fs = require("fs");

/* 
  Use 'https://api.push.apple.com' for production build
*/

const deviceToken =
  "f25a3addef4ba8c89a90576d37af33344e3b8c66185c58cfab41e8fc9e09b774";
host = "https://api.sandbox.push.apple.com";
path = "/3/device/" + deviceToken;

/*
Using certificate converted from p12.
The code assumes that your certificate file is in same directory.
Replace/rename as you please
*/

const client = http2.connect(host, {
  key: fs.readFileSync(__dirname + "/newfile.key.pem"),
  cert: fs.readFileSync(__dirname + "/newfile.crt.pem"),
});

client.on("error", (err) => console.error(err));

body = {
  aps: {
    alert: "hello Supapon",
    "content-available": 1,
  },
};

headers = {
  ":method": "POST",
  "apns-topic": "com.pucknavin.TestNotification11Sep", //you application bundle ID
  ":scheme": "https",
  ":path": path,
};

const request = client.request(headers);

request.on("response", (headers, flags) => {
  for (const name in headers) {
    console.log(`${name}: ${headers[name]}`);
  }
});

request.setEncoding("utf8");

let data = "";
request.on("data", (chunk) => {
  data += chunk;
});
request.write(JSON.stringify(body));
request.on("end", () => {
  console.log(`\n${data}`);
  client.close();
});
request.end();
