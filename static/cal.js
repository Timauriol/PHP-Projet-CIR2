// modes de fonctionnement
var CAL_GLOBAL=0;
var CAL_UTILISATEUR=1;

var mode = null;
var login = null;
var nom_prenom = null;


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

function nomMois(mois){
    return ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"][mois%12];
}

function changeMois(annee, mois){
    if(!annee || (!mois && mois != 0)){
        var d = new Date();
        annee = d.getFullYear();
        mois = d.getMonth();
    }
    window.annee = annee;
    window.mois = mois;

    clearConges();

    var span_date = document.querySelector(".selecteur-mois .date");
    span_date.innerHTML = "";
    span_date.appendChild(document.createTextNode(nomMois(mois) + " " + annee));

    var date = datePremiereCase();

    for(var y = 0; y < 6; y++){
        for(var x = 0; x < 7; x++){
            var tdjour = document.querySelector("table.calendrier > tbody > tr:nth-child("+(y+1)+") > td:nth-child("+(x+1)+")");

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

            tdjour.querySelector("polygon.matin").date = new Date(date.getFullYear(), date.getMonth(), date.getDate(), 0);
            tdjour.querySelector("polygon.apresmidi").date = new Date(date.getFullYear(), date.getMonth(), date.getDate(), 12);

            date.setDate(date.getDate()+1);
        }
    }
    majHash();
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
        for(var x = 0; x < 7; x++){
            var tdjour = document.querySelector("table.calendrier > tbody > tr:nth-child("+(y+1)+") > td:nth-child("+(x+1)+")");
            var matin = tdjour.querySelector("polygon.matin");
            var apresmidi = tdjour.querySelector("polygon.apresmidi");
            matin.classList.remove("conge");
            apresmidi.classList.remove("conge");

            for(var i = 0; i < conges.length; i++){
                date.setHours(0);
                if(conges[i].ts * 1000 == date - 0)
                    matin.classList.add("conge");
                date.setHours(12);
                if(conges[i].ts * 1000 == date - 0)
                    apresmidi.classList.add("conge");
            }
            date.setDate(date.getDate()+1);
        }
    }
    document.querySelector(".container").style.opacity = 1;
}

var recherche_xhr;

function recherche(){
    if(recherche_xhr && recherche_xhr.readyState < 4)
        recherche_xhr.abort();
    recherche_xhr = new XMLHttpRequest();
    recherche_xhr.open("GET", "utilisateur.json.php?q=" + encodeURIComponent(document.querySelector(".recherche").value), true);
    recherche_xhr.onreadystatechange = function(){
        if(recherche_xhr.readyState == 4){
            document.querySelector(".recherche").classList.remove("spinner");
            construireAutocomplete(eval(recherche_xhr.responseText));
        }
    }
    recherche_xhr.send();
    window.setTimeout(function(){
        if(recherche_xhr.readyState < 4)
            document.querySelector(".recherche").classList.add("spinner");
    }, 400);
}

function montrerAutocomplete(){
    document.querySelector(".autocomplete").style.display = "block";
}

function cacherAutocomplete(){
    document.querySelector(".autocomplete").style.display = "none";
}

