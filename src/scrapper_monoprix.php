<?php 
## USAGE -> launch geckodriver (sudo snap install firefox -> >$geckodriver)
// For document file 
/**
 * Short description for file
 *
 * Long description for file
 *
 * PHP version >= 8.2
 *
 * LICENSE: --
 *
 * @package    scrapper_monoprix.php
 * @author     chrisSmile0
 * @copyright  2024 -> @author
 * @license    [NO_LICENSE]
 * @version    1.0
 * @link       https://github.com/chriSmile0/Scrapper/scrapper_monoprix.php
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

// URL1 = https://courses.monoprix.fr/products/search?q=

namespace Facebook\WebDriver;
namespace ChriSmile0\Scrapper;
use Exception;
use Facebook\WebDriver\Firefox\FirefoxOptions as FirefoxOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities as DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver as RemoteWebDriver;
use Facebook\WebDriver\Firefox\FirefoxDriver as FirefoxDriver;
use Facebook\WebDriver\Firefox\FirefoxProfile as FirefoxProfile;
//require __DIR__ . '/../../../autoload.php'; // EXPORT 
require __DIR__ . '/../vendor/autoload.php'; // DEV

function change_quantity_m(string $libelle) : string { 
	$libelle = strtolower($libelle);
	$i = 0;
	$s_l = strlen($libelle);
	while($i+1 < $s_l) {
		if((($libelle[$i]==' ')) && (is_numeric($libelle[$i+1])))
			break;
		$i++;
	}
	$wanted = substr($libelle,$i);
	preg_match_all("/[x|^-]*[0-9]+[x|g|| |,]+/",$libelle,$matches);
	$str = implode(" ",$matches[0]);
	preg_match_all("/[0-9]+/",$str,$matches2);
	$siz = sizeof($matches2[0]);
	if($siz < 2) 
		return $libelle;
	else {
		if(($matches[0][0][0]=="x"))
			return $libelle;
		if((strpos($matches[0][0],"x")===FALSE) && (strpos($matches[0][0],"g")===FALSE))
			return $libelle;
	}

	$j = 0;
	$i_u = -1;
	$i_m = -1;
	$mul = "";
	foreach($matches[0] as $elem) {
		if(strpos($elem,"g")!==FALSE) {
			$i_u = $j;
		}
		else if(strpos($elem,"x")!==FALSE) {
			$i_m = $j;
			$mul = "x";
		}
		$j++;
	}
	if(($j > 1) && ($mul == ""))
		$i_m = $j-1;
	
	if($i_m != -1) {
		if($i_u == -1)
			return $libelle;
		$rtn = $matches2[0][$i_m]."x".$matches2[0][$i_u]."g-".(intval($matches2[0][$i_m])*intval($matches2[0][$i_u]))."g";
		$len_wanted = strlen($wanted);
		$t = substr($libelle,0,$i) . " - " .$rtn . substr($libelle,$i+$len_wanted);
		return $t;
	}	
	return $libelle;
}

/**
 * [BRIEF]	generate an instance of a firefox driver with 'geckodriver' server
 * 				(localhost:4444)
 * @param 	int		$o	port	
 * @param 	bool	$web_server	true of false 
 * @example	generate_driver_m()
 * @author	chriSmile0
 * @return	/
*/
function generate_driver_m(int $p, bool $web_server) {
	//-----------------Remote with geckodriver in terminal--------------------// 
	if(!$web_server) {
		$host = 'http://localhost:'.$p.'/';

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
		}
	}
	//------------FirefoxDriver, geckodriver directly on this process--------//
	//shell_exec("kill -s kill `ps -e | grep -e geckodriver | grep -Eo '[0-9]{1,10}' | head -n 1`");sleep(1);
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
 * [BRIEF]	simulate the url get in the browser and return the display content
 * 			with the first products and the $driver with the change state
 * @param	string	$url			the url to get in the browser
 * @param 	int		$js_or_selenium	0 for js 1 for sele
 * @param 	int 	$p				port
 * @param 	bool 	$web_server		true or false
 * @example	extract_source_monoprix((@see URL1),1)
 * @author	chriSmile0
 * @return	string	the source code
