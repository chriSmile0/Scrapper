<?php 

namespace ChriSmile0\Scrapper;

use function ChriSmile0\Scrapper\scrap_https;
use function ChriSmile0\Scrapper\content_scrap_auchan;
use function ChriSmile0\Scrapper\content_scrap_carrefour;
use function ChriSmile0\Scrapper\content_scrap_leclerc;
use function ChriSmile0\Scrapper\content_scrap_intermarche;
use function ChriSmile0\Scrapper\content_scrap_monoprix;
use function ChriSmile0\Scrapper\content_scrap_systemeu;
require_once('../vendor/autoload.php');

function use_scrapper(string $url, bool $with_js) { // OK 
	return scrap_https($url,$with_js);
}

function use_content_scrapper_auchan(string $product, string $city, int $p) { // OK 
	return content_scrap_auchan($product,$city,$p);
}

function use_content_scrapper_carrefour(string $product, string $city, int $p) { // OK 
	return content_scrap_carrefour($product,$city,$p);
}

function use_content_scrapper_leclerc(string $product, string $city) { // OK 
	return content_scrap_leclerc($product,$city);
}

function use_content_scrapper_intermarche(string $product, string $city, int $p) { // OK 
	return content_scrap_intermarche($product,$city,$p);
}

function use_content_scrapper_monoprix(string $product, int $p) { // OK 
	return content_scrap_monoprix($product,$p);
}

function use_content_scrapper_systemeu(string $product, string $city) { // OK
	return content_scrap_systemeu($product,$city);
}


//var_dump(use_scrapper("https://Wikipedia.com",true)); 		// OK 
//var_dump(use_content_scrapper_leclerc("Lardons","Voglans")); 	// OK
//var_dump(use_content_scrapper_carrefour("lardons","Paris")); 	// OK 
//var_dump(use_content_scrapper_monoprix("Lardons")); 			// OK 
//var_dump(use_content_scrapper_auchan("lardons","Paris")); 	// OK
//var_dump(use_content_scrapper_intermarche("lardons","Paris"));// OK
//var_dump(use_content_scrapper_systemeu("Lardons","Voglans")); // OK


// WITH INTERNAL GECKO TEST : 
/*exec("geckodriver &");
var_dump(use_content_scrapper_carrefour("lardons","Paris")); */

/*
$childs = array();

// Fork some process.
$ids_exec = [];
for($i = 0; $i < 2; $i++) {
	if($i == 1)
		sleep(1);
    $pid = pcntl_fork();
    if($pid == -1)
        die('Could not fork');

    if ($pid) {
        echo "parent \n";
        $childs[] = $pid;
		echo "child : $pid\n";
		if(empty($ids_exec)) {
			$ids_exec[] = $i;
			echo "gecko is launch \n";
		}
		if(sizeof($childs)==2) {
			echo "go to dump \n";
			//var_dump(content_scrap_carrefour($search,$city));
		}
		else {
			$i = 0; // re run loop
			//$childs = array();
		}
		
	
    } 
	else {
		if($i == 0) {
			echo "h\n";
			exec("geckodriver &");
		}
       // sleep(1);
        
        // The child process needed to end the loop.
        exit();
    }
}

//var_dump(content_scrap_carrefour($search,$city));
//exec("kill -s kill `ps -e | grep -e geckodriver | grep -Eo '[0-9]{1,10}' | head -n 1`");
//exec("kill -s kill $ch_pid");
//posix_kill($ch_pid,SIGKILL);
while(count($childs) > 0) {
    foreach($childs as $key => $pid) {
        $res = pcntl_waitpid($pid, $status, WNOHANG);
		echo $res;
		echo "pid:$pid\n";
        
        // If the process has already exited
        if($res == -1 || $res > 0)
            unset($childs[$key]);
		if(($res == 0) && (sizeof($childs)==1)) {
			echo "pid:_to__finish : $pid\n";
			//EXEC ALL DUMP HERE 
			var_dump(use_content_scrapper_carrefour('lardons',"Paris"));
			//posix_kill($pid,SIGKILL);
			system("kill -s kill $pid");
			//var_dump($childs);

		}
    }
    
    sleep(1);
}
exec("kill -s kill `ps -e | grep -e geckodriver | grep -Eo '[0-9]{1,10}' | head -n 1`"); // kill geckodriver
*/

