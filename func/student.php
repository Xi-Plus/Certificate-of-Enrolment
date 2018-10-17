<?php
require_once(__DIR__."/../config/config.php");
require_once(__DIR__."/student_input.php");
require_once(__DIR__."/data.php");

$G['safety_check_student'] = array_diff($G['safety_check_student'], ['student_format_source_exist']);

$handle = fopen(__DIR__."/../data/student.csv", "r");
if ($handle === false) {
	exit("取得student.csv錯誤");
}

$D['student'] = [];
while (($data = fgetcsv($handle)) !== FALSE) {
	if (count($data) == 8) {
		$hash = md5(serialize($data));
		$D['student'][$hash] = $data;
		if (in_array($data[0], $D['student_data']['head'])) {
			$D['student'][$hash]['source'] = "data";
		} else if (isset($D['student_input'][$data[0]]) && $D['student_input'][$data[0]][0] === "custom") {
			$D['student'][$hash]['source'] = "input";
		} else if (isset($C["function"][$data[0]])) {
			$D['student'][$hash]['source'] = "function";
		} else {
			$D['student'][$hash]['source'] = "none";
			$G['safety_check_student'] []= 'student_format_source_exist';
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
uasort($D['student'], $cmp);

fclose($handle);
unset($handle);
unset($cmp);
