<?php 

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


/*$t1 = to_json($test,$keys);
$T1 = json_decode($t1,true);
$t11 = //$T1['PostalCode']=>$T1] //json_encode([$T1['PostalCode']=>$T1]);

$t2 = to_json($test2,$keys);
$T2 = json_decode($t2,true);
$t22 = //json_encode([$T2['PostalCode']=>$T2]);*/
//var_dump(json_encode([$T1['PostalCode']=>$T1,[$T2['PostalCode']=>$T2]]));*/
/*var_dump($t11);
//print_r($t1['PostalCode']);
$t2 = to_json($test2,$keys);
$rt = [$t1,$t2];
var_dump($rt);*/
$rtn = array_lines_to_json([$test,$test2],$keys,"PostalCode");
var_dump($rtn);
echo "after \n";
//var_dump(json_encode(["V"=>explode("_",$test)]));
$t1 = in_assoc_arr($test,$keys);
$t2 = in_assoc_arr($test2,$keys);
$arr = array();
//$arr[] = [$t1['PostalCode']=>$t1]; dont-work
//$arr[] = [$t2['PostalCode']=>$t2]; dont-work
//$arr = [$t1['PostalCode']=>$t1,$t2['PostalCode']=>$t2];
$arr += [$t1['PostalCode']=>$t1];
$arr += [$t2['PostalCode']=>$t2];
var_dump(json_encode($arr));
var_dump(json_encode([$t1['PostalCode']=>$t1,$t2['PostalCode']=>$t2]));
var_dump(json_encode(["P"=>["nom"=>"Paul","Prenom"=>"Max"],"K"=>["nom"=>"Paul","Prenom"=>"Max"]]));


//print_r(json_decode($rtn,true));

?>