<?php
/*
permet de récupérer une liste d'utilisateurs à partir d'une chaîne de caractères de recherche
 */
include_once("outils.php");

header("Content-Type: application/json");

if(!estConnecte()){
    header("HTTP/1.1 403 Forbidden");
    echo("[]");
}
else{

    include_once("utilisateur.class.php");
    include_once("config.php");
    include_once("db.class.php");

    $utilisateurs = isset($_GET["q"])?Utilisateur::recherche($_GET["q"], 4):Utilisateur::tous();
    echo(json_encode($utilisateurs));
}
?>
