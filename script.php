<?php
header("Content-Type: text/html; charset=utf-8");
mb_internal_encoding("UTF-8");
mb_http_output("UTF-8");

function getDeclensions($words, $case) {
    $url = "https://ws3.morpher.ru/russian/declension?case=" . urlencode($case);
    $url .= "&s=" . urlencode(implode(",", $words));

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPGET, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        echo 'Error: ' . curl_error($ch);
    }

    curl_close($ch);

    return $response;
}

function processTxt($inputFile, $outputFile, $case) {
    $inputHandle = fopen($inputFile, 'r');
    $outputHandle = fopen($outputFile, 'w');

    if ($inputHandle !== false && $outputHandle !== false) {
        while (($line = fgets($inputHandle)) !== false) {
            $word = trim($line); 
            if (!empty($word)) {
                $declensions = getDeclensions([$word], $case); 
                fwrite($outputHandle, $declensions . PHP_EOL);
            }
        }
        fclose($inputHandle);
        fclose($outputHandle);
    }
}

$inputFile = $_FILES['file']['tmp_name'];
$outputFile = 'output.txt';
$case = $_POST['case'];

processTxt($inputFile, $outputFile, $case);
echo "Склонение завершено. Проверьте файл <a href='$outputFile'>$outputFile</a>.";
?>