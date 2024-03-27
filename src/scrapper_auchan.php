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
use Exception;
use Facebook\WebDriver\Firefox\FirefoxOptions as FirefoxOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities as DesiredCapabilities;
use Facebook\WebDriver\Firefox\FirefoxDriver as FirefoxDriver;
use Facebook\WebDriver\Firefox\FirefoxProfile as FirefoxProfile;
use Facebook\WebDriver\Remote\RemoteWebDriver as RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy as WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition as WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverKeys as WebDriverKeys;
require __DIR__ . '/../../../autoload.php'; // EXPORT 
//require __DIR__ . '/../vendor/autoload.php'; // DEV



/**
 * [BRIEF]	generate an instance of a firefox driver with 'geckodriver' server
 * 				(localhost:4444)
 * @example	generate_driver_a()
 * @author	chriSmile0
 * @return	/
*/
function generate_driver_a() {
	//-----------------Remote with geckodriver in terminal--------------------// 
	/*$host = 'http://localhost:4444/';

	$capabilities = DesiredCapabilities::firefox();
	$firefoxOptions = new FirefoxOptions;
	$firefoxOptions->addArguments(['--headless']);
	$capabilities->setCapability(FirefoxOptions::CAPABILITY, $firefoxOptions);
	try {
		return RemoteWebDriver::create($host,$capabilities);
	}
	catch (Exception $e) {
		echo "ERRRRRR_REMOTE : ".$e->getMessage()."\n";
		return NULL;
	}*/

	//------------FirefoxDriver, geckodriver directly on this process--------//
	shell_exec("kill -s kill `ps -e | grep -e geckodriver | grep -Eo '[0-9]{1,10}' | head -n 1`");
	sleep(1);
	$firefoxOptions = new FirefoxOptions();
	$firefoxOptions->setProfile(new FirefoxProfile());
	$capabilities = DesiredCapabilities::firefox();
	$firefoxOptions->addArguments(['--headless']);
	$capabilities->setCapability(FirefoxOptions::CAPABILITY, $firefoxOptions);
	try {
		return FirefoxDriver::start($capabilities);
	}
	catch (Exception $e) {
		echo "ERRRRRR : ".$e->getMessage()."\n";
		return NULL;
	}
}

