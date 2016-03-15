var BtnLike = document.getElementsByClassName('like-post');
var BtnComment = document.getElementsByClassName('comment-post');
var validCom = document.getElementsByClassName('comment-input');
var BtnLikeLen = BtnLike.length;
var BtnCommentLen = BtnComment.length;
var validComLen = validCom.length;
var lastEvent;

// On ajoute l'evenListner pour le like (j'aime)...
for(var i = 0; i < BtnLikeLen; i++){
    BtnLike[i].addEventListener("click", function(){ likePost(resultLike, this.id)}, false);
}

// On ajoute l'eventListener qui détect quand on appuie sur commenter
for (var i = 0; i < BtnCommentLen; i++){
    BtnComment[i].addEventListener("click", function(){ commentPost(this.id)}, false);
}

// On ajoute l'eventListener qui écoutera la pression d'une touche dans l'input de commentaire
for (var i = 0; i < validComLen; i++){
    validCom[i].addEventListener("keyup", function(e){
        if (lastEvent && lasEvent.keyCode === e.keyCode)
            return ;
        if (e.keyCode === 13){
            lastEvent = e;
            alert(this.value);
            addCom(resultAddCom, this.id);
        }
    }, false);
}

// Cette fonction vérifie si un like est présent ou nom en bdd, l'ajoute ou le retire selon le cas
    function likePost(callback, elem){
        var elem_split = elem.split('-');
        var pics_id = encodeURIComponent(elem_split[2]);
        xhr = new XMLHttpRequest()

        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0))
                callback(xhr.responseText);
	    };
    
    xhr.open("POST", "script/like.php", true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send("submit=like_pics&pics_id="+pics_id);
    }

// Cette fonction est la fonction callback de likePost, qui incrémente le compteur de like de 1, ou le décrémente
    function resultLike(data){
        var result = JSON.parse(data);
        console.log(result);
        if (result[0] === "true"){
            var post = document.getElementById('like-post-'+result[2]);
            var likeCount = document.getElementById('like-count-'+result[2]);
            var count = Number(likeCount.innerHTML);
            if (result[1] === "0"){
                post.innerHTML = "Je n'aime plus";
                count++;
            }
            else {
                post.innerHTML = "J'aime";
                count--;  
            }
            likeCount.innerHTML = count;
        }
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
            alert("KeyCode =  / id = " + elem_id);
    }

// Cette function est la callback de addCom
    function resultAddCom(data){
        
    }