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

// URL1 = "https://www.intermarche.com/" -> choice magasin  
// URL2 = "https://www.intermarche.com/acceuil" -> after choice 

/* PATHS
	$url_entrance = "https://www.intermarche.com/";
	$path_to_choice = "/html/body/div[2]/div[1]/main/div[1]/div[2]/div[2]/div[2]/div[1]/div/input"; //-> xpath for location
	$path_to_proposal = "/html/body/div[2]/div[1]/main/div[1]/div[2]/div[2]/div[2]/div[2]"; // -> selection proposition 
	$path_to_first_choice =  "/html/body/div[2]/div[1]/main/div[1]/div[2]/div[2]/div[2]/div[2]/div[2]"; // -> for first choice 
*/

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
//require __DIR__ . '/../../../autoload.php'; // EXPORT 
require __DIR__ . '/../vendor/autoload.php'; // DEV



/**
 * [BRIEF]	generate an instance of a firefox driver with 'geckodriver' server
 * 				(localhost:4444)
 * @param 	void 
 * @example	generate_driver_i()
 * @author	chriSmile0
 * @return	/
*/
function generate_driver_i() {
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
 * @param	string	$type	id/tagname/classname/classnames/xpath
 * @param	string	$path	the path or the id or the classname of the tagname
 * @param	string	$error	the recently error in the call stack
 * @example	findElement($driver,"id","my_id","");
 * @author	chriSmile0
 * @return 	array 	[$elem(the element found),$error(the generate error)]
*/
function findElement_i($driver, string $type, string $path, string $error) : array {
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
			case 'classes':
				try {
					$driver->wait()->until(WebDriverExpectedCondition::presenceOfAllElementsLocatedBy((WebDriverBy::className($path))));
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
 * @param	string	$url	the url to get in the browser
 * @param 	/		$driver	the driver instance
 * @example	extract_source((@see URL1),$driver)
 * @author	chriSmile0
 * @return	string	the display content of the url renderer
*/
function extract_source(string $url, $driver) {
	$driver->get($url);
	$src = $driver->getPageSource();
	return $src;
}

/**
 * [BRIEF]	simulate the url get in the browser and return the display content
 * @param	string	$url	the url to get in the browser
 * @param 	/		$driver	the driver instance
 * @param 	string	$town 	the city in the research area
 * @param	string	$target	the product we want to research
 * @example	extract_source_intermarche((@see URL1),$driver,"Paris","lardons")
 * @author	chriSmile0
 * @return	string	the display content of the url renderer
*/
function extract_source_intermarche(string $url,$driver, string $town, string $target) : string {
	$src = "";
	$error = "";
	if($driver !== NULL) {
		try {
			$driver->get($url);
			$res_find = array("","");

			//COOKIE
			$res_find = findElement_i($driver,"xpath","//*[@id=\"didomi-popup\"]/div/div/div/span",$res_find[1]);
			if($res_find[0]!=="") $res_find[0]->click();
			//COOKIE DISABLE -> use js for click on the button 


			$res_find = findElement_i($driver,"class","selectAddressForStore__search",$res_find[1]);
			if($res_find[0]!=="") $res_find[0]->findElement(WebDriverBy::tagName('input'))->sendKeys($town);			
			
			$res_find = findElement_i($driver,"classes","selectAddressForStore__suggestion",$res_find[1]);
			if($res_find[0]!=="") {
				try {
					$driver->executeScript("document.getElementsByClassName('selectAddressForStore__suggestion')[1].click();");
				}
				catch (Exception $e) {
					$res_find[1] = $e->getMessage();
				}
			}
		
			$res_find = findElement_i($driver,"xpath","/html/body/div[2]/div[2]/div/section/div/div[1]/div/div[2]/div[1]/div/div[4]/div/button",$res_find[1]);	
			if($res_find[0]!=="") $res_find[0]->click();


			$res_find = findElement_i($driver,"class","search-input__input",$res_find[1]);
			if($res_find[0]!=="") {
				$res_find[0]->sendKeys($target);
				$res_find[0]->sendKeys(WebDriverKeys::ENTER);
			}
			
			// - /html/body/div[2]/div[1]/main/div/div[2]/div[2]/div[2]/div/div[1]/div/div = PRODUCT
			$res_find = findElement_i($driver,"xpath","/html/body/div[2]/div[1]/main/div/div[2]/div[2]/div[2]/div/div[1]/div/div",$res_find[1]);
			if($res_find[1]==="")
				$src = $driver->getPageSource();
			$error = $res_find[1];
		}
		catch (Exception $e) {
			$error = $e->getMessage();
		}
	}
	var_dump($error);
	return $src;
}

/**
 * [BRIEF]	Detection of the closest element of $end_content array we are in the 
 * 			$output
 * 			This is use for create sub content of unique trunk but with many
 * 			possible end
 * @param	string	$output			the string to parse	
 * @param 	string	$trunk			the trunk to research in the string
 * @param	array	$end_content	many string to end the sub content
 * @example	util_subcontenttrunk("Life Dead Repeat","L",["ead","eat"])
 * @author	chriSmile0
 * @return	array	[$end_content[?],the offset of the begin of this element, 
 * 						the $offset of the $trunk is detected] // USAGE $trunk = "" -> V1.0
 * @version	1.0
*/
function util_subcontent_trunk_i(string $output,string $trunk = "", array $end_content) : array {
	$min_offset = strlen($output);
	$min_index = 0;
	$len_end_content = sizeof($end_content);
	$offst = 0;
	if($trunk !== "")
		$output = substr($output,$offst+=strpos($output,$trunk));
	$end = $end_content[0];
	$min_offset = strpos($output,$end,0);
	$found = ($min_offset !== FALSE) ? true : false;
	if($found === false)
		$min_offset = strlen($output);
	for($i = 1; $i < $len_end_content; $i++) {
		$tmp_pos = strpos($output,$end_content[$i],0);
		if(($tmp_pos !== FALSE) && ($tmp_pos < $min_offset)) {
			$min_offset = $tmp_pos;
			$min_index = $i;
			$found = true;
		}
	}
	if($found == false)
		return array();
	$true_end = $end_content[$min_index];
	return [$true_end,$min_offset,$offst];
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
 * @param	string			$str			the str to search trunk
 * @param	string			$trunk			the trunk to search
 * @param	array			$end_content	the end(s) delimiter
 * @param 	bool 			$with_end		if we want add end delimiter or not in the substring
 * @param	int<-len_end,0> $size_end		0 complete end, -len_end = false on $with_end
 * @example	all_subcontent_with_trunk("Hello world it's me","world",["me"],true,0)
 * @author	chriSmile0
 * @return	array	array with the trunk without the end content in 
 * 					in tabs for each instance of trunk in str
 * @version	2.1	-> NEW VERSION -> deprecated [CARREFOUR] and [LECLERC] version 
*/
function all_subcontent_with_trunk_v21_i(string $str, string $trunk = "", 
										array $end_content, bool $with_end, 
										int $size_end = 0) : array {
	$res = array();
	$copy_str = $str;
	$original_trunk = $trunk;
	$original_end = (empty($end_content)) ? "" : $end_content[0];
	$next = "";
	if($original_trunk != "") {
		if($original_end != "") {
			while(($pos = strpos($copy_str,$trunk)) !== FALSE) {
				//USE util_subcontent_trunk(...) here if it"s necessary !!
				$s_str = substr($copy_str,$pos);
				$offset_next = 0;
				$res[] = substr($s_str,0,$offset_next=strpos($s_str,$original_end));
				$next = substr($s_str,$offset_next);
				$copy_str = $next;
			}
		}
		else {
			while(($pos = strpos($copy_str,$trunk)) !== FALSE) {
				$s_str = $copy_str;
				$extra_next_b = substr($s_str,1);
				$next_trunk = strpos($extra_next_b,$trunk);
				if($next_trunk === FALSE) {
					$next = $s_str;
					break;
				}
				$extra_next = substr($extra_next_b,strpos($extra_next_b,$trunk));
				$res[] = substr($s_str,0,strpos($extra_next_b,$trunk));
				$next = $extra_next;
				$copy_str = $next;
			}
		}
	}
	else {
		while(!empty($res_util = util_subcontent_trunk_i($copy_str,"",$end_content))) {
			$with_end_trunk = ($with_end == true) ? strlen($res_util[0])+$size_end : 0;
			$s_str = $copy_str;
			$offset_next = 0;
			$res[] = substr($s_str,0,($offset_next=$res_util[1])+$with_end_trunk);
			$next = substr($s_str,$offset_next+strlen($res_util[0]));
			$copy_str = $next;
		}
	}
	if(($next != "") && (($original_trunk == "") || ($original_end == "")))
		$res[] = $next;
	return $res;
}

/**
 * [BRIEF]	Split the data by product if the target product is in a predefined 
 * 			list 
 * 									
 * @param	string	$output				datas
 * @param	string	$product			product to research in datas
 * @example	search_product_in_script_json("search:{"data:[....]","lardons")
 * @author	chriSmile0
 * @return	array	split the data by product or empty array if product is not
 * 						in the list
 * @version CARREFOUR_VERSION -> to generalize with $first,$end, and $possible_end -> NEXT_VERSION
*/
function search_product_in_script_json_i(string $output, string $product) : array  {
	$first = "\"list\":{\"products\":[";
	$end = "e},\"filters\":"; // e because true or false on hasNextPage has same end (e)
	$possible_end = [",\"hasReduction\":false},",",\"hasReduction\":true},"];
	$subcontent = all_subcontent_with_trunk_v21_i($output,$first,[$end],false);
	$subcontent[0] .= $end; // "close meta" -> the end of meta is not interesting
	$subcontent[0] = substr($subcontent[0],strlen($first));
	$all_products = all_subcontent_with_trunk_v21_i($subcontent[0],"",$possible_end,true,-1);
	$all_informations = all_subcontent_with_trunk_v21_i(array_pop($all_products),"\"meta\":{\"",[$end],false);
	$subcontent = array("products"=>$all_products,"informations"=>"{".$all_informations[0]."e}}");
	return $subcontent;
}

/**
 * [BRIEF]	json_decode in array -> (associative -> true)
 * @param	$output_json	output_json string to transform in array 
 * @example	parse_json_product(...)
 * @author	chriSmile0
 * @return	array	json -> array 
 * @version CARREFOUR_VERSION -> to generalize
*/
function parse_json_product_i(string $output_json) : array {
	return json_decode($output_json,true);
}

$list_of_product = [
	"lardons",
	"oeufs"
];


$product_needed_key = [ // On ATTRIBUTES
	"type" => [],
	"offers" => [],
	"promotions" => [],
	"prices" => [
		"productPrice"
	],
	"id" => [],
	"ean" => [],
	"informations" => [
		"title",
		"packaging",
		"brand",
	],
	"url" => [],
	"hasReduction" => []
];

$page_needed_key = [ // On META 
	"total",
	"page",
	"perPage",
	"hasPreviousPage",
	"hasNextPage",
];

/**
 * [BRIEF]	Information on the research and the pages information
 * @param	array	$json			
 * @param	array	$needed_key			
 * @example extract_needed_information($json, [title,price])
 * @author	chriSmile0
 * @return	array	array with the data with want to store/share/print
*/
function extract_needed_information_i(array $json, array $needed_key) : array {
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
function extract_info_for_all_products_i(array $tab_json, array $needed_key) : array {
	$rtn = array();
	foreach($tab_json as $json) {
		array_push($rtn,extract_needed_information_pro_i(
									parse_json_product_i($json),$needed_key));
	}
	
	return $rtn;
}

/**
 * [BRIEF]	It's possible to store all data but not for you, it's important
 * 			to store the display the most useful data 
 * @param	array	$json			
 * @param	array	$needed_key			
 * @example extract_needed_information_pro($json, [title,price])
 * @author	chriSmile0
 * @return	array	array with the data with want to store/share/print
*/
function extract_needed_information_pro_i(array $json, array $needed_key) : array {
	$rtn = array();
	foreach($needed_key as $k=>$value) {
		if((strcmp($k,"prices")==0) || (strcmp($k,"informations")==0))
			continue;
		$rtn = array_merge($rtn,[$k=>$json[$k]]);
	}
	$sub_info = $json["prices"]["productPrice"];
	$rtn = array_merge($rtn,["prices"=>$sub_info]);
	$sub_key_info = $needed_key["informations"];
	$rtn = array_merge($rtn,["informations"=>[]]);
	foreach($sub_key_info as $k=>$value)
		$rtn["informations"] = array_merge($rtn["informations"],[$value=>$json["informations"][$value]]);
	return $rtn;
}

/**
 * [BRIEF]	The main procedure -> for include in other path 
 * @param	string	$url			the url to scrap
 * @param	string 	$target_product	the target product
 * @example content_scrap_intermarche((@see URL1),"lardons")
 * @author	chriSmile0
 * @return	array 	array of all product with specific information that we needed
*/
function content_scrap_intermarche(string $url, string $target_product, string $town) : array {
	$rtn = array();
	$driver = generate_driver_i();
	$list_of_product = [
		"lardons",
		"oeufs"
	];
	
	
	$product_needed_key = [ // On ATTRIBUTES
		"type" => [],
		"offers" => [],
		"promotions" => [],
		"prices" => [
			"productPrice"
		],
		"id" => [],
		"ean" => [],
		"informations" => [
			"title",
			"packaging",
			"brand",
		],
		"url" => [],
		"hasReduction" => []
	];
	
	$page_needed_key = [ // On META 
		"total",
		"page",
		"perPage",
		"hasPreviousPage",
		"hasNextPage",
	];
	$file_content = extract_source_intermarche($url,$driver,$town,$target_product);
	$sp_res = search_product_in_script_json_i($file_content,$target_product,$list_of_product);
	if(empty($sp_res)) {
		$driver->quit();
		return array();
	}
	$rtn = array_merge($rtn,extract_info_for_all_products_i($sp_res["products"],$product_needed_key));
	$infos = extract_needed_information_i(parse_json_product_i($sp_res["informations"]),$page_needed_key);
	
	$nb_page = $infos['total'] / $infos['perPage'];

	$current_page = $infos['page'];
	$new_url = $driver->getCurrentURL();
	$next_page = $current_page+1;
	for($i = $next_page ; $i < $nb_page+1 ; $i++) {
		$url_ = $new_url."?page=".$i;
		$file_content = extract_source($url_,$driver);
		$sp_res = search_product_in_script_json_i($file_content,$target_product,$list_of_product);
		if(empty($sp_res)) {
			$driver->quit();
			return $rtn;
		}
		$rtn = array_merge($rtn,extract_info_for_all_products_i($sp_res["products"],$product_needed_key));
	}
	$driver->quit();
	return $rtn;
}

/**
 * [BRIEF]	[MAIN_PROGRAM] -> for manuel execution
 * @param	$argc	The number of parameter in the command line execution
 * @param	$argv	The parameters of the command line execution
 * @example	main($argc,"php7.2 scrapper_intermarche.php (@see URL1) lardons Paris")
 * @author	chriSmile0
 * @return	bool 	1 if all is good, 0 if error in the command line or in the phase
 * 					test or if the scrapping failed 
*/
function main_i($argc, $argv) : bool {
	if($argc == 5) {
		if(empty(content_scrap_intermarche($argv[1],$argv[2],$argv[3]))) {
			echo "NO CORRESPONDENCE FOUND \n";
			return 0;
		}
	}
	else {
		echo "ERROR : format : ". $argv[0] . " [url] [research_product_type] [town] --with-openssl\n";
		return 0;
	}
	echo "EXECUTION FINISH WITH SUCCESS \n";
	return 1;
}
//var_dump(content_scrap_intermarche("https://www.intermarche.com/","lardons","Paris"));
//main_i($argc,$argv);
//var_dump(content_scrap_intermarche("https://www.intermarche.com/","Lardons","Paris"));
/**
 * [BRIEF]	
 * @param	
 * @example	
 * @author	chriSmile0
 * @return	
*/
?>