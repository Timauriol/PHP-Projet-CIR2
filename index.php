<?php
include_once("db.php");
include_once("outils.php");
include_once("utilisateur.class.php");

session_start(); // initialise $_SESSION

if(!isset($_SESSION["conge_login"]) || $_SESSION["conge_login"] == "")
    redirect("login.php"); // l'utilisateur n'est pas connecté, on redirige

$user = new Utilisateur($_SESSION["conge_login"]);

if($user->login == ""){
    $_SESSION["conge_login"] = "";
    redirect("login.php"); // l'utilisateur n'existe pas
}

include("_head.php");
?>
<nav>
    <ul>
        <li><a <?=isset($_GET["utilisateur"])?'':'class="actif"'?> href=".">Calendrier global</a></li>
        <li><a <?=isset($_GET["utilisateur"])?'class="actif"':''?> href=".">Calendrier utilisateur</a></li>
        <li><a href="logout.php">Déconnexion</a></li>
    </ul>
</nav>

<p>Bienvenue, <?=$user->nom_prenom?>!</p>

<?php
include("_tail.php");

?>
