<?php
require "vendor/autoload.php";

use Dompdf\Dompdf;

$pdf = new Dompdf();
$pdf->loadHtml("<h1>DomPDF OK</h1><p>Xuất PDF thành công</p>");
$pdf->render();
$pdf->stream("test.pdf", ["Attachment"=>false]);
