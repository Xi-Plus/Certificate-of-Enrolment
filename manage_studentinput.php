<?php
require_once(__DIR__."/config/config.php");
require_once(__DIR__."/func/student_input.php");
require_once(__DIR__."/func/log.php");

$showform = true;
if (!$U["islogin"]) {
	?>
	<div class="alert alert-danger alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		此功能需要驗證帳號，請<a href="<?=$C["path"]?>/login/">登入</a>
	</div>
	<?php
	$showform = false;
	writelog(sprintf("[manage_studentinput] %s view no premission.", $U["ip"]));
}

function write_student_input($data) {
	$handle = fopen(__DIR__."/data/student-input.csv", "w");
	if ($handle === false) {
		writelog(sprintf("[manage_studentinput] %s %s read student.csv failed.", $U["data"]["account"], $U["ip"]));
		exit("取得student-input.csv錯誤");
	}
	foreach ($data as $row) {
		unset($row['index']);
		fputcsv($handle, $row);
	}
	fclose($handle);
}

if ($showform && (isset($_POST["moveup"]) || isset($_POST["movedown"]))) {
	$key = $_POST["moveup"] ?? $_POST["movedown"];
	$index = array_search($key, array_keys($D['student_input']));
	$index += (isset($_POST["moveup"]) ? -1 : 1);
	if ($index < 0 || $index >= count($D['student_input'])) {
		?>
		<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			無法執行此移動
		</div>
		<?php
	} else {
		$otherkey = array_keys($D['student_input'])[$index];
		list($D['student_input'][$key]['index'], $D['student_input'][$otherkey]['index']) = [$D['student_input'][$otherkey]['index'], $D['student_input'][$key]['index']];
		function cmp($a, $b) {
			if ($a['index'] == $b['index']) {
				return 0;
			}
			return ($a['index'] < $b['index'] ? -1 : 1);
		}
		uasort($D['student_input'], 'cmp');
		write_student_input($D['student_input']);
		require(__DIR__."/func/student_input.php");
	}
}

if ($showform && isset($_POST["delete"])) {
	writelog(sprintf("[manage_studentinput] %s %s delete %s successed.", $U["data"]["account"], $U["ip"], $_POST["delete"]));
	?>
	<div class="alert alert-success alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		已刪除 <?=$_POST["delete"]?>
	</div>
	<?php
	unset($D['student_input'][$_POST["delete"]]);
	write_student_input($D['student_input']);
	require(__DIR__."/func/student_input.php");
}

if ($showform && isset($_POST["new"])) {
	if (isset($D['student_input'][$_POST["column"]])) {
		?>
		<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			該欄位已存在
		</div>
		<?php
		writelog(sprintf("[manage_studentinput] %s %s new %s failed. dup.", $U["data"]["account"], $U["ip"], $_POST["column"]));
	} else {
		$D['student_input'] []= [$_POST["type"], $_POST["column"], $_POST["text"]];
		write_student_input($D['student_input']);
		require(__DIR__."/func/student_input.php");
		?>
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			已新增 <?=$_POST["column"]?>
		</div>
		<?php
		writelog(sprintf("[manage_studentinput] %s %s new %s successed.", $U["data"]["account"], $U["ip"], $_POST["column"]));
	}
}

if ($showform && in_array('student_input_has_authentication_column', $G['safety_check_student'])) {
	?>
	<div class="alert alert-danger alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		設定錯誤：沒有任何供 驗證用 欄位，基於安全性下載功能已自動停用
	</div>
	<?php
}

if ($showform && in_array('student_input_authentication_exist_in_data', $G['safety_check_student'])) {
	foreach ($D['student_input'] as $row) {
		if (isset($row['notexist'])) {
			?>
			<div class="alert alert-danger alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				設定錯誤：欄位 <?=$row[1]?> 不存在於學生資料
			</div>
			<?php
		}
	}
}

?>
<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
<title><?=$C["titlename"]?>/管理學生下載輸入欄位</title>

<style type="text/css">
body {
	padding-top: 4.5rem;
}
</style>
</head>
<body>
<?php
require("header.php");
if ($showform) {
?>
<div class="container">
	<h2>管理學生下載輸入欄位</h2>
	<form action="" method="post">
		<div class="table-responsive">
			<table class="table">
				<tr>
					<th>類型</th>
					<th>欄位</th>
					<th>顯示文字</th>
					<th>操作</th>
				</tr>
				<?php
				foreach ($D['student_input'] as $row) {
				?>
				<tr <?=(isset($row['notexist'])?' class="table-danger"':'')?>>
					<td><?=$G["input_type"][$row[0]]?></td>
					<td><?=$row[1]?></td>
					<td><?=$row[2]?></td>
					<td>
						<button type="submit" name="moveup" value="<?=$row[1]?>" class="btn btn-info btn-sm"><i class="fas fa-sort-up"></i> 上移</button>
						<button type="submit" name="movedown" value="<?=$row[1]?>" class="btn btn-info btn-sm"><i class="fas fa-sort-down"></i> 下移</button>
						<button type="submit" name="delete" value="<?=$row[1]?>" class="btn btn-danger btn-sm"><i class="fa fa-trash" aria-hidden="true"></i> 刪除</button>
					</td>
				</tr>
				<?php
				}
				?>
			</table>
		</div>
	</form>
	<h3>新增</h3>
	<form action="" method="post">
		<div class="row">
			<label class="col-sm-2 form-control-label">類型</label>
			<div class="col-sm-10">
				<select class="form-control" name="type">
					<?php
					foreach ($G["input_type"] as $key => $value) {
						?>
						<option value="<?=$key?>"><?=$value?></option>
						<?php
					}
					?>
				</select>
			</div>
		</div>
		<div class="row">
			<label class="col-sm-2 form-control-label">欄位</label>
			<div class="col-sm-10">
				<input class="form-control" type="text" name="column" required>
			</div>
		</div>
		<div class="row">
			<label class="col-sm-2 form-control-label">顯示文字</label>
			<div class="col-sm-10">
				<input class="form-control" type="text" name="text" required>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-10 offset-sm-2">
				<button type="submit" class="btn btn-success" name="new"><i class="fa fa-plus" aria-hidden="true"></i> 新增</button>
			</div>
		</div>
	</form>
</div>

<?php
}
require("footer.php");
?>
<script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DzthAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous"></body>
</html>
