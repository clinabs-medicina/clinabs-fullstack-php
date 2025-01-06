<?php
$icon = $_GET['icon'];
$color = $_GET['color'];


$data = file_get_contents("{$icon}-solid.svg");
$data = str_replace('/>', ' fill="'.$color.'"/>', $data);
header('Content-Type: image/svg+xml');

echo $data;