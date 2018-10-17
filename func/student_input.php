<?php
require_once(__DIR__."/data.php");

$handle = fopen(__DIR__."/../data/student-input.csv", "r");
if ($handle === false) {
	exit("取得student-input.csv錯誤");
}

$G['safety_check_student'] = array_diff($G['safety_check_student'], ['student_input_authentication_exist_in_data', 'student_input_has_authentication_column']);

$D['student_input'] = [];
$authentication_exist = false;
$index = 0;
while (($data = fgetcsv($handle)) !== FALSE) {
	if (count($data) == 3) {
		$D['student_input'][$data[1]] = $data;
		$D['student_input'][$data[1]]['index'] = $index++;

		if ($data[0] === "authentication") {
			$authentication_exist = true;
			
			if (!in_array($data[1], $D['student_data']['head'])) {
				$D['student_input'][$data[1]]['notexist'] = true;
				$G['safety_check_student'] []= 'student_input_authentication_exist_in_data';
			}
		}
	}
}

if ($authentication_exist === false) {
	$G['safety_check_student'] []= "student_input_has_authentication_column";
}

fclose($handle);
unset($handle);
unset($authentication_exist);
