<?php 
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
 * @version    2.0
 * @link       https://github.com/chriSmile0/Scrapper/scrapper.php
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

namespace ChriSmile0\Scrapper;
use DOMElement;
use DOMNodeList;
use DOMXPath;
use DOMDocument;

$white_color = "0m";
$red_color = "31m";
$green_color = "32m"; 
$begin_color = "\033[01;";
$end_color = "\033[0m";
$text_in_color = "TEXT";
$build_color_text = $begin_color . $white_color . $text_in_color . $end_color;
$version = "2.0";

// FOR HTTPS : 
// Thanks to CopyProgramming

/**
 * [BRIEF] 	Check not null/empty/-1/false argument 
 * @param	$arg	
 * @example check(2<1)
 * @author 	chriSmile0
 * @return	int	0/1	if $arg is empty or false ->0 if is initialize or true ->1
 * @version 2.0
 */
function check($arg = NULL) : int {
	if($arg == NULL)
		return print_error("ERROR : empty arg");
	if($arg === "")
		return print_error("ERROR : empty arg");
	if(!$arg)
		return print_error("ERROR : empty arg");
	return 1;
}

/**
 * [BRIEF] 	Check if the $arg is in the list of $possible_args
 * @param	void	$arg			the arg to check
 * @param	array	$possible_arg	array of args 
 * @example check_args(2,[1,2,3])
 * @author 	chriSmile0
 * @return	int	0/1	if $arg is not in the array ->0 if is in the array ->1 
 * @version 1.0
 */
function check_args($arg , array $possible_args) : bool {
	foreach($possible_args as $p_a) 
		if($p_a == $arg)
			return true;
	$ar_to_str = implode('/',$possible_args);
	echo "ERROR : $arg not equal to $ar_to_str\n";
	return false;
}

/**
 * [BRIEF]	print an error message  
 * @param	string	$php_errormsg 
 * @example print_error("ERROR, usage : prog arg1 arg2")
 * @author	chriSmile0
 * @return	int	0
 */
function print_error(string $php_errormsg) : int {
	echo $php_errormsg . "\n";
	return 0;
}

/**
 * [BRIEF]	return the number of similar tag of the second param 
 * 			in the nodeList
 * @param	DOMNodeList $nodesList	the nodelist of the doc_xpath->query response
 * @param	string 		$tag 		the tag to analyze
 * @example	nb_tag_in_same_level($childs, "div")
 * @author	chriSmile0
 * @return	int the number of similar tag of $tag (include this tag)
*/
function nb_tag_in_same_level(DOMNodeList $nodesList, string $tag) : int  {
	$cpt = 0;
	foreach($nodesList as $node) 
		if($node->localName != NULL)
			if($node->tagName == $tag)
				$cpt++;
	return $cpt; 
}

/**
 * [BRIEF]	Return the alphanuricals content of a string
 * @param	string 	$str	the string to extract the alphanumericals content
 * @example	alphanumericals_of_str("\n HERE "\n")
 * @author	chriSmile0
 * @return	string "HERE" in example 
*/
function alphanumericals_of_str(string $str) : string {
	$i = 0;
	$len = strlen($str);
	
	while($i < $len && (!((ord($str[$i]) > 32 && ord($str[$i]) <= 127))))
		$i++;
	if($i != $len) {
		$debut = $i;
		while($i < $len && ((ord($str[$i]) > 32 && ord($str[$i]) <= 127)))
			$i++;
		$end = $i;
		$length_str = $end-$debut;
		$insert = substr($str,$debut,$length_str);
		$insert = substr($insert,0 ,$end);
		return $insert;
	}
	return "";
}

