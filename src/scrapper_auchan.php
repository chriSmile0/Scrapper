<?php 
## USAGE -> launch geckodriver (sudo snap install firefox -> >$geckodriver)
// For document file 
/**
 * Short description for file
 *
 * Long description for file
 *
 * PHP version 7.2
 *
 * LICENSE: --
 *
 * @package    scrapper_auchan.php
 * @author     chrisSmile0
 * @copyright  2024 -> @author
 * @license    [NO_LICENSE]
 * @version    1.0
 * @link       https://github.com/chriSmile0/Scrapper/scrapper_auchan.php
 * @since      File available since Release 0.0
 * @deprecated NO_DECPRECATED
*/

// For document classe 
/**
 * [BRIEF]-> class description
 * @param		-> class constructor params	
*/

// For document function 
/**
 * [BRIEF]->  
 * @param  
 * @example 
 * @author 	-> chriSmile0
 * @return
 */

// URL1 = https://www.auchan.fr/
namespace Facebook\WebDriver;
namespace ChriSmile0\Scrapper;
use Facebook\WebDriver\Firefox\FirefoxOptions as FirefoxOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities as DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver as RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy as WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition as WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverKeys as WebDriverKeys;
require __DIR__ . '/../../../autoload.php';




/**
 * [BRIEF]	generate an instance of a firefox driver with 'geckodriver' server
 * 				(localhost:4444)
 * @example	generate_driver_a()
 * @author	chriSmile0
 * @return	/
*/
function generate_driver_a() {
	$host = 'http://localhost:4444/';

	$capabilities = DesiredCapabilities::firefox();
	$firefoxOptions = new FirefoxOptions;
	$firefoxOptions->addArguments(['-headless']);
	$capabilities->setCapability(FirefoxOptions::CAPABILITY, $firefoxOptions);

	return RemoteWebDriver::create($host, $capabilities);
}

/**
 * [BRIEF]	Transform the getText return of element in an array with
 * 			associative element
 * @param	string 	The element text
 * @param	array	the associative_list ["price","label",etc]
 * @param	string	the separator element ("\n"," ")
 * @example	getTextToAssociativeArra
 * @author	chriSmile0
 * @return	array	the associative array
*/
function text_to_associative_array(string $get_text, array $associative_list, 
									string $separator) : array {
	$rtn = array();
	$text_to_explode = explode($separator,$get_text);
	if(($size = sizeof($text_to_explode)) > 1) {
		if(($size_list = sizeof($associative_list)) > $size) 
			for($j=0, $i = 0; $i < $size_list ;$i++, $j++) {
				if($i == 4)
					$i += 2;
				$rtn = array_merge($rtn,[$associative_list[$i]=>$text_to_explode[$j]]);
			}
		
		else if($size_list == $size) 
			for($i = 0; $i < $size_list ;$i++) 
				$rtn = array_merge($rtn,[$associative_list[$i]=>$text_to_explode[$i]]);

		
		
		else 
			for($i = 1; $i < $size ;$i++) 
				$rtn = array_merge($rtn,[$associative_list[$i-1]=>$text_to_explode[$i]]);

		
	}	
	return $rtn;
}


/**
 * [BRIEF]	(@see extract_needed_information_pro) but for all products	
 * @param	/	$prods	The html element which contain all products	
 * @example extract_info_for_all_products($tab_json, [totalPage,currentPage])
 * @author	chriSmile0
 * @return	array	array with the data with want to store/share/print for all 
 * 					products
*/
function extract_info_for_all_products_a($prods) : array {
	$rtn = array();

	$associative_list = [
		"reviewCount",
		"label",
		"quantity",
		"pricePerKg",
		"promoOffer",
		"seeOffer",
		"dealMode",
		"price"
	];
	foreach($prods as $prod) 
		if(!empty($res = text_to_associative_array($prod->getText(),$associative_list,"\n")))
			$rtn[] = $res;
	

	return $rtn;
}

