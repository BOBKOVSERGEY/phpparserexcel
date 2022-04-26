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

function parser($urlPage)
{
    $ch = curl_init($urlPage);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, false);

    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}

$urlCatalog = "https://elektronika.store/";
$result = parser($urlCatalog);

$pq = phpQuery::newDocument($result);

/* получаем дполнительные параметры */
$listParamsProduct = $pq->find(".product-thumb-block a");

foreach ($listParamsProduct as $link) {
    $elemLink = pq($link);

    $arrLinks[] = "https://elektronika.store" . $elemLink->attr('href');
}



foreach ($arrLinks as $link) {
    $result = parser($link);
    $pq = phpQuery::newDocument($result);

    /*важные параметры для товара*/
    $arrMainParams = [
        'url' => $link,
        'price' => preg_replace("/[^0-9]*/", "", $pq->find("#price-field")->text()),
        'oldPrice' => preg_replace("/[^0-9]*/", "", $pq->find("#old-price-field")->text()),
        'name' => $pq->find('h1')->text(),
        'description' => trim($pq->find('#tab-description-content')->text()),
    ];

    /* получаем изображения */
    $listImages = $pq->find('a.js-varaint-image');
    foreach ($listImages as $image) {
        $elemImage = pq($image);

        $arrListImages[] = $elemImage->attr('data-image');
    }

    $arrListProduct[] = [
      'id' => '123',
      'listImages' => $arrListImages,
      'listMainParams' => $arrMainParams
    ];
}

//dump($arrListProduct);

$jsonDataProduct = json_encode($arrListProduct);

file_put_contents('data_product.txt', $jsonDataProduct);