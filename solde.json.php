<?php
include_once("db.php");
include_once("utilisateur.class.php");

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
