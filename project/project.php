<?php  
require_once('infos_programs.php');
$version = "1.5";
$programsv2 = [
	"scrapper"=>print_info_scrapper(),"leclerc"=>print_info_scrapper_leclerc(),
	"carrefour"=>print_info_scrapper_carrefour(),"intermarche"=>print_info_scrapper_intermarche(),
	"auchan"=>print_info_scrapper_auchan(),"monoprix"=>print_info_scrapper_monoprix(),
	"systemeu"=>print_info_scrapper_systemeu()
];

/**
 * [BRIEF]	[LOCAL_INFO_PRINTER]
 * @param	void
 * @example	print_info_local()
 * @author	chriSmile0
 * @return	void
*/
function print_info_local() {
	$retour = "[USAGE]\n 	php project.php --info ";
	foreach($GLOBALS['programsv2'] as $k=>$p) 
		$retour .= "\033[01;37m$k\033[0m|";
	echo $retour . "\n";
	echo "[END INFO LOCAL]\n";
}

/**
 * [BRIEF]	[INFO_PRINTER]
 * @example	print_info("") / print_info("scrapper.php")
 * @author	chriSmile0
 * @return	void
*/
function print_info(string $arg = "N") {
	if($arg != "N") 
		echo $GLOBALS['programsv2'][$arg];
	else 
		print_info_local();

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
	$dt = new DateTime("now", new DateTimeZone('America/New_York'));
	echo "Copyright @-".$dt->format('Y')." [:chriSmile0:] \n";
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
			case "--info": print_info(($argc > 2) ? $argv[2] : "N");
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