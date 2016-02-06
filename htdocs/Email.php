<?php
class Email{
    
    private $_email;
    private $_message;
    private $_subject;
    private $_headers;
    
    public function __construct(array $data){
        foreach($data as $key => $value){
            $func = "set".ucfirst($key);
            $this->$func($value);
        }
    }
    
    public function sendEmail(){
        mail($this->getMail(), $this->getSubject(), $this->getMessage(), $this->getHeaders())
    }
    
    public function setEmail($value){
        if (isset($value))
            $this->_email = $value;
    }
    
    public function setMessage($value){
        if (isset($value))
            $this->_message = $value;
    }
    
    public function setSubject($value){
        if (isset($value))
            $this->_subject = $value;
    }
    
    public function setHeaders($value){
        if (isset($value))
            $this->_headers = $value;
    }
    
    public function getEmail(){
        return($this->_email);
    }
    
    public function getMessage(){
        return($this->_message);
    }
    
    public function getSubject(){
        return($this->_subject);
    }
    
    public function getHeaders(){
        return($this->_headers);
    }
    
    public function describeObj(){
        echo '<p class = "alert" >Email : '.$this->getEmail().' / Login : '.$this->getSubject().' / Clef : '.$this->getHeaders().'</p>';
        echo '<p class = "alert" >Message : '.$this->getMessage().'</p>';
    }
}
?>