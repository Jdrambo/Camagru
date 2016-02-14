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
	echo '<p>Bienvenue sur Camagru le site de retouche photo ultime</p>';
    
    echo '<video id="video"></video>
            <button id="startbutton">Prendre une photo</button>
            <canvas id="canvas"></canvas>
            <img src="http://placekitten.com/g/320/261" id="photo" alt="photo">';
	
	$query = $db->prepare('SELECT account.login, pictures.id AS picture_id, pictures.url, pictures.title, pictures.comment, DAY(pictures.date_ajout) AS day_add, MONTH(pictures.date_ajout) AS month_add,
	YEAR(pictures.date_ajout) AS year_add, HOUR(pictures.date_ajout) AS hour_add, MINUTE(pictures.date_ajout) AS min_add FROM pictures INNER JOIN account ON account.id = pictures.user_id WHERE pictures.published = 1 ORDER BY pictures.date_ajout DESC LIMIT 10');
	$query->execute();
	while ($data = $query->fetch(PDO::FETCH_ASSOC)){
		if ($data['day_add'] <= 9)
			$data['day_add'] = "0".$data['day_add'];
		if ($data['month_add'] <= 9)
			$data['month_add'] = "0".$data['month_add'];
        if ($data['hour_add'] <= 9)
            $data['hour_add'] = "0".$data['hour_add'];
        if ($data['min_add'] <= 9)
            $data['min_add'] = "0".$data['min_add'];
		echo '<div class = "border_pics"><p class = "title_pics">'.$data['title'].'</p><p>Par : '.$data['login'].'</p><img class = "main_pics" src = "'.$data['url'].'"><p class = "comment_pics">'.$data['comment'].'</p><p>Le '.$data['day_add'].'/'.$data['month_add'].'/'.$data['year_add'].' à '.$data['hour_add'].'h'.$data['min_add'].'</p></div>';
	}
    ?>
    <script>
       (function() {

  var streaming = false,
      video        = document.querySelector('#video'),
      cover        = document.querySelector('#cover'),
      canvas       = document.querySelector('#canvas'),
      photo        = document.querySelector('#photo'),
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
      video.setAttribute('width', width);
      video.setAttribute('height', height);
      canvas.setAttribute('width', width);
      canvas.setAttribute('height', height);
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