<?php
require_once(__DIR__."/config/config.php");
require_once(__DIR__."/func/account_list.php");
require_once(__DIR__."/func/password_security.php");
require_once(__DIR__."/func/log.php");
?>
<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
<title><?=$C["titlename"]?>/管理帳號</title>

<style type="text/css">
body {
	padding-top: 4.5rem;
}
</style>
</head>
<body>
<?php
$showform = true;
if (!$U["islogin"]) {
	?>
	<div class="alert alert-danger alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		此功能需要驗證帳號，請<a href="<?=$C["path"]?>/login/">登入</a>
	</div>
	<?php
	$showform = false;
	writelog(sprintf("[manage_account] %s view no premission.", $U["ip"]));
} else if (isset($_POST["action"])) {
	if ($_POST["action"] === "new") {
		if (isset($D['account'][$_POST["account"]])) {
			?>
			<div class="alert alert-danger alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				已有帳號 <?=htmlentities($_POST["account"])?>
			</div>
			<?php
			writelog(sprintf("[manage_account] %s %s new failed. dup: %s.", $U["data"]["account"], $U["ip"], $_POST["account"]));
		} else if ($_POST["account"] === "" || $_POST["password"] === "" || $_POST["name"] === "") {
			?>
			<div class="alert alert-danger alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				帳號、密碼、姓名不可為空
			</div>
			<?php
			writelog(sprintf("[manage_account] %s %s new failed. empty: %s.", $U["data"]["account"], $U["ip"], $_POST["account"]));
		} else if ($C["PasswordSecurityEnabled"] && ($res = PasswordSecurity($_POST["password"], $_POST["account"])) !== true) {
			?>
			<div class="alert alert-danger alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				密碼安全性不足：<?=$G["PasswordSecurityText"][$res]?>
			</div>
			<?php
			writelog(sprintf("[manage_account] %s %s new failed. password security: %s.", $U["data"]["account"], $U["ip"], $_POST["account"]));
		} else {
			$sth = $G["db"]->prepare("INSERT INTO `admin` (`account`, `password`, `name`) VALUES (:account, :password, :name)");
			$sth->bindValue(":account", $_POST["account"]);
			$sth->bindValue(":password", password_hash($_POST["password"], PASSWORD_DEFAULT));
			$sth->bindValue(":name", $_POST["name"]);
			$sth->execute();
			$D["account"][$_POST["account"]] = array("account"=>$_POST["account"], "name"=>$_POST["name"]);
			?>
			<div class="alert alert-success alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				已新增 <?=htmlentities($_POST["name"])?>
			</div>
			<?php
			writelog(sprintf("[manage_account] %s %s new successed. %s / %s.", $U["data"]["account"], $U["ip"], $_POST["account"], $_POST["name"]));
		}
	} else {
		if ($_POST["password"] !== "") {
			if (isset($D['account'][$_POST["account"]])) {
				if ($C["PasswordSecurityEnabled"] && ($res = PasswordSecurity($_POST["password"], $_POST["account"])) !== true) {
					?>
					<div class="alert alert-danger alert-dismissible" role="alert">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						密碼安全性不足：<?=$G["PasswordSecurityText"][$res]?>
					</div>
					<?php
					writelog(sprintf("[manage_account] %s %s edit password failed. password security: %s.", $U["data"]["account"], $U["ip"], $_POST["account"]));
				} else {
					$sth = $G["db"]->prepare("UPDATE `admin` SET `password` = :password WHERE `account` = :account");
					$sth->bindValue(":password", password_hash($_POST["password"], PASSWORD_DEFAULT));
					$sth->bindValue(":account", $_POST["account"]);
					$sth->execute();
					?>
					<div class="alert alert-success alert-dismissible" role="alert">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						已修改 <?=htmlentities($_POST["account"])?> 的密碼
					</div>
					<?php
					writelog(sprintf("[manage_account] %s %s edit password successed. %s.", $U["data"]["account"], $U["ip"], $_POST["account"]));
				}
			} else {
				?>
				<div class="alert alert-danger alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					找不到帳號 <?=htmlentities($_POST["account"])?>
				</div>
				<?php
				writelog(sprintf("[manage_account] %s %s edit password failed. not found %s.", $U["data"]["account"], $U["ip"], $_POST["account"]));
			}
		}
		if ($_POST["name"] !== "") {
			$sth = $G["db"]->prepare("UPDATE `admin` SET `name` = :name WHERE `account` = :account");
			$sth->bindValue(":name", $_POST["name"]);
			$sth->bindValue(":account", $_POST["account"]);
			$sth->execute();
			$D["account"][$_POST["account"]]["name"] = $_POST["name"];
			?>
			<div class="alert alert-success alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				已修改 <?=htmlentities($_POST["account"])?> 的姓名
			</div>
			<?php
			writelog(sprintf("[manage_account] %s %s edit name successed. %s.", $U["data"]["account"], $U["ip"], $_POST["account"]));
		}
	}
} else if (isset($_POST["delete"])) {
	if ($_POST["delete"] == $U["data"]["account"]) {
		?>
		<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			你無法刪除自己的帳號
		</div>
		<?php
		writelog(sprintf("[manage_account] %s %s delete failed. delete self.", $U["data"]["account"], $U["ip"], $_POST["account"]));
	} else if (isset($D['account'][$_POST["delete"]])) {
		$sth = $G["db"]->prepare("DELETE FROM `admin` WHERE `account` = :account");
		$sth->bindValue(":account", $_POST["delete"]);
		$sth->execute();
		unset($D["account"][$_POST["delete"]]);
		?>
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			已刪除帳號 <?=htmlentities($_POST["delete"])?>
		</div>
		<?php
		writelog(sprintf("[manage_account] %s %s delete successed. delete %s.", $U["data"]["account"], $U["ip"], $_POST["account"]));
	}
}

