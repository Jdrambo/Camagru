<?php
class Email{
    
    private $_email;
    private $_login;
    private $_clef;
    private $_message;
    
    public function __construct(array $data){
        /*$this->setEmail($email);
        $this->setLogin($login);
        $this->setClef($clef);
        $this->setmessage($message);*/
        foreach($data as $key => $value){
            $func = "set".ucfirst($key);
            $this->$func = $value;
        }
    }
    
    public function sendEmail(){
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
    
    public function setMessage($value){
        if (isset($value))
            $this->_message = $value;
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
    
    public function getMessage(){
        return($this->_message);
    }
    
    public function describeObj(){
        echo '<p class = "alert" >Email : '.$this->getEmail().' / Login : '.$this->getLogin().' / Clef : '.$this->getClef().'</p>';
        echo '<p class = "alert" >Message : '.$this->getMessage().'</p>';
    }
}
?>