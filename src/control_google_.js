const {By, Builder, Browser} = require('selenium-webdriver');
const assert = require("assert");

(async function firstTest() { // TEST SOON WAITED WORK, TRY CLICK 
  let driver;
  
  try {
    driver = await new Builder().forBrowser(Browser.CHROME).build();
    await driver.get('https://intermarche.com');
  
    let title = await driver.getTitle();
	  console.log(title);
    //assert.equal("Web form", title);
  
    await driver.manage().setTimeouts({implicit: 500});
  
    /*let textBox = await driver.findElement(By.name('my-text'));
    let submitButton = await driver.findElement(By.css('button'));
  
    await textBox.sendKeys('Selenium');
    await submitButton.click();
  
    let message = await driver.findElement(By.id('message'));
    let value = await message.getText();
    assert.equal("Received!", value);*/
  } catch (e) {
    console.log(e)
  } finally {
    await driver.quit();
  }
}())