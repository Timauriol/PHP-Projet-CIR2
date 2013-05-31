<?php
include_once("db.php");
include_once("utilisateur.class.php");

session_start();

header("Content-Type: application/json");

if(!isset($_SESSION["conge_login"]) || $_SESSION["conge_login"] === "" || (new Utilisateur($_SESSION["conge_login"]))->login === ""){
    header("HTTP/1.1 403 Forbidden");
    die("{}");
}

if(!isset($_GET["login"]) || !isset($_GET["annee"]))
    die("{}");

$annee = $_GET["annee"];
$u = new Utilisateur($_GET["login"]);

if($u->login == "")
    die("{}");

if(isset($_POST["changement"]))
    $u->editSolde($_POST["changement"], $annee);

$solde = $u->getSolde($annee);

?>
{"solde": <?=$solde?>}
