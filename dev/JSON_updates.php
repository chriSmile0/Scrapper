<?php


//-------------------------------- LECLERC SUB_PART --------------------------//
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
				$rtn .= (is_array($next)) ? ((array_keys($next)[0]!="0") ? "$tabs},\n" : ((sizeof($next) > 1) ?  "$tabs],\n" : ",\n" )) :  ",\n";
			}
			$next = $associative_tabs[$keys[$size-1]];
			$rtn .= "$tabs\"".$keys[$size-1] . "\" : " .  my_json_rec_encode($actual,$next,$tabs."\t")."";
			$rtn .= (is_array($next)) ? ((array_keys($next)[0]!="0") ? "$tabs}\n" : ((sizeof($next) > 1) ?  "$tabs],\n" : "\n" ))  : "\n";
		
		}
		else {
			$size = sizeof($associative_tabs);
			if($size == 1) {
				$rtn .= $associative_tabs[0] ."";
			}
			else {
				$rtn .= $actual . "[\n";
				for($cpt = 0 ; $cpt < $size-1;$cpt++) {
					$rtn .= "$tabs".$associative_tabs[$cpt].",\n";
				}
				$rtn .= "$tabs".$associative_tabs[$size-1]."\n";
			}
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

//-------------------------------- SYSTEMU SUB_PART --------------------------//

/*$file_content = file_get_contents('copy_all_leclerc.html');
$rtn = array_lines_to_my_json(explode("\n",$file_content),$keys,"PostalCode");
echo $rtn;*/
/*function extract_href_su() {
	$file_content = file_get_contents('copy_all_systemeu.html');
	$lines = explode("\nurl",$file_content);
	$sub_lines = "";
	foreach($lines as $l) {
		$sub_lines .= "url".substr($l,0,strpos($l,">"))."\n";
	}
	return  $sub_lines;
}
echo extract_href_su();*/
function town_in_specific_syntax(string $town) : string {
	$unwanted_array = array(    'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
                            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
                            'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
                            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
                            'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', '&amp;' => '');
	$str = strtr( $town, $unwanted_array );
	$tr = str_replace(array(' ','+','\''), '',$str);
	return strtolower($tr);
}

function change_lines_su() {
	$file_content = file_get_contents('links_idf_systemeu.html');
	$lines = explode("\n",$file_content);
	$sub_lines = "";
	foreach($lines as $l) {
		$town_type = (explode(";",town_in_specific_syntax($l)));
		$sub_lines .= $town_type[1] . "-".$town_type[0]."\n";
	}
	return  $sub_lines;
}
//echo change_lines_su();

function create_json_per_ens() {
	$file_content = file_get_contents('links_systemeu_sort.html');
	$lines = explode("\n",$file_content);
	$types = ["uexpress","hyperu","superu"];
	$arr = array();//array([$types[0],$types[1],$types[2]]);
	foreach($lines as $l) {
		$i = 0;
		$l = substr($l,31);
		$t = "";
		for($i = 0; $i < 3; $i++) 
			if((strpos($l,$t=$types[$i])===0))
				break;
		
		$arr[$t][] = "\"".substr($l,strlen($t)+1)."\"";
	}
	return $arr;
}

function create_json_per_city() {
	$file_content = file_get_contents('links_systemeu_sort.html');
	$lines = explode("\n",$file_content);
	foreach($lines as $l) {
		$l = substr($l,31);
		$t = substr($l,0,$p=strpos($l,"-"));
		$arr[substr($l,$p+1)][] = "\"".$t."\"";
	}
	return $arr;
}
//echo my_json_encode(create_json_per_ens());
//echo my_json_encode(create_json_per_city());
?>