<!DOCTYPE html>
<?php
require("config/config.php");
?>
<html lang="zh-Hant-TW">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
<title><?=$C["titlename"]?></title>

<style type="text/css">
body {
	padding-top: 4.5rem;
}
</style>

</head>
<body>

<?php
require("header.php");
?>
<div class="container">
	<div class="jumbotron">
		<h1><?=$C["sitename"]?></h1>
		<p class="lead"></p>
		<p>
			學生用
		</p>
		<p>
			<a class="btn btn-lg btn-success" href="<?=$C["path"]?>/student/" role="button">
				<i class="fa fa-graduation-cap" aria-hidden="true"></i>
				學生自行下載
			</a>
		</p>
		<p>
			學校用
		</p>
		<p>
			<a class="btn btn-lg btn-primary" href="<?=$C["path"]?>/school/" role="button">
				<i class="fas fa-school"></i>
				學校下載
			</a>
			<a class="btn btn-lg btn-primary" href="<?=$C["path"]?>/manage/data/" role="button">
				<i class="fas fa-database"></i>
				管理學生資料
			</a>
			<a class="btn btn-lg btn-primary" href="<?=$C["path"]?>/manage/studentinput/" role="button">
				<i class="fa fa-graduation-cap" aria-hidden="true"></i>
				管理學生下載輸入欄位
			</a>
			<a class="btn btn-lg btn-primary" href="<?=$C["path"]?>/manage/studentformat/" role="button">
				<i class="fa fa-graduation-cap" aria-hidden="true"></i>
				管理學生下載格式
			</a>
			<a class="btn btn-lg btn-primary" href="<?=$C["path"]?>/manage/schoolinput/" role="button">
				<i class="fas fa-school"></i>
				管理學校下載輸入欄位
			</a>
			<a class="btn btn-lg btn-primary" href="<?=$C["path"]?>/manage/schoolformat/" role="button">
				<i class="fas fa-school"></i>
				管理學校下載格式
			</a>
			<a class="btn btn-lg btn-primary" href="<?=$C["path"]?>/manage/function/" role="button">
				<i class="fas fa-cogs"></i>
				查看內建函數
			</a>
			<a class="btn btn-lg btn-primary" href="<?=$C["path"]?>/manage/fonts/" role="button">
				<i class="fas fa-font"></i>
				查看字型
			</a>
			<a class="btn btn-lg btn-primary" href="<?=$C["path"]?>/manage/pdf/" role="button">
				<i class="fas fa-file-pdf"></i>
				管理PDF
			</a>
			<a class="btn btn-lg btn-primary" href="<?=$C["path"]?>/manage/account/" role="button">
				<i class="fa fa-user" aria-hidden="true"></i>
				管理帳號
			</a>
		</p>
	</div>
</div>

<?php
require("footer.php");
?>
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous"></body>
</html>