/**
 * [BRIEF]	Find element function simplification
 * 
 * @param 			$driver	the driver create by WebDriver()
 * @param	string	$type	id/tagname/classname/xpath
 * @param	string	$path	the path or the id or the classname of the tagname
 * @param	string	$error	the recently error in the call stack
 * @param 	string 	$type_2	optional	presence/visibility
 * @example	findElement($driver,"id","my_id","");
 * @author	chriSmile0
 * @return 	array 	[$elem(the element found),$error(the generate error)]
*/
function findElement_a($driver, string $type, string $path, string $error, string $type_2 = "presence") : array {
	$elem = "";
	sleep(1);
	if($type_2 !== "presence" && $type_2 !== "visibility")
		return [$elem,"'presence' or 'visibility' for type_2 argument"];
	if($error === "") {
		switch($type) {
			case 'id':
				try {
					if($type_2 === "presence")
						$driver->wait(1)->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id($path))); // this or sleep 
					else 
						$driver->wait(1)->until(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id($path)));
					$elem = $driver->findElement(WebDriverBy::id($path));
				}
				catch (Exception $e) {
					$error = $e->getMessage();
				}
				break;
			case 'tag':
				try {
					if($type_2 === "presence")
						$driver->wait(1)->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::tagName($path))); // this or sleep 
					else 
						$driver->wait(1)->until(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::tagName($path)));
					$elem = $driver->findElement(WebDriverBy::tagName($path));
				}
				catch (Exception $e) {
					$error = $e->getMessage();
				}
				break;
			case 'class':
				try {
					if($type_2 === "presence")
						$driver->wait(1)->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::className($path))); // this or sleep 
					else 
						$driver->wait(1)->until(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::className($path)));
					$elem = $driver->findElement(WebDriverBy::className($path));
				}
				catch (Exception $e) {
					$error = $e->getMessage();
				}
				break;
			case 'xpath';
				try { 
					if($type_2 === "presence")
						$driver->wait(1)->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::xpath($path))); // this or sleep 
					else 
						$driver->wait(1)->until(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::xpath($path)));
					$elem = $driver->findElement(WebDriverBy::xpath($path));
				}
				catch (Exception $e) {
					$error = $e->getMessage();
				}
				break;
			default:
				$elem = NULL;
				$error = "id/tag/class/xpath";
				break;
		}
	}
	//var_dump($error);
	return [$elem,$error];
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
	$src = "";
	$error = "";
	$prods = "";
	if($driver !== NULL) {
		try {
			$driver->get($url);
			$res_find = array("","");
			$res_find = findElement_a($driver,"id","onetrust-reject-all-handler",$res_find[1]); // click option
			if($res_find[0]!=="") $res_find[0]->click();
			$res_find = findElement_a($driver,"class","header-search__input",$res_find[1]);
			if($res_find[0]!=="") {
				$res_find[0]->sendKeys($target);
				$res_find[0]->sendKeys(WebDriverKeys::ENTER);
			}
			

			$res_find =  findElement_a($driver,"xpath","/html/body/div[3]/div[2]/div[2]/div[4]/article[1]/div[2]/footer/button",$res_find[1]); // click option
			if($res_find[0]!=="") {
				try {
					sleep(1); // for the moment 
					$driver->executeScript("document.getElementsByClassName('product-thumbnail__see-prices-button')[0].click();");
				}
				catch(Exception $e) {
					$res_find[1] = $e->getMessage();
				}
			}
			
			$res_find = findElement_a($driver,"xpath","/html/body/div[13]/div[1]/main/div[1]/div[1]/div/div[1]/input",$res_find[1]);
			if($res_find[0]!=="") $res_find[0]->sendKeys($town);


			///suggests 
			// /html/body/div[13]/div[1]/main/div[1]/div[1]/div/div[1]/input = path
			$res_find =  findElement_a($driver,"class","journey__search-suggests-list",$res_find[1]); // click option
			if($res_find[0]!=="") {
				try {
					sleep(1); // for the moment 
					$driver->executeScript("(document.getElementsByClassName('journey__search-suggests-list')[0]).childNodes[0].click()");
				}
				catch(Exception $e) {
					$res_find[1] = $e->getMessage();
				}
			}
			$res_find = findElement_a($driver,"xpath","/html/body/div[13]/div[1]/main/div[1]/div[2]/div[2]/section/div[1]/div/div/div[2]/form/button",$res_find[1]);
			if($res_find[0]!=="") $res_find[0]->submit();
			
			echo "submit form\n";

			try {
				sleep(4); // necessary for load all products
				$prods = $driver->findElements(WebDriverBy::xpath('/html/body/div[3]/div[2]/div[2]/div[4]/article'));
				sleep(1);
			}
			catch(Exception $e) {
				$res_find[1] = $e->getMessage();
			}
			$error = $res_find[1];
			$src = $driver->getPageSource();
		}
		catch (Exception $e) {
			$error = $e->getMessage();
		}
	}
	var_dump($error);
	if($error !== "") {
		$driver->quit();
		return array();
	}
	//var_dump($prods);
	return [$driver,$src,extract_info_for_all_products_a($prods)];
}

/**
 * [BRIEF]	The main procedure -> for include in other path 
 * 
 * @param	string 	$target_product	the target product
 * @param	string 	$town 			the research area
 * @example content_scrap_auchan((@see URL1),"lardons","Paris")
 * @author	chriSmile0
 * @return	array 	array of all product with specific information that we needed
*/
function content_scrap_auchan(string $target_product, string $town) : array {
	$url = "https://www.auchan.fr/";
	$driver = generate_driver_a();
	if($driver === NULL) 
		return array();
	
	$file_content_and_prods = extract_source_auchan($url,$driver,$town,$target_product);
	if(!empty($file_content_and_prods)) {
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
	return array();
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
	if($argc == 4) {
		if(empty(content_scrap_auchan($argv[1],$argv[2]))) {
			echo "NO CORRESPONDENCE FOUND \n";
			return 0;
		}
		return 1;
	}
	else {
		echo "ERROR : format : ". $argv[0] . "[research_product_type] [town] --with-openssl\n";
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