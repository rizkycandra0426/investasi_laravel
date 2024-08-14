const puppeteer = require('puppeteer');

(async () => {
    const url = 'https://www.idx.co.id/primary/NewsAnnouncement/GetNewsSearch?locale=id-id&pageNumber=1&pageSize=20';

    try {
        const browser = await puppeteer.launch({
            headless: false,
            args: ['--window-size=100,100', '--no-sandbox', '--disable-setuid-sandbox', '--disable-gpu', '--disable-web-security'],
        });
        const page = await browser.newPage();

        await page.goto(url, { waitUntil: 'networkidle2' }); // Wait for page to load fully

        // Wait for specific element to be loaded (if necessary)
        // await page.waitForSelector('.your-selector');
        const allText = await page.evaluate(() => {
            return document.body.innerText;
        });
        var obj = JSON.parse(allText);

        /*
 $title = $item["Title"];
            $imageUrl = "https://www.idx.co.id/" + $item["ImageUrl"];

            // https://www.idx.co.id/id/berita/berita/0ad6f5ee-a858-ef11-b809-005056aec3a4?id=10845
            $id = $item["Id"];
            $itemId = $item["ItemId"];
            $url = "https://www.idx.co.id/id/berita/berita/$itemId?id=$id";
            $publishedDate = $item["PublishedDate"];
            $description = $item["Summary"];
        */

        var items = obj["Items"];
        for (var i = 0; i < items.length; i++) {
            var item = items[i];
            var title = item["Title"];
            var imageUrl = "https://www.idx.co.id/" + item["ImageUrl"];
            var id = item["Id"];
            var itemId = item["ItemId"];
            var newsUrl = "https://www.idx.co.id/id/berita/berita/" + itemId + "?id=" + id;
            var publishedDate = item["PublishedDate"];
            var description = item["Summary"];

            //SEND POST REQUEST TO SERVER
            const url = 'http://localhost:8000/api/berita';
            const data = {
                "key": "f2139dff-b812-5391-eb6c-d8897461",
                "title": title,
                "image_url": imageUrl,
                "url": newsUrl,
                "published_date": publishedDate,
                "description": description,
            };

            try {
                let response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data),
                });
                //log status code
                console.log(response.status);
                browser.close();
            } catch (error) {
                console.error('Error:', error);
            }
        }

        console.log(items);

        await browser.close();
    } catch (error) {  // Handle errors
        console.error('Error scraping:', error);
    }
})();
