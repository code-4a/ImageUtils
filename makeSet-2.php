<?php

const TEMPLATE_TYPE1 = '#^\d{1,2}-\w+-\d+\.jpg$#';
const TEMPLATE_TYPE2 = '#^DSCN\d+\.JPG$#';


function getFilesSequence($files)
{
    $filesType1 = [];
    $filesType2 = [];
    $result = [];
    foreach ($files as $name) {
        if(preg_match(TEMPLATE_TYPE1, $name)) {
            $filesType1[] = $name;
        }
        if(preg_match(TEMPLATE_TYPE2, $name)) {
            $filesType2[] = $name;
        }
    }
    if(count($filesType1) != count($filesType2)) return false;
    foreach ($filesType1 as $index => $name) {
        $result[] = [$name, $filesType2[$index]];
    }
    return $result;
}

function getResultFileName($set)
{
  $mtime = date('dmY', filectime($set[1]));
  $result_name = pathinfo($set[0])['filename'] . '-' . $mtime . '.jpg';
  return $result_name;
}

$files = getFilesSequence($argv);
foreach ($files as $set) {
    $parameters = implode(' ', $set) . ' ' . getResultFileName($set);
    // Уменьшить размер и сделать рамку
    `convert $set[1] -resize x793 -bordercolor white -border 3 $set[1]`;
    // Сделать общий файл из двух изображений
    `convert +append $parameters`;
    foreach ($set as $file) {
        unlink($file);
    }
}