<?php 
## USAGE -> launch geckodriver (sudo snap install firefox -> >$geckodriver)


// https://github.com/adonistividad/web-scraping/blob/master/scrape_carrefour.py
// Carrefour : https://www.carrefour.fr/s?q=lardons
// https://www.carrefour.fr/s?q=lardons&filters%5Bproduct.categories.name%5D=Charcuterie%20et%20Traiteur
// https://www.carrefour.fr/s?q=lardons&filters[product.categories.name]=Charcuterie et Traiteur
//sub_main($argv[1],$argv[2],$argv[3],$argv[4],$argv[5])
// https://www.carrefour.fr/promotions?noRedirect=0&page=0 ;
// https://www.carrefour.fr/s?q=lardons&noRedirect=1&page=2
//$url =  "https://www.carrefour.fr/promotions?noRedirect=0&page=0";

namespace Facebook\WebDriver;

use Facebook\WebDriver\Firefox\FirefoxOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

require_once('vendor/autoload.php');

function generate_driver() {
	$host = 'http://localhost:4444/';

	$capabilities = DesiredCapabilities::firefox();
	$firefoxOptions = new FirefoxOptions();
	$firefoxOptions->addArguments(['-headless']);
	$capabilities->setCapability(FirefoxOptions::CAPABILITY, $firefoxOptions);

	return RemoteWebDriver::create($host, $capabilities);
}

function extract_source_carrefour($url,$driver) {
	$driver->get($url);
	$src = $driver->getPageSource();
	return $src;
}

function all_subcontent_with_trunk(string $str, string $trunk, string $end_content = "") : array {
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

function search_product_in_script_json(string $output, string $product, array $list_of_product) : array  {
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

function extract_needed_information(array $json, array $needed_key) : array {
	$rtn = array();
	$sub_json_needed = $json["meta"];
	foreach($needed_key as $val) 
		$rtn = array_merge($rtn,[$val=>$sub_json_needed[$val]]);
	
	return $rtn;
}

function extract_info_for_all_products(array $tab_json, array $needed_key) : array {
	$rtn = array();
	foreach($tab_json as $json) {
		array_push($rtn,extract_needed_information_pro(
									parse_json_product($json),$needed_key));
	}
	
	return $rtn;
}

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
/*$url = "https://carrefour.fr/s?q=lardons";
$target = "lardons";
print_r(content_scrap_carrefour($url,$target));*/
?>