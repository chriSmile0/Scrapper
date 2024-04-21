
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
				,"Oeufs","Fruits","Legumes","Lardons","Saumon"	
]; // add Lardons for test

$rayons = ["Boulangerie","Fruits"]; // FOR NEXT UPDATE

$choice_ens = array();
$possible_ens = ["Carrefour","Leclerc","Monoprix","Auchan","Intermarche"];//ADD SU LATER
$active_ens =  ["Carrefour"=>false,"Leclerc"=>false,"Monoprix"=>false,"Auchan"=>false,"Intermarche"=>false];

//---------------------------------UTILS--------------------------------------//
function cmp_brand(string $brand1, string $brand2) {
	$b1 = strtolower($brand1);
	$b2 = strtolower($brand2);
	echo "b1 : $b1\n";
	echo "b2 : $b2\n";
	if($b1 === $b2) {
		if($b1 === "")
			return "...";
		return $b1;
	}
	return "";
}

function cmp_label_v2(string $label1, string $label2) {
	$split_l1 = explode(" ",strtolower($label1)." ");
	$split_l2 = explode(" ",strtolower($label2)." ");
	echo "label1:|$label1|\n";
	echo "label2:|$label2|\n";

	$s1 = sizeof($split_l1);
	$s2 = sizeof($split_l2);
	$i1 = 0;
	$i2 = 0;
	$commons1 = "";
	foreach($split_l1 as $sl1) {
		foreach($split_l2 as $sl2) {
			if($sl1 == $sl2) {
				$commons1 .= " $sl1";
				$i1++;
				break;
			}
		}
	}

	$commons2 = "";
	foreach($split_l2 as $sl2) {
		foreach($split_l1 as $sl1) {
			if($sl2 == $sl1) {
				$commons2 .= " $sl2";
				$i2++;
				break;
			}
		}
	}
	echo "s1:$s1 , i1:$i1\n";
	echo "s2:$s2 , i2:$i2\n";
	if(($s1 === $i1)) {
		echo "f \n";
		return substr($commons1,1);
	}
	return "";
}

function cmp_prod_v3(array $p1, array $p2) {
	$s_label1 = $p1['name'];
	$s_label2 = $p2['name'];
	$split_l1 = explode(" ",strtolower($s_label1)." ");
	$split_l2 = explode(" ",strtolower($s_label2)." ");
	sort($split_l1);
	sort($split_l2);
	$s1 = sizeof($split_l1);
	$s2 = sizeof($split_l2);
	$i1 = 0;
	$commons1 = "";
	$sp1 = $sp2 = NULL;
	if($s1>=$s2) {
		$sp1 = $split_l1;
		$sp2 = $split_l2;
	}
	else {
		$sp1 = $split_l2;
		$sp2 = $split_l1;
		$tmp = $s1;
		$s1 = $s2;
		$s2 = $tmp;
	}
	foreach($sp1 as $sl1) {
		foreach($sp2 as $sl2) {
			if($sl1 == $sl2) {
				$commons1 .= " $sl1";
				$i1++;
				break;
			}
		}
	}
	return [$p1["name"],$p2["name"],"dif"=>$s1-$i1,"common"=>($s1==$i1) ? substr($commons1,1) : ""];
}
	
function sort_list_cmp(array $list) {
	$i = 0;
	foreach($list as $elem) {
		$el = $elem;
		$j = 0;
		$somme_dif = 0;
		$list[$i][0] = 0;
		foreach($list as $ele) {
			if($j != $i) {
				$dif = cmp_prod_v3($el,$ele);
				$list[$i][1][] = $dif;
				$somme_dif += $dif["dif"];
			}
			$j++;

		}
		$dif = $list[$i];
		usort($list[$i][1],function (array $a1, array $a2) { // up the dif = 0 
			return (($a1["dif"]==$a2["dif"]) ? 0 : (($a1["dif"]<$a2["dif"]) ? -1 : 1));	
		});
		$list[$i][0] = $somme_dif;
		$i++;
	}
	usort($list, function(array $a1, array $a2) {
		if($a1[0]==$a2[0]) {
			$i = 0; // same size !!
			$s1a1 = sizeof($a1[1]);
			while($i < $s1a1) {
				if($a1[1][$i]["dif"] !== $a2[1][$i]["dif"])
					return ($a1[1][$i]["dif"] < $a2[1][$i]["dif"]) ? -1 : 1;
				$i++;
			}
			return 0;
		}
		else 
			return ($a1[0]<$a2[0]) ? -1 : 1;
	});
	return $list;
}