function construireUtilAutocomplete(u, i){
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
        window.nom_prenom = u.nom_prenom;
        recherche.blur();
        window.login = u.login;
        document.querySelector(".container").style.opacity = 0;
        clearConges();
        getConges();
        majHash();
    }
    li.addEventListener("mousedown", choix, false);
    li.choix = choix;
    if(i == 0) li.classList.add("actif");
    autocomplete.appendChild(li);
}
function construireAutocomplete(utilisateurs){
    if(!utilisateurs) return;
    autocomplete = document.querySelector(".autocomplete");
    autocomplete.innerHTML = "";
    for(var i = 0; i < utilisateurs.length; i++){
        construireUtilAutocomplete(utilisateurs[i], i);
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

function setBarreOutils(){
    var actif = document.querySelector(".bouton.actif");
    if(actif) actif.classList.remove("actif");
    this.classList.add("actif");
}

function initBarreOutils(){
    var ajout = document.querySelector(".bouton.ajout");
    var suppr = document.querySelector(".bouton.suppression");
    var depla = document.querySelector(".bouton.deplacement");
    ajout.addEventListener("click", setBarreOutils, false);
    ajout.addEventListener("click", function(){outil = OUTIL_AJOUT;}, false);
    suppr.addEventListener("click", setBarreOutils, false);
    suppr.addEventListener("click", function(){outil = OUTIL_SUPPR;}, false);
    depla.addEventListener("click", setBarreOutils, false);
    depla.addEventListener("click", function(){outil = OUTIL_DEPLA;}, false);
}

function initNavCalendrier(){
    var suivant = document.querySelector(".mois-suivant");
    var precedent = document.querySelector(".mois-precedent");
    var selecteur = document.querySelector(".selecteur-mois");
    suivant.addEventListener("click", moisSuivant, false);
    precedent.addEventListener("click", moisPrecedent, false);
    selecteur.addEventListener("click", function(e){
        e.preventDefault();
    }, false);
}

function rafraichir(){
    if(window.mode == CAL_UTILISATEUR && window.login){
        getConges();
    }
    else{
        // d'autres trucs
    }
}

function moisSuivant(){
    mois = (mois + 1) % 12;
    if(mois == 0) annee += 1;
    changeMois(annee, mois);
    rafraichir();
}

function moisPrecedent(){
    mois -= 1;
    if(mois == -1){
        annee -= 1;
        mois = 11;
    }
    changeMois(annee, mois);
    rafraichir();
}

function changeMode(mode){
    if(window.mode == mode) return;
    window.mode = mode;
    var actif = document.querySelector("nav .actif");
    if(actif) actif.classList.remove("actif")
    document.querySelector(".container").style.opacity = 0;
    clearConges();
    if(mode == CAL_UTILISATEUR){
        document.querySelector("#bouton-util").classList.add("actif");
    }
    else if(mode == CAL_GLOBAL){
        document.querySelector("#bouton-global").classList.add("actif");
        cacherAutocomplete();
    }
    majHash();
}

window.onload = function(){
    var i = new Image();
    i.src = "static/spinner.gif"; // préchargement du spinner de la recherche

    var recherche_input = document.querySelector(".recherche");
    recherche_input.addEventListener("focus", montrerAutocomplete, false);
    recherche_input.addEventListener("blur", cacherAutocomplete, false);
    recherche_input.addEventListener("focus", recherche, false);
    recherche_input.addEventListener("input", recherche, false);
    recherche_input.addEventListener("keydown", navigationAutocomplete, false);
    document.querySelector("#bouton-global").addEventListener("click", function(){
        changeMode(CAL_GLOBAL);
        rafraichir();
    }, false);
    document.querySelector("#bouton-util").addEventListener("click", function(){
        changeMode(CAL_UTILISATEUR);
        recherche_input.focus();
        recherche_input.select();
        rafraichir();
    }, false);
    recherche_input.addEventListener("click", function(e){
        e.cancelBubble = true; // évite que le listener sur #bouton-util ↑↑↑ soit activé et sélectionne le texte
    }, false);
    initNavCalendrier();
    initBarreOutils();
    initSelection();
    if(window.location.hash != ""){
        var args = window.location.hash.slice(1).split("&");
        for(var i = 0; i < args.length; i++){
            var arg = args[i].split("=");
            if(arg.length == 2){
                switch(arg[0]){
                    case "n":
                        document.querySelector(".recherche").value = arg[1];
                        window.nom_prenom = arg[1];
                        break;
                    case "l":
                        window.login = arg[1];
                        break;
                    case "m":
                        window.mois = arg[1] - 0;
                        break;
                    case "a":
                        window.annee = arg[1] - 0;
                        break;
                }
            }
        }
    }
    changeMois(window.annee, window.mois);
    if(window.login) changeMode(CAL_UTILISATEUR);
    rafraichir();
};

function majHash(){
    var hash = "";
    if(window.mode == CAL_UTILISATEUR){
        if(window.login){
            hash += "&l=" + window.login;
        }
        if(window.nom_prenom){
            hash += "&n=" + window.nom_prenom;
        }
    }
    if(typeof window.mois != "undefined"){
        hash += "&m=" + window.mois;
    }
    if(window.annee){
        hash += "&a=" + window.annee;
    }
    window.location.hash = hash;
}

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

function initSelection(){
    var polygons = document.querySelectorAll("polygon");
    for(var i = 0; i < polygons.length; i++)
        polygons[i].addEventListener("mousedown", debutSelection, false);
}

var selection;
var filtreSelection = function(el){ return true; }

function debutSelection(){
    if(outil == OUTIL_AJOUT)
        filtreSelection = function(el){ return el && !el.classList.contains("conge"); }
    else if(outil == OUTIL_SUPPR)
        filtreSelection = function(el){ return el && el.classList.contains("conge"); }
    else if(outil == OUTIL_DEPLA)
        filtreSelection = function(el){ return el && el.classList.contains("conge"); }
    else return

    var polygons = document.querySelectorAll("polygon");
    for(var i = 0; i < polygons.length; i++)
        polygons[i].addEventListener("mouseover", modifieSelection, false);
    document.addEventListener("mouseup", finSelection, false);
    selection = [this];
    this.classList.add("selectionne");
}

function congePrecedent(el){
    if(!el) return el;
    if(el.previousElementSibling) return el.previousElementSibling;
                 /* ↓ svg         ↓ td */
    var td = el.parentElement.parentElement.previousElementSibling;
    if(td) return td.querySelector("polygon:nth-child(2)");
                 /* ↓ svg         ↓ td          ↓ tr */
    var tr = el.parentElement.parentElement.parentElement.previousElementSibling;
    if(tr) return tr.querySelector("td:nth-child(7) polygon:nth-child(2)");
    return null;
}

function congeSuivant(el){
    if(!el) return el; // congeSuivant(null) = null
    if(el.nextElementSibling) return el.nextElementSibling;
                 /* ↓ svg         ↓ td */
    var td = el.parentElement.parentElement.nextElementSibling;
    if(td) return td.querySelector("polygon:nth-child(1)");
                 /* ↓ svg         ↓ td          ↓ tr */
    var tr = el.parentElement.parentElement.parentElement.nextElementSibling;
    if(tr) return tr.querySelector("td:nth-child(1) polygon:nth-child(1)");
    return null;
}

function modifieSelection(){
    if(selection[0].compareDocumentPosition(this) & Node.DOCUMENT_POSITION_PRECEDING)
        var suiv = congePrecedent;
    else if(selection[0].compareDocumentPosition(this) & Node.DOCUMENT_POSITION_FOLLOWING)
        var suiv = congeSuivant;
    else
        var suiv = function(){return null;};
    var el = selection[0];

    // ça pourrait être plus propre et ne pas rechercher toute la sélection à chaque fois
    // mais bon ça tourne raisonnablement vite tel quel
    var polygons = document.querySelectorAll("polygon.selectionne");
    for(var i = 0; i < polygons.length; i++){
        polygons[i].classList.remove("selectionne");
    }

    selection = []
    while(el){
        if(filtreSelection(el)){
            selection.push(el);
            el.classList.add("selectionne");
        }
        el = el==this?null:suiv(el);
    }
}

function finSelection(){
    var polygons = document.querySelectorAll("polygon");
    for(var i = 0; i < polygons.length; i++){
        polygons[i].classList.remove("selectionne");
        polygons[i].removeEventListener("mouseover", modifieSelection, false);
    }
    document.removeEventListener("mouseup", finSelection, false);

}

var OUTIL_AJOUT = 0;
var OUTIL_SUPPR = 1;
var OUTIL_DEPLA = 2;

var outil = null;
