<?php
require_once(__DIR__."/config/config.php");
require_once(__DIR__."/func/student.php");
require_once(__DIR__."/func/student_input.php");
require_once(__DIR__."/func/data.php");
require_once(__DIR__."/func/fonts.php");
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
	writelog(sprintf("[manage_studentformat] %s view no premission.", $U["ip"]));
}

$showpdf = false;

if (isset($_POST["grid"])) {
	$showpdf = true;
	$_SESSION["file"] = "student";
	$_SESSION["mode"] = "grid";
}

if (isset($_POST["preview"])) {
	$showpdf = true;
	$_SESSION["file"] = "student";
	$_SESSION["mode"] = "preview";
}

function write_student($data) {
	$handle = fopen(__DIR__."/data/student.csv", "w");
	if ($handle === false) {
		exit("取得student.csv錯誤");
		writelog(sprintf("[manage_studentformat] %s %s read school.csv failed.", $U["data"]["account"], $U["ip"]));
	}
	foreach ($data as $row) {
		unset($row['source']);
		fputcsv($handle, $row);
	}
	fclose($handle);
}

if ($showform && isset($_POST["delete"])) {
	foreach ($_POST["deleteitem"] as $hash) {
		?>
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			已刪除 <?=$D['student'][$hash][0]?>
		</div>
		<?php
		unset($D['student'][$hash]);
		writelog(sprintf("[manage_studentformat] %s %s delete %s successed.", $U["data"]["account"], $U["ip"], $D['school'][$hash][0]));
	}
	write_student($D['student']);
	require(__DIR__."/func/student.php");
}

if ($showform && isset($_POST["new"])) {
	$data = [$_POST["column"], $_POST["positionx"], $_POST["positionx2"], $_POST["positiony"], $_POST["font"], $_POST["size"], $_POST["style"], $_POST["align"]];
	$hash = md5(serialize($data));
	if (isset($D['student'][$hash])) {
		?>
		<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			該欄位已存在
		</div>
		<?php
		writelog(sprintf("[manage_studentformat] %s %s new failed. dup:%s.", $U["data"]["account"], $U["ip"], json_encode($data)));
	} else {
		$D['student'] []= $data;
		write_student($D['student']);
		require(__DIR__."/func/student.php");
		?>
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			已新增 <?=$_POST["column"]?>
		</div>
		<?php
		writelog(sprintf("[manage_studentformat] %s %s new successed. %s.", $U["data"]["account"], $U["ip"], json_encode($data)));
	}
}

if ($showform && isset($_POST["edit"])) {
	foreach ($_POST["format"] as $hash => $row) {
		if (isset($D['student'][$hash])) {
			$old = $D['student'][$hash];
			unset($old['source']);
			$new = [
				$row["column"],
				$row["positionx"],
				$row["positionx2"],
				$row["positiony"],
				$row["font"],
				$row["size"],
				$row["style"],
				$row["align"],
			];
			if ($old != $new) {
				?>
				<div class="alert alert-success alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					已變更 <?=$D['student'][$hash][0]?>
				</div>
				<?php
				$D['student'][$hash] = $new;
				writelog(sprintf("[manage_studentformat] %s %s edit successed. %s -> %s.", $U["data"]["account"], $U["ip"], json_encode($old), json_encode($new)));
			}
		}
	}
	write_student($D['student']);
	require(__DIR__."/func/student.php");
}

foreach ($D['student'] as $row) {
	if ($row['source'] === "none") {
		?>
		<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			設定錯誤：欄位 <?=$row[0]?> 不存在於學生資料、手動輸入、內建函數
		</div>
		<?php
	}
}

