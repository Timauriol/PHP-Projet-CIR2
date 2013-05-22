function joursDansMois(mois, annee){
    var jours = [31,28,31,30,31,30,31,31,30,31,30,31]
    if(mois == 1 && (annee % 4 == 0 && annee % 100 != 0) || annee % 400 == 0)
        return 29
    else
        return jours[mois];
}

function remplirJours(mois, annee){
    if(typeof annee === "undefined" || typeof mois === "undefined"){
        var d = new Date();
        annee = d.getFullYear();
        mois = d.getMonth();
    }
    var date = new Date(annee, mois, 1);
    date.setDate(date.getDate()-(date.getDay()-1+7)%7); // rembobine jusqu'au dernier lundi
    for(var y = 0; y < 6; y++){
        for(var x = 0; x < 7; x++){                                                        // ↓ il faut sauter la ligne avec les noms des jours de la semaine
            var tdjour = document.querySelector("table.calendrier > tbody > tr:nth-child("+(y+2)+") > td:nth-child("+(x+1)+")");

            var numjour = tdjour.querySelector("div.numjour");
            if(!numjour.hasChildNodes())
                numjour.appendChild(document.createTextNode(date.getDate()));
            else
                numjour.firstChild.data = date.getDate();

            if(date.getMonth() != mois)
                tdjour.className = "autremois";
            else
                tdjour.className = "";

            date.setDate(date.getDate()+1);
        }
    }
}

window.onload = function(){remplirJours();};