function nb_ens(array $p1, array $p2) {

	echo "cmp \n";
	$r = cmp_prod_v3($p1,$p2)["common"];
	echo "r : $r\n";
	if(substr_count($p1['ens'],"|")>substr_count($p2['ens'],"|"))
		return 0;
	return $r;
	//return 1;
}

function sort_list(array $products) {
	$l = $products;
	$sort = array();
	$index = 0;
	foreach($l as $p) {
		$found = false;
		$index = 0;
		foreach($sort as $s) {
			if(($c = cmp_label_v2($p['name'],$s['name'])) !== "") {
				$found = TRUE;
				break;
			}
			$index++;
		}
		if($found === TRUE) {
			if(stristr($sort[$index]['ens'],$p['ens'])!==FALSE)
				continue;
			$sort[$index]['ens'] .= "|".$p['ens'];
		}
		else 
			$sort[] = $p;

	}
	//usort($sort,'nb_ens');
	return $sort;
}

function sort_list_v2(array $products) {
	$l = sort_list_cmp($products);
	$sort = array();
	$index = 0;
	foreach($l as $p) {
		$found = false;
		$index = 0;
		foreach($sort as $s) {
			foreach($s[1] as $sub) {
				echo "p_name:".$p["name"].",sub0:".$sub[0]."\n";
				if($p["name"]===$sub[0] || ($p["name"]===$sub[1])) {
					if($sub["common"]!=="") {
						$found = TRUE;
						echo "sub \n";
						var_dump($sub);
						break;
					}
				}
			}
			if($found == TRUE)
				break;
			/*if(($c = cmp_prod_v3($p,$s))["common"] !== "") {
				$found = TRUE;
				echo "found c \n";
				var_dump($c);
				break;
			}*/

			$index++;
		}
		if($found === TRUE) {
			if(stristr($sort[$index]['ens'],$p['ens'])!==FALSE)
				continue;
			$sort[$index]['ens'] .= "|".$p['ens'];
		}
		else 
			$sort[] = $p;

	}
	return $sort;
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
					$save_products[] = ["brand"=>$val['brand'],"name"=>$val['title'],"ens"=>$k,"prix"=>$val['price']['price']];
				}
			}
			if($k === "Leclerc") {
				foreach($v as $val) {
					$save_products[] = ["brand"=>$val['brand'],"name"=>$val['sLibelleLigne1']." ".$val['sLibelleLigne2'],"ens"=>$k,"prix"=>$val['nrPVUnitaireTTC']];
				}
			}
			if($k === "Monoprix") {
				foreach($v as $val) {
					$save_products[] = ["brand"=>$val['brand'],"name"=>$val['name'],"ens"=>$k,"prix"=>$val['price']['current']['amount']];
				}
			}
			if($k === "Auchan") {
				foreach($v as $val) {
					$save_products[] = ["brand"=>$val['brand'],"name"=>$val['label']." ".$val['quantity'],"ens"=>$k,"prix"=>$val['price']];
				}
			}
			if($k === "Intermarche") {
				foreach($v as $val) {
					$infos = $val["informations"];
					$save_products[] = ["brand"=>$infos['brand'],"name"=>$infos['title']." ".$infos["packaging"]." ".$infos["brand"],"ens"=>$k,"prix"=>$val['prices']['value']];
				}
			}
			if($k === "SystemeU") {
				foreach($v as $val) {
					$save_products[] = ["brand"=>$val['brand'],"name"=>$val['name'],"ens"=>$k,"prix"=>$val['price']];
				}
			}
		}

	}
	$all_brands = array();
	$products_by_brand = array();
	foreach($save_products as $elem) {
		if(!in_array($lower=strtolower($elem['brand']),$all_brands)) {
			$all_brands[] = $lower;
			$products_by_brand = array_merge($products_by_brand,[$lower=>[$elem['ens'],[$elem]]]);
		}
		else {
			$products_by_brand[$lower][1][] = $elem;
			if(stristr($products_by_brand[$lower][0],$elem['ens'])===FALSE) // REPLACE BY STR_CONTAINS IN PHP8.0
				$products_by_brand[$lower][0] .= "|".$elem['ens'];
		}

	}
	$comparable_ens = array();
	foreach($products_by_brand as $k=>$pb) {
		if(($nb_ens = substr_count($pb[0],"|"))>0)
			$comparable_ens = array_merge($comparable_ens,[$k=>$pb]);
	}

	$sort_list_p = array();
	foreach($comparable_ens as $k=>$pb) {
		$sort_list_p = array_merge($sort_list_p,[$k=>sort_list($pb[1])]);
	}
	var_dump($sort_list_p);
	//($sort_list_p,'cmp_prod_v3');
	return $sort_list_p;

}

