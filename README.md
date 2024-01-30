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

## Why 
- For my project **PriceComparator**
- Developpement of your own tools is important to understand and learn many things.

## Usage 
- `project.php` for known how the different tools works
- `scrapper*.php` the differents files for scraping mission
- `vendor` add lib for php 
- `node_modules(hide with .gitignore)` for node.js module 
- `*.json/*.txt` for different test to build program to efficient scraping 

`php project.php --info` it's a good start

## Version 

### V0.5
- Basic version of scrapper : 
  - [x] http, https
  - [x] html content generate by JS -> `puppeteer`  
  - [ ] cloudflare security 
  - [x] text in tag with another tag $\color{green}\textsf{(V2.0\ scrapper.php)}$


- Specific version for specific website : 
  - The french supermarket compagny : 
    - [Leclerc](https://leclerc.fr) : 
      - [x] usage of https of basic version 
      - [x] parse specific JS -> json
      - [ ] bypass cloudflare -> no cloudflare on this website 
    - [Carrefour](https://www.carrefour.fr) : 
      - [ ] usage of https of basic version 
      - [x] parse specifig JS -> json 
      - [x] bypass cloudflare -> cloudflare security 
  
### V1.0 -> Next release  
- Final first version 
- Others compagny to scrape :
  - [ ] [Auchan](https://www.auchan.fr)
  - [ ] [Lidl](https://www.lidl.fr)
  - [ ] [Systeme_U](https://www.magasins-u.com)
  - [ ] [Monoprix](https://www.monoprix.fr)
  - [ ] [Intermaché](https://www.intermarche.com)


## Features 
