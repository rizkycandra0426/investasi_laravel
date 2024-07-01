// node .\scheduler.js

fetch('https://investasi.9code.id//api/send-notifications');
console.log("Scheduler is running...");
setInterval(() => {
console.log("Scheduler is executed..");
    fetch('https://investasi.9code.id//api/send-notifications');
// }, 5000);
}, 60000);



setInterval(() => {
   console.log("Scheduler is running...");
}, 5000);
