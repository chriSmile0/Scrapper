<?php 
// URL1 = "https://fd7-courses.leclercdrive.fr/magasin-037301-037301-Voglans/rayon-315991-Charcuteries.aspx?Filtres=4-316011"
// URL2 = "https://fd7-courses.leclercdrive.fr/magasin-037301-037301-Voglans/recherche.aspx?TexteRecherche=lardons"
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
 * @package    scrapper.php
 * @author     chrisSmile0
 * @copyright  2024 -> @author
 * @license    [NO_LICENSE]
 * @version    1.0
 * @link       https://github.com/chriSmile0/Scrapper/scrapper_leclerc.php
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

$test_product = [
	"Lardons"
];

$list_of_product = [
	"Lardons",
	"Saucisse"
];

$extract_list_item = [
	"sLibelleLigne1",
	"sLibelleLigne2",
	"sPrixUnitaire",
	"nrPVUnitaireTTC",
	"sPrixPromo",
	"sPrixParUniteDeMesure",
	"nrPVParUniteDeMesureTTC",
	"sUrlPageProduit"
];

/**
 * [BRIEF]	A function for extract the html content of the leclerc website
 * @param	string 	$url	The number of paramter in the command line execution
 * @example	extract_data_script_leclerc((@see URL1))
 * @author	chriSmile0
 * @return	string 	the content of the file
*/
function extract_data_script_leclerc(string $url) : string {
	$options = [
		'ssl' => [
		'verify_peer' => false,
		'verify_peer_name' => false,
		],
		'http'=> [
			'method'=>"GET",
			'header'=>"Accept-language: en\r\n" .
					"Cookie: foo=bar\r\n" .  // check function.stream-context-create on php.net
					"User-Agent: 
						Mozilla/5.0 (iPad; U; CPU OS 3_2 like Mac OS X; en-us) 
						AppleWebKit/531.21.10 (KHTML, like Gecko) Version/4.0.4 Mobile/7B334b 
						Safari/531.21.102011-10-16 20:23:10\r\n" // i.e. An iPad 
		],
	];
	$context = stream_context_create($options);
	$output = file_get_contents($url, false,$context);
	return $output;
}

/**
 * [BRIEF]	
 * @param	string	$str			the str to search trunk
 * @param	string	$trunk			the trunk to search
 * @param	string	$end_content	the end delimiter
 * @example	all_subcontent_with_trunk("Hello world it's me","world","me")
 * @author	chriSmile0
 * @return	array	array with the trunk without the end content in 
 * 					in tabs for each instance of trunk in str
 * @version	1.0 	NEW_VERSION IN SCRAPPER_CARREFOUR.php file 
 * @deprecate soon 
*/
function all_subcontent_with_trunk(string $str, string $trunk, string $end_content = "") : array {
	$res = array();
	$offset = 0;
	$copy_str = $str;
    while(($pos = strpos($copy_str, $trunk, $offset)) !== FALSE) {
		$tmp = substr($copy_str,$pos);
		$pos_end = strpos($tmp,$end_content);
		$res[] = substr($tmp,$offset,$pos_end);
		$offset = $pos_end + 1;
		$copy_str = $tmp;
    }
	return $res;
}

/**
 * [BRIEF]	Split the data by product if the target product is in a predefined 
 * 			list 
 * 			[NB:] -> the second line if pop because the datas in store
 * 								in the last CDATA script		
 * @param	string	$output				datas
 * @param	string	$product			product to research in datas
 * @param	array	$list_of_product	the list of searchable product
 * @example	search_product("CDATA..sLibelleLigne1,price ...//]","lardons",["lardons"])
 * @author	chriSmile0
 * @return	array	split the data by product or empty array if product is not
 * 						in the list
*/
function search_product(string $output, string $product, array $list_of_product) : array {
	$subcontent = all_subcontent_with_trunk($output,"CDATA","//]]");
	$last_cdata_content = array_pop($subcontent);
	if(!in_array($product,$list_of_product))
		return array();
	$array_lardons = explode("sLibelleLigne1",$last_cdata_content);
	$size = sizeof($array_lardons);
	$index = 1;
	$retour = array();
	for($i = $index; $i < $size ; $i++) {
		$create_map = array_values(explode(",","\"sLibelleLigne1".$array_lardons[$i]));
		$create_remap = array();
		foreach($create_map as $elem) {
			$explod = array_values(explode(':',$elem));
			$lashes = html_entity_decode(str_replace(['"',"'"],"",$explod[0]));
			if(sizeof($explod) > 1)
				if(strcmp($lashes,"") != 0) 
					$create_remap = array_merge($create_remap,[$lashes=>html_entity_decode(str_replace(['"',"'"],"",implode("",array_slice($explod,1))))]);
		}
		$retour[] = $create_remap;
	}
	return $retour;
}


/**
 * [BRIEF]	It's possible to store all data but not for you, it's important
 * 			to store the display the most useful data 
 * @param	array	$product_sheet			
 * @param	array	$extract_list				
 * @example extract_needed_information($product_sheet, [price,title])
 * @author	chriSmile0
 * @return	array	array with the data with want to store/share/print
*/
function extract_needed_information(array $product_sheet, array $extract_list) : array  {
	$rtn = array();
	foreach($extract_list as $e_l)
		$rtn = array_merge($rtn,[$e_l=>$product_sheet[$e_l]]);

	return $rtn;
}

/**
 * [BRIEF]	(@see extract_needed_information) but for all products
 * @param	array	$products	all products we have store		
 * @param	array	$ex_list	the extract_list 			
 * @example extract_needed_information($product_sheet, [price,title])
 * @author	chriSmile0
 * @return	array	array with the data with want to store/share/print for all 
 * 					products
*/
function extract_needed_information_of_all_product(array $products, array $ex_list) : array {
	$rtn = array();
	foreach($products as $product)
		$rtn[] = extract_needed_information($product,$ex_list);

	return $rtn;
}

/**
 * [BRIEF]	The main procedure -> for include in other path 
 * @param	string	$url			the url to scrap
 * @param	string 	$target_product	the target product
 * @example content_scrap_leclerc((@see URL1),"lardons")
 * @author	chriSmile0
 * @return	array 	array of all product with specific information that we needed
*/
function content_scrap_leclerc(string $url, string $target_product) : array {
	$file_content = extract_data_script_leclerc($url);
	$s_p_res = search_product($file_content,$target_product,$GLOBALS['list_of_product']);
	if(empty($s_p_res))
		return array();
	$all_products_find = extract_needed_information_of_all_product($s_p_res,$GLOBALS['extract_list_item']);
	return $all_products_find;
}

/**
 * [BRIEF]	[MAIN_PROGRAM] -> for manuel execution
 * @param	$argc	The number of parameter in the command line execution
 * @param	$argv	The parameters of the command line execution
 * @example	main($argc,"php7.2 scrapper_leclerc.php (@see URL1) lardons")
 * @author	chriSmile0
 * @return	bool 	1 if all is good, 0 if error in the command line or in the phase
 * 					test or if the scrapping failed 
*/
function main($argc, $argv) : bool {
	if($argc == 4) {
		if(empty(content_scrap_leclerc($argv[1],$argv[2]))) {
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
 * @author	
 * @return	
*/
?>