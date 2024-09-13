fetch("https://50dev6p9k0-dsn.algolia.net/1/indexes/*/queries?x-algolia-agent=Algolia%20for%20vanilla%20JavaScript%20(lite)%203.25.1%3Binstantsearch.js%202.6.3%3BJS%20Helper%202.24.0&x-algolia-application-id=50DEV6P9K0&x-algolia-api-key=cd2dd138c8d64f40f6d06a60508312b0", {
    "headers": {
        "accept": "application/json",
        "accept-language": "en,en-US;q=0.9",
        "cache-control": "no-cache",
        "content-type": "application/x-www-form-urlencoded",
        "pragma": "no-cache",
        "sec-ch-ua": "\"Chromium\";v=\"128\", \"Not;A=Brand\";v=\"24\", \"Google Chrome\";v=\"128\"",
        "sec-ch-ua-mobile": "?1",
        "sec-ch-ua-platform": "\"Android\"",
        "sec-fetch-dest": "empty",
        "sec-fetch-mode": "cors",
        "sec-fetch-site": "cross-site"
    },
    "referrer": "https://www.fxstreet-id.com/",
    "referrerPolicy": "strict-origin-when-cross-origin",
    "body": "{\"requests\":[{\"indexName\":\"FxsIndexPro\",\"params\":\"query=&hitsPerPage=20&maxValuesPerFacet=9999&page=0&filters=CultureName%3Aid%20AND%20(Category%3A'Berita'%20OR%20Category%3A'Berita%20Sela'%20OR%20Category%3A'Saham'%20OR%20Category%3A'Kontributor%20Saham')&facets=%5B%22Tags%22%2C%22AuthorName%22%5D&tagFilters=\"}]}",
    "method": "POST",
    "mode": "cors",
    "credentials": "omit"
}).then(async (response) => {
    let data = await response.json();
    for (var index in data.results[0].hits) {
        var item = data.results[0].hits[index];

        var timestamp = item.PublicationTime;
        var dateInYMD = new Date(timestamp).toISOString();

        const url = 'http://localhost:8000/api/berita';
        const postBody = {
            "title": item.Title,
            "image_url": item.ImageUrl,
            "url": item.FullUrl,
            "description": item.Summary,
        };
    }
});