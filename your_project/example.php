<?php 

namespace ChriSmile0\Scrapper;

use function ChriSmile0\Scrapper\scrap_https;
use function ChriSmile0\Scrapper\content_scrap_auchan;
use function ChriSmile0\Scrapper\content_scrap_carrefour;
use function ChriSmile0\Scrapper\content_scrap_leclerc;
use function ChriSmile0\Scrapper\content_scrap_intermarche;
use function ChriSmile0\Scrapper\content_scrap_monoprix;
use function ChriSmile0\Scrapper\content_scrap_systemeu;
require_once('../vendor/autoload.php');

function use_scrapper(string $url, bool $with_js) { // OK 
	return scrap_https($url,$with_js);
}

function use_content_scrapper_auchan(string $product, string $city) { // OK 
	return content_scrap_auchan($product,$city);
}

function use_content_scrapper_carrefour(string $product, string $city) { // OK 
	return content_scrap_carrefour($product,$city);
}

function use_content_scrapper_leclerc(string $product, string $city) { // OK 
	return content_scrap_leclerc($product,$city);
}

function use_content_scrapper_intermarche(string $product, string $city) { // OK 
	return content_scrap_intermarche($product,$city);
}

function use_content_scrapper_monoprix(string $product) { // OK 
	return content_scrap_monoprix($product);
}

function use_content_scrapper_systemeu(string $product, string $city) { // OK
	return content_scrap_systemeu($product,$city);
}


//var_dump(use_scrapper("https://Wikipedia.com",true)); 		// OK 
//var_dump(use_content_scrapper_leclerc("Lardons","Voglans")); 	// OK
//var_dump(use_content_scrapper_carrefour("lardons","Paris")); 	// OK 
//var_dump(use_content_scrapper_monoprix("Lardons")); 			// OK 
//var_dump(use_content_scrapper_auchan("Lardons","Paris")); 	// OK
//var_dump(use_content_scrapper_intermarche("lardons","Paris"));// OK
//var_dump(use_content_scrapper_systemeu("Lardons","Voglans")); // OK
?>