<?php  
require_once('infos_programs.php');
$version = "0.5";
$programs = ["scrapper.php","scrapper_leclerc.php","scrapper_carrefour.php",
				"scrapper_intermarche.php","scrapper_auchan.php"];

/**
 * [BRIEF]	[LOCAL_INFO_PRINTER]
 * @param	void
 * @example	print_info_local()
 * @author	chriSmile0
 * @return	void
*/
function print_info_local() {
	$retour = "[USAGE]\n 	php project.php --info ";
	foreach($GLOBALS['programs'] as $p) 
		$retour .= "\033[01;37m$p\033[0m|";
	echo $retour . "\n";
	echo "[END INFO LOCAL]\n";
}


/**
 * [BRIEF]	[INFO_PRINTER]
 * @example	print_info("") / print_info("scrapper.php")
 * @author	chriSmile0
 * @return	void
*/
function print_info(string $arg = "") {
	switch($arg) {
		case $GLOBALS['programs'][0]: 
				print_info_scrapper();
			break;
		case $GLOBALS['programs'][1]:
				print_info_scrapper_leclerc();
			break;
		case $GLOBALS['programs'][2]:
				print_info_scrapper_carrefour();
			break;
		case $GLOBALS['programs'][3]:
				print_info_scrapper_intermarche();
			break;
		case $GLOBALS['programs'][4]:
				print_info_scrapper_auchan();
			break;
		default: 
			print_info_local();
			break;
	}
}

/**
 * [BRIEF]	[HELP_PRINTER]
 * 			- --info	print info 
 * 			- --help	print this help
 * 			- --version print the program version (@see print_version)
 * 
 * @param	void
 * @author	chriSmile0
 * @return	void
*/
function print_help() {
	echo "\t --info \t \n";
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

/**
 * [BRIEF]	[MAIN_PROGRAM] 
 * @param	$argc	The number of parameter in the command line execution
 * @param	$argv	The parameters of the command line execution
 * @example	main($argc,"php7.2 project.php --help)
 * @author	chriSmile0
 * @return	bool 	1 if all is good, 0 if error in the command line or in the phase
 * 					test or if the scrapping failed 
*/
function main($argc, $argv) : bool {
	if($argc > 1) {
		switch($argv[1])  {
			case "--help": print_help();
				break;
			case "--info": print_info(($argc > 2) ? $argv[2] : "");
				break;
			case "--version": print_version();
				break;
			default:
				break;
		}
		return 1;
	}
	else {
		echo "ERROR : format : php ". $argv[0] . " [--help|--info|--version] \n";
		return 0;
	}
	echo "EXECUTION FINISH WITH SUCCESS \n";
	return 1;
}
main($argc,$argv);
?>