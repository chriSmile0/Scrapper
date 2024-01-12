// save this as scrape.js
// Thanks to : https://webscraping.ai/faq/php/how-can-i-handle-javascript-rendered-content-in-php-web-scraping
const { argv } = require('process');
const puppeteer = require('puppeteer');

async function scrape(url) {
  const browser = await puppeteer.launch({headless: 'new'});
  const page = await browser.newPage();
  await page.goto(url);
  const content = await page.content(); // Get the full HTML content
  console.log(content);
  await browser.close();
}

//console.log(argv[2])
scrape(argv[2]); // Replace with the target URL
