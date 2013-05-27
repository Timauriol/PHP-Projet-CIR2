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
        <li id="bouton-global"><a>Calendrier global</a>
            <input type="text" class="annee" placeholder="Année"/>
        </li>
        <li id="bouton-util"><a>Calendrier employé</a>
            <input type="text" class="recherche" placeholder="Nom ou login"/>
            <ul class="autocomplete">
            </ul>
        </li>
        <li><a href="logout.php">Déconnexion</a></li>
    </ul>
</nav>

<div class="container">

<div class="barre-outils">
<div class="bouton ajout" title="Ajouter"><span>&#10133;</span></div>
<div class="bouton deplacement" title="Déplacer"><span>&#9998;</span></div>
<div class="bouton suppression" title="Supprimer"><span>&#10060;</span></div>
<div class="bouton mois-suivant" title="Mois suivant"><span>▸</span></div>
<div class="bouton mois-precedent" title="Mois précédent"><span>◂</span></div>
<div class="bouton selecteur-mois"><span class="date">Janvier 1985</span> <span>▾</span></div>
<div style="clear:both;"></div>
</div>
<table class="jours">
<tr>
<th>Lundi</th>
<th>Mardi</th>
<th>Mercredi</th>
<th>Jeudi</th>
<th>Vendredi</th>
<th>Samedi</th>
<th>Dimanche</th>
</tr>
</table>
<table class="calendrier">
<col/> <col/> <col/> <col/> <col/> <col/> <col/>
<?php
for($ligne = 0; $ligne < 6; $ligne++){
    echo("<tr>");
    for($colonne = 0; $colonne < 7; $colonne++){
    ?>
<td>
    <div class="numjour"></div>
    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" viewbox="0 0 100 100" preserveAspectRatio="none">
        <polygon class="matin" points="0,0 0,101 101,0"/>
        <polygon class="apresmidi" points="100,100 0,100 100,0"/>
    </svg>
</td>
    <?php
    }
    echo("</tr>");
}
?>
</table>
</div>

<script src="static/cal.js"></script>

<?php
include("_tail.php");

?>
