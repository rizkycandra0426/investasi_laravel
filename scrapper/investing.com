// ==UserScript==
// @name         InvestingScrapper
// @namespace    http://tampermonkey.net/
// @version      2024-07-22
// @description  try to take over the world!
// @author       You
// @match        https://id.investing.com/indices/idx-composite-historical-data
// @icon         https://www.google.com/s2/favicons?sz=64&domain=investing.com
// @grant        none
// @require      https://code.jquery.com/jquery-3.7.1.min.js
// ==/UserScript==

(function() {
    'use strict';

    // Your code here...
    $(".historical-data-v2_selection-arrow__3mX7U").trigger("click");
    setTimeout(function(){
        $(".historical-data-v2_menu-row__oRAlf").trigger("click");
        $(".historical-data-v2_menu-row-text__ZgtVH").trigger("click");
    },1000);

    function formatDate(dateString) {
        const [day, month, year] = dateString.split('/');
        return `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
    }
    function number(str) {
        return parseFloat(str.replace(/./,"").replace(/,/,"."));
    }

    setInterval(function(){


        var table = $("table").first().find("tbody");
        table.find("tr").each(function(){

            var tr = $(this);

            var row = tr.find("td");
            var data = {
                "id": formatDate(row.eq(0).text()),
                "date": formatDate(row.eq(0).text()),
                "last": number(row.eq(1).text()),
                "open": number(row.eq(2).text()),
                "high": number(row.eq(3).text()),
                "low": number(row.eq(4).text()),
                "vol": number(row.eq(5).text()),
                "change": number(row.eq(6).text())
            };
            console.log(data);
            $.post("http://localhost:8000/api/v2/ihsg", data);

        });





    },5000);
})();