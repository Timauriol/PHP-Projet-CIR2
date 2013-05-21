<?php
header("content-type", "application/json; charset=UTF-8");

include("config.php");
include("db.php");
include("utilisateur.class.php");

$utilisateurs = isset($_GET["q"])?Utilisateur::recherche($_GET["q"]):Utilisateur::tous();

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
