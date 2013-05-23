// modes de fonctionnement
var CAL_GLOBAL=0;
var CAL_UTILISATEUR=1;

var mode = CAL_UTILISATEUR;
var login = null;


function joursDansMois(mois, annee){
    var jours = [31,28,31,30,31,30,31,31,30,31,30,31]
    if(mois == 1 && (annee % 4 == 0 && annee % 100 != 0) || annee % 400 == 0)
        return 29
    else
        return jours[mois];
}

var paques = {}

function ferie(annee, mois, jour){
    if(
        (jour == 1  && mois == 0 ) || // premier janvier : jour de l'an
        (jour == 1  && mois == 4 ) || // premier mai : fête du travail
        (jour == 8  && mois == 4 ) || // 8 mai : fête de la victoire
        (jour == 14 && mois == 6 ) || // 14 juillet
        (jour == 15 && mois == 7 ) || // 15 août : assomption
        (jour == 1  && mois == 10) || // premier novembre : toussaint
        (jour == 11 && mois == 10) || // 11 novembre : armistice
        (jour == 25 && mois == 11)    // 25 décembre : noël
    ) return true;
    if(!paques[annee]) paques[annee] = getPaques(annee);
    var p = new Date(annee, paques[annee][0] - 1, paques[annee][1]);
    p.setDate(p.getDate() + 1); // lundi de pâques
    if(jour == p.getDate() && mois == p.getMonth()) return true;
    p.setDate(p.getDate() + 38); // Jeudi de l'ascension
    if(jour == p.getDate() && mois == p.getMonth()) return true;
    p.setDate(p.getDate() + 11); // Lundi de la pentecôte
    if(jour == p.getDate() && mois == p.getMonth()) return true;

    return false;
}

//function weekend(annee, mois, jour);
var annee, mois;

function clearConges(){
    conges = document.querySelectorAll(".conge");
    for(var i = 0; i < conges.length; i++){
        conges[i].classList.remove("conge");
    }
}
function changeMois(annee, mois){
    if(typeof annee === "undefined" || typeof mois === "undefined"){
        var d = new Date();
        annee = d.getFullYear();
        mois = d.getMonth();
    }
    window.annee = annee;
    window.mois = mois;
    var date = datePremiereCase();

    clearConges();

    for(var y = 0; y < 6; y++){
        for(var x = 0; x < 7; x++){                                                        // ↓ il faut sauter la ligne avec les noms des jours de la semaine
            var tdjour = document.querySelector("table.calendrier > tbody > tr:nth-child("+(y+2)+") > td:nth-child("+(x+1)+")");

            var numjour = tdjour.querySelector("div.numjour");
            if(!numjour.hasChildNodes())
                numjour.appendChild(document.createTextNode(date.getDate()));
            else
                numjour.firstChild.data = date.getDate();

            tdjour.className = "";
            if(date.getMonth() != mois)
                tdjour.classList.add("autremois");

            if(ferie(date.getFullYear(), date.getMonth(), date.getDate()))
                tdjour.classList.add("ferie");
            else if(date.getDay() == 6 || date.getDay() == 0)
                tdjour.classList.add("weekend");

            date.setDate(date.getDate()+1);
        }
    }
    if(mode == CAL_UTILISATEUR && login)
        getConges();
    else if(mode == CAL_GLOBAL)
        console.log("TODO"); // TODO
}

function getConges(){
    var date = new Date(annee, mois, 1);
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "conge.json.php" +

            "?date_debut=" +
            (date.getFullYear() + (date.getMonth()==0?-1:0)) +
            "-" + ((date.getMonth()) || 12) + // -1 car les mois commencent à 0 en javascript
            "-22" + // cas extrème : Mars commence un dimanche, on veut les 6 derniers jours de Février

            "&date_fin=" +
            (date.getFullYear() + (date.getMonth()==11?1:0)) +
            "-" + ((date.getMonth() + 1)%12 + 1) +
            "-14" + // cas extrème : Février commence un Lundi, on veut les 6 premiers jours de Mars

            "&login=" + login
            , true);
    xhr.onreadystatechange = function(){
        if(xhr.readyState == 4){
            if(xhr.status != 200){
                alert("Une erreur est survenue lors de la récupération des congés.");
            }
            else
                remplirConges(eval(xhr.responseText));
        }
    }
    xhr.send();
}

function datePremiereCase(){
    var date = new Date(annee, mois, 1);
    date.setDate(date.getDate()-(date.getDay()-1+7)%7); // rembobine jusqu'au dernier lundi
    return date;
}

function remplirConges(conges){
    var date = datePremiereCase();
    for(var y = 0; y < 6; y++){
        for(var x = 0; x < 7; x++){                                                        // ↓ il faut sauter la ligne avec les noms des jours de la semaine
            var tdjour = document.querySelector("table.calendrier > tbody > tr:nth-child("+(y+2)+") > td:nth-child("+(x+1)+")");
            var matin = tdjour.querySelector("polygon.matin");
            var apresmidi = tdjour.querySelector("polygon.apresmidi");
            matin.classList.remove("conge");
            apresmidi.classList.remove("conge");

            for(var i = 0; i < conges.length; i++){
                if(conges[i].ts * 1000 == date - 0)
                    matin.classList.add("conge");
                if(conges[i].ts * 1000 == date - 0 + (60*60*12*1000))
                    apresmidi.classList.add("conge");
            }
            date.setDate(date.getDate()+1);
        }
    }
}

