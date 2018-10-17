<?php

if (!isset($_COOKIE[$C["cookiename"]])) {
	$U["islogin"] = false;
} else {
	$sth = $G["db"]->prepare('SELECT * FROM `login_session` WHERE `cookie` = :cookie');
	$sth->bindValue(":cookie", $_COOKIE[$C["cookiename"]]);
	$sth->execute();
	$cookie = $sth->fetch(PDO::FETCH_ASSOC);
	if ($cookie === false) {
		$U["islogin"] = false;
	} else {
		$sth = $G["db"]->prepare('SELECT * FROM `admin` WHERE `account` = :account');
		$sth->bindValue(":account", $cookie["account"]);
		$sth->execute();
		$U["data"] = $sth->fetch(PDO::FETCH_ASSOC);
		$U["islogin"] = true;
	}
}

$U["ip"] = "";
$ipserverkey = ["HTTP_CLIENT_IP", "HTTP_X_FORWARDED_FOR", "HTTP_X_FORWARDED", "HTTP_X_CLUSTER_CLIENT_IP", "HTTP_FORWARDED_FOR", "HTTP_FORWARDED", "REMOTE_ADDR", "HTTP_VIA"];
foreach ($ipserverkey as $ipkey) {
	if (!empty($_SERVER[$ipkey])) {
		$U["ip"] = $_SERVER[$ipkey];
	}
}
unset($ipserverkey);
