<?php

$keys = [ "hlx", "security", "user", "standard" ];

$array = [];
$arrayPtr = &$array;
foreach ($keys as $key) {
    $arrayPtr[$key] = [];
    $arrayPtr = &$arrayPtr[$key];
}

var_dump($array);
