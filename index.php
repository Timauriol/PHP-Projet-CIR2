<?php
function redirect($loc = "."){
    header("HTTP/1.1 302 Found");
    header("Location: " . $loc);
    exit();
}

include("db.php");

$error = "";
session_start();

if(isset($_POST["login"]) && isset($_POST["mdp"])){
    $req = $db->prepare("SELECT nom_prenom FROM utilisateur WHERE login = :login AND mdp = sha2(:mdp, 512)");
    $req->bindParam(':login', $_POST["login"]);
    $req->bindParam(':mdp', $_POST["mdp"]);

    if(!$req->execute()) die("Problème de connexion à la base MySQL");

    if($req->rowCount() == 0) $error = "Login ou mot de passe erroné.";
    else{
        //$_SESSION["login"] = $_POST["login"];
        die("Là normalement on redirige sur le calendrier, etc");
    }
}

$title = "lel";

include("_head.php");

if($error != "")
    echo('<div class="error">' . $error . '</div>');

?>

<form method="POST">
    <input type="text" name="login" placeholder="Login"/><br/>
    <input type="password" name="mdp" placeholder="Mot de passe"/><br/>
    <input type="submit">
</form>

<?php
include("_tail.php");
?>
