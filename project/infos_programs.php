<?php 
// THIS RELEASE 
/**
 * [BRIEF]	[INFO_PRINTER_SCRAPPER]
 * @param	void
 * @author	chriSmile0
 * @return	void
*/
function print_info_scrapper() {
	$rtn = "[**BASIC SCRAPPER INFORMATIONS**]\n";
	$prog = "scrapper.php";
	$rtn .= "\t[BRIEF]	 
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
	$rtn .= "		php $prog --test --with-openssl\n";
	$rtn .= "		php $prog --help \t print $prog help\n";
	$rtn .= "		php $prog --version \t print $prog version\n\n";
	$rtn .= "[**END BASIC SCRAPPER INFORMATIONS**]\n";
	return $rtn;
}

/**
 * [BRIEF]	[INFO_PRINTER_SCRAPPER_LECLERC]
 * @param	void
 * @author	chriSmile0
 * @return	void
*/
function print_info_scrapper_leclerc() {
	$rtn = "[**LECLERC SCRAPPER INFORMATIONS**]\n";
	$prog = "scrapper_leclerc.php";
	$rtn .= "\t[BRIEF]	 
		It's use for scrap the data in the js CDATA array/json 

		The CDATA array contain many data per product but we extract many of
		these for our tool (the most useful (price,brand) for the moment
		(The all items is on the same URL (not necessary to go to another url)

		The result is an array of all products we are in the URL target
		(Not usage of the second parameters for the moment)
	
	[MAIN]
		php $prog [research_product_type] --with-openssl

	[PARAMETERS]
		[research_product_type] -> the product we search,\033[01;37m lardons/allumettes\033[0m
		[city] -> \033[01;37m Paris/Lyon \033[0m\n\n";		
	$rtn .= "[**END LECLERC BASIC SCRAPPER INFORMATIONS**]\n";
	return $rtn;
}

/**
 * [BRIEF]	[INFO_PRINTER_SCRAPPER_CARREFOUR]
 * @param	void
 * @author	chriSmile0
 * @return	void
*/
function print_info_scrapper_carrefour() {
	$rtn = "[**CARREFOUR SCRAPPER INFORMATIONS**]\n";
	$prog = "scrapper_carrefour.php";
	$rtn .= "\t[BRIEF]	 
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
		php $prog [research_product_type] --with-openssl

	[PARAMETERS]
		[research_product_type] -> the product we search,\033[01;37m lardons/allumettes\033[0m\n\n";
	$rtn .= "[**END CARREFOUR SCRAPPER INFORMATIONS**]\n";
	return $rtn;
}

/**
 * [BRIEF]	[INFO_PRINTER_SCRAPPER_AUCHAN]
 * @param	void
 * @author	chriSmile0
 * @return	void
*/
function print_info_scrapper_auchan() {
	$rtn = "[**AUCHAN SCRAPPER INFORMATIONS**]\n";
	$prog = "scrapper_auchan.php";
	$rtn .= "\t[BRIEF]	 
		It's use for scrap the html content directly

		For this tool we use \033[01;37m php-webdriver\033[0m to simulate a browser and  
		use the source code of the page for obtain data and parse these last

		It's important to known that website display 14 items in first the
		page and 30 items in the next page of a research.
		It's necessary to get again the next page in the browser for 
		obtain all datas on a specific url. 

		The result is an array of all products we are in the URL target
		(Not usage of the second parameters for the moment)
	
	[MAIN]
		php $prog [research_product_type] [town] --with-openssl

	[PARAMETERS]
		[research_product_type] -> the product we search,\033[01;37m lardons/oeufs\033[0m
		[town] -> the research area \033[01;37mParis/Lyon\033[0m\n\n";

	$rtn .= "[**END AUCHAN BASIC SCRAPPER INFORMATIONS**]\n";
	return $rtn;
}

/**
 * [BRIEF]	[INFO_PRINTER_SCRAPPER_SYSTEMU]
 * @param	void
 * @author	chriSmile0
 * @return	void
*/
function print_info_scrapper_systemeu() {
	$rtn = "[**SYSTEME_U SCRAPPER INFORMATIONS**]\n";
	$prog = "scrapper_systemeu.php";
	$rtn .= "\t[BRIEF]	 
		It's use for scrap the data in the js array/json
		For scrap this set of data we use \033[01;37m puppeteer-extra-plugin-stealth\033[0m		

		This js/array contain many data per product but we extract many of
		these for our tool (the most useful (price,brand) for the moment.

		The result is an array of all products we are in the URL target
	
	[MAIN]
		php $prog  [research_product_type] [town] --with-openssl

	[PARAMETERS]
		[research_product_type] -> the product we search,\033[01;37m lardons/allumettes\033[0m
		[town] -> the research area \033[01;37mParis/Lyon\033[0m\n\n";	

	$rtn .= "[**END SYSTEME_U SCRAPPER INFORMATIONS**]\n";
	return $rtn;
}

/**
 * [BRIEF]	[INFO_PRINTER_SCRAPPER_MONOPRIX]
 * @param 	void
 * @author	chriSmile0
 * @return	void
*/
function print_info_scrapper_monoprix() {
	$rtn = "[**MONOPRIX SCRAPPER INFORMATIONS**]\n";
	$prog = "scrapper_monoprix.php";
	$rtn .= "\t[BRIEF]	 
		It's use for scrap the data in the js array/json
		For scrap this set of data we use \033[01;37m php-webdriver\033[0m or
		\033[01;37m puppeteer\033[0m		

		This js/array contain many data per product but we extract many of
		these for our tool (the most useful (price,brand) for the moment.
	
	[MAIN]
		php $prog [research_product_type] --with-openssl

	[PARAMETERS]
		[research_product_type] -> the product we search,\033[01;37m lardons/allumettes\033[0m\n\n";			

	$rtn .= "[**END MONOPRIX SCRAPPER INFORMATIONS**]\n";
	return $rtn;
}

/**
 * [BRIEF]	[INFO_PRINTER_SCRAPPER_INTERMARCHE]
 * @param	void
 * @author	chriSmile0
 * @return	void
*/
function print_info_scrapper_intermarche() {
	$rtn = "[**INTERMARCHE SCRAPPER INFORMATIONS**]\n";
	$prog = "scrapper_intermarche.php";
	$rtn .= "\t[BRIEF]	 
		It's use for scrap the data in the js we contain a very big
		json who contain very much informations on all products 

		For this tool we use \033[01;37m php-webdriver\033[0m to simulate a browser and  
		use the source code of the page for obtain data and parse these last

		It's important to known that website display 40 items per page
		and it's necessary to get again the next page in the browser for 
		obtain all datas on a specific url 

		The result is an array of all products we are in the URL target
		(Not usage of the second parameters for the moment)
	
	[MAIN]
		php $prog [research_product_type] [town] --with-openssl

	[PARAMETERS]
		[research_product_type] -> the product we search,\033[01;37m lardons/oeufs\033[0m
		[town] -> the research area  \033[01;37m Paris/Lyon\033[0m\n\n";

	$rtn .= "[**END INTERMARCHE SCRAPPER INFORMATIONS**]\n";
	return $rtn;
}
?> 