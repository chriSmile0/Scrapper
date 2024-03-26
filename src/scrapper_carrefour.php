<?php 
## USAGE -> launch geckodriver (sudo snap install firefox -> >$geckodriver)

// [DEBUT->]https://github.com/adonistividad/web-scraping/blob/master/scrape_carrefour.py
// UPDATE OF 13/02 
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
 * @package    scrapper_carrefour.php
 * @author     chrisSmile0
 * @copyright  2024 -> @author
 * @license    [NO_LICENSE]
 * @version    1.0
 * @link       https://github.com/chriSmile0/Scrapper/scrapper_carrefour.php
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
// URL1 = https://www.carrefour.fr/s?q=lardons
// URL2 = https://www.carrefour.fr/s?q=lardons&filters%5Bproduct.categories.name%5D=Charcuterie%20et%20Traiteur

namespace Facebook\WebDriver;
namespace ChriSmile0\Scrapper;

use Exception;
use Facebook\WebDriver\Firefox\FirefoxOptions as FirefoxOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities as DesiredCapabilities;
use Facebook\WebDriver\Firefox\FirefoxDriver as FirefoxDriver;
use Facebook\WebDriver\Firefox\FirefoxProfile as FirefoxProfile;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy as WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition as WebDriverExpectedCondition;
//require __DIR__ . '/../../../autoload.php'; // EXPORT 
require __DIR__ . '/../vendor/autoload.php'; // DEV


