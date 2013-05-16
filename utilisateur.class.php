<?php
include_once("db.php");

class Utilisateur{
    public $login = "";
    public $nom_prenom = "";
    public $admin = false;

    function __construct($login, $nom_prenom = null, $admin = null){
        global $db;
        /* Utilisateur("ppouch") vérifie le login et récupère les infos dans la BDD
           Utilisateur("ppouch", "Pascal Pouchard", false) remplit seulement les champs 
           utile si l'on récupère plusieurs utilisateurs dans une même requète */
        if(!$nom_prenom || !$admin){
            $req = $db->prepare("SELECT nom_prenom, admin FROM utilisateur WHERE login = :login");
            $req->bindParam(':login', $login);
            if(!$req->execute()) die("Problème de connexion à la base MySQL");
            $res = $req->fetch();
            if(!$res) // l'utilisateur n'existe pas
                return;
            $this->login = $login;
            $this->nom_prenom = $res["nom_prenom"];
            $this->admin = $res["admin"] == "1"; // beurk
        }
        else{
            $this->login = $login;
            $this->nom_prenom = $nom_prenom;
            $this->admin = $admin;
        }
    }

    public static function tous(){
        // renvoie un Array de tous les utilisateurs
        // TODO
    }
    public static function recherche($q){
        // renvoie un Array des utilisateurs correspondant à la recherche
        // TODO
    }
}
?>
