<?php
include_once("utilisateur.class.php");
include_once("outils.php");
header("Content-Type: application/json");

if(!estConnecte())
    header("HTTP/1.1 403 Forbidden");
    echo("[]");
}
else{


    include_once("config.php");
    include_once("db.php");

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
}
?>
