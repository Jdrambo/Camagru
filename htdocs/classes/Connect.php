<?php
class Connect{
    private $_login;
    private $_pass;
    private $_pref;
    private $_suff;
    private $_pattern;
    private $_passPatter;
    private $_db;
    
    public function __construct($login, $pass, $db){
        $this->setDb($db);
        $this->setLogin($login);
        $this->setPass($pass);
        $this->setPref();
        $this->setSuff();
        $this->setPattern();
        $this->setPassPattern();
    }
    
    public function checkUser(){
        $db = $this->getDb();
        $pass = $this->getPref().$this->getPass().$this->getSuff();
        $query = $db->prepare('SELECT id FROM account WHERE (pass = :pass && login = :login)');
        $query->bindValue(":pass", $pass);
        $query->bindValue(":login", $this->getLogin());
        $query->execute();
        $data = $query->fetch(PDO::FETCH_ASSOC);
        if (isset($data['id']))
            return (true);
        else
            return (false);
    }
    
    public function setDb($db, $dbLogin, $dbPass){
        if (isset($db))
            $this->_db = $db;
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
    
    public function setPref(){
        $db = $this->getDb();
        $query = $db->prepare('SELECT pass_prefixe FROM config');
        $query->execute();
        $data = $query->fetch(PDO::FETCH_ASSOC);
        $this->_pref = $data['pass_prefixe'];
    }
    
    public function setSuff(){
        $db = $this->getDb();
        $query = $db->prepare('SELECT pass_suffixe FROM config');
        $query->execute();
        $data = $query->fetch(PDO::FETCH_ASSOC);
        $this->_suff = $data['pass_suffixe'];
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
    
    public function getDb(){
        return ($this->_db);
    }
    
    public function getLogin(){
        return ($this->_login);
    }
    
    public function getPass(){
        return ($this->_pass);
    }
    
    public function getPref(){
        return ($this->_pref);
    }
    
    public function getSuff(){
        return ($this->_suff);
    }
    
    public function getPattern(){
        return ($this->_pattern);
    }
    
    public function getPassPattern(){
        return ($this->_passPattern);
    }
}
?>