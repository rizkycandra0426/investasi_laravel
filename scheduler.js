// node .\scheduler.js

<<<<<<< HEAD
fetch('https://investasi.9code.id//api/send-notifications');
console.log("Scheduler is running...");
setInterval(() => {
console.log("Scheduler is executed..");
    fetch('https://investasi.9code.id//api/send-notifications');
=======
// var baseUrl = `http://localhost:8000/api`;
var baseUrl = `https://investasi.9code.id/api`;
fetch(`${baseUrl}/send-notifications`);
setInterval(() => {
    fetch(`${baseUrl}/send-notifications`);
>>>>>>> d19e939bcaa36522b2c94bf0c8a00cf9afedf14c
// }, 5000);
}, 60000);



setInterval(() => {
   console.log("Scheduler is running...");
}, 5000);
