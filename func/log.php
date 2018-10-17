<?php

function writelog($text="") {
	global $C, $G;
	$sth = $G["db"]->prepare("INSERT INTO `log` (`text`) VALUES (:text)");
	$sth->bindValue(":text", $text);
	$sth->execute();
}
