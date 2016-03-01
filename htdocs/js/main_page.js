   var AllBtnLike = document.getElementsByClassName('like-post');
   var AllBtnComment = document.getElementsByClassName('comment-post');
    var AllBtnLikeLen = AllBtnLike.length;
    var AllBtnCommentLen = AllBtnComment.length;
    
    for(var i = 0; i < AllBtnLikeLen; i++){
        AllBtnLike[i].addEventListener("click", function(){likePost(resultLike, this.id)}, false);
    }
    
    for (var i = 0; i < AllBtnCommentLen; i++){
        AllBtnComment[i].addEventListener("click", function (){commentPost(resultComment, this.id)}, false);
    }
    
    function likePost(callback, elem){
        var elem_split = elem.split('_');
        var pics_id = encodeURIComponent(elem_split[2]);
        xhr = new XMLHttpRequest()

        xhr.onreadystatechange = function() {
            if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0))
                callback(xhr.responseText);
	    };
    
    xhr.open("POST", "script/edit_pics.php", true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send("submit=like_pics&pics_id="+pics_id);
    }
    
    function resultLike(data){
        var result = JSON.parse(data);
        console.log(result);
        if (result[0] === "true"){
            var post = document.getElementById('like_post_'+result[2]);
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
    
    /*function commentPost(callback, id){
        
    }
    
    function resultComment(data){
        var result = JSON.parse(data);
        if (result[0] === true){
            
        }
        else {
            
        }
    }*/