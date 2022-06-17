<?php
require_once(__DIR__.'/../vendor/autoload.php');
require_once(__DIR__."/../config/config.php");

use Wikimedia\CommonPasswords\CommonPasswords;

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

	if (CommonPasswords::isCommon($password)) {
		return "password_is_popular";
	}

    return true;
}