*/
function extract_source_monoprix(string $url, int $js_or_selenium, int $p, 
									bool $web_server) : string {
	if($js_or_selenium == 1) {
		$driver = generate_driver_m($p,$web_server);
		if($driver == NULL)
			return "";
		$driver->get($url);
		$src = $driver->getPageSource();
		$rtn = substr($src,strpos($src,"\"totalProducts\""));
		$rtn = substr($rtn,0,strpos($rtn,"}}},\"retailer\"")) . "}}}";
		$driver->quit();
		return $rtn;
	}

	//*************44K Octet difference in favor of JS on 914-960K octets************* */
	/**	PRO SELE = faster
	 * 	PRO JS = lighter 
	 * 	CONS JS = slower
	 * 	CONS SELE = need geckodriver
	*/

	$url = escapeshellarg($url); // Replace with the target URL
	$nodeScriptPath = __DIR__.'/scrape.js';
	$output = shell_exec("node $nodeScriptPath $url");
	return $output;
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
function util_subcontent_trunk_m(string $output,array $end_content, string $trunk = "") : array {
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
 * @param 	string			$adds			add in end of each subcontent
 * @example	all_subcontent_with_trunk_v21("Hello world it's me","world",["me"],true,0,"}")
 * @author	chriSmile0
 * @return	array	array with the trunk without the end content in 
 * 					in tabs for each instance of trunk in str
 * @version	2.2	-> NEW VERSION, $adds version 
*/
function all_subcontent_with_trunk_v21_m(string $str, string $trunk, 
										array $end_content, bool $with_end = false, 
										int $size_end = 0, string $adds = "") : array {
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
				$res[] = substr($s_str,0,$offset_next=strpos($s_str,$original_end)).$adds;
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
				$res[] = substr($s_str,0,strpos($extra_next_b,$trunk)).$adds;
				$next = $extra_next;
				$copy_str = $next;
			}
		}
	}
	else {
		while(!empty($res_util = util_subcontent_trunk_m($copy_str,$end_content,""))) {
			$with_end_trunk = ($with_end == true) ? strlen($res_util[0])+$size_end : 0;
			$s_str = $copy_str;
			$offset_next = 0;
			$res[] = substr($s_str,0,($offset_next=$res_util[1])+$with_end_trunk).$adds;
			$next = substr($s_str,$offset_next+strlen($res_util[0]));
			$copy_str = $next;
		}
	}
	if(($next != "") && (($original_trunk == "") || ($original_end == "")))
		$res[] = $next;
	return $res;
}

$product_needed_key = [ // On ATTRIBUTES
	"productId" => [],
	"retailerProductId" => [],
	"name" => [],
	"available" => [], // MAYBE
	"alternatives" => [], // MAYBE
	"price" => [],
	"brand" => []
];

/**
 * [BRIEF]	(@see extract_needed_information_pro) but for all products
 * @param	array	$tab_json	all products we have store		
 * @param	array	$needed_key	list of information we need			
 * @example extract_info_for_all_products($tab_json, [price,brand])
 * @author	chriSmile0
 * @return	array	array with the data with want to store/share/print for all 
 * 					products
*/
function extract_info_for_all_products_m(array $tab_json, array $needed_key) : array {
	$rtn = array();
	foreach($tab_json as $json) {
		array_push($rtn,extract_needed_information_pro_m(
									json_decode($json,true),$needed_key));
	}
	
	return $rtn;
}

/**
 * [BRIEF]	It's possible to store all data but not for you, it's important
 * 			to store the display the most useful data 
 * @param	array	$json			
 * @param	array	$needed_key			
 * @example extract_needed_information_pro($json, [name,price])
 * @author	chriSmile0
 * @return	array	array with the data with want to store/share/print
*/
function extract_needed_information_pro_m(array $json, array $needed_key) : array {
	$rtn = array();
	$json["name"] = change_quantity_m($json["name"]);
	foreach($needed_key as $k=>$value) 
		$rtn = array_merge($rtn,[$k=>$json[$k]]);

	return $rtn;
}

/**
 * [BRIEF]	The main procedure -> for include in other path 
 * 
 * @param	string 	$target_product	the target product
 * @param	int		$p				port
 * @param 	bool 	$web_server		true or false
 * @example content_scrap_monoprix((@see URL1),"lardons")
 * @author	chriSmile0
 * @return	array 	array of all product with specific information that we needed
*/
function content_scrap_monoprix(string $target_product, int $p, bool $web_server) : array {
	$url = "https://courses.monoprix.fr/products/search?q=";
	$rtn = array();
	//check if $target_product is in the list of product (lardons,oeufs , etc)
	$script = extract_source_monoprix($url.$target_product,1,$p,$web_server);
	if(empty($prods = all_subcontent_with_trunk_v21_m($script,"{\"productId\":",[",\"retailerFinancingPlanIds\""],false,0,"}")))
		return $rtn;
	
	$product_needed_key = [ // On ATTRIBUTES
		"productId" => [],
		"retailerProductId" => [],
		"name" => [],
		"available" => [], // MAYBE
		"alternatives" => [], // MAYBE
		"price" => [],
		"brand" => []
	];
	$rtn = array_merge($rtn,extract_info_for_all_products_m($prods,$product_needed_key));
	return $rtn;
}

/**
 * [BRIEF]	[MAIN_PROGRAM] -> for manuel execution
 * @param	$argc	The number of parameter in the command line execution
 * @param	$argv	The parameters of the command line execution
 * @example	main($argc,"php7.2 scrapper_monoprix.php (@see URL1) lardons ")
 * @author	chriSmile0
 * @return	bool 	1 if all is good, 0 if error in the command line or in the phase
 * 					test or if the scrapping failed 
*/
function main_m($argc, $argv) : bool {
	if($argc == 5) {
		if(empty(content_scrap_monoprix($argv[1],$argv[2],strtolower($argv[3])==="true"))) {
			echo "NO CORRESPONDENCE FOUND \n";
			return 0;
		}
	}
	else {
		echo "ERROR : format : ". $argv[0] . "[research_product_type] [port] [?webserver]  --with-openssl\n";
		return 0;
	}
	echo "EXECUTION FINISH WITH SUCCESS \n";
	return 1;
}
//main_m($argc,$argv);
//var_dump(content_scrap_monoprix("https://courses.monoprix.fr/products/search?q=","lardons"));
/**
 * [BRIEF]	
 * @param	
 * @example	
 * @author	
 * @return	
*/
?>
