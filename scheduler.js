fetch(`http://localhost:8000/api/send-notifications`);
console.log("Scheduler is running...");
setInterval(() => {
    fetch(`http://localhost:8000/api/send-notifications`);
}, 60000);

setInterval(() => {
    console.log("Scheduler is running...");
}, 5000);