$scrappers_usages_example = [
	"Carrefour" => ["lardons","Paris"],
	"Leclerc" => ["lardons","Paris"],
	"Auchan" => ["lardons","Paris"],
	"SystemeU" => ["lardons","Paris"],
	"Intermarche" => ["lardons","Paris"],
	"Monoprix" => ["lardons"]
];
$scrappers_usages_min = [
	"Carrefour" => ["lardons","Paris"]
];

$scrappers_usages_min2 = [
	"Carrefour" => ["lardons","Paris"],
	"Monoprix" => ["lardons"]
];
function geckodriver_with_scrappers(array $scrappers_usage) { // ON SAME GECKODRIVER INSTANCE -> SEARCH FOR MANY OF THESE -> for parallelize
	$childs = array();

	// Fork some process.
	$rtn = "";
	for($i = 0; $i < 2; $i++) {
		if($i == 1)
			sleep(1);
		$pid = pcntl_fork();
		if($pid == -1)
			die('Could not fork');

		if($pid) {
			echo "parent \n";
			$childs[] = $pid;
			echo "child : $pid\n";
			if(empty($ids_exec)) 
				echo "gecko is launch \n";
			
			if(sizeof($childs)==2) 
				echo "go to dump \n";
			else 
				$i = 0; // re run loop
			
		} 
		else {
			if($i == 0) 
				exec("geckodriver &");
			exit();
		}
	}

	while(count($childs) > 0) {
		foreach($childs as $key => $pid) {
			$res = pcntl_waitpid($pid, $status, WNOHANG);
			echo $res;
			echo "pid:$pid\n";
			
			// If the process has already exited
			if($res == -1 || $res > 0)
				unset($childs[$key]);
			if(($res == 0) && (sizeof($childs)==1)) {
				echo "pid:_to__finish : $pid\n";
				//EXEC ALL DUMP HERE 
				$rtn = execs_scrappers($scrappers_usage);
				//posix_kill($pid,SIGKILL);
				system("kill -s kill $pid");
				//var_dump($childs);
	
			}
		}
		
		sleep(1);
	}
	return $rtn;
}

function execs_scrappers_v2(array $scrappers_usage) {
	$exec = [];
	$ports = 4444;
	$ports_inits = 4444;
	$childs_all = [];
	$j = 0;
	//foreach($scrappers_usage as $k => $v) {
		$port = $ports;
		$ports++;
		echo "launch geckodriver";
		echo "port : $port";

		// Fork some process.
	for($h = 0; $h < 2; $h++) {
		$port = 4444+$h;
		echo "port : $port\n";
		$childs = array();

		for($i = 0; $i < 2; $i++) {
			if($i == 1)
				sleep(1);
			$pid = pcntl_fork();
			if($pid == -1)
				die('Could not fork');

			if($pid) {
				echo "parent \n";
				$childs[] = $pid;
				echo "child : $pid\n";
				
				if(sizeof($childs)==2) 
					echo "go to dump \n";
				else 
					$i = 0; // re run loop
				
			} 
			else {
				if($i == 0) 
					exec("geckodriver -p $port &");
				exit();
			}
		}
		while(count($childs) > 0) {
			foreach($childs as $key => $pid) {
				$res = pcntl_waitpid($pid, $status, WNOHANG);
				echo $res;
				echo "pid:$pid\n";
				
				// If the process has already exited
				if($res == -1 || $res > 0)
					unset($childs[$key]);
				if(($res == 0) && (sizeof($childs)==1)) {
					echo "pid:_to__finish : $pid\n";
					//EXEC ALL DUMP HERE 
					//execs_scrappers($scrappers_usage);
					$exec[] = use_content_scrapper_carrefour("lardons","Paris",$port);
					//posix_kill($pid,SIGKILL);
					system("kill -s kill $pid");
					//var_dump($childs);
		
				}
			}
			
			sleep(1);
		}
	}
	return $exec;

			
}

