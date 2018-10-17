<?php

$D['student_data'] = [];

$handle = fopen(__DIR__."/../data/data-student.csv", "r");
if ($handle === false) {
	exit("取得student.csv錯誤");
}

$D['student_data']['head'] = fgetcsv($handle);
if ($D['student_data']['head'] === false) {
	exit("解析student.csv錯誤");
}

$D['student_data']['data'] = [];
while (($data = fgetcsv($handle)) !== false) {
	$temp = [];
	foreach($D['student_data']['head'] as $key => $value) {
		$temp[$value] = $data[$key];
	}
	$D['student_data']['data'] []= $temp;
}

fclose($handle);
unset($handle);
