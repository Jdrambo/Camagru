<?php
class PicsLib{
    private $_db;
    
    public function __construct($db){
        $this->setDb($db);
    }
    
    public function displayLib(){
        $db = $this->getDb();
        $query = $db->prepare('SELECT * FROM pictures WHERE user_id = :user_id ORDER BY date_ajout DESC');
        $query->bindValue(":user_id", $_SESSION['id']);
        $query->execute();
        
        echo '<div class = "pics-lib">';
        while ($data = $query->fetch(PDO::FETCH_ASSOC)){
            echo '<div class = "pics-border"><img id = "delete-'.$data['id'].'" class = "img-delete" src = "img/delete.png" alt = "delete" title = "Supprimer la photo"><img class = "img-pics-lib" src = "'.$data['url'].'"></div>';
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