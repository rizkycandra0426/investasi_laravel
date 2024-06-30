// node .\scheduler.js

fetch('http://localhost:8000/api/send-notifications');
setInterval(() => {
    fetch('http://localhost:8000/api/send-notifications');
// }, 5000);
}, 60000);
