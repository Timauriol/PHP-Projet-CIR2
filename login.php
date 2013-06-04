<?php
include_once("db.class.php");
include_once("outils.php");

// page de login

$error = "";
session_start(); // initialise $_SESSION

if(isset($_POST["login"]) && isset($_POST["mdp"])){
    $db = DB::getInstance()->getPdo();
    $req = $db->prepare("SELECT admin FROM utilisateur WHERE login = :login AND mdp = sha2(:mdp, 512)");
    // ↑ requète qui retournera un utilisateur si ses identifiants sont corrects, rien sinon
    $req->bindValue(':login', $_POST["login"]);
    $req->bindValue(':mdp', $_POST["mdp"]);

    $req->execute();

    if($req->rowCount() == 0) $error = "Login ou mot de passe erroné.";
    else if($req->fetch()["admin"] != "1") $error = "Vous n'avez pas les droits d'accès à la console administrateur.";
    else
        $_SESSION["conge_login"] = $_POST["login"];
        // l'utilisateur sera redirigé ci-dessous ↓
}

if(isset($_SESSION["conge_login"]) && $_SESSION["conge_login"] != "")
    // si la session existe déjà (ou si elle vient d'être créée, on redirige
    redirect(); // vers index.php par défaut
else {
    // on affiche le formulaire de connexion
    include_once("_head.php");

    echo("<div class=\"connexion\"><h1>Connexion</h1>");


    if($error != "")
        echo('<div class="error">' . $error . '</div>');
    ?>

    <form method="POST">
        <input type="text" name="login" placeholder="Login"/><br/>
        <input type="password" name="mdp" placeholder="Mot de passe"/><br/>
        <input type="submit" value="Se connecter"/>
    </form>
    </div>

    <?php
    include_once("_tail.php");
}
    ?>
