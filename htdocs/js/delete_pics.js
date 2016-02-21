var elems = document.getElementsByClassName("img-delete");
var len = elems.length;
for(var i = 0; i < len; i++){
	elems[i].addEventListener("click", function(){ deletePics(resultDelete, this.id)}, false);
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
    result = JSON.parse(data);
    if (result[0] === "true"){
        var parent_pics = document.getElementsByClassName('pics-lib');
        var border = document.getElementById('border-'+result[2]);
        while (border.firstChild){
            border.removeChild(border.firstChild);
        }
        var len = parent_pics.length;
        for(var i = 0; i < len; i++){
            parent_pics[i].removeChild(border);
        }
    }
    else
        alert ("ERROR");
}