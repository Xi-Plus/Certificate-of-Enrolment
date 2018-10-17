<?php
require_once(__DIR__."/config/config.php");
require_once(__DIR__."/func/student.php");
require_once(__DIR__."/func/student_input.php");
require_once(__DIR__."/func/data.php");
require_once(__DIR__."/func/log.php");

$showform = true;
$showpdf = false;

if (count($G['safety_check_student'])) {
	?>
	<div class="alert alert-danger alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		因為系統設定錯誤，基於安全性下載功能已被停用（代碼：<?=implode("、", array_unique($G['safety_check_student']));?>）
	</div>
	<?php
	$showform = false;
}

unset($_SESSION["input"]);
unset($_SESSION["file"]);
unset($_SESSION["mode"]);
if ($showform && isset($_POST["input"]) && is_array($_POST["input"])) {
	$_SESSION["input"] = [];
	$_SESSION["file"] = "student";
	$_SESSION["mode"] = "download";
	foreach ($_POST["input"] as $key => $value) {
		if (isset($D['student_input'][$key])) {
			$_SESSION["input"][$key] = $value;
		}
	}

	$found = false;
	foreach ($D['student_data']['data'] as $data) {
		$checked = true;
		foreach($D['student_input'] as $key => $value) {
			if ($value[0] === "authentication") {
				$required = $value[1];
				if ($data[$required] !== $_SESSION["input"][$required]) {
					$checked = false;
					break;
				}
			}
		}
		if ($checked) {
			$found = true;
			foreach ($D['student_data']['head'] as $key) {
				$_SESSION["input"][$key] = $data[$key];
			}
			writelog(sprintf("[student] %s found. %s", $U["ip"], json_encode($_POST)));
			break;
		}
	}
	if (!$found) {
		?>
		<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			輸入的資料無法查到
		</div>
		<?php
		writelog(sprintf("[student] %s not found. %s", $U["ip"], json_encode($_POST)));
	} else {
		$showpdf = true;
	}
}
?>
<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
<title><?=$C["titlename"]?>/學生下載</title>

<style type="text/css">
body {
	padding-top: 4.5rem;
}
</style>
<?php
if ($C["CAPTCHAusestudent"]) {
	?><script src='https://www.google.com/recaptcha/api.js'></script><?php
}
?>
</head>
<body>
<?php
require("header.php");
if ($showform) {
?>
<div class="container-fluid">
	<h2>學生下載</h2>
	<div class="row">
		<div class="col-12 col-md-4">
			<form method="post">
				<?php
				foreach ($D['student_input'] as $data) {
					?>
					<div class="row">
						<label class="col-4 form-control-label"><?=$data[2]?></label>
						<div class="col-8">
							<input type="text" class="form-control" name="input[<?=$data[1]?>]" required value="<?php echo $_POST["input"][$data[1]]??""; ?>">
						</div>
					</div>
					<?php
				}
				?>
				<div class="row">
					<div class="col-8 offset-4">
						<button type="submit" class="btn btn-success"><i class="fa fa-check" aria-hidden="true"></i> 送出</button>
					</div>
				</div>
			</form>
		</div>
		<div class="col-12 col-md-8">
			<?php
			if ($showpdf) {
				?>
				<embed width="100%" height="900" src="<?=$C["path"]?>/download.php#zoom=80"></embed>
				<?php
			}
			?>
		</div>
	</div>
</div>

<?php
}
require("footer.php");
?>
<script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous"></body>
<script type="text/javascript">
$(function () {
	$('[data-toggle="tooltip"]').tooltip()
})
</script>
</body>
</html>