var recherche_xhr;

function recherche(){
    if(recherche_xhr && recherche_xhr.readyState < 4)
        recherche_xhr.abort();
    recherche_xhr = new XMLHttpRequest();
    recherche_xhr.open("GET", "utilisateur.json.php?q=" + encodeURIComponent(document.querySelector(".recherche").value), true);
    recherche_xhr.onreadystatechange = function(){
        if(recherche_xhr.readyState == 4)
            construireAutocomplete(eval(recherche_xhr.responseText));
    }
    recherche_xhr.send();
}

function montrerAutocomplete(){
    document.querySelector(".autocomplete").style.display = "block";
}

function cacherAutocomplete(){
    document.querySelector(".autocomplete").style.display = "none";
}

function construireUtilAutocomplete(u){
    var li = document.createElement("li");
    li.className = "utilisateur";
    var nom = document.createElement("div");
    nom.className = "nom";
    nom.appendChild(document.createTextNode(u.nom_prenom));
    var login = document.createElement("div");
    login.className = "login";
    login.appendChild(document.createTextNode(u.login));
    li.appendChild(nom);
    li.appendChild(login);
    function choix(){
        var recherche = document.querySelector(".recherche");
        recherche.value = u.nom_prenom;
        recherche.blur();
        window.login = u.login;
        getConges();
    }
    li.addEventListener("mousedown", choix, false);
    li.choix = choix;
    autocomplete.appendChild(li);
}
function construireAutocomplete(utilisateurs){
    if(!utilisateurs) return;
    autocomplete = document.querySelector(".autocomplete");
    autocomplete.innerHTML = "";
    for(var i = 0; i < utilisateurs.length; i++){
        construireUtilAutocomplete(utilisateurs[i]);
    }
}

function navigationAutocomplete(e){
    var autocomplete = document.querySelector(".autocomplete");
    var actif = autocomplete.querySelector(".actif");
    switch(e.keyCode){
        case 38: // ↑
            if(actif){
                actif.classList.remove("actif");
                if(actif.previousElementSibling)
                    actif.previousElementSibling.classList.add("actif");
            }
            else if(autocomplete.lastElementChild){
                autocomplete.lastElementChild.classList.add("actif");
            }
            break;
        case 40: // ↓
            if(actif){
                actif.classList.remove("actif");
                if(actif.nextElementSibling)
                    actif.nextElementSibling.classList.add("actif");
            }
            else if(autocomplete.firstElementChild){
                autocomplete.firstElementChild.classList.add("actif");
            }
            break;
        case 13: // ↵
            if(actif){
                actif.classList.remove("actif");
                actif.choix();
                this.blur();
            }
            break;
    }
}

window.onload = function(){
    changeMois();
    var recherche_input = document.querySelector(".recherche");
    recherche_input.addEventListener("focus", montrerAutocomplete, false);
    recherche_input.addEventListener("blur", cacherAutocomplete, false);
    recherche_input.addEventListener("focus", recherche, false);
    recherche_input.addEventListener("input", recherche, false);
    recherche_input.addEventListener("keydown", navigationAutocomplete, false);
    document.querySelector("#bouton-global").addEventListener("click", function(){
        mode = CAL_GLOBAL;
        var buttons = this.parentElement.children;
        for(var i=0; i < buttons.length; i++)
            buttons[i].classList.remove("actif");
        this.classList.add("actif");
    }, false);
    document.querySelector("#bouton-util").addEventListener("click", function(){
        mode = CAL_UTILISATEUR;
        var buttons = this.parentElement.children;
        for(var i=0; i < buttons.length; i++)
            buttons[i].classList.remove("actif");
        this.classList.add("actif");
        recherche_input.focus();
        recherche_input.select();
    }, false);
    recherche_input.addEventListener("click", function(e){
        e.cancelBubble = true; // évite que le listener sur #bouton-util ↑↑↑ soit activé et sélectionne le texte
    }, false);
};

/* http://stackoverflow.com/a/1284335 */
function getPaques(Y) {
    var C = Math.floor(Y/100);
    var N = Y - 19*Math.floor(Y/19);
    var K = Math.floor((C - 17)/25);
    var I = C - Math.floor(C/4) - Math.floor((C - K)/3) + 19*N + 15;
    I = I - 30*Math.floor((I/30));
    I = I - Math.floor(I/28)*(1 - Math.floor(I/28)*Math.floor(29/(I + 1))*Math.floor((21 - N)/11));
    var J = Y + Math.floor(Y/4) + I + 2 - C + Math.floor(C/4);
    J = J - 7*Math.floor(J/7);
    var L = I - J;
    var M = 3 + Math.floor((L + 40)/44);
    var D = L + 28 - 31*Math.floor(M/4);

    return [M, D];
}
