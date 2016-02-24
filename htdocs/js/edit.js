var btn_save = document.getElementById('save');


btn_save.addEventListener("click", function (){ savePics(finishSave)}, false);

function savePics(callback){
	var xhr = new XMLHttpRequest();

	var canvas = document.getElementById('canvas').toDataURL('image/png');
	var title = document.getElementById('pics_title').value;
	var comment = document.getElementById('pics_comment').value;
	var published = document.getElementById('pics_published');
	if (published.checked === true)
		published = "1";
	else
		published = "0";

	xhr.onreadystatechange = function() {
		if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
			callback(xhr.responseText);
		}
	};

	xhr.open("POST", "script/edit_pics.php", true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send("submit=save_pics&pics="+canvas+"&title="+title+"&comment="+comment+"&published="+published);
}

function finishSave(data){
    var elem = document.getElementById('state_message');
	if (data === "true"){
        elem.innerHTML = "La photo a bien été enregistrée";
		elem.style.visibility = "visible";
        elem.style.opacity = "1";
        setTimeout((function(){
            elem.style.opacity = "0";
            elem.style.visibility = "hidden";
        }),2000);
       
	}
	else {
		elem.innerHTML = "Une erreur est survenue pendant l'enregistrement de l'image";
        elem.style.backgroundColor = "#822";
		elem.style.visibility = "visible";
        elem.style.opacity = "1";
        setTimeout((function(){
            elem.style.opacity = "0";
            elem.style.visibility = "hidden";
        }),2000);
	}
}

function hideMessage(elem){
    elem.style.height = "0";
    elem.style.visibility = "hidden";
}

function showElement(id, disp){
	document.getElementById(id).style.display = disp;
}

function hideElement(id){
	document.getElementById(id).style.display = "none";
}