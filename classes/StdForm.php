<?php
class StdForm{
    private $_method;
    private $_action;
    private $_class;
    private $_id;
    private $_title;
    
    public function __construct($method, $action, $class, $id, $title){
        $this->setMethod($method);
        $this->setAction($action);
        $this->setClass($class);
        $this->setId($id);
        $this->setTitle($title);
    }
    
    public function addInputs($inputs = array()){
        echo '<form method = "'.$this->getMethod().'" action = "'.$this->getAction().'" class = "'.$this->getClass().'" id = "'.$this->getId().'"><h3>'.$this->getTitle().'</h3>';
        /*
        0 => tag de l'input (input, button, radio, select...)
        1 => type de l'input (text, password, date...)
        2 => name de l'input
        3 => classe css
        4 => placeholder pour input
        */
        foreach($inputs as $value){
            if ($value[0] === "input")
                echo '<input type = "'.$value[1].'" name = "'.$value[2].'" class = "'.$value[3].'" placeholder = "'.$value[4].'">';
            else
                echo '<button type = "'.$value[1].'" name = "'.$value[1].'" value = "'.$value[2].'" class = "'.$value[3].'">'.$value[4].'</button>';
        }
        echo '</form>';
    }
    
    public function setMethod($value){
        if (isset($value))
            $this->_method = $value;
    }
    
    public function setAction($value){
        if (isset($value))
            $this->_action = $value;
    }
    
    public function setClass($value){
        if (isset($value))
            $this->_class = $value;
    }
    
    public function setId($value){
        if (isset($value))
            $this->_id = $value;
    }

    public function setTitle($value){
        if (isset($value))
            $this->_title = $value;
    }
    
    public function getMethod(){
        return ($this->_method);
    }
    
    public function getAction(){
        return ($this->_action);
    }
    
    public function getClass(){
        return ($this->_class);
    }
    
    public function getId(){
        return ($this->_id);
    }

    public function getTitle(){
        return ($this->_title);
    }
}
?>