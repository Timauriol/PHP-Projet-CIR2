<?php
/*
retourne le solde pour un utilisateur et une année
*/
include_once("db.class.php");
include_once("utilisateur.class.php");
include_once("outils.php");

header("Content-Type: application/json");

if(!estConnecte()){
    header("HTTP/1.1 403 Forbidden");
    echo("{}");
}
else if(!isset($_GET["login"]) || !isset($_GET["annee"]))
    echo("{}");
else {
    $annee = $_GET["annee"];
    $u = new Utilisateur($_GET["login"]);

    if($u->login == ""){
        echo("{}");
    } else {
        if(isset($_POST["changement"]))
            $u->editSolde($_POST["changement"], $annee);

        $solde = $u->getSolde($annee);

        ?>
        {"solde": <?=$solde?>}
<?php
    }
}
?>
