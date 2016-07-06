window.onload = (function(){
    var i = 0;
    var firstCanvas;
    var allLayer = [];
    var layerId = 1;
    var selectedEmote;
    var alphaValue = 0.5;

    //Protoype d'un objet Layer qui sert pour les icones et les filtres
    function Layer(id, name, src, x, y, w, h, alpha){
        this.id = id;
        this.name = name;
        this.src = src;
        this.x = x;
        this.y = y;
        this.w = w;
        this.h = h;
        this.alpha = alpha;
    }
    
    //Bouton pour afficher la section d'upload
    var upload_section_btn = document.getElementById('edit-selection-upload');
    if (upload_section_btn){
        upload_section_btn.addEventListener('click', function(){ 
            showElement('file-upload-section', 'block');
            hideElement('edit-selection-section');
        }, false);
    }

    //Bouton pour afficher la section camera
    var cam_section_btn = document.getElementById('edit-selection-cam');
    if(cam_section_btn){
        cam_section_btn.addEventListener('click', function(){
            showElement('cam-section', 'block');
            hideElement('edit-selection-section');
        }, false);
    }

    //Bouton de chargement d'un fichier
    var form_upload = document.getElementById('upload-form');
    if (form_upload){
        form_upload.addEventListener('submit', function(event){
            event.preventDefault();
            uploadNewFile(resultUpload);
        }, false);
    }

    //Fonction qui charge le fichier temporaire
    function uploadNewFile(callback){
        var files = document.getElementById('upload-field').files;
        if(files && files[0]){
        	allLayer = [];
            layerId = 1;
            var formData = new FormData();
            formData.append("sumbit", "upload_file");
            formData.append("file", files[0]);

            var xhr = new XMLHttpRequest();
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && (xhr.status == 200 || xhr.status == 0)) {
                    callback(xhr.responseText);
                }
            };

            xhr.open("POST", "script/edit_pics.php", true);
            xhr.send(formData);
        }
    }

    //Fonction de retour de l'uplod d'un fichier
    function resultUpload(data){
    	if (data){
	        var result = JSON.parse(data);
	        console.log(result);
	        if (result && result[0] === "true"){
	        	var width = 460;
	        	var height = 340;
	        	var img = new Image();
	        	img.src = result[1];
	        	var canvas = document.getElementById("canvas");
	        	
			    showElement("pics_title", "inline-block");
			    showElement("pics_comment", "inline-block");
			    showElement("container-published", "block");
			    showElement("edit-menu", "inline-block");
			    showElement("edit-area", "inline-block");

		    	img.onload = function(){
		    		canvas.width = width;
		    		canvas.height = height;
			    	canvas.getContext('2d').drawImage(img, 0, 0, width, height);
	            	firstCanvas = canvas.toDataURL('image/png');
				}
	        }
	        else {
	        	showMessage("Une erreur c'est produite au chargement du fichier", "#822");
	        }
	    }
	    else{
	    	showMessage("Une erreur c'est produite au chargement du fichier", "#822");
	    }
    }

    //Chaque emote icone
    var all_btn_emote = document.getElementsByClassName('emote-img');
    var all_btn_emote_length = all_btn_emote.length;

    for (i = 0; i < all_btn_emote_length; i++){
        all_btn_emote[i].addEventListener("dragstart", function(event){ handleDragStart(event);}, false);
        all_btn_emote[i].addEventListener("drag", function(event){ drag(event);}, false);
        all_btn_emote[i].draggable = "true";
    }
    
    //Chaque filtres
    var all_btn_filtre = document.getElementsByClassName('filter-pics');
    var all_btn_filtre_lentght = all_btn_filtre.length;
    for (i = 0; i < all_btn_filtre_lentght; i++){
        all_btn_filtre[i].addEventListener("click", function(){ addFilter(this);}, false);
    }

    var mainCanvas = document.getElementById('canvas');
    mainCanvas.addEventListener("dragover", function(e){ allowDrop(e)}, false);
    mainCanvas.addEventListener("drop", function(event){ addEmote(event);}, false);

    //Le bouton de sauvegarde de la photo
    var btn_save = document.getElementById('save');
    btn_save.addEventListener("click", function (){ savePics(finishSave)}, false);

    //La fonction qui appelle en ajax le script qui joindra les filtre et enregistrera la photo
    function savePics(callback){
        var layers = JSON.stringify(allLayer);
        var xhr = new XMLHttpRequest();
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
        xhr.send("submit=save_pics&pics="+firstCanvas+"&title="+title+"&comment="+comment+"&published="+published+"&layers="+layers);
    }

    // La fonction de retour du script de sauvegarde de la photo
    function finishSave(data){
        var result = JSON.parse(data);
        var elem = document.getElementById('state_message');
        if (result[0] == "true"){
            var list = document.getElementById('last-pics-list');
            if (list){
                var newLink = document.createElement('a');
                newLink.href = result[1];
                var newPicsDiv = document.createElement('div');
                newPicsDiv.className = "last-pics";
                var newPics = document.createElement('img');
                newPics.src = result[1];
                newPics.alt = result[2];
                newPics.title = result[2];
                newPics.style.opacity = 0;
                newLink.appendChild(newPics);
                newPicsDiv.appendChild(newLink);
                
                if (list.firstChild)
                    list.insertBefore(newPicsDiv, list.firstChild);
                else
                    list.appendChild(newPicsDiv);
                setTimeout((function(){
                    newPics.style.opacity = 1;
                }),10);
            }
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
    
    function showMessage(text, color){
    	var elem = document.getElementById('state_message');
    	elem.innerHTML = text;
        elem.style.backgroundColor = color;
        elem.style.visibility = "visible";
        elem.style.opacity = "1";
        setTimeout((function(){
            elem.style.opacity = "0";
            elem.style.visibility = "hidden";
        }),2000);
    }

    function handleDragStart(e){
        selectedEmote = e.target;
    }
    
    function drag(e){
        e.dataTransfer.setData("text", e.target.id);
    }
    
    function allowDrop(e){
        if (selectedEmote.className == "emote-img"){
            e.preventDefault();
            e.dataTransfer.dropEffect = "copy";
        }
    }
    
    function addFilter(obj){
        var ctx = mainCanvas.getContext("2d");
        var img = new Image();
        alphaValue = document.getElementById('alpha-value').value;
        var addedLayer = new Layer(layerId, obj.title, obj.src, 0, 0, mainCanvas.width, mainCanvas.height, alphaValue);
        allLayer.push(addedLayer);
        console.log(addedLayer);
        img.src = addedLayer.src;
        ctx.globalAlpha = alphaValue;
        img.onload = function(){
            ctx.drawImage(img, addedLayer.x, addedLayer.y, addedLayer.w, addedLayer.h);
        }
        layerId += 1;
    }
    
    function addEmote(e){
        e.preventDefault();
        
        var pos = getMousePos(e);
        var ctx = mainCanvas.getContext("2d");
        ctx.globalAlpha = 1;
        var img = new Image();
        var emoteWidth = 128;
        var emoteHeight = 128;

        var addedLayer = new Layer(layerId, selectedEmote.title, selectedEmote.src, pos.x - (emoteWidth / 2), pos.y - (emoteHeight / 2), emoteWidth, emoteHeight, 1);
        allLayer.push(addedLayer);
        img.src = addedLayer.src;
        img.onload = function(){
            ctx.drawImage(img, addedLayer.x, addedLayer.y, addedLayer.w, addedLayer.h);
        }
        layerId += 1;
    }

    function getMousePos(e){
        var rect = mainCanvas.getBoundingClientRect();
        var pos = {
            x: (Math.floor(e.clientX - Number(rect.left))),
            y: (Math.floor(e.clientY - Number(rect.top)))
        };
        return (pos);
    }
    
    function hideMessage(elem){
        elem.style.height = "0";
        elem.style.visibility = "hidden";
    }
    
    // Affiche un element
    function showElement(id, disp){
        document.getElementById(id).style.display = disp;
    }

    // Cache un element
    function hideElement(id){
        document.getElementById(id).style.display = "none";
    }
    
        hideElement("pics_title");
        hideElement("pics_comment");
        hideElement("container-published");
        hideElement("edit-area");
        hideElement("edit-menu");

       (function() {
		  var streaming = false,
		      video        = document.querySelector('#video'),
		      canvas       = document.querySelector('#canvas'),
		      startbutton  = document.querySelector('#startbutton'),
		      width = 460,
		      height = 340;

		  navigator.getMedia = ( navigator.getUserMedia ||
		                         navigator.webkitGetUserMedia ||
		                         navigator.mozGetUserMedia ||
		                         navigator.msGetUserMedia);

		  navigator.getMedia(
		    {
		      video: true,
		      audio: false
		    },
		    function(stream) {
		      if (navigator.mozGetUserMedia) {
		        video.mozSrcObject = stream;
		      } else {
		        var vendorURL = window.URL || window.webkitURL;
		        video.src = vendorURL.createObjectURL(stream);
		      }
		      video.play();
		    },
		    function(err) {
		      console.log("An error occured! " + err);
		    }
		  );

		  video.addEventListener('canplay', function(ev){
		    if (!streaming) {
		      height = video.videoHeight / (video.videoWidth/width);
		      streaming = true;
		    }
		  }, false);

		  function takepicture() {
            allLayer = [];
            layerId = 1;
		    canvas.width = width;
		    canvas.height = height;
		    canvas.getContext('2d').drawImage(video, 0, 0, width, height);
		    var dataURL = canvas.toDataURL('image/png');
            firstCanvas = canvas.toDataURL('image/png');
		    showElement("pics_title", "inline-block");
		    showElement("pics_comment", "inline-block");
		    showElement("container-published", "block");
		    showElement("edit-menu", "inline-block");
		    showElement("edit-area", "inline-block");
		  }

		  startbutton.addEventListener('click', function(ev){
		      takepicture();
		    ev.preventDefault();
		  }, false);

		})();
}());