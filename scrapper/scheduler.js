function sendNotifications() {
    fetch(`http://localhost:8000/api/send-notifications?key=f2139dff-b812-5391-eb6c-d8897461&now=false`);
}

function scrapNews() {
    var shellCommand = "node scrapper/index.js";
    var exec = require('child_process').exec;
    exec(shellCommand, function (error, stdout, stderr) {
        console.log(stdout);
    });
}

sendNotifications();
scrapNews();

setInterval(() => {
    console.log(`Send notifications at ${new Date()}`);
    sendNotifications();
}, 60000);


setInterval(() => {
    console.log(`Scrap news at ${new Date()}`);
    scrapNews();
}, 60000);