function parrallelize_process() {
	$childs = array();
	for ($x = 0; $x < 2; $x++) {
		switch ($pid = pcntl_fork()) {
		   	case -1:
				die('Fork failed');
				exit(0);
			case 0:
				print "I'm child $x\n";
				sleep(1);
				exit(1); // IMPORTANT
			default:
				print "I'm father $x\n";
				$childs[] = $pid;
				break;
		}
	}
	while(count($childs) > 0) {
		foreach($childs as $key => $pid) {
			$res = pcntl_waitpid($pid, $status, WNOHANG);
			if($res == -1 || $res > 0) {
				echo "child $key is finish \n";
				unset($childs[$key]);
			}
		}
	}
}

function classic_process() {
	switch ($pid = pcntl_fork()) {
		case -1:
			die('Fork failed');
			exit(0);
	 	case 0:
			print "I'm child \n";
			sleep(1);
			exit(1); // IMPORTANT
		default:
			print "I'm father \n";
			pcntl_wait($status);
			print "Son finished \n";
			exit(1);
	 
	}
}


function classic_communication_between_processes() {
	$ary = array();
	$strone = 'Message From Parent.';
	$strtwo = 'Message From Child.';

	if (socket_create_pair(AF_UNIX, SOCK_STREAM, 0, $ary) === false) {
		echo "socket_create_pair() failed. Reason: ".socket_strerror(socket_last_error());
	}
	$pid = pcntl_fork();
	if ($pid == -1) {
		echo 'Could not fork Process.';
	} elseif ($pid) {
		/*parent*/
		socket_close($ary[0]);
		if (socket_write($ary[1], $strone, strlen($strone)) === false) {
			echo "socket_write() failed. Reason: ".socket_strerror(socket_last_error($ary[1]));
		}
		if (socket_read($ary[1], strlen($strtwo), PHP_BINARY_READ) == $strtwo) {
			echo "Received $strtwo\n";
		}
		socket_close($ary[1]);
	} else {
		/*child*/
		socket_close($ary[1]);
		if (socket_write($ary[0], $strtwo, strlen($strtwo)) === false) {
			echo "socket_write() failed. Reason: ".socket_strerror(socket_last_error($ary[0]));
		}
		if (($r = trim(socket_read($ary[0], strlen($strone)))) == $strone) {
			echo "Received $strone\n";
			echo "ary :$r\n ";
		}
		socket_close($ary[0]);
	}
}

function test_int_value_in_socket_between_process() {
	$ary = array();
	$strone = 1;
	$strtwo = 2;

	if (socket_create_pair(AF_UNIX, SOCK_STREAM, 0, $ary) === false) {
		echo "socket_create_pair() failed. Reason: ".socket_strerror(socket_last_error());
	}
	$pid = pcntl_fork();
	if ($pid == -1) {
		echo 'Could not fork Process.';
	} elseif ($pid) {
		/*parent*/
		socket_close($ary[0]);
		if (socket_write($ary[1], $strone,4) === false) {
			echo "socket_write() failed. Reason: ".socket_strerror(socket_last_error($ary[1]));
		}
		if (socket_read($ary[1], 4) == $strtwo) {
			echo "Received $strtwo\n";
		}
		socket_close($ary[1]);
	} else {
		/*child*/
		socket_close($ary[1]);
		if (socket_write($ary[0], $strtwo, 4) === false) {
			echo "socket_write() failed. Reason: ".socket_strerror(socket_last_error($ary[0]));
		}
		if (($r = trim(socket_read($ary[0], 4))) == $strone) {
			echo "Received $strone\n";
			echo "ary :$r\n ";
		}
		socket_close($ary[0]);
	}
}