?>
<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
<title><?=$C["titlename"]?>/管理學生下載格式</title>

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
<div class="container-fluid">
	<h2>管理學生下載格式</h2>
	<div class="row">
		<div class="col-12 col-md-4">
			<form action="" method="post">
				<button class="btn btn-default" type="submit" name="grid"><i class="fa fa-eye" aria-hidden="true"></i> 檢視格線檔</button>
				<button class="btn btn-default" type="submit" name="preview"><i class="fa fa-eye" aria-hidden="true"></i> 檢視預覽檔</button>
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
	<div class="row">
		<div class="col">
			<form action="" method="post" class="form-inline">
				<div class="table-responsive">
					<table class="table">
						<tr>
							<th>欄位</th>
							<th>位置(X軸)</th>
							<th>位置(Y軸)</th>
							<th>字型</th>
							<th>文字大小</th>
							<th>樣式</th>
							<th>對齊</th>
							<th>操作</th>
						</tr>
						<?php
						foreach ($D['student'] as $hash => $row) {
						?>
						<tr <?=($row['source']==='none'?' class="table-danger"':'')?>>
							<td><input class="form-control" type="text" name="format[<?=$hash?>][column]" value="<?=$row[0]?>"> (<?=$G["source_text"][$row['source']]?>)</td>
							<td><input class="form-control" type="number" name="format[<?=$hash?>][positionx]" value="<?=$row[1]?>" style="width: 80px;">~<input class="form-control" type="number" name="format[<?=$hash?>][positionx2]" value="<?=$row[2]?>" style="width: 80px;"> (<?=$row[2]-$row[1]?>)</td>
							<td><input class="form-control" type="number" name="format[<?=$hash?>][positiony]" value="<?=$row[3]?>" style="width: 80px;"></td>
							<td>
								<select class="form-control" name="format[<?=$hash?>][font]">
									<?php
									foreach ($D['fonts'] as $font) {
										?>
										<option value="<?=$font["name"]?>" <?=($row[4]==$font["name"]?"selected":"")?> ><?=$font["text"]?></option>
										<?php
									}
									?>
								</select>
							</td>
							<td><input class="form-control" type="number" name="format[<?=$hash?>][size]" value="<?=$row[5]?>" style="width: 80px;"></td>
							<td>
								<select class="form-control" name="format[<?=$hash?>][style]">
									<option value="" <?=($row[6]==""?"selected":"")?> >無</option>
									<option value="B" <?=($row[6]=="B"?"selected":"")?> >粗體</option>
									<option value="I" <?=($row[6]=="I"?"selected":"")?> >斜體</option>
									<option value="U" <?=($row[6]=="U"?"selected":"")?> >底線</option>
									<option value="BI" <?=($row[6]=="BI"?"selected":"")?> >粗斜</option>
									<option value="BU" <?=($row[6]=="BU"?"selected":"")?> >粗底</option>
									<option value="IU" <?=($row[6]=="IU"?"selected":"")?> >斜底</option>
									<option value="BIU" <?=($row[6]=="BIU"?"selected":"")?> >粗斜底</option>
								</select>
							</td>
							<td>
								<select class="form-control" name="format[<?=$hash?>][align]">
									<option value="L" <?=($row[7]=="L"?"selected":"")?> >靠左對齊</option>
									<option value="C" <?=($row[7]=="C"?"selected":"")?> >置中</option>
									<option value="R" <?=($row[7]=="R"?"selected":"")?> >靠右對齊</option>
									<option value="J" <?=($row[7]=="J"?"selected":"")?> >左右對齊</option>
								</select>
							</td>
							<td>
								<label><input type="checkbox" name="deleteitem[]" value="<?=$hash?>"> 刪除此項</label>
							</td>
						</tr>
						<?php
						}
						?>
					</table>
				</div>
				<div class="row">
					<div class="col">
						<button type="submit" name="edit" class="btn btn-success"><i class="fas fa-edit"></i> 編輯</button>
						<button type="submit" name="delete" class="btn btn-danger"><i class="fas fa-trash"></i> 刪除勾選的項目</button>
					</div>
				</div>
			</form>
		</div>
	</div>
	<h3 style="margin-top: 1em;">新增</h3>
	<div class="row">
		<div class="col">
			<form action="" method="post">
				<div class="row">
					<label class="col-sm-2 form-control-label">欄位</label>
					<div class="col-sm-10">
						<input class="form-control" type="text" name="column" required>
					</div>
				</div>
				<div class="row">
					<label class="col-sm-2 form-control-label">位置(X軸，左)</label>
					<div class="col-sm-10">
						<input class="form-control" type="number" name="positionx" required>
					</div>
				</div>
				<div class="row">
					<label class="col-sm-2 form-control-label">位置(X軸，右)</label>
					<div class="col-sm-10">
						<input class="form-control" type="number" name="positionx2" required>
					</div>
				</div>
				<div class="row">
					<label class="col-sm-2 form-control-label">位置(Y軸)</label>
					<div class="col-sm-10">
						<input class="form-control" type="number" name="positiony" required>
					</div>
				</div>
				<div class="row">
					<label class="col-sm-2 form-control-label">字型</label>
					<div class="col-sm-10">
						<select class="form-control" name="font">
							<?php
							foreach ($D['fonts'] as $font) {
								?>
								<option value="<?=$font["name"]?>"><?=$font["text"]?></option>
								<?php
							}
							?>
						</select>
					</div>
				</div>
				<div class="row">
					<label class="col-sm-2 form-control-label">文字大小</label>
					<div class="col-sm-10">
						<input class="form-control" type="number" name="size" value="16" required>
					</div>
				</div>
				<div class="row">
					<label class="col-sm-2 form-control-label">樣式</label>
					<div class="col-sm-10">
						<select class="form-control" name="style">
							<option value="" selected>無</option>
							<option value="B">粗體</option>
							<option value="I">斜體</option>
							<option value="U">底線</option>
							<option value="BI">粗體+斜體</option>
							<option value="BU">粗體+底線</option>
							<option value="IU">斜體+底線</option>
							<option value="BIU">粗體+斜體+底線</option>
						</select>
					</div>
				</div>
				<div class="row">
					<label class="col-sm-2 form-control-label">對齊</label>
					<div class="col-sm-10">
						<select class="form-control" name="align">
							<option value="L">靠左對齊</option>
							<option value="C" selected>置中</option>
							<option value="R">靠右對齊</option>
							<option value="J">左右對齊</option>
						</select>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-10 offset-sm-2">
						<button type="submit" class="btn btn-success" name="new"><i class="fa fa-plus" aria-hidden="true"></i> 新增</button>
					</div>
				</div>
			</form>
		</div>
	</div>
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
