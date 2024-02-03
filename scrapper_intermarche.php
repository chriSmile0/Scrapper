<?php 
// URL1 = "https://www.intermarche.com/" -> choice magasin  
// URL2 = "https://www.intermarche.com/acceuil" -> after choice 

/* PATHS
	$url_entrance = "https://www.intermarche.com/";
	$path_to_choice = "/html/body/div[2]/div[1]/main/div[1]/div[2]/div[2]/div[2]/div[1]/div/input"; //-> xpath for location
	$path_to_proposal = "/html/body/div[2]/div[1]/main/div[1]/div[2]/div[2]/div[2]/div[2]"; // -> selection proposition 
	$path_to_first_choice =  "/html/body/div[2]/div[1]/main/div[1]/div[2]/div[2]/div[2]/div[2]/div[2]"; // -> for first choice 
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
	//$firefoxOptions->addArguments(['-headless']); -> !!
	$capabilities->setCapability(FirefoxOptions::CAPABILITY, $firefoxOptions);

	return RemoteWebDriver::create($host, $capabilities);
}


/**
 * [BRIEF]	simulate the url get in the browser and return the display content
 * 			[THIS TECHNIC IS USE FOR BYPASS CLOUDFLARE]
 * @param	string	$url	the url to get in the browser
 * @param 	/		$driver	the driver instance
 * @param 	/		$town 	the city in the research area
 * @example	extract_source_carrefour((@see URL1),$driver)
 * @author	chriSmile0
 * @return	string	the display content of the url renderer
*/
function extract_source_intermarche(string $url,$driver, string $town) : string {
	$driver->get($url);
	$choice_by_class = "selectAddressForStore__search";

	//COOKIE
	$driver->findElement(WebDriverBy::xpath('//*[@id="didomi-popup"]/div/div/div/span'))->click();
	//COOKIE DISABLE -> use js for click on the button 

	$choice = $driver->findElement(WebDriverBy::className($choice_by_class));
	$choice->findElement(WebDriverBy::tagName('input'))->sendKeys($town);
	sleep(1); // wait_until is better 
	$driver->executeScript("document.getElementsByClassName('selectAddressForStore__suggestion')[1].click();");
	sleep(1); // wait until is better 
	
	$driver->findElement(WebDriverBy::xpath('/html/body/div[2]/div[2]/div/section/div/div[1]/div/div[2]/div[1]/div/div[4]/button'))->click();
	$research_box = $driver->findElement(WebDriverBy::className('search-input__input'));
	$research_box->sendKeys('Lardons');
	$research_box->sendKeys(WebDriverKeys::ENTER);
	//source code to receive 
	sleep(1); // wait until is better 
	$src = $driver->getPageSource();
	return $src;
}


function util_subcontenttrunk(string $output,string $trunk = "", array $end_content,int  $offset) : array {
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
 * @example	all_subcontent_with_trunk("Hello world it's me","world","me")
 * @author	chriSmile0
 * @return	array	array with the trunk without the end content in 
 * 					in tabs for each instance of trunk in str
 * @version	2.1	-> NEW VERSION
*/
function all_subcontent_with_trunk_v21(string $str, string $trunk = "", 
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
				//USE util_subcontenttrunk here if it"s necessary !!
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
		while(!empty($res_util = util_subcontenttrunk($copy_str,"",$end_content,0))) {
			$with_end_trunk = ($with_end == true) ? strlen($res_util[0])+$size_end : 0;
			var_dump($res_util);
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
 * @example	search_product_in_script_json("search:{"data:[....]","lardons",["lardons"])
 * @author	chriSmile0
 * @return	array	split the data by product or empty array if product is not
 * 						in the list
 * @version CARREFOUR_VERSION -> to generalize
*/
function search_product_in_script_json(string $output, string $product) : array  {
	$first = "\"list\":{\"products\":[";
	$end = "e},\"filters\":"; // e because true or false on hasNextPage has same end (e)
	$possible_end = [",\"hasReduction\":false},",",\"hasReduction\":true},"];
	$subcontent = all_subcontent_with_trunk_v21($output,$first,[$end],false);
	$subcontent[0] .= $end; // "close meta" -> the end of meta is not interesting
	$subcontent[0] = substr($subcontent[0],strlen($first));
	$all_products = all_subcontent_with_trunk_v21($subcontent[0],"",$possible_end,true,-1);
	var_dump($all_products);
	$all_informations = all_subcontent_with_trunk_v21(array_pop($all_products),"\"meta\":{\"",[$end],false);
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
function parse_json_product(string $output_json) : array {
	return json_decode($output_json,true);
}

$list_of_product = [
	"lardons"
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
	"totalPage",
	"hasPreviousPage",
	"hasNextPage",
	"keyword",
];

/**
 * [BRIEF]	The main procedure -> for include in other path 
 * @param	string	$url			the url to scrap
 * @param	string 	$target_product	the target product
 * @example content_scrap_carrefour((@see URL1),"lardons")
 * @author	chriSmile0
 * @return	array 	array of all product with specific information that we needed
*/
function content_scrap_intermarche(string $url, string $target_product, string $town) : array {
	$rtn = array();
	$driver = generate_driver();
	$file_content = extract_source_intermarche($url,$driver,$town);
	$sp_res = search_product_in_script_json($file_content,$target_product,$GLOBALS['list_of_product']);
	if(empty($sp_res)) {
		$driver->quit();
		return array();
	}
	/*$rtn = array_merge($rtn,extract_info_for_all_products($sp_res["products"],$GLOBALS['product_needed_key']));
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
	}*/
	$driver->quit();
	$rtn = array();
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
	if($argc == 5) {
		if(empty(content_scrap_intermarche($argv[1],$argv[2],$argv[3]))) {
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
//main($argc,$argv);
$res = search_product_in_script_json(file_get_contents("script_intermarche.txt"),"lardons");
var_dump($res);
echo "coucou it's intermarche\n";
//var_dump(util_subcontenttrunk("John marc John nny marcus John mike","John",["marc"],0));
//var_dump(all_subcontent_with_trunk_2("John marc nny marcus John mike nny","John",["nny"]));

//var_dump(all_subcontent_with_trunk_v2("John marc nny John mike nny dd","Johnnny",""));
/**
 * [BRIEF]	
 * @param	
 * @example	
 * @author	chriSmile0
 * @return	
*/
?>