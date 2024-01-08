<?php 
// FOR HTTP
// Thanks to octoparse
// USE php7.2 scrapper.php --with-openssl
// VERSION -> 0.1 
/** C VERSION OF COLORS : 
 * #define KNRM  "\x1B[0m" -> default in terminal 
 * #define KRED  "\x1B[31m"
 * #define KGRN  "\x1B[32m"
 * #define KYEL  "\x1B[33m"
 * #define KBLU  "\x1B[34m"
 * #define KMAG  "\x1B[35m"
 * #define KCYN  "\x1B[36m"
 * #define KWHT  "\x1B[37m"
*/ 
$white_color = "0m";
$red_color = "31m";
$green_color = "32m"; 
$begin_color = "\033[01;";
$end_color = "\033[0m";
$text_in_color = "TEXT";
$build_color_text = $begin_color . $white_color . $text_in_color . $end_color;
$version = "0.1";

// FOR HTTPS : 
// Thanks to CopyProgramming

function check($arg = NULL) {
	if($arg == NULL)
		return 0;
	if($arg == "")
		return 0;
	return 1;
}

function print_error(string $php_errormsg) : int {
	echo $php_errormsg . "\n";
	return 0;
}

function nb_tag_in_same_level(DOMNodeList $nodesList, string $tag) {
	$cpt = 0;
	foreach($nodesList as $node) 
		if($node->localName != NULL)
			if($node->tagName == $tag)
				$cpt++;
	return $cpt; 
}

function childs_path(DOMNodeList $nodesList, string $origin_node) {
	$nb_child = $nodesList->length;
	$childs = $nodesList;
	$child_nametags = [];
	$same_tag = array();
	if($nb_child > 1) {
		foreach($childs as $child) {
			if($child->localName != "") {
				$res = $child->tagName;
				$child_of_tag = nb_tag_in_same_level($childs,$child->tagName);
				if($res) {
					if(array_key_exists($res,$same_tag))
						$same_tag[$res][1]++;
					else
						$same_tag = array_merge($same_tag,[$res=>[$child_of_tag,0]]);
				}
				
				if($child_of_tag > 1) {
					$val = strval($same_tag[$res][1]+1);
					$res .= ($same_tag[$res][0] > 1) ? "[$val]" : "";
				}
				$add = childs_path($child->childNodes,"");
				if(!empty($add)) {
					$child_nametags = array_merge($child_nametags,[$res=>$add]);
					if(!array_key_exists("path",$child_nametags)) 
						$child_nametags[$res] = array_merge($child_nametags[$res],["path"=>$res]);
				}
				else {
					$child_nametags = array_merge($child_nametags,[$res=>[]]);
					if(!array_key_exists("path",$child_nametags)) 
						$child_nametags[$res] = array_merge($child_nametags[$res],["path"=>$res]);
					
				}
			}
		}
	}
	if($origin_node != "") 
		$child_nametags += ["path"=>$origin_node];

	if($nb_child == 1) {
		if(!array_key_exists("data",$childs[0])) {
			if($childs[0]->textContent) // ->data first vesion -> bug with google
				$child_nametags += ["data"=>$childs[0]->textContent]; // ""
		}
	}
	return $child_nametags;	
}

function all_paths_v2($child_path, string $origin_node) {
	$paths = "";
	$size_of_path = 0;
	if(is_array($child_path)) 
		$size_of_path = sizeof($child_path);
	if($size_of_path == 2) {
		$key = "".array_keys($child_path)[0];
		if($key != "data") 
			$paths .= all_paths_v2($child_path[$key],$origin_node."/".$key);
		else 
			$paths .= "/" . $origin_node;
	}
	else {
		if($size_of_path > 2) {
			$keys = array_keys($child_path);
			$size_wo_path = $size_of_path-1;
			for($i = 0; $i < $size_wo_path; $i++) 
				$paths .= all_paths_v2($child_path[$keys[$i]],$origin_node. "/".$keys[$i]) . "\n";
		}
		else {
			if($size_of_path != 0)
				$paths .= "/" . $origin_node;
		}
	}
	return $paths;
}

function all_datas(array $child_path) {
	$datas = "";
	$save = "";
	if(is_array($child_path)) 
		if((array_key_exists("data",$child_path)))
			if($child_path["data"] != " ")
				$save = "[:" . $child_path["data"] . ":]";

	foreach($child_path as $elem) 
		if(!is_string($elem)) 
			if((key($elem)) != "path") 
				$datas .= all_datas($elem) . "\n";

	$datas .= $save;
	return $datas;
}

function all_datas_with_paths_v2($child_path, string $origin_node) {
	$paths = "";
	$size_of_path = 0;
	if(is_array($child_path)) 
		$size_of_path = sizeof($child_path);
	if($size_of_path == 2) {
		$save = "";
		$key = "".array_keys($child_path)[0];
		if($key != "data") {
			$paths .= all_datas_with_paths_v2($child_path[$key],$origin_node."/".$key);
		}
		else {
			$save .= "/" . $origin_node;
			$save .=  "[:" . $child_path["data"] . ":]";
			$paths .= $save;
		}
	}
	else {
		if($size_of_path > 2) {
			$keys = array_keys($child_path);
			$size_wo_path = $size_of_path-1;
			for($i = 0; $i < $size_wo_path; $i++) 
				$paths .= all_datas_with_paths_v2($child_path[$keys[$i]],$origin_node. "/".$keys[$i]) . "\n";
		}
		else {
			if($size_of_path != 0)
				$paths .= "/" . $origin_node;
		}
	}
	return $paths;
}

