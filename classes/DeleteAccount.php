<?php
class DeleteAccount{

	private $_db;
	private $_pass;
	private $_id;

	public function __construct($id, $pass, $db){
		$this->setDb($db);
		$this->setPass($pass);
		$this->setId($id);
	}

	public function eraseAccount(){
		$db = $this->getDb();
		$query = $db->prepare('SELECT * FROM `account` WHERE `id` = :id');
		$query->bindValue(':id', $this->getId());
		$query->execute();
        
        if ($query->rowCount() > 0){
		$data = $query->fetch(PDO::FETCH_ASSOC);
            if (isset($data['id']) && password_verify($this->getPass(), $data['pass'])){
                $query = $db->prepare('SELECT `pictures`.`url`, `account`.`pictures_dir` FROM `account` INNER JOIN `pictures` ON `pictures`.`user_id` = `account`.`id` WHERE `account`.`id` = :id');
                $query->bindValue(':id', $this->getId());
                $query->execute();
                while($data = $query->fetch(PDO::FETCH_ASSOC)){
                    if (isset($data['pictures_dir']))
                        $dir = $data['pictures_dir'];
                    if (isset($data['url']) && file_exists($data['url']))
                        unlink($data['url']);
                }
                if (isset($dir) && file_exists($dir))
                    rmdir($dir);
                $query = $db->prepare('DELETE `account`, `pictures`, `tablk`, `comments` FROM `account` INNER JOIN `pictures` ON `pictures`.`user_id` = `account`.`id` INNER JOIN `tablk` ON `tablk`.`user_id` = `account`.`id` INNER JOIN `comments` ON `comments`.`user_id` = `account`.`id` WHERE `account`.`id` = :id');
                $query->bindValue(':id', $this->getId());
                $query->execute();
                /*if (isset($_SESSION)){
                    foreach ($_SESSION as $key => $value){
                        unset($_SESSION[$key]);
                    }
                }*/
            }
            else {
                $message = array("Vous n'avez pas saisi le bon mot de passe", "error");
                return ($message);
            }
        }
		else {
			$message = array("Vous n'avez pas saisi le bon mot de passe", "error");
			return ($message);
		}
	}

	public function setDb($value){
		if (isset($value))
			$this->_db = $value;
	}

	public function setPass($value){
		if (isset($value))
			$this->_pass = $value;
	}

	public function setId($value){
		if (isset($value))
			$this->_id = $value;
	}

	public function getDb(){
		return($this->_db);
	}

	public function getPass(){
		return($this->_pass);
	}

	public function getId(){
		return($this->_id);
	}
}
?>