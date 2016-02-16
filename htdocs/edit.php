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
<div class = "container">
<?php include('header.php');
if (isset($_SESSION['id']))
{
	include("db.php");
	echo '<p>Edition de photos</p>';

	echo '<video id="video" class = "cam-video"></video>
            <img id="startbutton" class = "cam-btn" alt = "prendre une photo" src = "img/cam.png">
            <canvas id="canvas" class = "cam-pics"></canvas>';
	?>
	  <script>
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
		    var data = canvas.toDataURL('image/png');
		    photo.setAttribute('src', data);
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
	echo '<p>Bienvenue sur Camagru le site de retouche photo ultime</p>';
}
?>
</div>
</body>
</html>