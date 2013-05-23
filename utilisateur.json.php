<?php
include("utilisateur.class.php");

session_start(); // initialise $_SESSION

if(!isset($_SESSION["conge_login"]) || $_SESSION["conge_login"] === "" || (new Utilisateur($_SESSION["conge_login"]))->login === ""){
    header("HTTP/1.1 403 Forbidden");
    die("[]");
}

header("Content-Type: application/json");

include("config.php");
include("db.php");

$utilisateurs = isset($_GET["q"])?Utilisateur::recherche($_GET["q"], 4):Utilisateur::tous();

$echappement = array('\\' => '\\\\', '"' => '\\"');

echo("[\n");
$premier = true;
foreach($utilisateurs as $u){
    if($premier)
        $premier = false;
    else
        echo(",");
?>
    {
        "login": "<?=strtr($u->login, $echappement)?>",
        "nom_prenom": "<?=strtr($u->nom_prenom, $echappement)?>",
        "admin": <?=$u->admin?"true":"false"?>
    }
<?php
}
echo("]\n");
?>
