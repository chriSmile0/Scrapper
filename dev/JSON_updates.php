<?php

use function PHPSTORM_META\type;

$test = "0011_2_23_Aix_25km";
$test2 = "0012_3_24_Paris_800km";
$keys = ["LeclercCode","idBaseLeclerc","PostalCode","NameCity","DistanceToTarget"];


function to_json(string $file_content, array $array_keys) {
	$values = explode("_",$file_content);
	$arr = array();
	$i = 0;
	for($i = 0; $i < 5; $i++) {
		$arr = array_merge($arr,[$array_keys[$i]=>$values[$i]]);
	}
	return json_encode($arr,JSON_FORCE_OBJECT);
}

function in_assoc_arr(string $file_content, array $array_keys) {
	$values = explode("_",$file_content);
	$arr = array();
	$i = 0;
	for($i = 0; $i < 5; $i++) {
		$arr = array_merge($arr,[$array_keys[$i]=>$values[$i]]);
	}
	return $arr;
}

function array_lines_to_json(array $to_json, array $keys, string $main_key) {
	$arr = array();
	foreach($to_json as $t_j) {
		$encode_part = in_assoc_arr($t_j,$keys);
		$arr += [$encode_part[$main_key]=>$encode_part];
	}
	return json_encode($arr);
}

function array_lines_to_my_json(array $to_json, array $keys, string $main_key) {
	$arr = array();
	foreach($to_json as $t_j) {
		$encode_part = in_assoc_arr($t_j,$keys);
		$arr += [$encode_part[$main_key]=>$encode_part];
	}
	return my_json_encode($arr);
}

function my_json_encode(array $arr) : string {
	return my_json_rec_encode("",$arr,"\t")."}";
} 

function my_json_rec_encode(string $actual,$associative_tabs, string $tabs) {
	$rtn = "";
	if(is_array($associative_tabs)) {
		if(array_keys($associative_tabs)[0]!="0") { // EXCLUDE 0 IN FIRST KEY 
			$rtn .= $actual . "{\n";
			$keys = array_keys($associative_tabs);
			$size = sizeof($associative_tabs);
			for($cpt = 0 ; $cpt < $size-1;$cpt++) {
				$next = $associative_tabs[$keys[$cpt]];
				$rtn .= "$tabs\"".$keys[$cpt] . "\" : " . my_json_rec_encode($actual,$next,$tabs."\t")."";
				$rtn .= (is_array($next)) ? ((array_keys($next)[0]!="0") ? "$tabs},\n" :  "$tabs],\n") :  ",\n";
			}
			$next = $associative_tabs[$keys[$size-1]];
			$rtn .= "$tabs\"".$keys[$size-1] . "\" : " .  my_json_rec_encode($actual,$next,$tabs."\t")."";
			$rtn .= (is_array($next)) ? ((array_keys($next)[0]!="0") ? "$tabs}\n" :  "$tabs]\n")  : "\n";
		
		}
		else {
			$rtn .= $actual . "[\n";
			$size = sizeof($associative_tabs);
			for($cpt = 0 ; $cpt < $size-1;$cpt++) {
				$rtn .= "$tabs".$associative_tabs[$cpt].",\n";
			}
			$rtn .= "$tabs".$associative_tabs[$size-1]."\n";
		}
		return $rtn;
	}
	else {
		$rtn .= $actual . "[\n";
		$type = gettype($associative_tabs);
		if($type === "string")
			return "\"".$associative_tabs."\"";
		else if($type === "integer" || $type === "boolean" || $type === "double"
				|| $type === "NULL")
			return $associative_tabs;
	}



	
}

$file_content = file_get_contents('copy_all_leclerc.html');
$rtn = array_lines_to_my_json(explode("\n",$file_content),$keys,"PostalCode");
echo $rtn;
?>