
<?php 
/**
 * [BRIEF] HERE WE USE THE RETURN OF PROCESSES
 * 		(beause pcntl_fork()) is not support on the webserver
 * 		Results : All informations we write by the user is transmit in string (JSON_STR)
 * 					On the exec of the file usage.php
 * 				  - Parser and creater is necessary  
*/


$scrappers_usages_example = [
	"Carrefour" => ["lardons","Paris"],
	"Leclerc" => ["lardons","Paris"],
	"Auchan" => ["lardons","Paris"],
	"SystemeU" => ["lardons","Paris"],
	"Intermarche" => ["lardons","Paris"],
	"Monoprix" => ["lardons"]
];

$names_products = [ // NAME PRODUCT
	"Carrefour" => "title",
	"Leclerc" => "sLibelleLigne1|sLibelleLigne2",
	"Auchan" => "label",
	"SystemeU" => "brand",
	"Intermarche" => "informations",
	"Monoprix" => "name"
];


$finalLabels = [
	"name",
	"price"
];

$Carrefour = [ // replace by get_items
	"ean",
	"title", 
	"brand", 
	"slug",
	"offerServiceId",
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
	"packaging",
	"nutriscore"
];
$Leclerc = [ // replace by get items 
	"sLibelleLigne1",
	"sLibelleLigne2",
	"sPrixUnitaire",
	"nrPVUnitaireTTC",
	"sPrixPromo",
	"sPrixParUniteDeMesure",
	"nrPVParUniteDeMesureTTC",
	"sUrlPageProduit"
];



$dis = $zone = $type = $product = $choices = "";

$all_types = ["Poisson","Viande","EpicerieSa","EpicerieSu","Pains","Patisserie"
				,"Charcuterie","Surgele","Boisson","Bebe","Bio","Hygiene","Beaute"
				,"Entretien","Nettoyage","Animalerie","Loisirs","Maison","Laiterie"
				,"Oeufs","Fruits","Legumes","Lardons"	
]; // add Lardons for test

$rayons = ["Boulangerie","Fruits"]; // FOR NEXT UPDATE

$choice_ens = array();
$possible_ens = ["Carrefour","Leclerc","Monoprix","Auchan","Intermarche"];//ADD SU LATER
$active_ens =  ["Carrefour"=>false,"Leclerc"=>false,"Monoprix"=>false,"Auchan"=>false,"Intermarche"=>false];

//---------------------------------UTILS--------------------------------------//
function cmp_label(string $label1, string $label2) {
	$split_l1 = explode(" ",strtolower($label1)." ");
	$split_l2 = explode(" ",strtolower($label2)." ");
	$commons = "";
	$s = sizeof($split_l1);
	$i = 0;
	foreach($split_l1 as $sl1) {
		foreach($split_l2 as $sl2) {
			if($sl1 == $sl2) {
				$commons .= " $sl1";
				$i++;
				break;
			}
		}
	}
	if($s == $i) {
		/*if(in_array("bio",$split_l2))
			return "";*/
		return substr($commons,1);
	}
	return "";
}
//-----------------------------END UTILS--------------------------------------//


function main_(array $elements) {
	$str = my_json_encoding($elements);
	exec("php7.2 usage.php $str > out.txt");
	$rtn = file_get_contents("out.txt");
	$parsing = parse_exec_usage($rtn);
	return $parsing;
}

$arr = array("Auchan"=>["Lardons","Paris"]);
$arr2 = array("Leclerc"=>["lardons","Voglans"]);


function my_json_encoding(array $to_encode) { // FOR MAIN PARAMETER
	$str = json_encode($to_encode);
	$res = "";
	$size = strlen($str);
	for($i = 0; $i < $size ;$i++) 
		$res .= ($str[$i] === '"') ? '\"' : (($str[$i] === "'") ? "\'" : $str[$i]);
	
	return $res;
}

function parse_exec_usage(string $rtn) {
	return json_decode(substr($rtn,strpos($rtn,"{\"")),true);;
}

//$rt = main_(array(),$names_products);

function search_price(string $ens, array $item) {
	switch($ens) {
		case "Carrefour":
			return $item['offers']['ean']['offerServiceId']['attributes']['price'] . "€";
			break;
		case "Leclerc":
			return $item['nrPVUnitaireTTC'] . "€";
			break;
		default:
			break;
	}
}

