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
 * @package    scrapper_auchan.php
 * @author     chrisSmile0
 * @copyright  2024 -> @author
 * @license    [NO_LICENSE]
 * @version    1.0
 * @link       https://github.com/chriSmile0/Scrapper/scrapper_auchan.php
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

// URL1 = https://www.auchan.fr/
namespace Facebook\WebDriver;
namespace ChriSmile0\Scrapper;
use Exception;
use Facebook\WebDriver\Firefox\FirefoxOptions as FirefoxOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities as DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver as RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy as WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition as WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverKeys as WebDriverKeys;
require __DIR__ . '/../../../autoload.php'; // EXPORT 
//require __DIR__ . '/../vendor/autoload.php'; // DEV


function extract_brand_a(string $label) : string {
	preg_match('/[A-Z|&|,| ]+/',$label,$matches);
	preg_match_all('/[A-Z][A-Z]+/',$matches[0],$matches2);
	return implode(" ",$matches2[0]);
}

function change_quantity_a(string $libelle) : string  { 
	preg_match_all('!\d+(?:\.\d{1,2})?!', $libelle, $matches);
	$size_m = sizeof($matches[0]);
	if($size_m == 1) 
		return $matches[0][0]."g";
	else if($size_m == 2)
		return " - " . $matches[0][0]."x".$matches[0][1]."g"."-".($matches[0][0]*$matches[0][1])."g";
	else 
		return $libelle;

}

/**
 * [BRIEF]	generate an instance of a firefox driver with 'geckodriver' server
 * 				(localhost:4444)
 * @param 	int $p	port
 * @example	generate_driver_a()
 * @author	chriSmile0
 * @return	/
*/
function generate_driver_a(int $p) {
	//-----------------Remote with geckodriver in terminal--------------------// 
	$host = 'http://localhost:'.$p.'/';

	$capabilities = DesiredCapabilities::firefox();
	$firefoxOptions = new FirefoxOptions;
	$firefoxOptions->addArguments(['--headless']);
	$capabilities->setCapability(FirefoxOptions::CAPABILITY, $firefoxOptions);
	try {
		return RemoteWebDriver::create($host,$capabilities);
	}
	catch (Exception $e) {
		echo "ERRRRRR_REMOTE : ".$e->getMessage()."\n";
		return NULL;
	}
}

/**
 * [BRIEF]	Find element function simplification
 * 
 * @param 			$driver	the driver create by WebDriver()
 * @param	string	$type	id/tagname/classname/xpath
 * @param	string	$path	the path or the id or the classname of the tagname
 * @param	string	$error	the recently error in the call stack
 * @param 	string 	$type_2	optional	presence/visibility
 * @example	findElement($driver,"id","my_id","");
 * @author	chriSmile0
 * @return 	array 	[$elem(the element found),$error(the generate error)]
*/
function findElement_a($driver, string $type, string $path, string $error, string $type_2 = "presence") : array {
	$elem = "";
	sleep(1);
	if($type_2 !== "presence" && $type_2 !== "visibility")
		return [$elem,"'presence' or 'visibility' for type_2 argument"];
	if($error === "") {
		switch($type) {
			case 'id':
				try {
					if($type_2 === "presence")
						$driver->wait(1)->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::id($path))); // this or sleep 
					else 
						$driver->wait(1)->until(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id($path)));
					$elem = $driver->findElement(WebDriverBy::id($path));
				}
				catch (Exception $e) {
					$error = $e->getMessage();
				}
				break;
			case 'tag':
				try {
					if($type_2 === "presence")
						$driver->wait(1)->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::tagName($path))); // this or sleep 
					else 
						$driver->wait(1)->until(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::tagName($path)));
					$elem = $driver->findElement(WebDriverBy::tagName($path));
				}
				catch (Exception $e) {
					$error = $e->getMessage();
				}
				break;
			case 'class':
				try {
					if($type_2 === "presence")
						$driver->wait(1)->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::className($path))); // this or sleep 
					else 
						$driver->wait(1)->until(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::className($path)));
					$elem = $driver->findElement(WebDriverBy::className($path));
				}
				catch (Exception $e) {
					$error = $e->getMessage();
				}
				break;
			case 'xpath';
				try { 
					if($type_2 === "presence")
						$driver->wait(1)->until(WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::xpath($path))); // this or sleep 
					else 
						$driver->wait(1)->until(WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::xpath($path)));
					$elem = $driver->findElement(WebDriverBy::xpath($path));
				}
				catch (Exception $e) {
					$error = $e->getMessage();
				}
				break;
			default:
				$elem = NULL;
				$error = "id/tag/class/xpath";
				break;
		}
	}
	return [$elem,$error];
}

