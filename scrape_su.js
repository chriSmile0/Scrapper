// save this as scrape_su.js
// Thanks to : https://github.com/berstend/puppeteer-extra/tree/master/packages/puppeteer-extra-plugin-stealth contributors <333
const puppeteer = require('puppeteer-extra')
const { argv } = require('process');

// add stealth plugin and use defaults (all evasion techniques)
const StealthPlugin = require('puppeteer-extra-plugin-stealth')
puppeteer.use(StealthPlugin())

// Add adblocker plugin to block all ads and trackers (saves bandwidth)
const AdblockerPlugin = require('puppeteer-extra-plugin-adblocker')
puppeteer.use(AdblockerPlugin({ blockTrackers: true }))


/**
 * @brief   This function is for scrape the content of a html page
 *          with the html content generate with js file of the target
 *          Puppetter is use for create a recever browser
 *          Now we launch the page on this special browser and the generate
 *          content is on the new page.
 * 
 *          The 'extra-plugin' is a no trace content this is why
 *          is not dectected by the no-Bot solutions like as 'DataDome' or 'Cloudflare'
 *          and it's use for bypass captcha,recaptcha etcc..
 * 
 *          In this case I have many operations to process, like click on 
 *          element list or type in an input field 
 *          
 *          And the goal is to extract JSON content (line 20) of each page
 *          for have informations of the products display on each page.
 * 
 * @param { string } url
 * @param { string } town
 * @param { string } product 
 * @return { string } a parse solution to gather all products of the differents
 *                    pages in on string to parse with my 'php' algorithm 
 */

async function scrape(url,town,product) {
  const browser = await puppeteer.launch({headless: true});
  const page = await browser.newPage();
  await page.goto(url);
  const url_ = await page.url();
  console.log(url_);
  await page.waitForTimeout(1000);
  await page.waitForSelector('#popin_tc_privacy_button_2');
  await page.click('#popin_tc_privacy_button_2');


  await page.waitForSelector('#store-search');
  await page.type('#store-search',town,{delay: 100});
  await page.waitForTimeout(5000);
  await page.waitForSelector('.ab-prehome-search-suggestion');
  await page.click('.ab-prehome-search-suggestion');
  // PRECISE RESEARCH WITH ONE ELEMENT RESULT, MAYBE MORE IN THE FUTURE
  await page.waitForTimeout(1000);
  await page.waitForSelector('.store-delivery-mode-arrow'); // click on the store
  //CHECK FOR HAVE ALL ARROW WITH THE TEXT TO SELECT THE STORE !!!!
  await page.click('.store-delivery-mode-arrow');
  //console.log("arrow found \n");
  await page.waitForTimeout(2000);
  // end go to the store
  await page.waitForSelector('#q'); // click on the store
  console.log(product);
  await page.type('#q',product);
  await page.waitForTimeout(5000);
  // end search produt 
  await page.waitForSelector('.ui-dialog-titlebar-close'); // click on the store
  await page.click('.ui-dialog-titlebar-close');
  await page.waitForTimeout(5000);

  await page.screenshot({ path: 'screen.png', fullPage: true });
  await page.type('#q',product);
  await page.type('#q',String.fromCharCode(13));
  await page.waitForTimeout(5000);
 
  
  //await page.waitForSelector('.pagination-hidden > ul:nth-child(1) > li > a');
  const allLi = await page.$$(".pagination-hidden > ul:nth-child(1) > li > a");
  const _url = await page.url();
  const content = await page.content();
  const sub_line = content.substring(content.indexOf("products"));
  const extra_line = sub_line.substring(0,sub_line.indexOf("page_filter"));
  let retour = extra_line + "\n";
  for(i = 2; i < allLi.length+1; i++) {
    await page.goto(_url+ '&page='+i);
    await page.waitForTimeout(1000);
    const content = await page.content();
    const sub_line = content.substring(content.indexOf("products"));
    retour += sub_line.substring(0,sub_line.indexOf("page_filter")) + "\n";
  }
  await browser.close();
  console.log(retour);
}
url = "https://www.coursesu.com/drive/home";
town = argv[2];
product = argv[3];
scrape(url,town,product);