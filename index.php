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

<table class="calendrier">
<tr>
<th>Lundi</th>
<th>Mardi</th>
<th>Mercredi</th>
<th>Jeudi</th>
<th>Vendredi</th>
<th>Samedi</th>
<th>Dimanche</th>
</tr>
<?php
for($ligne = 0; $ligne < 6; $ligne++){
    echo("<tr>");
    for($colonne = 0; $colonne < 7; $colonne++){
    ?>
<td>
    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" viewbox="0 0 100 100" preserveAspectRatio="none">
        <polygon fill="#9cf" points="0,0 0,100 100,0"/>
        <polygon fill="#af9" points="100,100 0,100 100,0"/>
    </svg>
</td>
    <?php
    }
    echo("</tr>");
}
?>
</table>

<script src="static/cal.js"></script>

<?php
include("_tail.php");

?>
