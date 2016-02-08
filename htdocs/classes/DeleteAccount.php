<?php
class DeleteAccount{

	private $_db;
	private $_pass;
	private $_id;
	private $_pref;
	private $_suff;

	public function __construct($id, $pass, $db){
		$this->setDb($db);
		$this->setPass($pass);
		$this->setId($id);
		$this->setPref();
		$this->setSuff();
	}

	public function eraseAccount(){
		$db = $this->getDb();
		$pass = hash("whirlpool", $this->getPref().$this->getpass().$this->getSuff());
		$query = $db->prepare('SELECT id FROM account WHERE id = :id && pass = :pass');
		$query->bindValue(':id', $this->getId());
		$auery->bindValue(':pass', $this->getPass());
		$query->execute();
		$data = $query->fetch(PDO::FETCH_ASSOC);
		if (isset($data['id'])){
			$query = $db->prepare('DELETE FROM account WHERE id = :id');
			$query->bindValue(':id', $this->getId());
			$query->execute();
			if (isset($_SESSION)){
			    foreach ($_SESSION as $key => $value){
			        unset($_SESSION[$key]);
			    }
			    session_unset($_SESSION);
			}
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

	public function setPref(){
		$db = $this->getDb();
		$query = $db->prepare('SELECT pass_prefixe FROM config');
		$query->execute();
		$data = $query->fetch(PDO::FETCH_ASSOC);
	}

	public function setSuff(){
		$db = $this->getDb();
		$query = $db->prepare('SELECT pass_suffixe FROM config');
		$query->execute();
		$data = $query->fetch(PDO::FETCH_ASSOC);
	}

	public function getDb(){
		return($this->_db);
	}

	public function getPass(){
		return ($this->_pass);
	}

	public function getId(){
		return ($this->_id);
	}

	public function getPref(){
		return ($this->_pref);
	}

	public function getSuff(){
		return ($this->_suff);
	}
}
?>