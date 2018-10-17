<?php
require_once(__DIR__."/../config/config.php");

function PasswordSecurity($password, $username=null) {
	global $C;
	if (!is_null($username)) {
		if (strtolower($password) == strtolower($username)) {
			return "password_match_username";
		}
	}

    if (strlen($password) < $C["PasswordSecurityMinLength"]) {
        return "password_too_short";
    }

	$handle = fopen($C["PasswordSecurityPopularPasswordFile"], "r");
	if ($handle === false) {
		exit("取得PasswordSecurityPopularPasswordFile錯誤");
	}

	$count = 0;
    while (($line = fgets($handle)) !== false) {
        $line = trim($line);
        if ($line !== "") {
        	if ($password == $line) {
        		return "password_is_popular";
        	}

        	$count ++;
        }
        if ($count >= $C["PasswordSecurityCannotBePopular"]) {
        	break;
        }
    }
    fclose($handle);

    return true;
}