/**
 * [BRIEF]	A recursive function to explore the nodelist.
 * 			In doc_xpath->query the result is compose of many things.
 * 			But the interest is on the localName(ignore the "")
 * 			If the nodeList contain a child then 
 * 				search in each child if he had a child (recursion)
 * 				and add the path to the current path and in the
 * 				origin_node (recursion)
 * 				If this child has no content it's not necessary to 
 * 				continue (-> add the path and return)
 * 				
 * 				In the Xpath the div[@id] is use for determine the absolute
 * 				path, we analyze each child for create this xpath 
 * 				(-> @see nb_tag_in_same_level())
 * 
 * 				It's possible to certain tag contain another tag with content 
 * 				and content for example : <p>Bonjour, <b>You</b></p>
 * 				In this case we add Bonjour in the data of 'p' and 
 * 				the data of 'b' in the specific array for b
 * 				
 * 			Else (this child if the last child of the recursion) :
 * 				We add the content of this child
 * 
 * @param	DOMNodeList $nodesList		the nodelist of the doc_xpath->query
 * @param	string 		$origin_node	the begin of the exploration
 * @example	childs_path($childs, "/html/body/div")
 * @author	chriSmile0
 * @return	array of array (@example return):
 * 				- Array(Array(Array(data,path),path),path)
 * 				- Array([],path)
 * @version 2.0
*/
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
			else {
				if(($res = alphanumericals_of_str($child->textContent)) != "")
					$child_nametags += ["data"=>$res];
			}	
		}
	}
	if($origin_node != "") 
		$child_nametags += ["path"=>$origin_node];

	if($nb_child == 1) 
		if(!array_key_exists("data",$childs[0])) 
			if($childs[0]->textContent) // ->data first vesion -> bug with google
				$child_nametags += ["data"=>$childs[0]->textContent]; // ""
			
	return $child_nametags;	
}

/**
 * [BRIEF]	A recursive function to explore the child_path.
 * 			The usage of this function use childs_path function (@see childs_path)
 * 			If the array contain once array (parent have once child) then 
 * 				explore the child+add_path or add_path+return 
 * 			Else 
 * 				If the parent contain many childs then explore each child 
 * 				or just add path and return 
 * 
 * @param	$child_path				the array of childs_path function
 * @param	string	$origin_node	the begin of the exploration
 * @example	all_paths_v2($childs_path, "/html/body/div")
 * @author	chriSmile0
 * @return	array of string contain all paths of the $origin node
*/
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
			$paths .= "/" . $origin_node . "\n";
	}
	else {
		if($size_of_path > 2) {
			$keys = array_keys($child_path);
			$size_wo_path = $size_of_path-1;
			for($i = 0; $i < $size_wo_path; $i++) 
				$paths .= all_paths_v2($child_path[$keys[$i]],$origin_node. "/".$keys[$i]);
		}
		else {
			if($size_of_path != 0)
				$paths .= "/" . $origin_node ."\n";
		}
	}
	return $paths;
}

/**
 * [BRIEF]	A recursive function to explore the child_path.
 * 			The usage of this function use childs_path function (@see childs_path)
 * 
 * @param	array	$child_path		the array of childs_path function
 * @example	all_datas($child_path)
 * @author	chriSmile0
 * @return	array of string contain all datas of the $origin_node 
 *				(data = visible text in the website display)
*/
function all_datas(array $child_path) {
	$datas = "";
	$save = "";
	if(is_array($child_path)) 
		if((array_key_exists("data",$child_path)))
			if($child_path["data"] != " ")
				$save = "[:" . $child_path["data"] . ":]\n";

	foreach($child_path as $elem) 
		if(!is_string($elem)) 
			if((key($elem)) != "path") {
				$res = all_datas($elem);//. "\n";
				$datas .= ($res != " ") ? $res : "";
			}

	$datas .= $save;
	return $datas;
}

