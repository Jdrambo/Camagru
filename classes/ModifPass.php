<?php
class ModifPass{
    private $_db;
    private $_id;
    private $_oldPass;
    private $_newPass;
    private $_newPassVerif;
    private $_pref;
    private $_suff;
    private $_passPattern;
    private $_message = array("", "");
    
    public function __construct($db, $id, $oldPass, $newPass, $newPassVerif){
        $this->setDb($db);
        $this->setId($id);
        $this->setOldPass($oldPass);
        $this->setNewPass($newPass);
        $this->setNewPassVerif($newPassVerif);
        $this->setPref();
        $this->setSuff();
        $this->setPassPattern();
        $this->changePass();
    }
    
    public function changePass(){
        if ($this->getNewPass() === $this->getNewPassVerif()){
            if (preg_match($this->getPassPattern(), $this->getNewPass())){
                $db = $this->getDb();
                $query = $db->prepare('SELECT * FROM account WHERE id = :id');
                $query->bindValue(':id', $this->getId());
                $query->execute();
                $data = $query->fetch(PDO::FETCH_ASSOC);
                if (isset($data['id']) && isset($data['pass']) && $data['id'] === $this->getId() && password_verify($this->getOldPass(), $data['pass'])){
                    $newPass = password_hash($this->getNewPass(), PASSWORD_DEFAULT);
                    $db = $this->getDb();
                    $query = $db->prepare('UPDATE account SET pass = :newPass WHERE id = :id');
                    $query->bindValue(':newPass', $newPass);
                    $query->bindValue(':id', $this->getId());
                    $query->execute();
                    $this->setMessage(array("Le mot de passe a été changé avec succès", "ok"));
                }
                else
                    $this->setMessage(array("L'ancien mot de passe ne correspond pas à celui enregistré pour ce compte", "error"));
            }
            else
                $this->setMessage(array("Le mot de passe doit faire entre 6 et 18 caractères et ne contenir que des caractères alpha-numériques", "error"));
        }
        else
            $this->setMessage(array("Le mot de passe n'est pas identique à celui de vérification", "error"));
    }
    
    public function setDb($value){
        if (isset($value))
            $this->_db = $value;
    }
    
    public function setId($value){
        if (isset($value))
            $this->_id = ($value);
    }
    
    public function setOldPass($value){
        if (isset($value))
            $this->_oldPass = $value;
    }
    
    public function setNewPass($value){
        if (isset($value))
            $this->_newPass = $value;
    }
    
    public function setNewPassVerif($value){
        if (isset($value))
            $this->_newPassVerif = $value;
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
    
    public function setPassPattern(){
        $db = $this->getDb();
        $query = $db->prepare('SELECT pass_pattern FROM config');
        $query->execute();
        $data = $query->fetch(PDO::FETCH_ASSOC);
        $this->_passPattern = $data['pass_pattern'];
    }
    
    public function setMessage($value){
        if (isset($value))
            $this->_message = $value;
    }
    
    public function getDb(){
        return ($this->_db);
    }
    
    public function getId(){
        return ($this->_id);
    }
    
    public function getOldPass(){
        return ($this->_oldPass);
    }
    
    public function getNewPass(){
        return ($this->_newPass);
    }
    
    public function getNewPassVerif(){
        return ($this->_newPassVerif);
    }
    
    public function getPref(){
        return ($this->_pref);
    }
    
    public function getSuff(){
        return ($this->_suff);
    }
    
    public function getPassPattern(){
        return ($this->_passPattern);
    }
    
    public function getMessage(){
        return ($this->_message);
    }
}
?>