/**
 * [BRIEF]	generate an instance of a firefox driver with 'geckodriver' server
 * 				(localhost:4444)
 * @param 	void 
 * @example	generate_driver_c()
 * @author	chriSmile0
 * @return	/
*/
function generate_driver_c() {
	//-----------------Remote with geckodriver in terminal--------------------// 
	/*$host = 'http://localhost:4444/';

	$capabilities = DesiredCapabilities::firefox();
	$firefoxOptions = new FirefoxOptions;
	$firefoxOptions->addArguments(['-headless']);
	$capabilities->setCapability(FirefoxOptions::CAPABILITY, $firefoxOptions);
	try {
		return RemoteWebDriver::create($host,$capabilities);
	}
	catch (Exception $e) {
		echo "ERRRRRR_REMOTE : ".$e->getMessage()."\n";
		return NULL;
	}*/

	//------------FirefoxDriver, geckodriver directly on this process--------//
	$firefoxOptions = new FirefoxOptions();
	$firefoxOptions->setProfile(new FirefoxProfile());
	$capabilities = DesiredCapabilities::firefox();
	$firefoxOptions->addArguments(['-headless']);
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
 * @example	findElement($driver,"id","my_id","");
 * @author	chriSmile0
 * @return 	array 	[$elem(the element found),$error(the generate error)]
*/
function findElement_c($driver, string $type, string $path, string $error) : array {
	$elem = "";
	sleep(1);
	if($error === "") {
		switch($type) {
			case 'id':
				try {
					$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated((WebDriverBy::id($path)))); // this or sleep 
					$elem = $driver->findElement(WebDriverBy::id($path));
				}
				catch (Exception $e) {
					$error = $e->getMessage();
				}
				break;
			case 'tag':
				try {
					$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated((WebDriverBy::tagName($path))));
					$elem = $driver->findElement(WebDriverBy::tagName($path));
				}
				catch (Exception $e) {
					$error = $e->getMessage();
				}
				break;
			case 'class':
				try {
					$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated((WebDriverBy::className($path))));
					$elem = $driver->findElement(WebDriverBy::className($path));
				}
				catch (Exception $e) {
					$error = $e->getMessage();
				}
				break;
			case 'xpath';
				try { 
					$driver->wait()->until(WebDriverExpectedCondition::presenceOfElementLocated((WebDriverBy::xpath($path))));
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
	var_dump($error);
	return [$elem,$error];
}

/**
 * [BRIEF]	simulate the url get in the browser and return the display content
 * 			[THIS TECHNIC IS USE FOR BYPASS CLOUDFLARE]
 * @param	string	$url	the url to get in the browser
 * @param 	/		$driver	the driver instance
 * @param	string	$city	the city of the research store
 * @param 	string 	$target	product target
 * @example	extract_source_carrefour((@see URL1),$driver)
 * @author	chriSmile0
 * @return	string	the display content of the url renderer
*/
function extract_source_carrefour(string $url,$driver,string $city, string $target) : string {
	$src = "";
	$error = "";
	if($driver !== NULL) {
		try {
			$driver->get($url);
			$res_find = array("","");
			$res_find = findElement_c($driver,"xpath","//*[@id=\"onetrust-reject-all-handler\"]",$res_find[1]); // click option
			if($res_find[0]!=="") $res_find[0]->click();
			$res_find = findElement_c($driver,"xpath","/html/body/div[2]/main/section/div/div[1]/div[2]/div/div/ul/li[1]/button",$res_find[1]); // click
			if($res_find[0]!=="") $res_find[0]->click();
			$res_find = findElement_c($driver,"xpath","/html/body/div[2]/header/div/div[2]/div[2]/div/div/div/div[2]/div/div/span/div/section/div[1]/div[1]/div/section/div/div/div[1]/div[2]/input",$res_find[1]); // sendKeys
			if($res_find[0]!=="") $res_find[0]->sendKeys($city);
			$res_find = findElement_c($driver,"xpath","/html/body/div[2]/header/div/div[2]/div[2]/div/div/div/div[2]/div/div/span/div/section/div[1]/div[1]/div/section/div/ul/li[2]/button",$res_find[1]); // click 
			if($res_find[0]!=="") $res_find[0]->click();
			$res_find = findElement_c($driver,"xpath","/html/body/div[2]/header/div/div[2]/div[2]/div/div/div/div[2]/div/div/span/div/section/div[1]/div[3]/div/ul/li[1]/div/div[2]/ul/li/div/button",$res_find[1]); // click 
			if($res_find[0]!=="") $res_find[0]->click();
			$res_find = findElement_c($driver,"xpath","/html/body/div[2]/header/div/div[2]/div[1]/div[3]/div/form/div/div[1]/div/input",$res_find[1]); // sendKeys + Submit
			if($res_find[0]!=="") $res_find[0]->sendKeys($target)->submit();
			sleep(1);
			if($res_find[1] === "") {
				$src = $driver->getPageSource();
				//$driver->manage()->deleteAllCookies();
				$error = "NO";
				return $src;
			}
			else {
				$error = $res_find[1];
				$driver->quit();
			}
			//var_dump($res_find[1]);
		}
		catch (Exception $e) {
			echo $e->getMessage();
		}
	}
	var_dump($error);
	return $src;
}

/**
 * [BRIEF]	That's a new version of the same name function in 'scrapper_leclerc.php'
 * 			If the trunk is empty and end_content not empty then 
 * 				the substr begin in the offset '0' of the str and the end is in the of 
 * 				the end_content trunk
 *			Else 
 * 				we search in the the str the trunk and the end_content and we 
 * 				create the substr between these trunks
 * 
 * @param	string	$str			the str to search trunk
 * @param	string	$trunk			the trunk to search
 * @param	string	$end_content	the end delimiter
 * @example	all_subcontent_with_trunk("Hello world it's me","world","me")
 * @author	chriSmile0
 * @return	array	array with the trunk without the end content in 
 * 					in tabs for each instance of trunk in str
 * @version	1.5		
 * @deprecate?	soon
*/
function all_subcontent_with_trunk_c(string $str, string $trunk = "", string $end_content = "") : array {
	$res = array();
	$offset = 0;
	$copy_str = $str;
	$end_offset = 1;
	$original_trunk = $trunk;
	if(($trunk === "") && ($end_content !== "")) {
		$trunk = $end_content;
		$end_offset += strlen($trunk);
		$res[] = substr($str,0,($offset=strpos($str,$end_content))+$end_offset-1);
		$offset += $end_offset;
		$copy_str = substr($str,$offset);
	}
    while(($pos = strpos($copy_str, $trunk, $offset)) !== FALSE) {
		if($original_trunk !== "") {
			$tmp = substr($copy_str,$pos);
			$pos_end = strpos($tmp,$end_content);
			$res[] = substr($tmp,$offset,$pos_end);
			$offset = $pos_end + $end_offset;
			$copy_str = $tmp;
		}
		else {
			$tmp = $copy_str;
			$pos_end = strpos($tmp,$end_content);
			$res[] = substr($tmp,0,($offset=$pos_end)+$end_offset-1);
			$offset += $end_offset;
			$next = substr($copy_str,$offset);
			$copy_str = $next;
			$offset = 0;
		}
    }
	return $res;
}

/**
 * [BRIEF]	Split the data by product if the target product is in a predefined 
 * 			list 
 * 									
 * @param	string	$output				datas
 * @param	string	$product			product to research in data
 * @example	search_product_in_script_json("search:{"data:[....]","lardons",["lardons"])
 * @author	chriSmile0
 * @return	array	split the data by product or empty array if product is not
 * 						in the list
*/
function search_product_in_script_json_c(string $output, string $product) : array  {
	$first = "\"search\":{\"data\":[";
	$end = "\"keyword\":\"".$product."\"";
	$subcontent = all_subcontent_with_trunk_c($output,$first,$end);
	$subcontent[0] .= $end; // "close meta" -> the end of meta is not interesting
	$subcontent[0] = substr($subcontent[0],strlen($first));
	$all_products = all_subcontent_with_trunk_c($subcontent[0],"","reviews\"}}");
	$all_informations = all_subcontent_with_trunk_c($subcontent[0],"\"links\":{\"default\":",$end);
	$subcontent = array("products"=>$all_products,"informations"=>"{".$all_informations[0].$end."}}");
	return $subcontent;
}

/**
 * [BRIEF]	json_decode in array -> (associative -> true)
 * @param	$output_json	output_json string to transform in array 
 * @example	parse_json_product(...)
 * @author	chriSmile0
 * @return	array	json -> array 
*/
function parse_json_product(string $output_json) : array {
	return json_decode($output_json,true);
}

$list_of_product = [
	"lardons"
];

$product_needed_key = [ // On ATTRIBUTES
	"ean" => [],
	"title" => [],
	"brand" => [],
	"slug" => [],
	"offerServiceId" => [],
	"offers" => [
		"ean" => [
			"offerServiceId" => [
				"attributes" => [
					"price",
					"promotion",
					"promotions"
				],
			],
		],
	],
	"packaging" => [],
	"nutriscore" => [],
];

$page_needed_key = [ // On META 
	"total",
	"itemsPerPage",
	"totalPage",
	"currentPage",
	"keyword",
];

/**
 * [BRIEF]	It's possible to store all data but not for you, it's important
 * 			to store the display the most useful data 
 * @param	array	$json			
 * @param	array	$needed_key			
 * @example extract_needed_information_pro($json, [title,price])
 * @author	chriSmile0
 * @return	array	array with the data with want to store/share/print
*/
function extract_needed_information_pro_c(array $json, array $needed_key) : array {
	$rtn = array();
	$sub_json_needed = $json["attributes"];
	foreach($needed_key as $k=>$value) {
		if(strcmp($k,"offers")==0)
			continue;
		$rtn = array_merge($rtn,[$k=>$sub_json_needed[$k]]);
	}
	$ean = $rtn["ean"];
	$offerServiceId = $rtn["offerServiceId"];
	$sub_attributes = $sub_json_needed["offers"][$ean][$offerServiceId]["attributes"];
	$sub_needed = $needed_key["offers"]["ean"]["offerServiceId"]["attributes"];
	foreach($sub_needed as $k=>$v)
		$rtn = array_merge($rtn, [$v=>$sub_attributes[$v]]);
	return $rtn;
}

/**
 * [BRIEF]	Information on the research and the pages information
 * @param	array	$json			
 * @param	array	$needed_key			
 * @example extract_needed_information($json, [title,price])
 * @author	chriSmile0
 * @return	array	array with the data with want to store/share/print
*/
function extract_needed_information_c(array $json, array $needed_key) : array {
	$rtn = array();
	$sub_json_needed = $json["meta"];
	foreach($needed_key as $val) 
		$rtn = array_merge($rtn,[$val=>$sub_json_needed[$val]]);
	
	return $rtn;
}

/**
 * [BRIEF]	(@see extract_needed_information_pro) but for all products
 * @param	array	$tab_json	all products we have store		
 * @param	array	$needed_key	list of information we need			
 * @example extract_info_for_all_products($tab_json, [totalPage,currentPage])
 * @author	chriSmile0
 * @return	array	array with the data with want to store/share/print for all 
 * 					products
*/
function extract_info_for_all_products_c(array $tab_json, array $needed_key) : array {
	$rtn = array();
	foreach($tab_json as $json) {
		array_push($rtn,extract_needed_information_pro_c(
									parse_json_product($json),$needed_key));
	}
	
	return $rtn;
}

/**
 * [BRIEF]	The main procedure -> for include in other path 
 * @param	string	$url			the url to scrap
 * @param	string 	$target_product	the target product
 * @param 	string 	$city 			the city to target
 * @example content_scrap_carrefour((@see URL1),"lardons")
 * @author	chriSmile0
 * @return	array 	array of all product with specific information that we needed
*/
function content_scrap_carrefour(string $url, string $target_product, string $city) : array {
	$rtn = array();
	$driver = generate_driver_c();
	if($driver !== NULL) {
		$list_of_product = [
			"lardons"
		];
		
		$product_needed_key = [ // On ATTRIBUTES
			"ean" => [],
			"title" => [],
			"brand" => [],
			"slug" => [],
			"offerServiceId" => [],
			"offers" => [
				"ean" => [
					"offerServiceId" => [
						"attributes" => [
							"price",
							"promotion",
							"promotions"
						],
					],
				],
			],
			"packaging" => [],
			"nutriscore" => [],
		];
		
		$page_needed_key = [ // On META 
			"total",
			"itemsPerPage",
			"totalPage",
			"currentPage",
			"keyword",
		];
		
		$file_content = extract_source_carrefour($url,$driver,$city,$target_product);
		if($file_content !== "") {
			$sp_res = search_product_in_script_json_c($file_content,$target_product,$list_of_product);
			if(empty($sp_res)) {
				$driver->quit();
				return array();
			}
			$rtn = array_merge($rtn,extract_info_for_all_products_c($sp_res["products"],$product_needed_key));
			$infos = extract_needed_information_c(parse_json_product($sp_res["informations"]),$page_needed_key);
			$nb_page = $infos['totalPage'];
			$current_page = $infos['currentPage'];
			$next_page = $current_page+1;
			for($i = $next_page ; $i < $nb_page+1 ; $i++) {
				$url_ = $url."&noRedirect=1&page=".$i;
				$file_content = extract_source_carrefour($url_,$driver,$city,$target_product);
				$sp_res = search_product_in_script_json_c($file_content,$target_product,$list_of_product);
				if(empty($sp_res)) {
					$driver->quit();
					return $rtn;
				}
				$rtn = array_merge($rtn,extract_info_for_all_products_c($sp_res["products"],$product_needed_key));
			}
			$driver->quit();
		}
	}
	return $rtn;
}

/**
 * [BRIEF]	[MAIN_PROGRAM] -> for manuel execution
 * @param	$argc	The number of parameter in the command line execution
 * @param	$argv	The parameters of the command line execution
 * @example	main($argc,"php7.2 scrapper_carrefour.php (@see URL1) lardons")
 * @author	chriSmile0
 * @return	bool 	1 if all is good, 0 if error in the command line or in the phase
 * 					test or if the scrapping failed 
*/
function main_c($argc, $argv) : bool {
	if($argc == 5) {
		if(empty(content_scrap_carrefour($argv[1],$argv[2],$argv[3]))) {
			echo "NO CORRESPONDENCE FOUND \n";
			return 0;
		}
	}
	else {
		echo "ERROR : format : ". $argv[0] . " [url] [research_product_type] [city] --with-openssl\n";
		return 0;
	}
	echo "EXECUTION FINISH WITH SUCCESS \n";
	return 1;
}
//main($argc,$argv);
/*$url = "https://www.carrefour.fr/courses";
$search = "lardons";
$city = "Paris";
//var_dump(content_scrap_carrefour($url,$search,$city));
//var_dump(content_scrap_carrefour($url,$search,$city));*/
/**
 * [BRIEF]	
 * @param	
 * @example	
 * @author	chriSmile0
 * @return	
*/
?>