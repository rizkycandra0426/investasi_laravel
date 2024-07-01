// node .\scheduler.js

// var baseUrl = `http://localhost:8000/api`;
var baseUrl = `https://investasi.9code.id/api`;
fetch(`${baseUrl}/send-notifications`);
setInterval(() => {
    fetch(`${baseUrl}/send-notifications`);
// }, 5000);
}, 60000);
