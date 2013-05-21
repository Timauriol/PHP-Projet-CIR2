<?php
include("config.php");

// Connexion à la base de donnée conge

try
{
    $options = array(
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
    );
    $db = new PDO($SQL_DSN, $SQL_USER, $SQL_PASS, $options);
}
catch (PDOException $e) {
  die("Echec : " . $e->getMessage());
}
?>
