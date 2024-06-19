// save this as scrape_su.js
// Thanks to : https://github.com/berstend/puppeteer-extra/tree/master/packages/puppeteer-extra-plugin-stealth contributors <333
var break_js = require('./DatadomeBreaker/break');
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
 * @param { string }  url
 * @param { string }  town
 * @param { string }  product 
 * @return { string } a parse solution to gather all products of the differents
 *                    pages in on string to parse with my 'php' algorithm 
 */

async function scrape(url,town,product) {
  // -- ERROR BOT DETECTION AFTER MANY GOOD TRY WITH THIS SEQUENCE OF INSTRUCTIONS -- //
  const browser = await puppeteer.launch({headless: 'new'});
  const page = await browser.newPage();
  await page.goto(url);
  const url_ = await page.url();
  console.log(url_);
  console.log(town);
  console.log(product);
  const dD = url_.indexOf("on/demand"); 
  console.log(dD);
  if(dD != -1) {
    console.log("DataDome activate");
    //break_js.loadedBrk(page,url,'#captcha__puzzle','.slider',"canva_rd.png","screen_su.png");
  }
  else {
    console.log("No DataDome");
  }
  // -----------------------NO DETECTION BOT USAGE (15 hit OK) ----------------------------// 
  //await page.waitForTimeout(2000);
  await page.screenshot({path:'screen_home_su.png'});
  await page.waitForSelector('#popin_tc_privacy_button_2');
  
  /*await page.click('#popin_tc_privacy_button_2');
  // -----------------------NO DETECTION BOT USAGE (15 hit OK)----------------------------// 
  await page.waitForSelector('#store-search');
  await page.type('#store-search',town,{delay: 100});

  // -----------------------NO DETECTION BOT USAGE (15 hit OK)----------------------------//   
  //await page.waitForTimeout(3000); //-> necessary ?  delay ?
  const sleep = ms => new Promise(res => setTimeout(res, ms));
  await sleep(3000);
  await page.waitForSelector('.ab-prehome-search-suggestion');
  await page.click('.ab-prehome-search-suggestion');

  // -----------------------NO DETECTION BOT USAGE(12 hit OK)----------------------------//  
  // PRECISE RESEARCH WITH ONE ELEMENT RESULT, MAYBE MORE IN THE FUTURE
  await page.waitForSelector('.store-delivery-mode-arrow'); // click on the store
  //CHECK FOR HAVE ALL ARROW WITH THE TEXT TO SELECT THE STORE !!!!
  await page.click('.store-delivery-mode-arrow');
  await page.waitForSelector('.ui-button');
  const wq2 = await page.$('.ui-button');
  wq2.click();*/
  /*if(wq2 === null) { // - NO Question
    console.log("no wq2"); // OK 
  }
  else {
    //await page.waitForSelector('#q'); // click on the store
    //await page.click('#q');
    const wq2_pos = await wq2.boundingBox();
    console.log(wq2_pos);
    console.log("message to quit2");
    if(wq2_pos !== null)
      wq2.click();
  }*/
  //await page.waitForTimeout(1000);
  /*await sleep(1000);
  await page.type('#q',product,{delay: 100});
  await page.waitForTimeout(1000);
  const suggets1 = await page.$('xpath//html/body/div[3]/main/div[1]/header/div[4]/div[2]/div/div[1]/div[1]/div[1]/a');
  if(suggets1 !== null) {
    const suggets1_bouding = await suggets1.boundingBox();
    console.log(suggets1_bouding);
    suggets1.click();
  }
  await page.waitForTimeout(5000);

  // -----------------------NO DETECTION BOT USAGE(n hit OK ?) ----------------------------//

  await page.screenshot({ path: 'screen_after_search_.png', fullPage: true }); // necessary Idk why
  const content = await page.content();
  const nb_prod = content.substring(content.indexOf('internal_search_results'));
  const cp_nb_prod = nb_prod;
  const nb_prod_trully = cp_nb_prod.substring(nb_prod.indexOf(':')+1,nb_prod.indexOf("internal_search_type")-2);
  console.log("nb_prod_truly : "+nb_prod_trully);


  const sub_line = nb_prod.substring(nb_prod.indexOf("products"));
  const extra_line = sub_line.substring(0,sub_line.indexOf("page_filter"));
  let retour = extra_line + "\n";
  const to_int_page =  parseInt(nb_prod_trully);
  const div_nb_page = parseInt(to_int_page/20);
  const nb_page = (to_int_page%20==0) ? div_nb_page : div_nb_page+1; // 20 = nb_max prod per page 
  console.log(nb_page);
  const _url =  await page.url();
  // -----------------------NO DETECTION BOT USAGE(n hit OK ?)----------------------------//
  for(var i = 2 ; i < nb_page+1; i++) {
    await page.goto(_url+ '&page='+i);
    //await page.waitForTimeout(1000);
    await sleep(1000);
    const content =  await page.content();
    const sub_line = content.substring(content.indexOf("products"));
    retour += sub_line.substring(0,sub_line.indexOf("page_filter")) + "\n";
  }*/
  //console.log(retour);
  const content = await page.content();
  console.log(content)
  await browser.close();
}
//url = "https://www.coursesu.com/drive/home";
//url2 = "https://www.coursesu.com/drive-superu-";
link = argv[2];
product = argv[3];
scrape_v2(link,product);


async function scrape_v2(url,product) {
  const sleep = ms => new Promise(res => setTimeout(res, ms));
  const browser = await puppeteer.launch({headless: 'new'});
  const page = await browser.newPage();
  //parse town if it's necessary -> PHP better 
  await page.goto(url);
  const url_ = await page.url();
  console.log(url_);
  console.log(product);
  const title = await page.title();
  console.log(title);

  const dD = url_.indexOf("on/demand"); 
  console.log(dD);
  if(dD != -1) {
    console.log("DataDome activate");
    //break_js.loadedBrk(page,url,'#captcha__puzzle','.slider',"canva_rd.png","screen_su.png");
  }
  else {
    console.log("No DataDome");
  }
  // -----------------------NO DETECTION BOT USAGE (15 hit OK) ----------------------------// 
  await page.screenshot({path:'screen_home_su2.png'});
  await page.waitForSelector('#popin_tc_privacy_button'); 
  await page.click('#popin_tc_privacy_button');
  /*await page.waitForSelector('#q');
  await page.type('#q',product,{delay: 100});

  await sleep(1000);
  await page.screenshot({ path: 'screen_after_search_.png', fullPage: true }); // necessary Idk why
  const suggets1 = await page.$('xpath//html/body/div[3]/main/div[1]/header/div[4]/div[2]/div/div[1]/div[1]/div[1]/a');
  if(suggets1 !== null) {
    const suggets1_bouding = await suggets1.boundingBox();
    console.log(suggets1_bouding);
    suggets1.click();
  }
  await sleep(1000);
  await page.screenshot({ path: 'screen_after_search2_.png', fullPage: true });

  await sleep(5000);
  await page.screenshot({ path: 'screen_after_search3_.png', fullPage: true });
  await sleep(1000);*/
  const content = await page.content();
  console.log(content);
  await browser.close();
}