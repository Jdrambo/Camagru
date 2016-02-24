<?php
class PicsLib{
    private $_db;
    
    public function __construct($db){
        $this->setDb($db);
    }
    
    public function displayLib(){
        $db = $this->getDb();
        $query = $db->prepare('SELECT id, title, url, comment, DAY(date_ajout) AS day_add, MONTH(date_ajout) AS month_add, YEAR(date_ajout) AS year_add, published FROM pictures WHERE user_id = :user_id ORDER BY date_ajout DESC');
        $query->bindValue(":user_id", $_SESSION['id']);
        $query->execute();
        
        echo '<div class = "pics-lib">';
        while ($data = $query->fetch(PDO::FETCH_ASSOC)){
            if ($data['day_add'] <= 9)
                $data['day_add'] = "0".$data['day_add'];
            if ($data['month_add'] <= 9)
                $data['month_add'] = "0".$data['month_add'];
            if ($data['published'] === "0"){
                $published = "Privée";
                $url = "img/lock.png";
            }
            else{
                $published = "Publique";
                $url = "img/unlock.png";
            }
            echo '<div class = "pics-border" id = "border-'.$data['id'].'"><p class = "pics-lib-title">'.$data['title'].'</p><img class = "img-pics-lib" src = "'.$data['url'].'"><p class = "pics-lib-title">'.$data['comment'].'</p><p class = "pics-lib-title">'.$data['day_add'].'/'.$data['month_add'].'/'.$data['year_add'].'<br><span id = "privacy-'.$data['id'].'">'.$published.'</span></p><img id = "delete-'.$data['id'].'" class = "img-delete" src = "img/delete.png" alt = "delete" title = "Supprimer la photo"><img id = "img-privacy-'.$data['id'].'" class = "img-privacy" src = "'.$url.'"></div>';
        }
        echo '</div>';
    }
    
    public function setDb($value){
        if (isset($value))
            $this->_db = $value;
    }
    
    public function getDb(){
        return ($this->_db);
    }
}
?>