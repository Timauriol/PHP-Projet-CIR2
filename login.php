<?php
include_once("db.php");
include_once("outils.php");

$error = "";
session_start(); // initialise $_SESSION

if(isset($_POST["login"]) && isset($_POST["mdp"])){
    $req = $db->prepare("SELECT 1 FROM utilisateur WHERE login = :login AND mdp = sha2(:mdp, 512)");
    $req->bindParam(':login', $_POST["login"]);
    $req->bindParam(':mdp', $_POST["mdp"]);

    if(!$req->execute()) die("Problème de connexion à la base MySQL");

    if($req->rowCount() == 0) $error = "Login ou mot de passe erroné.";
    else
        $_SESSION["conge_login"] = $_POST["login"];
}

if(isset($_SESSION["conge_login"]) && $_SESSION["conge_login"] != "")
    redirect();

include("_head.php");

echo("<h1>Connexion</h1>");


if($error != "")
    echo('<div class="error">' . $error . '</div>');
?>

<form method="POST">
    <input type="text" name="login" placeholder="Login"/><br/>
    <input type="password" name="mdp" placeholder="Mot de passe"/><br/>
    <input type="submit" value="Se connecter"/>
</form>

<?php
include("_tail.php");
?>
