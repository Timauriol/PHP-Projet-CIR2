<?php
include_once("outils.php");

// vide la session et redirige sur le formulaire de connexion

session_start();
$_SESSION["conge_login"] = "";

redirect("login.php");
?>
