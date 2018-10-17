<?php
require_once(__DIR__."/config/config.php");
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
	writelog(sprintf("[manage_fonts] %s view no premission.", $U["ip"]));
}

?>
<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
<title><?=$C["titlename"]?>/查看字型</title>

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
	<h2>查看字型</h2>
	<div class="row">
		<div class="col">
			<div class="table-responsive">
				<table class="table">
					<tr>
						<th>字型名</th>
						<th>常用名</th>
					</tr>
					<?php
					foreach ($D['fonts'] as $font) {
					?>
					<tr>
						<td><?=$font["name"]?></td>
						<td><?=$font["text"]?></td>
					</tr>
					<?php
					}
					?>
				</table>
			</div>
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
