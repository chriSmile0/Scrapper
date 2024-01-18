<?php 
// URL = "https://fd7-courses.leclercdrive.fr/magasin-037301-037301-Voglans/rayon-315991-Charcuteries.aspx?Filtres=4-316011"
// URL2 = "https://fd7-courses.leclercdrive.fr/magasin-037301-037301-Voglans/recherche.aspx?TexteRecherche=lardons"
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

function extract_data_script_leclerc($url) {
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

function extract_needed_information(array $product_sheet, array $extract_list) : array  {
	$rtn = array();
	foreach($extract_list as $e_l)
		$rtn = array_merge($rtn,[$e_l=>$product_sheet[$e_l]]);

	return $rtn;
}

function extract_needed_information_of_all_product(array $products, array $ex_list) : array {
	$rtn = array();
	foreach($products as $product)
		$rtn[] = extract_needed_information($product,$ex_list);

	return $rtn;
}

function content_scrap_leclerc(string $url, string $target_product) : array{
	$file_content = extract_data_script_leclerc($url);
	$s_p_res = search_product($file_content,$target_product,$GLOBALS['list_of_product']);
	if(empty($s_p_res))
		return array();
	$all_products_find = extract_needed_information_of_all_product($s_p_res,$GLOBALS['extract_list_item']);
	return $all_products_find;
}

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
?>