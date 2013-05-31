<?php
include_once("config.php");
header("Content-Type: text/html; charset=UTF-8");
?>
<!doctype html>
<html>
    <head>
        <title>Super congé 2000<?=isset($title)?" - " . $title : ""?></title>
        <link rel="stylesheet" type="text/css" href="<?=$WEBROOT?>/static/style.css"/>
    </head>
    <body>