function test_array_in_socket_between_process() { // IF DONT WORK MAKE JSON STRING
	$ary = array();
	$strone = ["abc"];
	$strtwo = ["def"];
	$strone = json_encode($strone);
	$strtwo = json_encode($strtwo);

	if (socket_create_pair(AF_UNIX, SOCK_STREAM, 0, $ary) === false) {
		echo "socket_create_pair() failed. Reason: ".socket_strerror(socket_last_error());
	}
	$pid = pcntl_fork();
	if ($pid == -1) {
		echo 'Could not fork Process.';
	} elseif ($pid) {
		/*parent*/
		socket_close($ary[0]);
		if (socket_write($ary[1], $strone, strlen($strone)) === false) {
			echo "socket_write() failed. Reason: ".socket_strerror(socket_last_error($ary[1]));
		}
		if (socket_read($ary[1], strlen($strtwo), PHP_BINARY_READ) == $strtwo) {
			echo "Received $strtwo\n";
		}
		socket_close($ary[1]);
	} else {
		/*child*/
		socket_close($ary[1]);
		if (socket_write($ary[0], $strtwo, strlen($strtwo)) === false) {
			echo "socket_write() failed. Reason: ".socket_strerror(socket_last_error($ary[0]));
		}
		if (($r = trim(socket_read($ary[0], strlen($strone)))) == $strone) {
			echo "Received $strone\n";
			echo "ary :$r\n ";
			$arr_res = json_decode($r);
			var_dump($arr_res);
		}
		socket_close($ary[0]);
	}
	
}

function process_in_process() {
	switch ($pid = pcntl_fork()) {
		case -1:
			die('Fork failed');
			exit(0);
		case 0:
			print "I'm child process \n";
			classic_process();
			exit(1); // IMPORTANT
		default:
			print "I'm father process\n";
			pcntl_wait($status);
			print "process in process finished \n";
			break;
		
	}
}

function many_classic_process_parralllize() {
	$childs = array();
	for($i = 0; $i < 2; $i++) {
		switch ($pid = pcntl_fork()) {
			case -1:
				die('Fork failed');
				exit(0);
			case 0:
				print "I'm child classic_process $i\n";
				if($i == 0) {
					sleep(4); // TEST
				}
				classic_process();
				exit(1); // IMPORTANT
			default:
				print "I'm father process $i\n";
				$childs[] = $pid;
				break;
			
		}
	}
	while(count($childs) > 0) {
		foreach($childs as $key => $pid) {
			$res = pcntl_waitpid($pid, $status, WNOHANG);
			if($res == -1 || $res > 0) {
				echo "child $key is finish \n";
				unset($childs[$key]);
			}
		}
	}
}

function my_exec_parrallelize_process(int $port) {
	$childs = array();
	echo "port : $port\n";
	for($x = 0; $x < 2; $x++) {
		if($x == 1)
			sleep(1);
		switch($pid = pcntl_fork()) {
		   	case -1:
				die('Fork failed');
				exit(0);
			case 0:
				print "I'm child $x\n";
				//sleep(1);
				if($x == 0) {
					exec("geckodriver -p $port");
					echo "gecko was killed \n";
				}
				exit(); // IMPORTANT
			default:
				print "I'm father $x\n";
				$childs[] = $pid;
				var_dump($childs);
				if(sizeof($childs)==1) 
					echo "go to dump \n";
				/*else 
					$x = 0; // re run loop//*/
				break;
		}
	}
	$exec_scrapper = 0;
	$rtn = "";
	while(count($childs) > 0) {
		foreach($childs as $key => $pid) {
			//var_dump($childs);
			$res = pcntl_waitpid($pid, $status, WNOHANG);
			if($res == -1 || $res > 0) {
				echo "child $key is finish \n";
				unset($childs[$key]);
			}
			if(($res == 0) && (count($childs)==1)) { // geckodriver is launch
				echo "pid : $pid";
				//-- EXEC YOUR CODE USE GECKODRIVER HERE
				if(!$exec_scrapper) {
					echo "EXEC CODE IN GECKO \n";
					sleep(10);
					$exec_scrapper = 1;
					$rtn =  "1234567891011::PORT:".$port;
					//RES ON FILE 
				}
				//-- END CODE
				//echo "FIN!!!\n";
				system("kill -s kill $pid"); // kill geckodriver launcher
				//exec("kill -s kill `ps -e | grep -e geckodriver | grep -Eo '[0-9]{1,10}' | head -n 1`");
				exec("kill -s kill `lsof -i -P | grep -e :$port | grep -Eo '[0-9]{1,10}' | head -n 1`");

			}
		}
	}
	if($exec_scrapper)
		return "".strlen($rtn).",".$rtn;
	return "";
}