require("header.php");
if ($showform) {
?>
<div class="container">
	<h2>管理帳號</h2>
	<form action="" method="post">
		<div class="table-responsive">
			<table class="table">
				<tr>
					<th>帳號</th>
					<th>姓名</th>
					<th>刪除</th>
				</tr>
				<?php
				foreach ($D['account'] as $account) {
					?>
					<tr>
						<td><?=htmlentities($account["account"])?></td>
						<td><?=htmlentities($account["name"])?></td>
						<td>
							<button type="submit" name="delete" value="<?=$account["account"]?>" class="btn btn-danger btn-sm"><i class="fa fa-trash" aria-hidden="true"></i> 刪除</button>
						</td>
					</tr>
					<?php
				}
				?>
			</table>
		</div>
	</form>
	<h3>新增/修改</h3>
	<form action="" method="post">
		<div class="row">
			<label class="col-sm-2 form-control-label"><i class="fa fa-user" aria-hidden="true"></i> 帳號</label>
			<div class="col-sm-10">
				<input class="form-control" type="text" name="account" placeholder="必填">
			</div>
		</div>
		<div class="row">
			<label class="col-sm-2 form-control-label"><i class="fa fa-hashtag" aria-hidden="true"></i> 密碼</label>
			<div class="col-sm-10">
				<input class="form-control" type="password" name="password" placeholder="新增時必填，不修改留空">
			</div>
		</div>
		<div class="row">
			<label class="col-sm-2 form-control-label"><i class="fa fa-header" aria-hidden="true"></i> 姓名</label>
			<div class="col-sm-10">
				<input class="form-control" type="text" name="name" placeholder="新增時必填，不修改留空" autocomplete="name">
			</div>
		</div>
		<div class="row">
			<div class="col-sm-10 offset-sm-2">
				<button type="submit" class="btn btn-success" name="action" value="new"><i class="fa fa-plus" aria-hidden="true"></i> 新增</button>
				<button type="submit" class="btn btn-success" name="action" value="edit"><i class="fas fa-pencil-alt"></i> 修改</button>
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
<script type="text/javascript">
$(function () {
	$('[data-toggle="tooltip"]').tooltip()
})
</script>
</body>
</html>
