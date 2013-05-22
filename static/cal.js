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
    if(!paques[annee]) paques[annee] = getpaques(annee);
    var p = new Date(annee, paques[annee][0] - 1, paques[annee][1]);
    p.setDate(p.getDate() + 1); // lundi de pâques
    if(jour == p.getDate() && mois == p.getMonth()) return true;
    p.setDate(p.getDate() + 38); // Jeudi de l'ascension
    if(jour == p.getDate() && mois == p.getMonth()) return true;
    p.setDate(p.getDate() + 11); // Lundi de la pentecôte
    console.log(p, jour, mois);
    if(jour == p.getDate() && mois == p.getMonth()) return true;
    console.log("false");

    return false;
}

function remplirJours(mois, annee){
    if(typeof annee === "undefined" || typeof mois === "undefined"){
        var d = new Date();
        annee = d.getFullYear();
        mois = d.getMonth();
    }
    var date = new Date(annee, mois, 1);
    var xhr = new XMLHttpRequest();
    xhr.open("GET", "conge.json.php" +
            "?date_debut=" +
            date.getFullYear() + "-" + (date.getMonth() + 0) + "-" + date.getDay() +
            "&date_fin=" +
            date.getFullYear() + "-" + (date.getMonth() + 3) + "-" + date.getDay() +
            "&login=admin"
            , false);
    xhr.send();
    var conges = eval(xhr.responseText);
    date.setDate(date.getDate()-(date.getDay()-1+7)%7); // rembobine jusqu'au dernier lundi
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

            var matin = tdjour.querySelector("polygon.matin");
            var apresmidi = tdjour.querySelector("polygon.apresmidi");

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

window.onload = function(){remplirJours();};

/* http://stackoverflow.com/a/1284335 */
function getpaques(Y) {
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
