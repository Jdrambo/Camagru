window.onload = (function(){
    var elems = document.getElementsByClassName("img-delete");
    var camagruPrivacy = document.getElementsByClassName("img-privacy");
    var len = elems.length;
    var camagruPrivacyLen = camagruPrivacy.length;

    for(var i = 0; i < len; i++){
        elems[i].addEventListener("click", function(){ deletePics(resultDelete, this.id)}, false);
    }

    for(var i = 0; i < camagruPrivacyLen; i++){
        camagruPrivacy[i].addEventListener("click", function(){ changePrivacy(resultPrivacy, this.id)}, false);
    }

    function deletePics(callback, elem){
        var elem_split = elem.split('-');
        var id_pics = encodeURIComponent(elem_split[1]);
        xhr = new XMLHttpRequest()

        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
                callback(xhr.responseText);
            }
        };

        xhr.open("POST", "script/edit_pics.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.send("submit=delete_pics&id_pics="+id_pics);
    }

    function resultDelete(data){
        console.log(data);
        var result = JSON.parse(data);
        if (result[0] === "true"){
            var parent_pics = document.getElementsByClassName('pics-lib');
            var border = document.getElementById('border-'+result[2]);
            while(border.firstChild){
                border.removeChild(border.firstChild);
            }
            var len = parent_pics.length;
            for(var i = 0; i < len; i++){
                parent_pics[i].removeChild(border);
            }
            displayMessage("L'image a bien été supprimée", "#228");
            console.log(result[3] + " >> " + result[4]);
        }
        else{
            displayMessage("Erreur lors de la suppression de l'image", "#822");
        }
    }

    function changePrivacy(callback, elemId){
        var elemSplit = elemId.split('-');
        var id_pics = encodeURIComponent(elemSplit[2]);
        xhr = new XMLHttpRequest()

        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
                callback(xhr.responseText);
            }
        };

        xhr.open("POST", "script/edit_pics.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.send("submit=privacy_pics&id_pics="+id_pics);
    }

    function resultPrivacy(data){
        console.log(data);
        var result = JSON.parse(data);

        if (result[0] === "true"){
            var picsPrivacy = document.getElementById('privacy-'+result[2]);
            picsPrivacy.innerHTML = result[3];
            if (result[3] === "Privée")
                document.getElementById('img-privacy-'+result[2]).src = "img/lock.png";
            else
                document.getElementById('img-privacy-'+result[2]).src = "img/unlock.png";
            displayMessage(result[1], "#228");
        }
        else{
            displayMessage(result[1], "#822");
        }
    }

    function displayMessage(text, color){
        var mess = document.getElementById('state_message');
        mess.style.backgroundColor = color;
        mess.innerHTML = text;
        mess.style.visibility = "visible";
        mess.style.opacity = "1";
        setTimeout((function(){
            mess.style.opacity = "0";
            mess.style.visibility = "hidden";
        }),2000);
    }
})();