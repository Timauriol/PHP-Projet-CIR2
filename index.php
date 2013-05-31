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
        <li id="bouton-global"><a>Congés partagés</a>
        </li>
        <li id="bouton-util"><a>Gestion des congés par employé</a>
            <input type="text" class="recherche" placeholder="Nom ou login"/>
            <ul class="autocomplete">
            </ul>
        </li>
        <li><a href="logout.php">Déconnexion</a></li>
    </ul>
</nav>

<div class="container init">
<table>
<col/><col/>
<tr>
    <td>Année civile</td><td><input type="text" class="annee">
        <div class="avertissement">⚠ <span class="reste">Il existe déjà des congés cette année. Ils seront écrasés lorsque vous enregistrerez. Cette opération est irréversible.</span></div>
    </td>
</tr>
<tr>
    <td>Congés</td><td>
        <ul class="conges"></ul>
        <a class="ajout-periode"><span>+</span> Ajouter une periode de congés</a>
    </td>
</tr>
<tr>
    <td>Demi-journées de congé mobiles</td><td><input type="text" class="solde" value=4></td>
</tr>
<tr>
    <td></td><td><button class="envoi">Envoyer</button></td>
</tr>
</table>
</div>




<div class="container cal">

<div class="barre-outils">
<div class="bouton ajout" title="Ajouter"><img src="static/plus.png"/></div>
<div class="bouton deplacement" title="Déplacer"><img src="static/depl.png"/></div>
<div class="bouton suppression" title="Supprimer"><img src="static/suppr.png"></div>
<div class="bouton mois-suivant" title="Mois suivant"><img src="static/suiv.png"></div>
<div class="bouton mois-precedent" title="Mois précédent"><img src="static/prec.png"></div>
<div class="bouton selecteur-mois"><span class="date">Janvier 1985</span><!-- <img src="static/menu.png">--></div>
<div class="solde"></div>
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
