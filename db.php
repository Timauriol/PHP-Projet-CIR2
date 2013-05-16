<?php
include("config.php");

// Connexion à la base de donnée conge

try
{
	$db = new PDO($SQL_DSN, $SQL_USER, $SQL_PASS);
}
catch (PDOException $e) {                 
  echo "Echec : " . $e->getMessage();
}
?>
