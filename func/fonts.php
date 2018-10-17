<?php

$handle = file_get_contents(__DIR__."/../data/fonts.json", "r");
if ($handle === false) {
	exit("取得fonts.json錯誤");
}

$json = json_decode($handle, true);
if ($json === false) {
	exit("解析fonts.json錯誤");
}

$D['fonts'] = [];
foreach ($json as $row) {
	$D['fonts'][$row['name']] = $row;
}

fclose($handle);
unset($handle);
unset($json);
