<?php
class Email{
    private $_email;
    private $_login;
    private $_clef;
    
    public function __construct($email, $login, $clef){
        $this->hydrate();
    }
    
    public function hydrate($email, $login, $clef){
        $this->setEmail($email);
        $this->setLogin($login);
        $this->setClef($clef);
    }
    
    public function sendEmail(){
        $message = "Bonjour et bienvenue sur Camagru";
        //ICI ON PREPARE LE MAIL A ENVOYER
        //mail();
    }
    
    public function setEmail($value){
        if (isset($value))
            $this->_email = $value;
    }
    
    public function setLogin($value){
        if (isset($value))
            $this->_login = $value;
    }
    
    public function setClef($value){
        if (isset($value))
            $this->_clef = $value;
    }
    
    public function getEmail(){
        return($this->_email);
    }
    
    public function getLogin(){
        return($this->_login);
    }
    
    public function getClef(){
        return($this->_clef);
    }
    
    public function describeObj(){
        console.log('Email : '.$this->getEmail().' / Login : '.$this->getLogin().' / Clef : '.$this->getClef());
    }
}
?>