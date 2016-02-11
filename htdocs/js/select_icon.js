var elems = document.getElementsByClassName("icon-selector");
var len = elems.length;
for(var i = 0; i < len; i++){
	elems[i].addEventListener("click", function(){ requestIcon(selectIcon, this.id)}, false);
}

function requestIcon(callback, elem){
	var elem_split = elem.split("-");
	var id_icon = encodeURIComponent(elem_split[1]);
	var xhr = new XMLHttpRequest();

	xhr.onreadystatechange = function() {
		if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
			callback(xhr.responseText);
		}
	};

	xhr.open("POST", "script/iconHandler.php", true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send("submit=select_icon&id_icon="+id_icon);
};

function selectIcon(data){
	var result = JSON.parse(data);
	if (result[0] === "true"){
		var elems = document.getElementsByClassName("icon-selector");
		var len = elems.length;
		for(var i = 0; i < len; i++){
			elems[i].className = "icon-selector";
		}
		document.getElementById('icon-'+result[1]).className = "icon-selector icon-selected";
		document.getElementById('main-profile-picture').src = result[2];
		document.getElementById('menu-profile-picture').src = result[2];
	}
};