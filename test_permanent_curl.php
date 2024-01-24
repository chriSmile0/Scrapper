<?php

echo "IT'S CURL TIME !!\n";


$url = "https://www.carrefour.fr";
/* 
// Initialize a CURL session.
$ch = curl_init(); 
 
// Return Page contents.
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
 
//grab URL and pass it to the variable.
curl_setopt($ch, CURLOPT_URL, $url);
 
$result = curl_exec($ch);*/

/*
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTP_VERSION,CURL_HTTP_VERSION_2_0);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_UPKEEP_INTERVAL_MS, 200);
if (curl_exec($ch)) {
    usleep(300);
    var_dump(curl_upkeep($ch));
}
curl_close($ch);*/
 
#echo $result; 

// error reporting
/*error_reporting(E_ALL);
ini_set("display_errors", 1);

//setting url



try {
    $ch = curl_init($url);

    if (FALSE === $ch)
        throw new Exception('failed to initialize');

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json', 'Content-Length: ' . strlen($data_string)));
        curl_setopt($ch, CURLOPT_TIMEOUT, 5000);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5000);

        $output = curl_exec($ch);

    if (FALSE === $output)
        throw new Exception(curl_error($ch), curl_errno($ch));

    // ...process $output now
} catch(Exception $e) {

    trigger_error(sprintf(
        'Curl failed with error #%d: %s',
        $e->getCode(), $e->getMessage()),
        E_USER_ERROR);
}

*/

/*
function connect($url) {
 
    $cookies_file = __DIR__.'/cookies.txt';
 
    $url_connection = $url;
    $timeout = 1000;
 
    $ch = curl_init();
 
    //Initialise une nouvelle connexion
    curl_setopt($ch, CURLOPT_URL, $url_connection);
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
 
    //Initialise les cookies
    curl_setopt($ch, CURLOPT_COOKIESESSION, true);
 
 
    // Sauvgarde de la session dans un cookie
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookies_file);
    // Lecture du cookie enregistré
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookies_file);
 
    curl_exec($ch);
 
    // Session ouverte et connexion établie
 
 
    curl_setopt($ch, CURLOPT_URL, $url);
 
    $page_content = curl_exec($ch);
 
    curl_close($ch);
 
 
    return $page_content;
}
 
$onglet = 'construction';
$page = connect($url);
 
echo $page;*/

?>