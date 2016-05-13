<?php
class IconsLib{
    private $_db;
    
    public function __construct($db){
        $this->setDb($db);
    }
    
    public function iconSelector(){
        $db = $this->getDb();
        $query = $db->prepare('SELECT * FROM icons');
        $query->execute();
        while($data = $query->fetch(PDO::FETCH_ASSOC)){
            if ($data['id'] === $_SESSION['id_icon'])
                echo '<div class = "icon-selector icon-selected" id = "icon-'.$data['id'].'"><img alt = "'.$data['name'].'" title = "'.$data['name'].'" class = "display-picture" src = "'.$data['url'].'"></div>';
            else
                echo '<div class = "icon-selector" id = "icon-'.$data['id'].'"><img alt = "'.$data['name'].'" title = "'.$data['name'].'" class = "display-picture" src = "'.$data['url'].'"></div>';
        }
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