# Scrapper 

## Disclaimer 
- **_This tool is not for collect personal information_**
- Please respect the [RGPDs rules](https://eur-lex.europa.eu/legal-content/EN/TXT/?uri=CELEX:32016R0679)

## What is a scrapper
A tool to collect any information of website pages :For example javascript,html,css sources
It's possible to look the content of a website pages with the browser with this tips : 
- `firefox view-source:https://www.mozilla.org/fr/` or `CTRL^U`
- `CRTL+MAJ+I` for web inspector -> Console, possibility to change the display conten

Or with special library and framework like : 
- Selenium (PHP)
- Goutte (Symfony)
- Scrapy (Python)

Or API : 
- ScrapFly
- ScraperAPI

## How 
- With PHP and the docXpath
- With [php-webdriver](https://github.com/php-webdriver/php-webdriver)
- With [puppeteer](https://github.com/puppeteer/puppeteer)
- With [puppeteer-extra](https://github.com/berstend/puppeteer-extra)



## Why 
- For my project **PriceComparator**
- Developpement of your own tools is important to understand and learn many things.

## Paths 
<details open>
<summary>Paths</summary>
<pre>
dev
├── copy_all_leclerc.html
└── JSON_updates.php
project
├── infos_programs.php
└── project.php
src
├── libJSON
│   └── leclercs.json
├── scrape.js
├── scrape_su.js
├── scrapper_auchan.php
├── scrapper_carrefour.php
├── scrapper_intermarche.php
├── scrapper_leclerc.php
├── scrapper_monoprix.php
├── scrapper.php
├── scrapper_systemeu.php
└── test_extra_puppeteer.js
your_project
├── example.php
└── README.md
composer.json
package.json
README.md
</pre>
</details>

## Usage 
### LIKE A PACKAGE : 
  - `composer require php-webdriver/php-webdriver`
  - in each exec file in [YOUR_PROJECT] folder `namespace ChriSmile0\Scrapper`
  - `use ChriSmile0\Scrapper\FILE_WO_PHP_EXT`
### LIKE A PROJECT : 
- `composer require php-webdriver/php-webdriver`
- `project.php` for known how the different tools works
- `src/scrapper*.php` the differents files for scraping mission
- `vendor` add lib for php-webdriver 
- `node_modules(hide with .gitignore)` for node.js module 
- `*.json/*.txt` for different test to build program to efficient scraping 

`php project/project.php --info` it's a good start

## Version 

### V1.0 
- Basic version of scrapper : 
  - [x] http, https
  - [x] html content generate by JS -> `puppeteer`  
  - [ ] cloudflare security 
  - [x] text in tag with another tag $\color{green}\textsf{(V2.0\ scrapper.php)}$


- Specific version for specific website : 
  - The french supermarket compagny : 
    - [Leclerc](https://leclerc.fr) : 
      - [x] parse specific JS -> json
      - [x] usage of https of [basic version](scrapper.php) : 
      - [x] NoBot Solutions **DataDome** Solution
      - [x] Bypass NoBot Solutions with knownledge of all stores (`libJSON/leclercs.json`)
    - [Carrefour](https://www.carrefour.fr) : 
      - [x] parse specific JS -> json
      - [x] usage of `php-webdriver` 
      - [x] NoBot Solutions -> **Cloudflare**
    - [Auchan](https://www.auchan.fr) : 
      - [x] parse text in html tag
      - [x] usage of `php-webdriver`
      - [ ] NoBot Solutions 
    - [Monoprix](https://www.monoprix.fr) : 
      - [x] parse specific JS -> json 
      - [x] usage of `puppeteer` or `php-webdriver` is possible
      - [x] products for all stores in the target country
      - [ ] NoBot Solutions
    - [Intermaché](https://www.intermarche.com) :
      - [x] parse specific JS -> json 
      - [x] usage of `php-webdriver`
      - [ ] NoBot Solutions 
    - [Systeme_U](https://www.magasins-u.com) : 
      - [x] parse specific JS -> json (products only on the display page)
      - [ ] usage of `puppeteer` or `php-webdriver` **IMPOSSIBLE**
      - [x] NoBot Solutions -> **DataDome** Solution
      - [x] Necessary to use `puppeteer-extra-plugin-stealth`   


## Features 