function search_name(string $ens, array $item) {
	switch($ens) {
		case "Carrefour":
			return $item['title'] . "|" . $item['brand'];
			break;
		case "Leclerc":
			return $item['sLibelleLigne1'] . "|" . $item['sLibelleLigne2'];
			break;
		default:
			break;
	}
}

function parse_keys(string $ens, array $content) { // FOR LISTINGS MAYBE 
	$keys = $GLOBALS[$ens];
	$rtn = "";
	//$first_val = $content[0];
	foreach($content as $elem) {
		$rtn .= "<div>Prix : ".search_price($ens,$elem).", produit : ".search_name($ens,$elem)."</div>";
	}
	//$price_f_val = search_price($ens,$first_val);//$first_val[$keys['']]
	return $rtn;
}

function create_cmp_product(array $all_products, string $compare_product) { // easy with 1.2 Scrapper Version
	$save_products = array();
	foreach($all_products as $k => $v) {
		if($v !== NULL) {
			if($k === "Carrefour") {
				foreach($v as $val) {
					$save_products[] = ["name"=>$val['title'],"ens"=>[$k],"prix"=>[$val['price']['price']]];
				}
			}
			if($k === "Leclerc") {
				foreach($v as $val) {
					$save_products[] = ["name"=>$val['sLibelleLigne1']." ".$val['sLibelleLigne2'],"ens"=>[$k],"prix"=>[$val['nrPVUnitaireTTC']]];
				}
			}
			if($k === "Monoprix") {
				foreach($v as $val) {
					$save_products[] = ["name"=>$val['name'],"ens"=>[$k],"prix"=>[$val['price']['current']['amount']]];
				}
			}
			if($k === "Auchan") {
				foreach($v as $val) {
					$save_products[] = ["name"=>$val['label']." ".$val['quantity'],"ens"=>[$k],"prix"=>[$val['price']]];
				}
			}
			if($k === "Intermarche") {
				foreach($v as $val) {
					$infos = $val["informations"];
					$save_products[] = ["name"=>$infos['title']." ".$infos["packaging"]." ".$infos["brand"],"ens"=>[$k],"prix"=>[$val['prices']['value']]];
				}
			}
		}

	}
	$unique_products = array();
	$compare_products = array();
	$others = array();
	foreach($save_products as $elem) {
		$found = false;
		$index = 0;
		$l_ = "";
		foreach($unique_products as $u_p) {
			//echo "|".$elem['name']."|".$u_p['name']."|\n";
			if((($l = cmp_label($elem['name'],$u_p['name']))!="")) {
				$l_ = $l;
				echo "f \n";
				$found = true;
				break;
			}
			$index++;
		}
		if($found == true) {
			if(in_array($elem['ens'][0],$unique_products[$index]['ens']))
				continue; // LOGICAL BUT SECURITY 

			array_push($unique_products[$index]['prix'],$elem['prix'][0]);
			array_push($unique_products[$index]['ens'],$elem['ens'][0]);
			$unique_products[$index]['name'] = $l_; // option
			if(!in_array($index,$others));
				$others[] = $index;
			
		}
		else {
			$unique_products[] = $elem;
		}
	}
	$t_others = array_unique($others);
	foreach($t_others as $o) {
		$compare_products[] = $unique_products[$o];
	}
	//echo "uniq \n";
	var_dump($compare_products);
	return $compare_products;

}

function display_each_product(array $create_cmp_pro) {
	$rtn = "";
	foreach($create_cmp_pro as $c_cmp) {
		$rtn .= "<div>";
		$rtn .= "<span>".$c_cmp['name']."</span>";
		$rtn .= "<ul>";
		$size = sizeof($c_cmp['ens']);
		echo "size : $size\n";
		for($i = 0 ; $i < $size ; $i++) {
			$rtn .= "<li>".$c_cmp['ens'][$i]."|".$c_cmp['prix'][$i]."</li>";
		}
		$rtn .= "</ul>";
		$rtn .= "</div>\n";
	}
	return $rtn;
}

function display_compare(array $ens, string $product, string $label, array $cities) {
	$elements = array();
	$i = 0;
	foreach($ens as $e) {
		$elements = array_merge($elements,[$e=>[$product,$cities[$i]]]);
		$i++;
	}

	$to_display = main_($elements); // OK
	$full_rtn = display_each_product(create_cmp_product($to_display,$label));
	return $full_rtn;//"WAITED\n";//$rtn;
}

function display_ens(array $checkbox_ens) : string {
	$rtn = "";
	foreach($checkbox_ens as $k => $v) 
		$rtn .= ($v) ? $k.";" : "";
	
	return $rtn;
}