/**
 * [BRIEF]	A recursive function to explore the child_path.
 * 			The usage of this function use childs_path function (@see childs_path)
 * 			This function return the data with the associative path. 
 * 			(@see all_paths_v2 -> with the add of data -> l13 of the code) 
 * 
 * @param	$child_path			the array of childs_path function
 * @param 	string $origin_node	the begin of the exploration
 * @example	all_datas_with_paths_v2($child_path, "/html/body/div")
 * @author	chriSmile0
 * @return	array of string contain all paths with the associative content 
 * 				or just a all paths if not associative content 
*/
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
			$save .=  "[:" . $child_path["data"] . ":]\n";
		}
	}
	else {
		if($size_of_path > 2) {
			$minus = 1;
			if(array_key_exists("data",$child_path)) {
				$minus++;
				$paths .= "/" . $origin_node . "[:".$child_path["data"].":]\n";
			}
			$keys = array_keys($child_path);
			$size_wo_path = $size_of_path-$minus;
			for($i = 0; $i < $size_wo_path; $i++) {
				$res = all_datas_with_paths_v2($child_path[$keys[$i]],$origin_node. "/".$keys[$i]);
				$paths .= ($res != " ") ? $res."" : "";
			}
		}
		else {
			if($size_of_path != 0)
				$paths .= "/" . $origin_node . "\n";
		}
	}
	return $paths;
}

/**
 * [BRIEF]	Create an absolute path with the target_elem (child to parent , etc)
 * 
 * @param	DOMElement $target_elem	the target_elem 	
 * @example	complete_path("div")
 * @author	chriSmile0
 * @return	string	the path 
 * @?deprecated [UNTESTED_FUNCTION]
*/
function complete_path(DOMElement $target_elem = NULL) {
	if($target_elem->tagName == "html") 
		return "html";
	else 
		return complete_path($target_elem->parentNode) . "/" . $target_elem->tagName;
}

/**
 * [BRIEF]	If the $research_data is found in the document so the path
 * 			is return 
 * 
 * @param	string	$research data	the specific content do we search
 * @param 	array	$child_path		(@see childs_path)
 * @param	string 	$cmp_or_pos		choice exact comparaison or include
 * @example	complete_path_with_childs_path("Connexion",$child_path,"cmp") -> 1 path -> Google test 
 * @example complete_path_with_childs_path("i","$childs_path","pos") -> many path -> Google test 
 * @author	chriSmile0
 * @return	string	the path to reach the $research_data
*/
function complete_path_with_childs_path(string $research_data, array $child_path, string $cmp_or_pos) : string {
	$retour = "";
	$cur_path_ok = "";
	$exist_cur_path = (array_key_exists("path",$child_path));
	$cur_path_ok = ($exist_cur_path) ? $child_path["path"] : "";
	$c_o_p_lower = strtolower($cmp_or_pos);
	$cmp_test = (!strcmp($c_o_p_lower,"cmp"));
	$pos_test = (!strcmp($c_o_p_lower,"pos"));
	$c_o_p_lower_test = ($cmp_test ? true : ($pos_test ? true : false)); 
	if(!$c_o_p_lower_test)
		return print_error("ERROR : choice for third argument in {'cmp','pos'}");
	if(array_key_exists("data",$child_path)) {
		if($cmp_test) {
			if(strcmp($child_path["data"],$research_data)==0) 
				$retour = (array_key_exists("path",$child_path)) ? "/$cur_path_ok " : "";
		}
		else if($pos_test) {
			if(strval(strpos($child_path["data"],$research_data)) >= "0") 
				$retour = (array_key_exists("path",$child_path)) ? "/$cur_path_ok " : "";
		}
			
	}

	foreach($child_path as $elem) 
		if($elem) 
			if(!is_string($elem)) {
				$elem["path"] = $child_path["path"] . "/" . $elem["path"];
				if(($rtn = complete_path_with_childs_path($research_data,$elem,$cmp_or_pos))!= "") 
					$retour .=  $rtn ;
			}		
	
	return $retour ;
}


function choice_printable_infos(string $adds, $all_childs, string $query = "") : string {
	$retour = "";
	switch($adds) {
		case "paths":
			echo "**PATHS**\n";
			$retour = all_paths_v2($all_childs,$query);
			break;
		case "datas":
			echo "**DATAS**\n";
			$retour = all_datas($all_childs);
			break;
		case "pa_with_da":
			echo "**PATHS_WITH_DATAS**\n";
			$retour = all_datas_with_paths_v2($all_childs,$query);
			break;
		default:
			break;
	}
	return $retour;
}

