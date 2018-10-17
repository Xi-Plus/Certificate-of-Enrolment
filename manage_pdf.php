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
	writelog(sprintf("[manage_pdf] %s view no premission.", $U["ip"]));
}

if ($showform && isset($_POST["download"])) {
	if (!in_array($_POST["download"], ["student", "school"])) {
		?>
		<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			下載失敗：未知的下載目標
		</div>
		<?php
		writelog(sprintf("[manage_pdf] %s %s download failed. no target. %s", $U["data"]["account"], $U["ip"], json_encode($_POST)));
	} else {
		header('Content-Description: File Transfer');
		header('Content-Type: application/pdf; charset=utf-8');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		if ($_POST["download"] === "student") {
			header('Content-Disposition: attachment; filename="student.pdf"');
			@readfile(__DIR__."/data/student.pdf");
		} else if ($_POST["download"] === "school") {
			header('Content-Disposition: attachment; filename="school.pdf"');
			@readfile(__DIR__."/data/school.pdf");
		}
		writelog(sprintf("[manage_pdf] %s %s download successed. %s", $U["data"]["account"], $U["ip"], json_encode($_POST)));
		exit;
	}
}

if ($showform && isset($_POST["upload"])) {
	if (isset($_FILES["file"])) {
		if ($_FILES["file"]["error"] == 0) {
			?>
			<div class="alert alert-info alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				檔案上傳成功
			</div>
			<?php
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$filemime = finfo_file($finfo, $_FILES["file"]["tmp_name"]);
			finfo_close($finfo);
			if (!file_exists($_FILES["file"]["tmp_name"])) {
				?>
				<div class="alert alert-danger alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					更新失敗：檔案讀取錯誤
				</div>
				<?php
				writelog(sprintf("[manage_pdf] %s %s upload failed. read failed.", $U["data"]["account"], $U["ip"]));
			} else if ($filemime !== "application/pdf") {
				?>
				<div class="alert alert-danger alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					更新失敗：檔案格式可能不是PDF檔
				</div>
				<?php
				writelog(sprintf("[manage_pdf] %s %s upload failed. not pdf.", $U["data"]["account"], $U["ip"]));
			} else if (!in_array($_FILES["file"]["type"], $G["pdfmime"])) {
				?>
				<div class="alert alert-warning alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					更新失敗：檔案格式可能不是PDF檔，你的檔案格式是<?=$_FILES["file"]["type"]?>
				</div>
				<?php
				writelog(sprintf("[manage_pdf] %s %s upload failed. not pdf: %s.", $U["data"]["account"], $U["ip"], $_FILES["file"]["type"]));
			} else {
				$res = true;
				if ($_POST["upload"] === "student") {
					$res = move_uploaded_file($_FILES["file"]["tmp_name"], __DIR__."/data/student.pdf");
				} else if ($_POST["upload"] === "school") {
					$res = move_uploaded_file($_FILES["file"]["tmp_name"], __DIR__."/data/school.pdf");
				} else {
					?>
					<div class="alert alert-danger alert-dismissible" role="alert">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						更新失敗：未知的更新目標
					</div>
					<?php
					writelog(sprintf("[manage_pdf] %s %s upload failed. no target.", $U["data"]["account"], $U["ip"]));
				}
				if ($res) {
					?>
					<div class="alert alert-success alert-dismissible" role="alert">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						更新成功
					</div>
					<?php
					writelog(sprintf("[manage_pdf] %s %s upload successed. %s.", $U["data"]["account"], $U["ip"], $_POST["upload"]));
				} else {
					?>
					<div class="alert alert-danger alert-dismissible" role="alert">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						更新失敗：覆寫檔案出錯
					</div>
					<?php
					writelog(sprintf("[manage_pdf] %s %s upload failed. rewrite failed: %s.", $U["data"]["account"], $U["ip"], $_POST["upload"]));
				}
			}
		} else {
		?>
		<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			檔案上傳失敗，錯誤代碼：<?=$_FILES["file"]["error"]?>
		</div>
		<?php
			writelog(sprintf("[manage_pdf] %s %s upload failed. upload error code: %s.", $U["data"]["account"], $U["ip"], $_FILES["file"]["error"]));
		}
	} else {
		?>
		<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			發生未知錯誤
		</div>
		<?php
		writelog(sprintf("[manage_pdf] %s %s upload failed.", $U["data"]["account"], $U["ip"]));
	}
}
?>
<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
<title><?=$C["titlename"]?>/管理PDF</title>

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
	<h2>管理PDF</h2>

	<h3 style="margin-top: 1em;">學生下載</h3>
	<form action="" method="post" enctype="multipart/form-data" class="form-inline">
		<div class="form-group">
			<label class="form-control-label" for="file">選擇檔案：</label>
			<input type="file" id="file" name="file" accept=".pdf" class="form-control-file" required>
		</div>
		<span style="color: red;">（會直接覆蓋舊檔案）</span>
		<button class="btn btn-success" type="submit" name="upload" value="student"><i class="fa fa-upload" aria-hidden="true"></i> 上傳</button> 
	</form>
	<form action="" method="post">
		<button class="btn btn-success" type="submit" name="download" value="student"><i class="fa fa-download" aria-hidden="true"></i> 下載</button>
	</form>

	<h3 style="margin-top: 1em;">學校下載</h3>
	<form action="" method="post" enctype="multipart/form-data" class="form-inline">
		<div class="form-group">
			<label class="form-control-label" for="file">選擇檔案：</label>
			<input type="file" id="file" name="file" accept=".pdf" class="form-control-file" required>
		</div>
		<span style="color: red;">（會直接覆蓋舊檔案）</span>
		<button class="btn btn-success" type="submit" name="upload" value="school"><i class="fa fa-upload" aria-hidden="true"></i> 上傳</button> 
	</form>
	<form action="" method="post">
		<button class="btn btn-success" type="submit" name="download" value="school"><i class="fa fa-download" aria-hidden="true"></i> 下載</button>
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
