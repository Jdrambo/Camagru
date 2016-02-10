
function requestIcon(callback){
	var xhr = new XMLHttpRequest();

	xhr.onreadystatechange = function() {
		if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
			callback(xhr.responseText);
		}
	};

	xhr.open("POST", "script/iconHandler.php", true);
	xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
	xhr.send("submit=select_icone&id_icon="+id_icon);

}

function selectIcon(data){
	var result = JSON.parse(data);
	if (result === "true"){

	}
}