/**
 * [BRIEF]	Research a specific content in a html file
 * 
 * @param	DOMXPath	$doc_xpath			the document in $doc_xpath
 * @param	string 		$query				the query
 * @param	string		$content_to_scrap	the content
 * @param	string		$cmp_or_pos			choice exact comparaison or include 
 * @example	content_to_scrap_html(NULL,"/html/body/div","Connexion")
 * @author	chriSmile0
 * @return	Array or bool if path is establish false if is not
*/
function content_scrap_html(DOMXPath $doc_xpath = NULL, string $query = "", 
							string $adds = "") {
	$checks = [check($doc_xpath),check($query),check_args($adds,["paths","datas","pa_with_da"])];
	foreach($checks as $check) 
		if($check == 0)
			return 0;

	$true_query = "/".$query; // CHECK IF THE QUERY IS A GOOD QUERY -> SOON 
	$doc_row = $doc_xpath->query($true_query);		
	$all_childs = childs_path($doc_row[0]->childNodes,$query);
	$retour = choice_printable_infos($adds,$all_childs,$query);

	if(!strcmp($retour,""))
		return array();
	return explode("//html",$retour);
}

function search_specific_information(DOMXPath $doc_xpath = NULL, string $query, 
										string $research = "", string $cmp_or_pos = "") {
	$checks = [check($doc_xpath),check($query),check($research),check_args($cmp_or_pos,["cmp","pos"])];
	foreach($checks as $check) 
		if($check == 0)
			return 0;
	
	$true_query = "/".$query; // CHECK IF THE QUERY IS A GOOD QUERY -> SOON 
	$doc_row = $doc_xpath->query($true_query);
	$all_childs = childs_path($doc_row[0]->childNodes,$query);
	$complete_path = complete_path_with_childs_path($research,$all_childs,$cmp_or_pos);
	if(!strcmp($complete_path,""))
		return array();
	return explode(" ",$complete_path);
}

/**
 * [BRIEF]	Create a stream context (with options) for create https context
 * 			to capture the file in the $url target
 * 
 * @param	string	$url		the url target
 * @param	bool 	$with_js 	if 
 * @example	scrap_https("https://www.google.com")
 * @author	chriSmile0
 * @return	string	the content of the file in the target url
*/
function scrap_https(string $url, bool $with_js) : string  {
	$output = "";
	if($with_js) {
		$url = escapeshellarg($url); // Replace with the target URL
		$nodeScriptPath = __DIR__.'/scrape.js';
		$output = shell_exec("node $nodeScriptPath $url");
	}
	else {
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
	}
	return $output;
}

/**
 * [BRIEF]	It's http not options and no context needed (for the moment)
 * 
 * @param	string	$url		the url target 
 * @param 	bool 	$with_js 	if we print the js renderer content
 * @example	scrap_http("http://example.com")
 * @author	chriSmile0
 * @return	string	the content of the file in the target url
*/
function scrap_http(string $url, bool $with_js) : string {
	$output = "";
	if($with_js) {
		$url = escapeshellarg($url); // Replace with the target URL
		$nodeScriptPath = __DIR__.'/scrape.js';
		$output = shell_exec("node $nodeScriptPath $url");
	}
	else {
		$output = file_get_contents($url);
	}
	return $output;
}

/**
 * [BRIEF]	[NOT IMPLEMENTED (-> for the moment)] -> 
 * @param	string	$protocol	the target protocol
 * @param	string 	$url		the target url
 * @example scrap_other("ftp", "ftp://example_ftp")
 * @author	chriSmile0
 * @return	string	the content of the file in the target url
*/
function scrap_other(string $protocol, string $url) : string  {
	return "";//FOR THE MOMENT
}

