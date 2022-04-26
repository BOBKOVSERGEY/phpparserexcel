<?php

function dump($arr)
{
    echo '<pre>';
    var_dump($arr);
    echo '</pre>';
}

header('Content-type: text/html; charset=utf-8');
setlocale(LC_ALL, 'ru_RU.UTF-8');

ini_set('error_reporting', E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require __DIR__ . './phpQuery-onefile.php';

require_once __DIR__ . './PHPExcel-1.8/Classes/PHPExcel.php';
require_once __DIR__ . './PHPExcel-1.8/Classes/PHPExcel/Writer/Excel2007.php';

$jsonDataProduct = file_get_contents("data_product.txt");
$dataProducts = json_decode($jsonDataProduct, true);

$xls = new PHPExcel();
$xls->setActiveSheetIndex(0);
$sheet = $xls->getActiveSheet();

$sheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
$sheet->getPageSetup()->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

$sheet->getPageMargins()->setTop(0.75);
$sheet->getPageMargins()->setRight(0.75);
$sheet->getPageMargins()->setLeft(0.75);
$sheet->getPageMargins()->setBottom(1);

$sheet->getDefaultRowDimension()->setRowHeight(22);

$sheet->setTitle("Облавка");
$sheet->setCellValue('A1', 'Название товара');
$sheet->setCellValue('B1', 'Цена');
$sheet->setCellValue('C1', 'Старая цена');
$sheet->setCellValue('D1', 'Ссылка');
$sheet->setCellValue('E1', 'Изображения');
$sheet->setCellValue('F1', 'Описание');

$sheet->setCellValue('F2', date('H:i:s d.m.y'));

foreach ($dataProducts as $key => $product) {
    $index = $key + 3;

    $productParam = $product['listMainParams'];
    //$image = $product['listImages'][0];
    $image = implode(';', $product['listImages']);

    $sheet->setCellValue('A'.$index, $productParam['name']);
    $sheet->setCellValue('B'.$index, $productParam['price']);
    $sheet->setCellValue('C'.$index, $productParam['oldPrice']);
    $sheet->setCellValue('D'.$index, $productParam['url']);
    $sheet->setCellValue('E'.$index, $image);
    $sheet->setCellValue('F'.$index, $productParam['description']);


}

$objWriter = new PHPExcel_Writer_Excel2007($xls);
$filePath = __DIR__ .'./file_catalog.xlsx';
$objWriter->save($filePath);