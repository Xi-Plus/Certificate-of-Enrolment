<?php
require_once(__DIR__.'/config/config.php');
require_once(__DIR__.'/func/log.php');

if (!isset($_SESSION["input"])) {
	writelog(sprintf("[download] %s no input. %s", $U["ip"], json_encode($_SESSION)));
}

use setasign\Fpdi;
require_once(__DIR__.'/vendor/autoload.php');

$pdf = new setasign\Fpdi\TcpdfFpdi();
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->AddPage();
if ($_SESSION["file"] === "student") {
	$pageCount = $pdf->setSourceFile(__DIR__.'/data/student.pdf');
} else if ($_SESSION["file"] === "school") {
	$pageCount = $pdf->setSourceFile(__DIR__.'/data/school.pdf');
} else {
	writelog(sprintf("[download] %s no file. %s", $U["ip"], json_encode($_SESSION)));
	exit("發生錯誤");
}
$page = $pdf->ImportPage(1);
$pdf->useTemplate($page, 0, 0);

$fontconfig = file_get_contents(__DIR__.'/data/fonts.json');
if ($fontconfig === false) {
	writelog(sprintf("[download] %s read fonts config failed. %s", $U["ip"], json_encode($_SESSION)));
	exit('read fonts config failed.\n');
}
$fontconfig = json_decode($fontconfig, true);
$fonts = [];
foreach ($fontconfig as $font) {
	$fonts[$font['name']] = TCPDF_FONTS::addTTFfont($font['path'], 'TrueTypeUnicode', '', 96);
}

if (in_array($_SESSION["mode"], ["download", "preview"])) {
	if ($_SESSION["file"] === "student") {
		$handle = fopen(__DIR__."/data/student.csv", "r");
		if ($handle === false) {
			writelog(sprintf("[download] %s read student.csv failed. %s", $U["ip"], json_encode($_SESSION)));
			exit("取得student.csv錯誤");
		}
	} else if ($_SESSION["file"] === "school") {
		$handle = fopen(__DIR__."/data/school.csv", "r");
		if ($handle === false) {
			writelog(sprintf("[download] %s read school.csv failed. %s", $U["ip"], json_encode($_SESSION)));
			exit("取得school.csv錯誤");
		}
	}

	while (($data = fgetcsv($handle)) !== false) {
		if (count($data) <= 1 && $data[0] == "") {
			continue;
		}

		if ($_SESSION["mode"] === "preview") {
			$content = $data[0];
		} else if (isset($_SESSION["input"][$data[0]])) {
			$content = $_SESSION["input"][$data[0]];
		} else if (isset($C["function"][$data[0]])) {
			$content = $C["function"][$data[0]]();
		} else {
			writelog(sprintf("[download] %s read data %s failed. %s", $U["ip"], $data[0], json_encode($_SESSION)));
			echo "取得資料".$data[0]."失敗\n";
			exit;
		}
		$positionx = $data[1];
		$positionx2 = $data[2];
		$width = $positionx2-$positionx;
		$positiony = $data[3];
		$font = $data[4];
		$size = $data[5];
		$style = $data[6];
		$align = $data[7];

		$pdf->SetXY($positionx, $positiony);

		if ($_SESSION["mode"] === "download") {
			$pdf->SetFont($fonts[$font], $style, $size, '', false);
			$pdf->Cell($width, 0, $content, 0, 0, $align);
		} else if ($_SESSION["mode"] === "preview") {
			$pdf->SetFont($fonts[$font], $style, 10, '', false);
			$pdf->Cell($width, 0, $content, 0, 0, $align);

			$pdf->SetXY($positionx, $positiony);
			$pdf->SetFont($fonts[$font], $style, $size, '', false);
			$pdf->Cell($width, 0, "", 1, 0, $align);
		}
	}
	fclose($handle);

} else if ($_SESSION["mode"] === "grid") {
	for ($x=0; $x <= 200; $x+=10) {
		for ($y=0; $y <= 260; $y+=10) {
			$pdf->SetXY($x, $y);
			if ($x == 0) {
				$pdf->Cell(10, 10, $y, 1);
			} else {
				$pdf->Cell(10, 10, $x, 1);
			}
		}
	}
}

$filename = "Certificate of Enrolment (".ucfirst($_SESSION["file"]).")";
if ($_SESSION["mode"] === "preview") {
	$filename .= " (Preview)";
}
if ($_SESSION["mode"] === "grid") {
	$filename .= " (Grid)";
}
$filename .= ".pdf";

$pdf->Output($filename);
