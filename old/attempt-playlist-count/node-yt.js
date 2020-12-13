const fs = require('fs');
const puppeteer = require("puppeteer");
// const buff = require("Buffer");

if(process.argv.length>=3 && process.argv[2].length) {
    var channelName = process.argv[2];
    scrape(`https://www.youtube.com/user/${channelName}/videos`);
    // console.log(`https://www.youtube.com/user/${channelName}/videos`);
} else {
    console.log("Error: Wrong Chain:: yt.php(channel_name) -> yt.js(channel_name) -> Result @ yt.php: playlist link")
}

function scrape(url) {
    return (async () => {
        const browser = await puppeteer.launch({
          headless: true,
          timeout: 0,
          defaultViewport: { width: 1300, height: 700 }
        });
    
        const page = await browser.newPage();
        const response = await page.goto(url, { timeout: 0 });
        var text = await response.text();

        fs.writeFile("viewsource.txt", text, (err, text)=> {});

        var a = text.indexOf("playlist?list=") + "playlist?list=".length;
        var b = a + ( text.substr(a) ).indexOf("\"");
        if(a*b>0) // no -1
            var playlistId = text.substr(a, b-a);
        else
            var playlistId = "";

        playlistId = playlistId.replace(/\\u0026/g, "").replace(/playnext=[0-9]/g, "").replace(/index=[0-9]/g, "");
        // console.log("a: ", a);
        // // console.log("a on: ", text.substr(a));
        // console.log("b: ", b);


        var link = `${playlistId}`;
        console.log(link);

        await browser.close();
      })();
} // scrape