function display_city(string $zone) : string {
	$rtn = "";
	//check valid city -> cities.json ?
	$rtn .= $zone;
	return $rtn;
}

function display_type(string $type) : string {
	$rtn = "";
	if(in_array($type,$GLOBALS['all_types'])) {
		return $type;
	}
	return $rtn;
}

function display_product(string $product) : string {
	$rtn = "";
	$rtn .= $product;
	return $rtn;
}

function parse_command_line(string $command_line) {
	// EXAMPLE : php7.2 process_p.php --ens=Auchan,Leclerc --cities="Lyon'ok" --products="Lardons" --label_products="Lardons fumes"
	$arr = explode(" ",$command_line,2);
	//var_dump($arr);
	if(sizeof($arr) == 1) {
		echo "ERROR arguments : --ens= --cities= --products= --label_product=[OPTION]\n";
	}
	else {
		$options = array_slice(explode(" --"," ".$arr[1]),1);
		if(($s = sizeof($options))>=3 && $s < 5) {
			$elements = [
					"ens"=>explode(",",substr($options[0],strpos($options[0],"=")+1)),
					"cities"=>explode(",",substr($options[1],strpos($options[1],"=")+1)),
					"products"=>explode(",",substr($options[2],strpos($options[2],"=")+1))
			];
			/*for($i = 0; $i < 5; $i++) {
				$elements["ens"][] = NULL;
			}*/
			if($s == 4) 
				$elements = array_merge($elements,["labels"=>explode(",",substr($options[3],strpos($options[3],"=")+1))]);
			
			//var_dump($elements);
			return $elements;
		}
		else {
			echo "ERROR arguments : --ens= --cities= --products= \n";
		}
		
	}
	return array();
}

function main(string $command_line, bool $web) {
	if(!$web) {
		$fields = parse_command_line($command_line);
		var_dump($fields);
		$ens = $fields["ens"];
		//$ens = array_values(array_intersect($GLOBALS['possible_ens'],$ens));
		$GLOBALS['active_ens'] = $ens;
		$city = $fields["cities"][0]; 		// for the moment is one city 
		$products = $fields["products"][0]; // ""
		$labels = $fields["labels"][0]; 	// ""
		$GLOBALS['zone'] = "Zone de recherche : ".($c=display_city($city));
		$GLOBALS['type'] = "Type de produit : ".($p=display_type($products));
		$GLOBALS['product'] = "Produit : ".($p2=display_product($labels)); // update now 
		$GLOBALS['choices'] = "Comparaison des enseignes : ".display_ens($ens);
		$GLOBALS['dis'] = display_compare($ens,$p,$p2,$fields["cities"]);
		echo $GLOBALS['dis'];
	}
	else {
		if(isset($_POST['submitproduct'])) {
			$keys = array_keys($GLOBALS['active_ens']);
			$len_ens2 = sizeof($keys);
			for($i = 1; $i < $len_ens2+1; $i++) 
				$GLOBALS['active_ens'][$keys[$i-1]] = (isset($_POST["c".$i]));
			//tab of $cities
			$cities = array();
			$GLOBALS['zone'] = "Zone de recherche : ".($c=display_city($_POST['city']));
			$GLOBALS['type'] = "Type de produit : ".($p=display_type($_POST['p']));
			$GLOBALS['product'] = "Produit : ".($p2=display_product($_POST['p2']));
			$GLOBALS['choices'] = "Comparaison des enseignes : ".display_ens($GLOBALS['active_ens']);
			$GLOBALS['dis'] = display_compare($GLOBALS['active_ens'],$p,$p2,[$c]); //$cities
		}
	}

}
main(implode(" ",$argv),false);
$l_leclerc = "lardons fumés herta 200g";
$l_monoprix = "Herta Lardons Fumés 200g";
$l_auchan = "HERTA Lardons fumés ";
$l_intermarche = "Lardons fumés La barquette de 200g  - Herta";
$l_carrefour = "Lardons fumés Herta 200g ";
$l_systemeu = ""; // ? soon 
//echo cmp_label($l_leclerc,$l_leclerc);
//my_json_encoding($argv);
//var_dump(main_($arr2));
$m_hlfsn = "herta lardons fumés sans nitrite 2x75g-150g";
$l_lfhsn = "lardons fumés herta sans nitrite - 2x75g-150g";
//echo cmp_label($m_hlfsn,$l_lfhsn);

?>