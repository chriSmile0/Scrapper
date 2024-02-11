
// save this as scrape.js
// Thanks to : https://webscraping.ai/faq/php/how-can-i-handle-javascript-rendered-content-in-php-web-scraping <3
const { argv } = require('process');
const puppeteer = require('puppeteer');

/**
 * @brief   This function is for scrape the content of a html page
 *          with the html content generate with js file of the target
 *          Puppetter is use for create a recever browser
 *          Now we launch the page on this special browser and the generate
 *          content is on the new page.
 * @param { string } url 
 * @return    / 
 */
async function scrape(url) {
  const browser = await puppeteer.launch({headless: 'new'});
  const page = await browser.newPage();
  await page.goto(url);
  const content = await page.content(); // Get the full HTML content
  console.log(content);
  await browser.close();
}

scrape(argv[2]);