function many_my_exec_parrallelize_process() { // OK 
	$childs = array();
	$recv_content = array();
	$ports = 4444;
	$returns = array();
	$arrys = array();
	
	for($i = 0; $i < 2; $i++) {
		$recv_content[] = false;
		$port = $ports+$i;
		$arrys[] = array();
		if (socket_create_pair(AF_UNIX, SOCK_STREAM, 0, $arrys[$i]) === false) {
			echo "socket_create_pair() failed. Reason: ".socket_strerror(socket_last_error());
		}
		switch ($pid = pcntl_fork()) {
			case -1:
				die('Fork failed');
				exit(0);
			case 0:
				print "I'm child classic_process $i\n";
				$rtn = my_exec_parrallelize_process($port); // DUMP RESULT IN PIPE
				$offset = 0;
				$size = substr($rtn,0,$offset=strpos($rtn,","));
				$rtn_t = substr($rtn,$offset+1);
				socket_close($arrys[$i][1]);
				if (socket_write($arrys[$i][0], $size) === false) 
					echo "socket_write() failed. Reason: ".socket_strerror(socket_last_error($arrys[$i][0]));
				
				if(socket_read($arrys[$i][0],2)=="OK") 
					if (socket_write($arrys[$i][0], $rtn_t, strlen($rtn_t)) === false) 
						echo "socket_write() failed. Reason: ".socket_strerror(socket_last_error($arrys[$i][0]));
				socket_close($arrys[$i][0]);
				exit(1); // IMPORTANT
			default:
				print "I'm father process $i\n";
				$childs[] = [$pid,$i];
				break;
			
		}
	}
	while(count($childs) > 0) {
		foreach($childs as $key => $pid) {
			$res = pcntl_waitpid($pid[0], $status, WNOHANG);
			if($res == -1 || $res > 0) {
				unset($childs[$key]);
			}
			if(($res == 0) && (!$recv_content[$key])) {
				socket_close($arrys[$pid[1]][0]);
				//SEND A FIRST SOCKET IN CHILD FOR COMMUNICATE THE SIZE
				$size = trim(socket_read($arrys[$pid[1]][1],10));
				echo "size : $size\n";
				if($size > 0)  // send content 
					if(socket_write($arrys[$pid[1]][1],"OK")) 
						if($rtn_tt = trim(socket_read($arrys[$pid[1]][1],$size))) {
							$returns[] = $rtn_tt;
							$recv_content[$key] = true;
						}
				
				socket_close($arrys[$pid[1]][1]);
			}
		}
	}
	return $returns;
}

function execs_scrappers(array $scrappers_usage) {
	$exec = [];
	$content = "";
	$ports = 4444;
	$port = $ports;
	foreach($scrappers_usage as $k => $v) {
		/*$port = $ports;
		$ports++;*/ // parrallelize
		switch($k) {
			case "Carrefour":
				$content = content_scrap_carrefour($v[0],$v[1],$port);
				break;
			case "Leclerc":
				$content = content_scrap_leclerc($v[0],$v[1]);
				break;
			case "Auchan":
				$content = content_scrap_auchan($v[0],$v[1],$port);
				break;
			case "SystemeU":
				$content = content_scrap_systemeu($v[0],$v[1]);
				break;
			case "Intermarche":
				$content = content_scrap_intermarche($v[0],$v[1],$port);
				break;
			case "Monoprix":
				$content = content_scrap_monoprix($v[0],$port);
				break;
			default:
				$content = "ERROR";
				break;
		}
		$exec = array_merge($exec,[$k=>$content]);
	}
	return $exec;
}

//var_dump(execs_scrappers_v2($scrappers_usages_min2));
//var_dump(execs_scrappers_v2($scrappers_usages_min2));
//exec("kill -s kill `ps -e | grep -e geckodriver | grep -Eo '[0-9]{1,10}' | head -n 1`"); // kill geckodriver
//parrallelize_process();
//classic_process();
//process_in_process();
//PARRALLELIZE THIS 
//my_exec_parrallelize_process(4444);
//my_exec_parrallelize_process(4445);
//many_classic_process_parralllize();
many_my_exec_parrallelize_process();
//classic_communication_between_processes();
//test_int_value_in_socket_between_process();
//test_array_in_socket_between_process();
?>