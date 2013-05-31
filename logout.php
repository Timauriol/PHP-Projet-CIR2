<?php
include_once("outils.php");

session_start();
$_SESSION["conge_login"] = "";

redirect("login.php");
?>