/**
 * [BRIEF]	Select a different scrap method for different protocol in target
 * 			url. Transform each scrap in html file 
 * 
 * @param	string	$url		the target url
 * @param 	bool 	$with_js 	if we print the js renderer content
 * @example	scrapping("https://www.google.com")
 * @author	chriSmile0
 * @return	
*/
function scrapping(string $url, string $with_js) {
	if(check_args($with_js,["true","false"]) == 0)
		return NULL;
	$protocol = parse_url($url, PHP_URL_SCHEME);
	$file = "";
	switch ($protocol) {
		case "https":
			$file = scrap_https($url,$with_js);
			break;
		case "http":
			$file = scrap_http($url,$with_js);
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

/**
 * [BRIEF]	[HELP_PRINTER]
 * 			- main 		print the command line for exec the main program	
 * 			- --test	launch the test procedure
 * 			- --help	print this help
 * 			- --version print the program version (@see print_version)
 * 
 * @param	string	$argv0	the program name 
 * @example	print_help("scrapper.php")
 * @author	chriSmile0
 * @return	void
*/
function print_help(string $argv0) {
	echo "\t --search \t command line : ". $argv0 . " --search [url] [query] [research] ['cmp'/'pos'] [with_js?] --with-openssl\n";
	echo "\t --content \t command line : ". $argv0 . " --content [url] [query] ['paths'/'datas'/'pa_with_da'] [with_js?] --with-openssl\n";
	echo "\t --test \t command line : ". $argv0 . " --test --with-openssl\n";
	echo "\t --help \t print this help\n";
	echo "\t --version \t print version\n";
}

/**
 * [BRIEF]	[VERSION_PRINTER]
 * @param	void
 * @example	print_version()
 * @author	chriSmile0
 * @return	void
*/
function print_version() {
	echo "Version of Scrapping program : ". $GLOBALS["version"] ."\n";
	echo "Copyright @-2024 [:chriSmile0:] \n";
}

$queries_array = [
	"/html/body/header",
	"/html/body",
	"/html/body"
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
		'res_c' => "Reservation",
		'res_p' => "tion"
	],
	[
		'url' => $urls_array[1],
		'query' => $queries_array[1],
		'res_c' => "Connexion",
		'res_p' => "i"
	],
	[
		'url' => $urls_array[2],
		'query' => $queries_array[2],
		'res_c' => "Bienvenue sur Wikipédia",
		'res_p' => "Bienvenue"
	]
];

/**
 * [BRIEF]	[TEST]
 * @param	string	$url		the target url	
 * @param	string	$query		the begin point of research in the capture file
 * @param	string 	$res		the waited result (not the path, the content)
 * @param	string 	$cmp_o_pos	choice exact comparaison or include
 * @param 	bool 	$with_js	if we print the js renderer content
 * @example	test("https://www.google.com","/html/body","Connexion")
 * @author	chriSmile0
 * @return	bool	
*/
function test(string $url, string $query, string $res, string $cmp_o_pos,
				string $with_js) : bool {
	return (!empty(search_specific_information(scrapping($url,$with_js),$query,$res,$cmp_o_pos)));
}

/**
 * [BRIEF]	[TESTS]	Launch the specific test with the array in param
 * @param	array	$res_test	the array of test 
 * @example	tests($result_test)
 * @author	chriSmile0
 * @return	bool 
*/
function tests(array $res_test) : bool {
	$all_tests_cmp = [
		test($res_test[0]['url'],$res_test[0]['query'],$res_test[0]['res_c'],"cmp",false),
		test($res_test[1]['url'],$res_test[1]['query'],$res_test[1]['res_c'],"cmp",false),
		test($res_test[2]['url'],$res_test[2]['query'],$res_test[2]['res_c'],"cmp",true)
	];
	$all_tests_pos = [
		test($res_test[0]['url'],$res_test[0]['query'],$res_test[0]['res_p'],"pos",true),
		test($res_test[1]['url'],$res_test[1]['query'],$res_test[1]['res_p'],"pos",false),
		test($res_test[2]['url'],$res_test[2]['query'],$res_test[2]['res_p'],"pos",true)//,
	];
	$cpt = 1;
	$cpt1 = 1;
	echo "EQUAL DATA TEST \n";
	foreach($all_tests_cmp as $res) {
		echo "\nTEST EQ n°$cpt1 : ". (($res == true) ? "\033[01;32m GOOD\033[0m" : "\033[01;31m BAD\033[0m") ." \n"; 
		$cpt1++;
		if($res == true)
			$cpt++;
	}
	$cpt2 = 1;
	echo "INCLUDE DATA TEST \n";
	foreach($all_tests_pos as $res) {
		echo "\nTEST INC n°$cpt2 : ". (($res == true) ? "\033[01;32m GOOD\033[0m" : "\033[01;31m BAD\033[0m") ." \n"; 
		$cpt2++;
		if($res == true)
			$cpt++;
	}
	return ($cpt == (sizeof($all_tests_cmp)+sizeof($all_tests_pos))); // 1 
}

/**
 * [BRIEF]	[TEST_PROCEDURE]
 * @param	void
 * @example	test_procedure()
 * @author	chriSmile0
 * @return	bool
*/
function test_procedure() : bool {
	echo "**TESTS** \n"; 
	$test_res = tests($GLOBALS["result_test"]);
	echo "TEST result : " . $test_res . "\n";
	return $test_res;
}

/**
 * [BRIEF]	The main procedure -> for include in other path 
 * @param	string	$url		the url
 * @param	string 	$query		the begin point of research in the capture file
 * @param	string	$research	the content to research
 * @param 	string 	$cmp_o_pos	choice exact comparaison or include
 * @param 	bool 	$with_js	if we print the js renderer content
 * @example sub_main("https://www.google.com","html/body/","Connexion","cmp",false)
 * @author	chriSmile0
 * @return	array of path
*/
function sub_main_search(string $url, string $query, string $research, 
					string $cmp_o_pos, string $with_js) {
	return search_specific_information(scrapping($url,$with_js),$query,$research,$cmp_o_pos);
}

/**
 * [BRIEF]	[MAIN_PROGRAM] -> for manuel execution
 * @param	$argc	The number of paramter in the command line execution
 * @param	$argv	The parameters of the command line execution
 * @example	main($argc,"php scrapper.php --test")
 * @author	chriSmile0
 * @return	bool 1 if all is good, 0 if error in the command line or in the phase
 * 				test or if the scrapping failed 
*/
function main($argc, $argv) : bool {
	$retour = 0;
	if($argc > 1) {
		switch($argv[1])  {
			case "--help": print_help($argv[0]);
				break;
			case "--test": return test_procedure();
				break;
			case "--version": print_version();
				break;
			case "--content":
				if($argc != 7) 
					return print_error("ERROR : ". $argv[0] . " --content [url] [query] ['paths'/'datas'/'pa_with_da'] [with_js?] --with-openssl");
				
				$retour = content_scrap_html(scrapping($argv[2],$argv[5]),$argv[3],$argv[4]);
				if($retour == 0)
					return 0;
				var_dump($retour);
				if(empty($retour)) 
					echo "NO CONTENT FOUND\n";
				break;
			case "--search": 
				if($argc != 8) 
					return print_error("ERROR : ". $argv[0] . " --search [url] [query] [research] ['cmp'/'pos'] [with_js?] --with-openssl");
				
				$retour = search_specific_information(scrapping($argv[2],$argv[6]),$argv[3],$argv[4],$argv[5]);
				if($retour == 0)
					return 0;
				var_dump($retour);
				if(empty($retour)) 
					echo "RESEARCH TARGET NOT FOUND\n";
				break;
			default:
				echo "**USAGE**\n";
				print_help($argv[0]);
				echo "**END USAGE**\n";
				return 0;
				break;
		}
	}
	else {
		return print_error("ERROR in arguments (@see --help\n");
	}
	echo "EXECUTION FINISH WITH SUCCESS \n";
	return 1;
}
//main($argc,$argv);
//sub_main_search($urls_array[3],$queries_array[3],"AUCHAN SOLIDAIRES","cmp",true);
//print_help("scrapper.php");

/**
 * [BRIEF]	
 * @param	
 * @example	
 * @author	
 * @return	
*/
?>