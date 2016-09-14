<?php
const FILES_IN_SET = 2;
const FILE_NAME_TEMPLATE = 'Set-%s.jpg';

function getFilesSequence($files, $filesPerNode)
{
    $counter = 0;
    $resultIndex = 0;
    $result = [];
    foreach ($files as $index => $name) {
        if($index == 0) continue;
        $result[$resultIndex][] = $name;
        $counter++;
        if($counter == $filesPerNode) {
            $counter = 0;
            $resultIndex++;
        }  
    }
    return $result;
}

function getNextNumber()
{
    static $maxNumber = 0;
    if($maxNumber > 0) return ++$maxNumber;
    foreach (glob(sprintf(FILE_NAME_TEMPLATE, '*')) as $name) {
        $matches = [];
        $regExp = '#^' . sprintf(FILE_NAME_TEMPLATE, '(\d+)\\') . '$#';
        if(preg_match($regExp, $name, $matches)) {
            $number = (int)$matches[1];
            if($number > $maxNumber) $maxNumber = $number;
        }
    }
    return ++$maxNumber;
}


$sets = getFilesSequence($argv, FILES_IN_SET);
foreach ($sets as $set) {
    if(count($set) != FILES_IN_SET) break;
    foreach ($set as $file) {
        // Уменьшить размер и сделать рамку
        `convert $file -resize x395 -bordercolor white -border 3 $file`;
    }
    $parameters = implode(' ', $set) . ' '
            . sprintf(FILE_NAME_TEMPLATE, getNextNumber());
    // Сделать общий файл из двух изображений
    `convert -append $parameters`;
    foreach ($set as $file) {
        unlink($file);
    }
}

