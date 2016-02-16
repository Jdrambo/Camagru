var btn_save = document.getElementById('save');


btn_save.addEventListener("click", function (){ savePics(finishSave)}, false);

function savePics(callback){
	var xhr = new XMLHttpRequest();

	var canvas = document.getElementById('canvas').toDataURL('image/png');
	var title = document.getElementById('pics_title').value;
	var comment = document.getElementById('pics_comment').value;
	var published = document.getElementById('pics_published').value;
	document.getElementById('text').innerHTML = title;

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
	var result = JSON.parse(data);
	if (data[0] === "true"){
		alert("SAVE OK");
	}
	else {
		alert(result);
	}
}