function complete_path(DOMElement $target_elem = NULL) {
	if($target_elem->tagName == "html") 
		return "html";
	else 
		return complete_path($target_elem->parentNode) . "/" . $target_elem->tagName;
}

function complete_path_with_childs_path(string $research_data, array $child_path) {
	$retour = "";
	$cur_path_ok = "";
	$exist_cur_path = (array_key_exists("path",$child_path));
	$cur_path_ok = ($exist_cur_path) ? $child_path["path"] : "";
	if(array_key_exists("data",$child_path)) 
		if(strcmp($child_path["data"],$research_data)==0) 
			$retour = (array_key_exists("path",$child_path)) ? "/$cur_path_ok" : "";

	foreach($child_path as $elem) 
		if($elem) 
			if(!is_string($elem)) 
				if(($rtn = complete_path_with_childs_path($research_data,$elem)) != "") 
					return "/" . $cur_path_ok . $rtn ;
	
	return $retour;
}

function content_scrap_html(DOMXPath $doc_xpath = NULL, string $query = "", 
							string $content_to_scrap = "") : bool {
	$checks = [check($doc_xpath),check($query),check($content_to_scrap)];
	$errors = ["doc_xpath empty","empty query","nothing to scrap"];
	$index = 0;
	foreach($checks as $check) {
		if($check == 0)
			return print_error($errors[$index]);
		$index++;
	}
	// FIRST ->  SEARCH IN ALL DOCUMENT 
	$true_query = "/".$query; // CHECK IF THE QUERY IS A GOOD QUERY -> SOON 
	$doc_row = $doc_xpath->query($true_query);
	$all_childs = childs_path($doc_row[0]->childNodes,$query);
	$complete_path = complete_path_with_childs_path($content_to_scrap,$all_childs);
	// - echo "paths :* \n" . all_paths_v2($all_childs,$query);
	// - echo "datas :* \n". all_datas($all_childs) . "\n";
	// - echo "paths_and_datas :* \n" . all_datas_with_paths_v2($all_childs,$query);

	// SECOND -> ADAPTATION WITH THE COMPLETE PATH
	echo "path of research :$complete_path \n";
	return ($complete_path != "");
}

function scrap_https(string $url)  {
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
	return file_get_contents($url, false,$context);
}

function scrap_http(string $url) {
	return file_get_contents($url);
}

function scrap_other(string $protocol, string $url) {
	return NULL;//FOR THE MOMENT
}

function scrapping(string $url) {
	$protocol = parse_url($url, PHP_URL_SCHEME);
	$file = "";
	switch ($protocol) {
		case "https":
			$file = scrap_https($url);
			break;
		case "http":
			$file = scrap_http($url);
			break;
		default;
			$file = scrap_other($protocol,$url);
			break;
	}
	$doc = new DOMDocument();
	libxml_use_internal_errors(TRUE);
	if(!empty($file)) {
		$doc->loadHTML($file);
		libxml_clear_errors();
		return new DOMXPath($doc);
	}
	return NULL;
}

function print_help(string $argv0) {
	echo "\t   main \t command line : ". $argv0 . " [url] [query] --with-openssl\n";
	echo "\t --test \t command line : ". $argv0 . " --test --with-openssl\n";
	echo "\t --help \t print this help\n";
	echo "\t --version \t print version\n";
}

function print_version() {
	echo "Version of Scrapping program : ". $GLOBALS["version"] ."\n";
	echo "Copyright @-2024 [:chriSmile0:] \n";
}

$queries_array = [
	"/html/body/header",
	"/html/body",
	"/html/body/div[1]"
];

$urls_array = [
	"http://localhost/projet_pw2",
	"https://www.google.com",
	"https://fr.wikipedia.org/wiki/Wikipédia:Accueil_principal"
];

$result_test = [
	[
		'url' => $urls_array[0],
		'query' => $queries_array[0],
		'res' => "Reservation"
	],
	[
		'url' => $urls_array[1],
		'query' => $queries_array[1],
		'res' => "Connexion"
	],
	[
		'url' => $urls_array[2],
		'query' => $queries_array[2],
		'res' => "Bienvenue sur Wikipédia"
	]
];

function test(string $url, string $query, string $res) : bool {
	return (content_scrap_html(scrapping($url),$query,$res));
}

function tests(array $res_test) : bool {
	$all_tests = [
		test($res_test[0]['url'],$res_test[0]['query'],$res_test[0]['res']),
		test($res_test[1]['url'],$res_test[1]['query'],$res_test[1]['res']),
		test($res_test[2]['url'],$res_test[2]['query'],$res_test[2]['res'])
	];
	$cpt = 1;
	foreach($all_tests as $res) {
		echo "\nTEST n°$cpt : ". (($res == true) ? "\033[01;32m GOOD\033[0m" : "\033[01;31m BAD\033[0m") ." \n"; 
		$cpt++;
		if($res == false)
			return false;
	}
	return true; // 1 
}

function test_procedure() {
	echo "**TESTS** \n"; 
	echo "TEST result : " . tests($GLOBALS["result_test"]) . "\n";
}

function main($argc, $argv) {
	if($argc == 4) 
		return content_scrap_html(scrapping($argv[1]),$argv[2]);
	else {
		if($argc > 1) 
			switch($argv[1])  {
				case "--help": print_help($argv[0]);
					break;
				case "--test": test_procedure();
					break;
				case "--version": print_version();
					break;
				default:
					break;
			}
		else 
			echo "ERROR : format : ". $argv[0] . " [url] [query] --with-openssl\n";
	}
	return 1;
}
main($argc,$argv);
?>