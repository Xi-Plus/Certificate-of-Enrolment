<?php
require_once(__DIR__."/config/config.php");
require_once(__DIR__."/func/data.php");
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
}

$action = "none";
if (isset($_POST["action"])) {
	$action = $_POST["action"];
} else if (isset($_GET["action"]) && in_array($_GET["action"], ["view", "viewhead"])) {
	$action = $_GET["action"];
}

if ($showform && $action === "download") {
	writelog(sprintf("[manage_data] %s %s download", $U["data"]["account"], $U["ip"]));
	header('Content-Description: File Transfer');
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename="學生資料.csv"');
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	echo $G['BOM'];
	@readfile(__DIR__."/data/data-student.csv");
	exit;
}

if ($showform && $action === "upload") {
	writelog(sprintf("[manage_data] %s %s upload", $U["data"]["account"], $U["ip"]));
	if (isset($_FILES["file"])) {
		if ($_FILES["file"]["error"] == 0) {
			?>
			<div class="alert alert-info alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				檔案上傳成功
			</div>
			<?php
			$file = @file_get_contents($_FILES["file"]["tmp_name"]);
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$filemime = finfo_file($finfo, $_FILES["file"]["tmp_name"]);
			finfo_close($finfo);
			if ($file === false) {
				?>
				<div class="alert alert-danger alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					更新失敗：檔案讀取錯誤
				</div>
				<?php
				writelog(sprintf("[manage_data] %s %s upload failed. read failed.", $U["data"]["account"], $U["ip"]));
			} else if ($filemime !== "text/plain") {
				?>
				<div class="alert alert-danger alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					更新失敗：檔案格式可能不是純文字檔
				</div>
				<?php
				writelog(sprintf("[manage_data] %s %s upload failed. not plain text.", $U["data"]["account"], $U["ip"]));
			} else if (!in_array($_FILES["file"]["type"], $G["csvmime"])) {
				?>
				<div class="alert alert-warning alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					更新失敗：檔案格式可能不是逗號分隔值(CSV)，你的檔案格式是<?=$_FILES["file"]["type"]?>
				</div>
				<?php
				writelog(sprintf("[manage_data] %s %s upload failed. foramt: %s.", $U["data"]["account"], $U["ip"], $_FILES["file"]["type"]));
			} else {
				$encoding = mb_detect_encoding($file);
				if ($encoding === false) {
					?>
					<div class="alert alert-warning alert-dismissible" role="alert">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						訊息：無法檢測檔案編碼，將強制轉為UTF-8
					</div>
					<?php
					// $file = mb_convert_encoding($file, "UTF-8");
					$file = iconv("BIG5", "UTF-8//IGNORE", $file);
				} else {
					?>
					<div class="alert alert-info alert-dismissible" role="alert">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						訊息：檢測檔案編碼為<?=$encoding?><?=($encoding!=="UTF-8"?"，將轉為UTF-8":"")?>
					</div>
					<?php
					if ($encoding !== "UTF-8") {
						// $file = mb_convert_encoding($file, "UTF-8");
						$file = iconv($encoding, "UTF-8//IGNORE", $file);
					}
				}
				if (substr($file, 0, 3) == $G['BOM']) {
					?>
					<div class="alert alert-info alert-dismissible" role="alert">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						訊息：自動處理BOM
					</div>
					<?php
					$file = substr($file, 3);
				}
				$res = file_put_contents(__DIR__."/data/data-student.csv", $file);
				if ($res === false) {
					?>
					<div class="alert alert-danger alert-dismissible" role="alert">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						更新失敗：覆寫檔案出錯
					</div>
					<?php
					writelog(sprintf("[manage_data] %s %s upload failed. rewrite failed.", $U["data"]["account"], $U["ip"]));
				} else {
					writelog(sprintf("[manage_data] %s %s upload successed.", $U["data"]["account"], $U["ip"]));
					?>
					<div class="alert alert-success alert-dismissible" role="alert">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						更新成功
					</div>
					<?php
				}
			}
		} else {
		?>
		<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			檔案上傳失敗，錯誤代碼：<?=$_FILES["file"]["error"]?>
		</div>
		<?php
		writelog(sprintf("[manage_data] %s %s upload failed. upload error code: %s", $U["data"]["account"], $U["ip"], $_FILES["file"]["error"]));
		}
	} else {
		?>
		<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			發生未知錯誤
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
<title><?=$C["titlename"]?>/管理學生資料</title>

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
	<h2>管理學生資料</h2>
	<form action="" method="post" enctype="multipart/form-data" class="form-inline">
		<div class="form-group">
			<label class="form-control-label" for="file">選擇檔案：</label>
			<input type="file" id="file" name="file" accept=".csv" class="form-control-file" required>
		</div>
		<span style="color: red;">（會直接覆蓋舊檔案）</span>
		<button class="btn btn-success" type="submit" name="action" value="upload"><i class="fa fa-upload" aria-hidden="true"></i> 上傳</button> 
	</form>
	<form action="" method="post">
		<button class="btn btn-default" type="submit" name="action" value="view"><i class="fa fa-eye" aria-hidden="true"></i> 檢視所有資料</button>
		<button class="btn btn-default" type="submit" name="action" value="viewhead"><i class="fa fa-eye" aria-hidden="true"></i> 檢視欄位標題</button>
		<button class="btn btn-success" type="submit" name="action" value="download"><i class="fa fa-download" aria-hidden="true"></i> 下載</button>
	</form>
	<?php
	if (in_array($action, ["view", "viewhead"])) {
		writelog(sprintf("[manage_data] %s %s %s.", $U["data"]["account"], $U["ip"], $action));
	?>
	<div class="table-responsive">
		<table class="table">
			<tr>
				<?php
				foreach ($D['student_data']['head'] as $head) {
					?><th><?=$head?></th><?php
				}
				?>
			</tr>
			<?php
			if ($action === "view") {
				foreach ($D['student_data']['data'] as $data) {
				?>
				<tr>
					<?php
					foreach ($D['student_data']['head'] as $head) {
						?><td><?=$data[$head]?></td><?php
					}
					?>
				</tr>
				<?php
				}
			}
			?>
		</table>
	</div>
	<?php
	}
	?>
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
