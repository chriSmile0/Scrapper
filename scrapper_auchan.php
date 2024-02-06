<?php 
// URL1(NO_PRICE) 	= https://www.auchan.fr/charcuterie-traiteur-pain/charcuterie/lardons-des-eminces/lardons-allumettes-poitrine/ca-n12010401?redirect_keywords=lardons
// URL2(WITH_PRICE) = https://www.auchan.fr/charcuterie-traiteur-pain/charcuterie/lardons-des-eminces/lardons-allumettes-poitrine/ca-n12010401

use function Facebook\WebDriver\extract_source_carrefour;
use function Facebook\WebDriver\generate_driver;

$url1 = "https://www.auchan.fr/charcuterie-traiteur-pain/charcuterie/lardons-des-eminces/lardons-allumettes-poitrine/ca-n12010401?redirect_keywords=lardons";
$url2 = "https://www.auchan.fr/charcuterie-traiteur-pain/charcuterie/lardons-des-eminces/lardons-allumettes-poitrine/ca-n12010401";


function scrap_https(string $url, bool $with_js) : string  {
	$output = "";
	if($with_js) {
		$url = escapeshellarg($url); // Replace with the target URL
		$nodeScriptPath = 'scrape.js';
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





?>