/**
 * [BRIEF]	Transform the getText return of element in an array with
 * 			associative element
 * @param	string 	The element text
 * @param	array	the associative_list ["price","label",etc]
 * @param	string	the separator element ("\n"," ")
 * @example	getTextToAssociativeArra
 * @author	chriSmile0
 * @return	array	the associative array
*/
function text_to_associative_array(string $get_text, array $associative_list, 
									string $separator) : array {
	$rtn = array();
	if($get_text === "") 
		return $rtn;
	$text_to_explode = explode($separator,$get_text);
	$h = 0;
	$dep = 0;
	foreach($text_to_explode as $e) {
		if((($s = strpos($e,"("))!==FALSE) && ($s == 0)) 
			$dep = $h+1; // OR SEARCH BRAND IN LABEL ? 
		if(strpos($e," / kg"))
			break;
		$h++;
	}
	$text_to_explode[$h-1] = change_quantity_a($text_to_explode[$h-1]);
	if($dep == 0) 
		if($text_to_explode[0]==="Nouveauté" || ($text_to_explode[0]=="Prix promo")
			|| (str_contains($text_to_explode[0],"supp au panier")))
			$dep=1;
	
	
	if((($size = sizeof($text_to_explode)) > 1) && ($size > 5)) {
		$rtn = [$associative_list[2]=>$text_to_explode[$dep],
					$associative_list[3]=>extract_brand_a($text_to_explode[$dep]),
					$associative_list[4]=>$text_to_explode[$h-1],
					$associative_list[9]=>substr($text_to_explode[$size-1],0,strpos($text_to_explode[$size-1],"€"))
		];
		return $rtn;
	} // $size == 5n 'Afficher le Prix' is present' -> products dont load correctly
	return $rtn;
}


/**
 * [BRIEF]	(@see extract_needed_information_pro) but for all products	
 * @param	/	$prods	The html element which contain all products	
 * @param 	int $nb_prods (yes not all ;-)
 * @example extract_info_for_all_products($tab_json, [totalPage,currentPage])
 * @author	chriSmile0
 * @return	array	array with the data with want to store/share/print for all 
 * 					products
*/
function extract_info_for_all_products_a($prods,int $nb_prods) : array {
	$rtn = array();

	$associative_list = [
		"info",
		"reviewCount",
		"label",
		"brand",
		"quantity",
		"pricePerKg",
		"promoOffer",
		"seeOffer",
		"dealMode",
		"price"
	];
	for($i = 0; $i < $nb_prods ; $i++) 
		if(!empty($res = text_to_associative_array(($prods[$i]!=NULL) ? $prods[$i]->getText() : "",$associative_list,"\n")))
			$rtn[] = $res;
	

	return $rtn;
}

/**
 * [BRIEF]	simulate the url get in the browser and return the display content
 * 			with the first products and the $driver with the change state
 * @param	string	$url	the url to get in the browser
 * @param 	/		$driver	the driver instance
 * @param 	string	$town 	the city in the research area
 * @param	string	$target	the product we want to research
 * @example	extract_source_auchan((@see URL1),$driver,"Paris","lardons")
 * @author	chriSmile0
 * @return	array	[$driver(the driver),sourceCode(the source code in string),
 * 						$firstProducts->array of the first product]
*/
function extract_source_auchan(string $url,$driver, string $town, string $target) : array {
	$src = "";
	$error = "";
	if($driver !== NULL) {
		try {
			$cpt = 1;
			$driver->get($url); 
			while((count($driver->findElements(WebDriverBy::className('editorial__block-title'))) != 0) && ($cpt < 5)) {
				$cpt++;
				sleep(1); // NO DDOS HERE :-)
				$driver->get($url);
			}
			if($cpt >= 5) {
				$driver->quit();
				return "";
			}
			$res_find = array("","");
			$res_find = findElement_a($driver,"id","onetrust-reject-all-handler",$res_find[1]); // click option
			if($res_find[0]!=="") $res_find[0]->click();

			echo "AUCHA1:".$res_find[1]."\n";
			$res_find = findElement_a($driver,"class","header-search__input",$res_find[1]);
			if($res_find[0]!=="") {
				$res_find[0]->sendKeys($target);
				$res_find[0]->sendKeys(WebDriverKeys::ENTER);
			}
			echo "AUCHA2:".$res_find[1]."\n";
			sleep(5);
			$res_find =  findElement_a($driver,"xpath","/html/body/div[3]/div[2]/div[2]/div[4]/article[1]/div[2]/footer/button",$res_find[1]); // click option
			if($res_find[0]!=="") {
				try {
					sleep(1); // for the moment 
					$driver->executeScript("document.getElementsByClassName('product-thumbnail__see-prices-button')[0].click();");
				}
				catch(Exception $e) {
					$res_find[1] = $e->getMessage();
				}
			}
			echo "AUCHA3:".$res_find[1]."\n";
			$res_find = findElement_a($driver,"xpath","/html/body/div[13]/div[1]/main/div[1]/div[1]/div/div[1]/input",$res_find[1]);
			if($res_find[0]!=="") $res_find[0]->sendKeys($town);

			echo "AUCHA4:".$res_find[1]."\n";
			sleep(2);
			$res_find =  findElement_a($driver,"class","journey__search-suggests-list",$res_find[1]); // click option
			if($res_find[0]!=="") {
				try {
					sleep(1); // for the moment 
					$driver->executeScript("(document.getElementsByClassName('journey__search-suggests-list')[0]).childNodes[0].click()");
				}
				catch(Exception $e) {
					$res_find[1] = $e->getMessage();
				}
			}
			echo "AUCHA5:".$res_find[1]."\n";
			$res_find = findElement_a($driver,"xpath","/html/body/div[13]/div[1]/main/div[1]/div[2]/div[2]/section/div[1]/div/div/div[2]/form/button",$res_find[1]);
			if($res_find[0]!=="") $res_find[0]->submit();
			
			sleep(4);
			echo "AUCHA6:".$res_find[1]."\n";
			$driver->executeScript('window.scrollTo(0,200);');
			$error = $res_find[1];
			$src = $driver->getPageSource();
		}
		catch (Exception $e) {
			$error = $e->getMessage();
		}
	}
	if($error !== "") {
		$driver->quit();
		return array();
	}
	return [$driver,$src];
}

