<?php
session_start();
function loadClass($name){
	require("classes/".$name.".php");
}
spl_autoload_register("loadClass");
?>
<!DOCTYPE html>
<html>
<?php
include("head.php");
?>
<body>
<?php 
if (isset($_SESSION['id']))
{
	include("db.php");
    echo '<div class = "container">';
    include('header.php');
    echo '<div id = "state_message" class = "state_message">UN MESSAGE</div>';
	echo '<p>Edition de photos</p>';

	echo '<video id="video" class = "cam-video"></video>
            <img title = "Prendre une photo" id="startbutton" class = "cam-btn" alt = "prendre une photo" src = "img/cam.png">
            <div class = "edit-area"><canvas id = "canvas" class = "cam-pics"></canvas><div class = "filter-area"></div></div>
            <input id = "pics_title" type = "text" name = "title" class = "field" placeholder = "Titre de la photo...">
            <input id = "pics_comment" type = "text" name = "comment" class = "field" placeholder = "Description...">
            <p id = "container-published" class = "container-check"><input class = "form-check" name = "published" id = "pics_published" type = "checkbox"> Publier</p>
            <div id = "edit-menu" class = "edit-menu">
            	<img id = "save" title = "Enregistrer la photo" class = "edit-btn" alt = "enregistrer l\'image" src = "img/save.png">
            	<img id = "edit" title = "Editer la photo" class = "edit-btn" alt = "éditer l\'image" src = "img/edit.png">
            </div></div>';
	?>
	<script src = "js/edit.js"></script>
	  <script>
	  		hideElement("pics_title");
		    hideElement("pics_comment");
		    hideElement("container-published");
		    hideElement("edit-menu");
       (function() {
		  var streaming = false,
		      video        = document.querySelector('#video'),
		      canvas       = document.querySelector('#canvas'),
		      startbutton  = document.querySelector('#startbutton'),
		      width = 320,
		      height = 0;

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
		    canvas.width = width;
		    canvas.height = height;
		    canvas.getContext('2d').drawImage(video, 0, 0, width, height);
		    var dataURL = canvas.toDataURL('image/png');
		    showElement("pics_title", "inline-block");
		    showElement("pics_comment", "inline-block");
		    showElement("container-published", "block");
		    showElement("edit-menu", "inline-block");
		  }

		  startbutton.addEventListener('click', function(ev){
		      takepicture();
		    ev.preventDefault();
		  }, false);

		})();
    </script>
	<?php
}
else
{
	echo '<div class = "container">';
    include('header.php');
    echo '<p>Bienvenue sur Camagru le site de retouche photo ultime</p></div>';
}
?>
</body>
</html>