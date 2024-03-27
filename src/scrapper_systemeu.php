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
 * @package    scrapper_systemeu.php
 * @author     chrisSmile0
 * @copyright  2024 -> @author
 * @license    [NO_LICENSE]
 * @version    1.0
 * @link       https://github.com/chriSmile0/Scrapper/scrapper_systemeu.php
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

// URL1 = https://www.coursesu.com/drive/home
namespace ChriSmile0\Scrapper;

/**
 * [BRIEF]	simulate the url get in the browser and return the display content
 * 
 * @param 	string	$town 	the city in the research area
 * @param	string	$target	the product we want to research
 * @example	extract_source_systemu((@see URL1),$driver,"Paris","lardons")
 * @author	chriSmile0
 * @return	string	the display content of the url renderer
*/
function extract_source_systemeu(string $town, string $target) : string {
	$town_ = escapeshellarg($town);
	$nodeScriptPath = __DIR__.'/scrape_su.js';
	// while $src.indexOF('products') == -1) ?? because not 100% regular for the moment 
	$src = shell_exec("node $nodeScriptPath $town_ $target");
	//$src = file_get_contents(__DIR__. "/products_su.txt"); // OK 
	//shell_exec("rm -r screen")
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
function util_subcontent_trunk_s(string $output,string $trunk = "", array $end_content) : array {
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
 * [BRIEF]	That's a new version of the same name function in 'scrapper_intermarche.php'
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
 * @version	2.5	-> NEW VERSION -> deprecated [INTERMARCHE/AUCHAN] version 
*/
function all_subcontent_with_trunk_v21_s(string $str, string $trunk = "", 
										array $end_content, bool $with_end, 
										int $size_end = 0) : array {
	$res = array();
	$copy_str = $str;
	$original_trunk = $trunk;
	$original_end = (empty($end_content)) ? "" : $end_content[0];
	$next = "";
	if($original_trunk != "") {
		if($original_end != "") {
			while(!empty($res_util = util_subcontent_trunk_s($copy_str,$trunk,$end_content))) {
				$with_end_trunk = ($with_end == true) ? strlen($res_util[0])+$size_end : 0;
				$s_str = substr($copy_str,$res_util[2]);
				if($res_util[1] === 0) 
					break;
				$offset_next=$res_util[1];
				$res[] = substr($s_str,0,$offset_next+$with_end_trunk);//+$with_end_trunk));
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
		while(!empty($res_util = util_subcontent_trunk_s($copy_str,"",$end_content))) {
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
 * @version SPECIFIC_VERSION 'scrape_su.js' make the first part of the oldest version
*/
function search_product_in_script_json(string $output, string $product) : array  {
	$end_product = ["},{","}],"];
	return all_subcontent_with_trunk_v21_s($output,"{\"promotion\"",$end_product,true,-2);
}


$product_needed_key = [ // On ATTRIBUTES
	"promotion" => [],
	"id" => "",
	"notation" => "",
	"EAN" => "",
	"brand" => "",
	"price" => "",
	"product_cat1" => "",
	"product_cat2" => "",
	"product_cat3" => ""
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
function extract_info_for_all_products(array $tab_json, array $needed_key) : array {
	$rtn = array();
	foreach($tab_json as $json) {
		array_push($rtn,extract_needed_information_pro(
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
function extract_needed_information_pro(array $json, array $needed_key) : array {
	$rtn = array();
	foreach($needed_key as $k=>$value) 
		$rtn = array_merge($rtn,[$k=>$json[$k]]);

	return $rtn;
}

/**
 * [BRIEF]	The main procedure -> for include in other path 
 * 
 * @param	string 	$target_product	the target product
 * @param 	string 	$town			the research area
 * @example content_scrap_systemeu((@see URL1),"lardons","75001, Paris")
 * @author	chriSmile0
 * @return	array 	array of all product with specific information that we needed
*/
function content_scrap_systemeu(string $target_product, string $town) : array {
	$rtn = array();
	//check if $target_product is in the list of product (lardons,oeufs , etc)
	$products_lines = extract_source_systemeu($town,$target_product);
	$sp_res = search_product_in_script_json($products_lines,$target_product);
	if(empty($sp_res))
		return $rtn;
	$product_needed_key = [ // On ATTRIBUTES
		"promotion" => [],
		"id" => "",
		"notation" => "",
		"EAN" => "",
		"brand" => "",
		"price" => "",
		"product_cat1" => "",
		"product_cat2" => "",
		"product_cat3" => ""
	];
	$res = extract_info_for_all_products($sp_res,$product_needed_key);
	return $res;
}

/**
 * [BRIEF]	[MAIN_PROGRAM] -> for manuel execution
 * @param	$argc	The number of parameter in the command line execution
 * @param	$argv	The parameters of the command line execution
 * @example	main($argc,"php7.2 scrapper_systemeu.php (@see URL1) lardons '75001, Paris'")
 * @author	chriSmile0
 * @return	bool 	1 if all is good, 0 if error in the command line or in the phase
 * 					test or if the scrapping failed 
*/
function main_s($argc, $argv) : bool {
	if($argc == 4) {
		if(empty(content_scrap_systemeu($argv[2],$argv[3]))) {
			echo "NO CORRESPONDENCE FOUND \n";
			return 0;
		}
	}
	else {
		echo "ERROR : format : ". $argv[0] . " [research_product_type] [town] --with-openssl\n";
		return 0;
	}
	echo "EXECUTION FINISH WITH SUCCESS \n";
	return 1;
}
//main_s($argc,$argv);
//var_dump(content_scrap_systemeu("Lardons","Voglans"));
//var_dump(extract_source_systemeu("Voglans","Lardons"));
/**
 * [BRIEF]	
 * @param	
 * @example	
 * @author	chriSmile0
 * @return	
*/
?>