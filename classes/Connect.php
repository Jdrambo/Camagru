<?php
class Connect{
    private $_login;
    private $_pass;
    private $_pattern;
    private $_passPattern;
    private $_db;
    private $_message = array("", "");
    
    public function __construct($login, $pass, $db){
        $this->setDb($db);
        $this->setPattern();
        $this->setPassPattern();
        $this->setLogin($login);
        $this->setPass($pass);
    }
    
    public function checkUser(){
        $db = $this->getDb();
        $query = $db->prepare('SELECT id, pass FROM account WHERE (login COLLATE utf8_bin = :login && actif = 1)');
        $query->bindValue(":login", $this->getLogin());
        $query->execute();
        $data = $query->fetch(PDO::FETCH_ASSOC);
        if (isset($data['id']) && password_verify($this->getPass(), $data['pass'])){
            $this->setMessage("Identification validée", "ok");
            return (true);
        }
        else {
            $this->setMessage("Identifiants non valides", "error");
            return (false);
        }
    }
    
    public function setDb($value){
        if (isset($value))
            $this->_db = $value;
    }
    
    public function setLogin($value){
        if (isset($value) && preg_match($this->getPattern(), $value)){
            $this->_login = $value;
        }
    }
    
    public function setPass($value){
        if (isset($value) && preg_match($this->getPassPattern(), $value)){
            $this->_pass = $value;
        }
    }
    
    public function setPattern(){
        $db = $this->getDb();
        $query = $db->prepare('SELECT pattern FROM config');
        $query->execute();
        $data = $query->fetch(PDO::FETCH_ASSOC);
        $this->_pattern = $data['pattern'];
    }
    
    public function setPassPattern(){
        $db = $this->getDb();
        $query = $db->prepare('SELECT pass_pattern FROM config');
        $query->execute();
        $data = $query->fetch(PDO::FETCH_ASSOC);
        $this->_passPattern = $data['pass_pattern'];
    }

    public function setMessage($text, $tag){
        if (isset($text) && isset($tag))
            $this->_message = array($text, $tag);
    }
    
    public function getDb(){
        return ($this->_db);
    }
    
    public function getLogin(){
        return ($this->_login);
    }
    
    public function getPass(){
        return ($this->_pass);
    }
    
    public function getPattern(){
        return ($this->_pattern);
    }
    
    public function getPassPattern(){
        return ($this->_passPattern);
    }

    public function getMessage(){
        return ($this->_message);
    }
}
?>