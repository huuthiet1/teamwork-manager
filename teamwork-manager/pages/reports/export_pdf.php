<?php
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set("display_errors", 0);

require_once "../../vendor/autoload.php";
use Dompdf\Dompdf;

$group_id = isset($_GET["group_id"]) ? (int)$_GET["group_id"] : 0;
if ($group_id<=0) die("Thiếu group_id");

ob_start();
include "pdf_template.php";
$html = ob_get_clean();

$pdf = new Dompdf(["isHtml5ParserEnabled"=>true,"isRemoteEnabled"=>true]);
$pdf->loadHtml($html,"UTF-8");
$pdf->setPaper("A4","portrait");
$pdf->render();
$pdf->stream("bao_cao_nhom_$group_id.pdf", ["Attachment"=>false]);
