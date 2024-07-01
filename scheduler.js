fetch(`https://investasi.9code.id/api/send-notifications`);
console.log("Scheduler is running...");
setInterval(() => {
    fetch(`https://investasi.9code.id/api/send-notifications`);
}, 60000);

setInterval(() => {
    console.log("Scheduler is running...");
}, 5000);