function display_each_brand(array $comparable_brand) {
	$rtn = "";
	$all_brands = array_keys($comparable_brand);
	foreach($all_brands as $b) {
		echo "b : $b\n";
		foreach($comparable_brand[$b] as $product) { // [1] for comparable ens
			$rtn .= display_each_product_v2($product);
		}
	}
	return $rtn;
}

function display_each_product_v2(array $product) {
	$rtn = "<span>";
	//var_dump($product);
	foreach($product as $k=>$elem)  {
		if(!is_array($elem)) {
			echo "elem : $elem \n";
			$rtn .= "<b>$k:$elem</b>";
		}
		/*echo "b::<b>$k:$elem</b>\n";
		
		/*echo "here \n";
		var_dump($elem);*/
	}
	
	$rtn .= "</span>\n";
	return $rtn;
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
	$full_rtn = display_each_brand(create_cmp_product($to_display,$label));
	//var_dump(create_cmp_product($to_display,$label));
	return $full_rtn;
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
/*$l_leclerc = "lardons fumés herta 200g";
$l_monoprix = "Herta Lardons Fumés 200g";
$l_auchan = "HERTA Lardons fumés ";
$l_intermarche = "Lardons fumés La barquette de 200g  - Herta";
$l_carrefour = "Lardons fumés Herta 200g ";
$l_systemeu = ""; // ? soon 
//echo cmp_label($l_leclerc,$l_leclerc);
//my_json_encoding($argv);
//var_dump(main_($arr2));
$m_lflfsn = ["name"=>"herta lardons fumés sans nitrite - 2x75g-150g"];
$l_lfhsn =  ["name"=>"allumettes natures herta sans nitrite - 2x75g-150g"];
$m2_lflfsn = ["name"=>"herta allumettes fumées sans nitrite - 2x75g-150g"];
$l_haf =  ["name"=>"allumettes fumées herta 200g"];
$l_han = ["name"=>"herta allumettes natures 200g"];
$l2_haf = ["name"=>"herta allumettes fumées 200g"];
//echo cmp_label($m_hlfsn,$l_lfhsn);
//echo str_contains("coucou","cou");
//var_dump(stristr("Coucou","ww"));
$list = array($m_lflfsn,$l_haf,$l_lfhsn,$l2_haf,$l_han,$m2_lflfsn); // ORDER NO IMPACT

$l = sort_list_cmp($list);
//var_dump($l);
array_walk($l, function($obj) { echo $obj["name"]."\n"; }); // print specific item in a row 


$save_products = array();
$save_products[] = ["brand"=>"Herta","name"=>$m_lflfsn["name"],"ens"=>"Monoprix","prix"=>"1.5"];
$save_products[] = ["brand"=>"Herta","name"=>$l_haf["name"],"ens"=>"Monoprix","prix"=>"1.5"];
$save_products[] = ["brand"=>"Herta","name"=>$l2_haf["name"],"ens"=>"Leclerc","prix"=>"1.5"];
$save_products[] = ["brand"=>"Herta","name"=>$l_lfhsn["name"],"ens"=>"Monoprix","prix"=>"1.5"];
$save_products[] = ["brand"=>"Herta","name"=>$l_lfhsn["name"],"ens"=>"Leclerc","prix"=>"1.5"];
$save_products[] = ["brand"=>"Herta","name"=>$l_han["name"],"ens"=>"Monoprix","prix"=>"1.5"];
$save_products[] = ["brand"=>"Herta","name"=>$m2_lflfsn["name"],"ens"=>"Monoprix","prix"=>"1.5"];
$l_sp = sort_list_cmp($save_products);
//var_dump($l_sp);
array_walk($l, function($obj) { echo $obj["name"]."\n"; }); // print specific item in a row // OK 


echo "sort list v2\n";
$s_list2 = sort_list_v2($save_products);
array_walk($s_list2, function($obj) { echo "name:".$obj["name"]." ens:".$obj["ens"]."\n"; }); // print specific item in a row // OK 
//var_dump($n_list);
*/

/*echo "sort list origin\n";
$s_list1 = sort_list($save_products);
array_walk($s_list1, function($obj) {  echo "name:".$obj["name"]." ens:".$obj["ens"]."\n"; }); // print specific item in a row // OK */



?>