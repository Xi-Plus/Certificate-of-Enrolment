<?php

$C["domain"] = 'https://example.com';
$C["path"] = '/certificate_of_enrolment';
$C["sitename"] = '在學證明書';
$C["titlename"] = '在學證明書';

$C["DBhost"] = 'localhost';
$C["DBuser"] = '';
$C["DBpass"] = '';
$C["DBname"] = 'certificate_of_enrolment';

$C["cookiename"] = 'coe';
$C["cookieexpire"] = 86400*7;

$C["CAPTCHAuselogin"] = false;
$C["CAPTCHAusestudent"] = false;
$C["CAPTCHAsitekey"] = '';
$C["CAPTCHAsecretkey"] = '';

$C["PasswordSecurityEnabled"] = true;
$C["PasswordSecurityMinLength"] = 4;
$G["PasswordSecurityText"] = [
	"password_match_username" => "密碼與帳號相同",
	"password_too_short" => "密碼太短，至少要".$C["PasswordSecurityMinLength"]."個字",
	"password_is_popular" => "密碼在常見密碼列表中前".$C["PasswordSecurityCannotBePopular"]."位"
];

$G["db"] = new PDO ('mysql:host='.$C["DBhost"].';dbname='.$C["DBname"].';charset=utf8', $C["DBuser"], $C["DBpass"]);

$C["function"] = [];
$C["function"]["print-date"] = function() {
	return date("Y.m.d");
};

$G["csvmime"] = array("application/vnd.ms-excel", "text/comma-separated-values");
$G["pdfmime"] = array("application/pdf");
$G["input_type"] = [
	"authentication" => "驗證用",
	"custom" => "自行輸入"
];
$G["source_text"] = [
	"data" => "來自學生資料",
	"input" => "來自手動輸入",
	"function" => "來自內建函數",
	"none" => "找不到來源"
];

$G['safety_check_student'] = [];
$G['safety_check_school'] = [];

$G['BOM'] = chr(239).chr(187).chr(191);

date_default_timezone_set("Asia/Taipei");

require("func/check_login.php");
session_start();
