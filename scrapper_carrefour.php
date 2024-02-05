<?php 
## USAGE -> launch geckodriver (sudo snap install firefox -> >$geckodriver)

// [DEBUT->]https://github.com/adonistividad/web-scraping/blob/master/scrape_carrefour.py


// URL1 = https://www.carrefour.fr/s?q=lardons
// URL2 = https://www.carrefour.fr/s?q=lardons&filters%5Bproduct.categories.name%5D=Charcuterie%20et%20Traiteur

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

namespace Facebook\WebDriver;

use Facebook\WebDriver\Firefox\FirefoxOptions;
use Facebook\WebDriver\Interactions\Internal\WebDriverCoordinates;
use Facebook\WebDriver\Interactions\Internal\WebDriverMouseAction;
use Facebook\WebDriver\Interactions\Touch\WebDriverDownAction;
use Facebook\WebDriver\Interactions\WebDriverActions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverKeyboard;
use Facebook\WebDriver\WebDriverHasInputDevices;
use Facebook\WebDriver\Support\Events\EventFiringWebDriver;
use Facebook\WebDriver\Support\Events\EventHandler;
use Facebook\WebDriver\Support\Events\EventFiringWebElement;

require_once('vendor/autoload.php');

/**
 * [BRIEF]	generate an instance of a firefox driver with 'geckodriver' server
 * 				(localhost:4444)
 * @example	generate_driver()
 * @author	chriSmile0
 * @return	/
*/
function generate_driver() {
	$host = 'http://localhost:4444/';

	$capabilities = DesiredCapabilities::firefox();
	$firefoxOptions = new FirefoxOptions();
	$firefoxOptions->addArguments(['-headless']);
	$capabilities->setCapability(FirefoxOptions::CAPABILITY, $firefoxOptions);

	return RemoteWebDriver::create($host, $capabilities);
}

/**
 * [BRIEF]	simulate the url get in the browser and return the display content
 * 			[THIS TECHNIC IS USE FOR BYPASS CLOUDFLARE]
 * @param	string	$url	the url to get in the browser
 * @param 	/		$driver	the driver instance
 * @example	extract_source_carrefour((@see URL1),$driver)
 * @author	chriSmile0
 * @return	string	the display content of the url renderer
*/
function extract_source_carrefour(string $url,$driver) : string {
	$driver->get($url);
	$src = $driver->getPageSource();
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
*/
function all_subcontent_with_trunk(string $str, string $trunk = "", string $end_content = "") : array {
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
 * @param	string	$product			product to research in datas
 * @example	search_product_in_script_json("search:{"data:[....]","lardons",["lardons"])
 * @author	chriSmile0
 * @return	array	split the data by product or empty array if product is not
 * 						in the list
*/
function search_product_in_script_json(string $output, string $product) : array  {
	$first = "\"search\":{\"data\":[";
	$end = "\"keyword\":\"".$product."\"";
	$subcontent = all_subcontent_with_trunk($output,$first,$end);
	$subcontent[0] .= $end; // "close meta" -> the end of meta is not interesting
	$subcontent[0] = substr($subcontent[0],strlen($first));
	$all_products = all_subcontent_with_trunk($subcontent[0],"","reviews\"}}");
	$all_informations = all_subcontent_with_trunk($subcontent[0],"\"links\":{\"default\":",$end);
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
function extract_needed_information_pro(array $json, array $needed_key) : array {
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
function extract_needed_information(array $json, array $needed_key) : array {
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
function extract_info_for_all_products(array $tab_json, array $needed_key) : array {
	$rtn = array();
	foreach($tab_json as $json) {
		array_push($rtn,extract_needed_information_pro(
									parse_json_product($json),$needed_key));
	}
	
	return $rtn;
}

/**
 * [BRIEF]	The main procedure -> for include in other path 
 * @param	string	$url			the url to scrap
 * @param	string 	$target_product	the target product
 * @example content_scrap_carrefour((@see URL1),"lardons")
 * @author	chriSmile0
 * @return	array 	array of all product with specific information that we needed
*/
function content_scrap_carrefour(string $url, string $target_product) : array {
	$rtn = array();
	$driver = generate_driver();
	$file_content = extract_source_carrefour($url,$driver);
	$sp_res = search_product_in_script_json($file_content,$target_product,$GLOBALS['list_of_product']);
	if(empty($sp_res)) {
		$driver->quit();
		return array();
	}
	$rtn = array_merge($rtn,extract_info_for_all_products($sp_res["products"],$GLOBALS['product_needed_key']));
	$infos = extract_needed_information(parse_json_product($sp_res["informations"]),$GLOBALS['page_needed_key']);
	$nb_page = $infos['totalPage'];
	$current_page = $infos['currentPage'];
	$next_page = $current_page+1;
	for($i = $next_page ; $i < $nb_page+1 ; $i++) {
		$url_ = $url."&noRedirect=1&page=".$i;
		$file_content = extract_source_carrefour($url_,$driver);
		$sp_res = search_product_in_script_json($file_content,$target_product,$GLOBALS['list_of_product']);
		if(empty($sp_res)) {
			$driver->quit();
			return $rtn;
		}
		$rtn = array_merge($rtn,extract_info_for_all_products($sp_res["products"],$GLOBALS['product_needed_key']));
	}
	$driver->quit();
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
function main($argc, $argv) : bool {
	if($argc == 4) {
		if(empty(content_scrap_carrefour($argv[1],$argv[2]))) {
			echo "NO CORRESPONDENCE FOUND \n";
			return 0;
		}
	}
	else {
		echo "ERROR : format : ". $argv[0] . " [url] [research_product_type] --with-openssl\n";
		return 0;
	}
	echo "EXECUTION FINISH WITH SUCCESS \n";
	return 1;
}
main($argc,$argv);

/**
 * [BRIEF]	
 * @param	
 * @example	
 * @author	chriSmile0
 * @return	
*/
?>