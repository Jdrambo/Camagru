<?php
class SessionUser{
    
    private $_db;
    private $_login;
    
    public function __construct($db, $login){
        $this->setDb($db);
        $this->setLogin($login);
    }
    
    public function createSession(){
        $query = $this->getDb()->prepare('SELECT account.id, account.login, account.mail, account.type, account.role, account.date_inscription, YEAR(account.date_inscription) AS ins_year, MONTH(account.date_inscription) AS ins_month, DAY(account.date_inscription) AS ins_day, HOUR(account.date_inscription) AS ins_hour, MINUTE(account.date_inscription) AS ins_min, SECOND(account.date_inscription) AS ins_sec, icons.id as id_icon, icons.name, icons.url
        FROM account INNER JOIN icons ON account.id_icon = icons.id
        WHERE account.login = :login');
        $query->bindValue(':login', $this->getLogin());
        $query->execute();
        $data = $query->fetch(PDO::FETCH_ASSOC);
        foreach($data as $key => $value){
            if ($key != 'pass' && $key != 'clef'){
                if (($key === 'ins_day' || $key === 'ins_month' || $key === 'ins_year' || $key === 'ins_hour' || $key === 'ins_min' || $key === 'ins_sec') && $value <= 9){
                    $value = "0".$value;
                }
                $_SESSION[$key] = $value;
            }
        }
    }
    
    public function setDb($value){
        if (isset($value))
            $this->_db = $value;
    }
    
    public function setLogin($value){
        if (isset($value))
            $this->_login = $value;
    }
    
    public function getDb(){
        return ($this->_db);
    }
    
    public function getLogin(){
        return ($this->_login);
    }
}
?>