/**
 * [BRIEF]	simulate the url get in the browser and return the display content
 * 			with the first products and the $driver with the change state
 * @param	string	$url	the url to get in the browser
 * @param 	/		$driver	the driver instance
 * @param 	string	$town 	the city in the research area
 * @param	string	$target	the product we want to research
 * @example	extract_source_auchan((@see URL1),$driver,"Paris","lardons")
 * @author	chriSmile0
 * @return	array	[$driver(the driver),sourceCode(the source code in string),
 * 						$firstProducts->array of the first product]
*/
function extract_source_auchan(string $url,$driver, string $town, string $target) : array {
	$driver->get($url);

	$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated((WebDriverBy::id('onetrust-reject-all-handler'))));
	$driver->findElement(WebDriverBy::id('onetrust-reject-all-handler'))->click();

	$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::className('header-search__input')));
	$research_box = $driver->findElement(WebDriverBy::className('header-search__input'));
	$research_box->sendKeys($target);
	$research_box->sendKeys(WebDriverKeys::ENTER);

	$driver->wait()->until(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::className('product-thumbnail__see-prices-button')));
	//click on the first 
	sleep(1); // wait until ?
	$driver->executeScript("document.getElementsByClassName('product-thumbnail__see-prices-button')[0].click();");
	
	$driver->wait()->until(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::xpath('/html/body/div[13]/div[1]/main/div[1]/div[1]/div/div[1]/input')));
	$select_town = $driver->findElement(WebDriverBy::xpath('/html/body/div[13]/div[1]/main/div[1]/div[1]/div/div[1]/input'));
	$select_town->sendKeys($town);

	//suggests 
	$driver->wait()->until(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::className('journey__search-suggests-list')));
	// $ul = $driver->findElement(WebDriverBy::className('journey__search-suggests-list')); 
	// $lis = $ul->findElements(WebDriverBy::tagName('li')); direct child of $ul
	$driver->executeScript("(document.getElementsByClassName('journey__search-suggests-list')[0]).childNodes[0].click()"); // OK 

	$driver->wait()->until(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::xpath('/html/body/div[13]/div[1]/main/div[1]/div[2]/div[2]/section/div[1]/div/div/div[2]/form/button')));
	$driver->findElement(WebDriverBy::xpath('/html/body/div[13]/div[1]/main/div[1]/div[2]/div[2]/section/div[1]/div/div/div[2]/form/button'))->submit();
	sleep(1); // wait-until() better 


	$prods = $driver->findElements(WebDriverBy::xpath('/html/body/div[3]/div[2]/div[2]/div[4]/article'));
	$src = $driver->getPageSource();
	return [$driver,$src,extract_info_for_all_products_a($prods)];
}

/**
 * [BRIEF]	The main procedure -> for include in other path 
 * @param	string	$url			the url to scrap
 * @param	string 	$target_product	the target product
 * @param	string 	$town 			the research area
 * @example content_scrap_auchan((@see URL1),"lardons","Paris")
 * @author	chriSmile0
 * @return	array 	array of all product with specific information that we needed
*/
function content_scrap_auchan(string $url, string $target_product, string $town) : array {
	$driver = generate_driver_a();
	$file_content_and_prods = extract_source_auchan($url,$driver,$town,$target_product);
	$driver = $file_content_and_prods[0];
	$file_content = $file_content_and_prods[1];
	$prods = $file_content_and_prods[2];
	$config_mark = "G.configuration.searchPages.trackingObject = ";
	
	$sub = substr($file_content,strpos($file_content,$config_mark)+strlen($config_mark));
	$end = strpos($sub,"};");
	$infos = substr($sub,0,$end);
	$infos_page = substr($infos,0,strpos($infos,"},")) . "}}";
	$pages = json_decode($infos_page,true)["page"];
	$nb_page = $pages["numberOfPages"];
	$cur_page = $pages["currentPage"];
	$new_url = $driver->getCurrentURL()."?redirect_keywords=$target_product&page=";

	for($i = $cur_page+1 ; $i < $nb_page+1; $i++) {
		$driver->get($new_url.$i);
		$produits = $driver->findElements(WebDriverBy::xpath('/html/body/div[3]/div[2]/div[2]/div[4]/article'));
		$prods = array_merge($prods,extract_info_for_all_products_a($produits));
	}
	$driver->manage()->deleteAllCookies();
	$driver->quit();
	return $prods;
}

/**
 * [BRIEF]	[MAIN_PROGRAM] -> for manuel execution
 * @param	$argc	The number of parameter in the command line execution
 * @param	$argv	The parameters of the command line execution
 * @example	main($argc,"php7.2 scrapper_auchan.php (@see URL1) lardons Paris")
 * @author	chriSmile0
 * @return	bool 	1 if all is good, 0 if error in the command line or in the phase
 * 					test or if the scrapping failed 
*/
function main_a($argc, $argv) : bool {
	if($argc == 5) {
		if(empty(content_scrap_auchan($argv[1],$argv[2],$argv[3]))) {
			echo "NO CORRESPONDENCE FOUND \n";
			return 0;
		}
		return 1;
	}
	else {
		echo "ERROR : format : ". $argv[0] . " [url] [research_product_type] [town] --with-openssl\n";
		return 0;
	}
	echo "EXECUTION FINISH WITH SUCCESS \n";
	return 1;
}

//main_a($argc,$argv);
//var_dump(content_scrap_auchan("https://www.auchan.fr/","Lardons","Paris"));
/**
 * [BRIEF]	
 * @param	
 * @example
 * @author chriSmile0
 * @return 
*/
?>