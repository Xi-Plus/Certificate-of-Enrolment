<?php
require_once(__DIR__."/../config/config.php");
require_once(__DIR__."/school_input.php");
require_once(__DIR__."/data.php");

$G['safety_check_school'] = array_diff($G['safety_check_school'], ['school_format_source_exist']);

$handle = fopen(__DIR__."/../data/school.csv", "r");
if ($handle === false) {
	exit("取得school.csv錯誤");
}

$D['school'] = [];
while (($data = fgetcsv($handle)) !== FALSE) {
	if (count($data) == 8) {
		$hash = md5(serialize($data));
		$D['school'][$hash] = $data;
		if (in_array($data[0], $D['student_data']['head'])) {
			$D['school'][$hash]['source'] = "data";
		} else if (isset($D['school_input'][$data[0]]) && $D['school_input'][$data[0]][0] === "custom") {
			$D['school'][$hash]['source'] = "input";
		} else if (isset($C["function"][$data[0]])) {
			$D['school'][$hash]['source'] = "function";
		} else {
			$D['school'][$hash]['source'] = "none";
			$G['safety_check_school'] []= 'school_format_source_exist';
		}
	}
}

$cmp = function($a, $b) {
	if ($a[3] == $b[3]) {
		if ($a[1] == $b[1]) {
			return 0;
		}
		return ($a[1] < $b[1] ? -1 : 1);
	}
	return ($a[3] < $b[3] ? -1 : 1);
};
uasort($D['school'], $cmp);

fclose($handle);
unset($handle);
unset($cmp);
