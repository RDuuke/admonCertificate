<?php

function moveUploadedFile(\Psr\Http\Message\UploadedFileInterface $uploadedFile)
{
    $extension = pathinfo($uploadedFile->getClientFilename(), PATHINFO_EXTENSION);
    $basename = bin2hex(random_bytes(8)); // see http://php.net/manual/en/function.random-bytes.php
    $filename = sprintf('%s.%0.8s', $basename, $extension);
    $uploadedFile->moveTo(BASE_DIR . "temp" . DS . $filename);
    return $filename;
}

function getHighestDataRow($worksheet) : array
{

    $array = array_filter(array_map("array_filter",$worksheet->toArray()));
    unset($array[0]);
    return $array;
}