/**
 * [BRIEF]	The main procedure -> for include in other path 
 * 
 * @param	string 	$target_product	the target product
 * @param	string 	$town 			the research area
 * @param 	int 	$p				port
 * @example content_scrap_auchan((@see URL1),"lardons","Paris")
 * @author	chriSmile0
 * @return	array 	array of all product with specific information that we needed
*/
function content_scrap_auchan(string $target_product, string $town, int $p) : array {
	$url = "https://www.auchan.fr/";
	$driver = generate_driver_a($p);
	if($driver === NULL) 
		return array();
	
	$file_content_and_prods = extract_source_auchan($url,$driver,$town,$target_product);
	if(!empty($file_content_and_prods)) {
		$driver = $file_content_and_prods[0];
		$file_content = $file_content_and_prods[1];
		$prods = array();
		$config_mark = "G.configuration.searchPages.trackingObject = ";
		
		$sub = substr($file_content,strpos($file_content,$config_mark)+strlen($config_mark));
		$end = strpos($sub,"};");
		$infos = substr($sub,0,$end);
		$infos_page = substr($infos,0,strpos($infos,"},")) . "}}";
		$pages = json_decode($infos_page,true)["page"];
		$nb_page = $pages["numberOfPages"];
		$cur_page = $pages["currentPage"];
		for($i = $cur_page ; $i < $nb_page+1; $i++) {
			$src_ = $driver->getPageSource();
			$config_mark_2 = "G.configuration.searchPages.trackingObject = ";
			$sub_ = substr($src_,strpos($src_,$config_mark_2)+strlen($config_mark_2));
			$end_ = strpos($sub_,"};");
			$infos_ = substr($sub_,0,$end_);
			$infos_page = substr($infos_,0,strpos($infos_,"},")) . "}}";
			$pages_ = json_decode($infos_page,true)["page"];

			$off_total = $pages_["total"]-$pages_["offset"];
			$nb_prod = 0;
			if($off_total <= $pages_["limit"])
				$nb_prod = $off_total;
			else 
				$nb_prod = $pages_["limit"];
			$produits = $driver->findElements(WebDriverBy::xpath('/html/body/div[3]/div[2]/div[2]/div[4]/article'));
			$prods = array_merge($prods,extract_info_for_all_products_a($produits,$nb_prod));
			if($i < $nb_page) {
				$driver->findElements(WebDriverBy::className('next'))[0]->click();
				sleep(1);
			}
		}
		$driver->manage()->deleteAllCookies();
		$driver->quit();
		return $prods;
	}
	return array();
}

/**
 * [BRIEF]	[MAIN_PROGRAM] -> for manuel execution
 * @param	$argc	The number of parameter in the command line execution
 * @param	$argv	The parameters of the command line execution
 * @example	main($argc,"php7.2 scrapper_auchan.php (@see URL1) lardons Paris")
 * @author	chriSmile0
 * @return	bool 	1 if all is good, 0 if error in the command line or in the phase
 * 					test or if the scrapping failed 
*/
function main_a($argc, $argv) : bool {
	if($argc == 5) {
		if(empty(content_scrap_auchan($argv[1],$argv[2],$argv[3]))) {
			echo "NO CORRESPONDENCE FOUND \n";
			return 0;
		}
		return 1;
	}
	else {
		echo "ERROR : format : ". $argv[0] . "[research_product_type] [town] [port] --with-openssl\n";
		return 0;
	}
	echo "EXECUTION FINISH WITH SUCCESS \n";
	return 1;
}

//main_a($argc,$argv);
//var_dump(content_scrap_auchan("https://www.auchan.fr/","Lardons","Paris"));
/**
 * [BRIEF]	
 * @param	
 * @example
 * @author chriSmile0
 * @return 
*/
?>