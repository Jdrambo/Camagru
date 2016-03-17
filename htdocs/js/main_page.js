// On récupère tous les boutons j'aime
var btnLike = document.getElementsByClassName('like-post');
// On récupère tous les boutons commenter
var btnComment = document.getElementsByClassName('comment-post');
// On récupère tous les champs de saisi de commentaire
var validCom = document.getElementsByClassName('comment-input');
// On récupère tous les boutons supprimer un commentaire
var btnDeleteCom = document.getElementsByClassName('delete-com');
var btnLikeLen = btnLike.length;
var btnCommentLen = btnComment.length;
var validComLen = validCom.length;
var btnDeleteComLen = btnDeleteCom.length;
var lastEvent;

// On ajoute l'evenListner pour le like (j'aime)...
for (var i = 0; i < btnLikeLen; i++){
    btnLike[i].addEventListener("click", function(){ likePost(resultLike, this.id)}, false);
}

// On ajoute l'eventListener qui détect quand on appuie sur commenter
for (var i = 0; i < btnCommentLen; i++){
    btnComment[i].addEventListener("click", function(){ commentPost(this.id)}, false);
}

// On ajoute l'eventListener qui écoutera la pression d'une touche dans l'input de commentaire
for (var i = 0; i < validComLen; i++){
    validCom[i].addEventListener("keyup", function(e){
        if (lastEvent && lastEvent.keyCode === e.keyCode)
            return ;
        else
            lastEvent = null;
        if (e.keyCode === 13){
            lastEvent = e;
            addCom(resultAddCom, this.id);
        }
    }, false);
}

// On ajoute l'eventlistener qui supprimera le commentaire sélectionné
for(var i = 0; i < btnDeleteComLen; i++){
    btnDeleteCom[i].addEventListener("click", function(){ deleteCom(resultDeleteCom, this.id)}, false);
}

// Cette fonction vérifie si un like est présent ou non en bdd, l'ajoute ou le retire selon le cas
    function likePost(callback, elem){
        var elem_split = elem.split('-');
        var pics_id = encodeURIComponent(elem_split[2]);
        xhr = new XMLHttpRequest()

        xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0))
            callback(xhr.responseText);
        };
        
        xhr.open("POST", "script/edit_pics.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.send("submit=lkPost&pics_id="+pics_id);
    }

// Cette fonction est la fonction callback de likePost, qui incrémente le compteur de like de 1, ou le décrémente
    function resultLike(data){
        var result = JSON.parse(data);
        console.log(result);
        if (result && result[0] === "true"){/*
            var btnLike = document.getElementById('like-post-'+result[2]);
            var likeCount = document.getElementById('like-count-'+result[2]);
            var count = Number(likeCount.innerHTML);
            if (result && result[1] === "addLike"){
                btnLike.innerHTML = "Je n'aime plus";
                count++;
            }
            else {
                btnLike.innerHTML = "J'aime";
                count--;  
            }
            likeCount.innerHTML = count;*/
            console.log("OK")
        }
        else
            console.log("ERROR");
    }

// Cette fonction a pour but d'afficher la zone de commentaire sous le post sélectionné
    function commentPost(id_elem){
        var elem_split = id_elem.split('-');
        var elem = document.getElementById("general-input-border-"+elem_split[2]);
        var elem_input = document.getElementById("comment-input-"+elem_split[2]);
        elem.style.display = "inline-block";
        elem_input.focus();
    }

// Cette fonction ajoute en bdd un commentaire
    function addCom(callback, elem_id){
        var content = document.getElementById(elem_id).value;
        document.getElementById(elem_id).value = "";
        content = content.trim();
        if (content != ""){
            var elem_split = elem_id.split('-');
            var pics_id = encodeURIComponent(elem_split[2]);
            xhr = new XMLHttpRequest()

            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0))
                    callback(xhr.responseText);
            };

            xhr.open("POST", "script/edit_pics.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.send("submit=comment_post&pics_id="+pics_id+"&content="+content);
        }
    }

// Cette function est la callback de addCom
    function resultAddCom(data){
        if (data)
            var result = JSON.parse(data);
        
        if (result && result[0] === "true"){
            var com_block = document.getElementById("comments-block-" + result[1]);
            var node = document.createElement("p");
            if (com_block.firstChild && com_block.firstChild.className === "line-comment2")
                node.className = "line-comment";
            else
                node.className = "line-comment2";
            node.id = "comment-id-"+result[5];
            var image = document.createElement("img");
            image.src = result[3];
            image.className = "icon-comment";
            var login = document.createElement("span");
            login.className = "com-login";
            login.innerHTML = result[4];
            var content = document.createElement("span");
            content.className = "com-text";
            content.innerHTML = result[2];
            var delete_img = document.createElement("img");
            delete_img.src = "img/delete_small.png";
            delete_img.className = "delete-com";
            delete_img.id = "delete-com-"+result[5];
            delete_img.title = "Spprimer le commentaire";
            delete_img.alt = "delete comment";
            delete_img.addEventListener("click", function(){ deleteCom(resultDeleteCom, this.id)}, false);
            
            node.appendChild(image);
            node.appendChild(login);
            node.appendChild(content);
            node.appendChild(delete_img);
            var firstNode = com_block.firstChild;
            if (firstNode)
                com_block.insertBefore(node, firstNode);
            else
                com_block.appendChild(node);
        }
        else
            console.log("ERROR");
    }

// Cette fonction demandera une suppression du commentaire voulu en BDD
    function deleteCom(callback, elem){
        var elem_split = elem.split('-');
        var com_id = encodeURIComponent(elem_split[2]);
        xhr = new XMLHttpRequest()

        xhr.onreadystatechange = function() {
        if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0))
            callback(xhr.responseText);
        };
        
        xhr.open("POST", "script/edit_pics.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.send("submit=delete_com&com_id="+com_id);
    }

// Cette fonction récupère la réponse suite à la demande de suppression d'un commentaire
    function resultDeleteCom(data){
        if (data)
            var result = JSON.parse(data);
        if (result && result[0] === "true"){
            var elem = document.getElementById('comment-id-'+result[1]);
            var parentElem = elem.parentElement;
            while (elem.firstChild){
                var oldElem = elem.removeChild(elem.firstChild);
                oldElem = null;
            }
            oldElem = parentElem.removeChild(elem);
            oldElem = null;
            
        }
        else if (result && result[0] === "false"){
            console.log(result);
        }
        else
            console.log("ERROR");
    }