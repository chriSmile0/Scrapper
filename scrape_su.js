const puppeteer = require('puppeteer-extra')
const { argv } = require('process');

// add stealth plugin and use defaults (all evasion techniques)
const StealthPlugin = require('puppeteer-extra-plugin-stealth')
puppeteer.use(StealthPlugin())

// Add adblocker plugin to block all ads and trackers (saves bandwidth)
const AdblockerPlugin = require('puppeteer-extra-plugin-adblocker')
puppeteer.use(AdblockerPlugin({ blockTrackers: true }))


// HELP SOURCE : https://github.com/puppeteer/puppeteer/blob/main/packages/puppeteer-core/src/api/Page.ts
/*
puppeteer.launch({ headless: true}).then(async browser => {
  const page = await browser.newPage()
  //TEST OTHER URL !! -> https://www.coursesu.com/drive-[STORE(uexpress-parispetitscarreaux)] 
  //TEST OTHER URL NEXT MOVE !! -> https://www.coursesu.com/recherche?q=[PRODUCT(Lardons)] -> puis click sur la croix + &page for number of page
  await page.goto('https://www.coursesu.com/drive/home'); 
  await page.waitForSelector('#popin_tc_privacy_button_2');
  console.log("found cookies ");
  await page.click('#popin_tc_privacy_button_2');
  //town 
  const title = await page.url();
  console.log(title);

  await page.waitForSelector('#store-search');
  console.log("store search found ");
  await page.type('#store-search','75001, Paris',{delay: 100});
  await page.waitForTimeout(4000);
  await page.waitForSelector('.ab-prehome-search-suggestion');
  await page.click('.ab-prehome-search-suggestion');
  // PRECISE RESEARCH WITH ONE ELEMENT RESULT, MAYBE MORE IN THE FUTURE
  console.log("list found" );
  await page.waitForTimeout(1000);
  
  // go to the store 
  await page.waitForSelector('.store-delivery-mode-arrow'); // click on the store
  //CHECK FOR HAVE ALL ARROW WITH THE TEXT TO SELECT THE STORE !!!!
  await page.click('.store-delivery-mode-arrow');
  console.log("arrow found \n");
  await page.waitForTimeout(2000);
  // end go to the store
  await page.waitForSelector('#q'); // click on the store
  await page.type('#q','saumon',{delay:100});
  await page.waitForTimeout(5000);
  // end search produt 
  await page.waitForSelector('.ui-dialog-titlebar-close'); // click on the store
  await page.click('.ui-dialog-titlebar-close');
  await page.waitForTimeout(5000);

  await page.type('#q','saumon');
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
})*/

async function scrape(url,town,product) {
  const browser = await puppeteer.launch({headless: true});
  const page = await browser.newPage();
  await page.goto(url);
  const url_ = await page.url();
  console.log(url_);
  await page.waitForTimeout(1000);
  await page.waitForSelector('#popin_tc_privacy_button_2');
  //console.log("found cookies ");
  await page.click('#popin_tc_privacy_button_2');
  //town 

  await page.waitForSelector('#store-search');
  //console.log("store search found ");
  //console.log(town);
  await page.type('#store-search',town,{delay: 100});
  await page.waitForTimeout(5000);
  await page.waitForSelector('.ab-prehome-search-suggestion');
  await page.click('.ab-prehome-search-suggestion');
  // PRECISE RESEARCH WITH ONE ELEMENT RESULT, MAYBE MORE IN THE FUTURE
  //console.log("list found" );
  await page.waitForTimeout(1000);
  
  // go to the store 
  await page.waitForSelector('.store-delivery-mode-arrow'); // click on the store
  //CHECK FOR HAVE ALL ARROW WITH THE TEXT TO SELECT THE STORE !!!!
  await page.click('.store-delivery-mode-arrow');
  //console.log("arrow found \n");
  await page.waitForTimeout(2000);
  // end go to the store
  await page.waitForSelector('#q'); // click on the store
  //console.log(product);
  await page.type('#q',product,{delay:100});
  await page.waitForTimeout(5000);
  // end search produt 
  await page.waitForSelector('.ui-dialog-titlebar-close'); // click on the store
  await page.click('.ui-dialog-titlebar-close');
  await page.waitForTimeout(5000);

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