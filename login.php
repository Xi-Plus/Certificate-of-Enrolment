<?php
require_once(__DIR__."/config/config.php");
require_once(__DIR__."/func/log.php");
?>
<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
<title><?=$C["titlename"]?>/登入</title>

<style type="text/css">
body {
	padding-top: 4.5rem;
}
</style>
</head>
<body>
<?php

$showform = true;
if ($_GET["action"] === "login") {
	if ($U["islogin"]) {
		?>
		<div class="alert alert-info alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			已經登入了
		</div>
		<?php
		$showform = false;
		writelog(sprintf("[login] %s %s logged in.", $U["data"]["account"], $U["ip"]));
	} else if (isset($_POST["account"])) {
		$sth = $G["db"]->prepare('SELECT * FROM `admin` WHERE `account` = :account');
		$sth->bindValue(":account", $_POST["account"]);
		$sth->execute();
		$account = $sth->fetch(PDO::FETCH_ASSOC);
		if ($account !== false && password_verify($_POST["password"], $account["password"])) {
			$cookie = md5(uniqid(rand(),true));
			$sth = $G["db"]->prepare('INSERT INTO `login_session` (`account`, `cookie`) VALUES (:account, :cookie)');
			$sth->bindValue(":account", $_POST["account"]);
			$sth->bindValue(":cookie", $cookie);
			$sth->execute();
			setcookie($C["cookiename"], $cookie, time()+$C["cookieexpire"], $C["path"]);
			?>
			<div class="alert alert-success alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				登入成功
			</div>
			<?php
			$U["data"] = $account;
			$U["islogin"] = true;
			$showform = false;
			writelog(sprintf("[login] %s %s log in successed.", $U["data"]["account"], $U["ip"]));
		} else {
			?>
			<div class="alert alert-danger alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				登入失敗
			</div>
			<?php
			writelog(sprintf("[login] %s %s log in failed.", $_POST["account"], $U["ip"]));
		}
	}
} else if ($_GET["action"] === "logout") {
	if ($U["islogin"]) {
		$sth = $G["db"]->prepare('DELETE FROM `login_session` WHERE `cookie` = :cookie');
		$sth->bindValue(":cookie", $_COOKIE[$C["cookiename"]]);
		$sth->execute();
		setcookie($C["cookiename"], "", time(), $C["path"]);
	}
	?>
	<div class="alert alert-success alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		已登出
	</div>
	<?php
	$U["islogin"] = false;
	$showform = false;
	writelog(sprintf("[login] %s %s log out successed.", $U["data"]["account"], $U["ip"]));
}
require("header.php");
if ($showform) {
?>
<div class="container">
	<h2>登入</h2>
	<form action="" method="post">
		<div class="row">
			<label class="col-sm-2 form-control-label"><i class="fa fa-user" aria-hidden="true"></i> 帳號</label>
			<div class="col-sm-10">
				<input class="form-control" type="text" name="account" required>
			</div>
		</div>
		<div class="row">
			<label class="col-sm-2 form-control-label"><i class="fa fa-hashtag" aria-hidden="true"></i> 密碼</label>
			<div class="col-sm-10">
				<input class="form-control" type="password" name="password" required>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-10 offset-sm-2">
				<button type="submit" class="btn btn-success" name="action" value="new"><i class="fa fa-sign-in" aria-hidden="true"></i> 登入</button>
			</div>
		</div>
	</form>
</div>

<?php
}
require("footer.php");
?>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous"></body>
</body>
</html>
