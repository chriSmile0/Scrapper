<?php 
// THIS RELEASE 
/**
 * [BRIEF]	[INFO_PRINTER_SCRAPPER]
 * @author	chriSmile0
 * @return	void
*/
function print_info_scrapper() {
	echo "[**BASIC SCRAPPER INFORMATIONS**]\n";
	$prog = "scrapper.php";
	echo "\t[BRIEF]	 
		It's use for scrap the data in the html content who 
		is display on the website (for example a h2 title section).
		Usage of docXpath is my move to travel the document 
		like an object (not parse the big string of source).
		
		Use this program for find not complexe form of information 
		It's work on http and https program like\033[01;37m google/wikipedia/_WithoutCookiesWebsite_\033[0m 
	
	[MAIN]
		php $prog [url] [query] --with-openssl

	[PARAMETERS]
		[URL] 	->	\033[01;37mhttps://example.com\033[0m
		[QUERY] ->	the start of research,\033[01;37m html/body\033[0m is obvious but if we known 
				the website it's possible to precise the path 
				maybe aftet a first scrape with the query\033[01;37m html/body/div/article \033[0mfor example   

	[OPTIONS]\n";
	echo "		php $prog --test --with-openssl\n";
	echo "		php $prog --help \t print $prog help\n";
	echo "		php $prog --version \t print $prog version\n\n";
	echo "[**END BASIC SCRAPPER INFORMATIONS**]\n";
}

/**
 * [BRIEF]	[INFO_PRINTER_SCRAPPER_LECLERC]
 * @author	chriSmile0
 * @return	void
*/
function print_info_scrapper_leclerc() {
	echo "[**LECLERC SCRAPPER INFORMATIONS**]\n";
	$prog = "scrapper_leclerc.php";
	echo "\t[BRIEF]	 
		It's use for scrap the data in the js CDATA array/json 

		The CDATA array contain many data per product but we extract many of
		these for our tool (the most useful (price,brand) for the moment
		(The all items is on the same URL (not necessary to go to another url)

		The result is an array of all products we are in the URL target
		(Not usage of the second parameters for the moment)
	
	[MAIN]
		php $prog [url] [research_product_type] --with-openssl

	[PARAMETERS]
		[URL] -> \033[01;37m https://fd7-courses.leclercdrive.fr/magasin-037301-037301-Voglans/recherche.aspx?TexteRecherche=lardons\033[0m
		[research_product_type] -> the product we search,\033[01;37m lardons/allumettes\033[0m\n\n";			
	echo "[**END LECLERC BASIC SCRAPPER INFORMATIONS**]\n";
}

/**
 * [BRIEF]	[INFO_PRINTER_SCRAPPER_CARREFOUR]
 * @author	chriSmile0
 * @return	void
*/
function print_info_scrapper_carrefour() {
	echo "[**CARREFOUR SCRAPPER INFORMATIONS**]\n";
	$prog = "scrapper_leclerc.php";
	echo "\t[BRIEF]	 
		It's use for scrap the data in the js we contain a very big
		json who contain very much informations on all products 

		For this tool we use \033[01;37m php-webdriver\033[0m to simulate a browser and  
		use the source code of the page for obtain data and parse these last

		It's important to known that website display 30 items per page
		and it's necessary to get again the next page in the browser for 
		obtain all datas on a specific url 

		The result is an array of all products we are in the URL target
		(Not usage of the second parameters for the moment)
	
	[MAIN]
		php $prog [url] [research_product_type] --with-openss

	[PARAMETERS]
		[URL] -> \033[01;37m https://www.carrefour.fr/s?q=lardons \033[0m
		[research_product_type] -> the product we search,\033[01;37m lardons/allumettes\033[0m\n\n";
	echo "[**END CARREFOUR SCRAPPER INFORMATIONS**]\n";
}

// NEXT RELEASE 
/**
 * [BRIEF]	[INFO_PRINTER_SCRAPPER_CARREFOUR]
 * @author	chriSmile0
 * @return	void
*/
function print_info_scrapper_auchan() {
	echo "[**AUCHAN SCRAPPER INFORMATIONS**]\n";



	echo "[**END AUCHAN BASIC SCRAPPER INFORMATIONS**]\n";
}

/**
 * [BRIEF]	[INFO_PRINTER_SCRAPPER_CARREFOUR]
 * @author	chriSmile0
 * @return	void
*/
function print_info_scrapper_lidl() {
	echo "[**LIDL SCRAPPER INFORMATIONS**]\n";


	echo "[**END LIDL BASIC SCRAPPER INFORMATIONS**]\n";
}

/**
 * [BRIEF]	[INFO_PRINTER_SCRAPPER_CARREFOUR]
 * @author	chriSmile0
 * @return	void
*/
function print_info_scrapper_systemeU() {
	echo "[**SYSTEME_U SCRAPPER INFORMATIONS**]\n";


	echo "[**END SYSTEME_U SCRAPPER INFORMATIONS**]\n";
}

/**
 * [BRIEF]	[INFO_PRINTER_SCRAPPER_CARREFOUR]
 * @author	chriSmile0
 * @return	void
*/
function print_info_scrapper_monoprix() {
	echo "[**MONOPRIX SCRAPPER INFORMATIONS**]\n";


	echo "[**END MONOPRIX SCRAPPER INFORMATIONS**]\n";
}

/**
 * [BRIEF]	[INFO_PRINTER_SCRAPPER_CARREFOUR]
 * @author	chriSmile0
 * @return	void
*/
function print_info_scrapper_intermarche() {
	echo "[**INTERMARCHE SCRAPPER INFORMATIONS**]\n";


	echo "[**END INTERMARCHE SCRAPPER INFORMATIONS**]\n";
}
?> 