<?php
class Link
{

	 // info for db connection
	protected $db_host = 'localhost';
	protected $db_user = 'root';
	protected $db_pass = 'root';
	protected $db_name = 'crawler';
	protected $port = '3306';
	
	
	// constructor
	function __construct()
	{
		$this->db_connect();
	}

	/*
	 * Conect to database
	 */
	private function db_connect()
	{
		$this->mysqli = new mysqli( $this->db_host, $this->db_user, $this->db_pass, $this->db_name, $this->port) or die("db error");
	}
	
	/*
	 * input: url
	 * Insert url in db
	 * output: -
	 */
	private function insertLink($url)
	{
		if($this->checkLink($url)) return true;
		
		$query = $this->mysqli->real_escape_string("INSERT INTO `urls`(`url`) VALUES (?)");
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param("s", $url);
		
		$stmt->execute();
		$stmt->close();
		
		return;
	}
	
	/*
	 * input: -
	 * get all inserted urls from db
	 * output: urls (as array)
	*/
	public function arrayLink()
	{
		$query = $this->mysqli->real_escape_string("SELECT `url` FROM `urls`");
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->execute();
		$stmt->bind_result($url);
		
		$a = array();
		while ($stmt->fetch()) {
	        array_push($a, $url);
	    }
	    
	    return $a;
	}
	
	/*
	 * input: url
	 * Is that url already in db
	 * output: true or false (boolean)
	 */ 
	private function checkLink($url)
	{
		$query = $this->mysqli->real_escape_string("SELECT `url` FROM `urls` WHERE `url` = ? LIMIT 1");
		
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param("s", $url);
		$stmt->execute();
		$stmt->store_result();
		
		if($stmt->num_rows > 0) return true;
		else	 				return false;
	}
	
	/*
	 * input: id (int)
 	 * get url with that ID
 	 * output: url or false if it isnt found
	 */
	private function getThisLink($id)
	{
		$query = $this->mysqli->real_escape_string("SELECT `url` FROM `urls` WHERE `id` = ? ");
		
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param("i", $id);
		$stmt->execute();
		$stmt->store_result();
		
		if($stmt->num_rows == 1)
		{
			$stmt->bind_result($url);
			$stmt->fetch();
			
			if($this->isCheckedLink($url) == true) return false;
			
			$query = $this->mysqli->real_escape_string("INSERT INTO `urls` (`url`) VALUES (?)");
			$stmt2 = $this->mysqli->prepare($query);
			$stmt2->bind_param("s", $url);
			
			$stmt2->execute();
			$stmt2->close();
			
			return $url;
		}
		
		return false;
	}
	
	/*
	 * input: url
	 * did we already crawl that link ?
	 * output: true or false (boolean)
	 */
	private function isCheckedLink($url)
	{
		$checked = 1;
		
		$query = "SELECT `url` FROM `urls` WHERE `url` = ? AND `checked` = ?";
		
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param("si", $url,$checked);
		$stmt->execute();
		$stmt->store_result();
		
		if($stmt->num_rows > 0) return true;
		
		return false;
	}
	
	/*
	 * input: url
	 * Marking link as checked, that crawling of that page is done
	 * output: -
	 * 
	*/
	private function setCheckedLink($url)
	{
		$checked = 1;

		$query = $this->mysqli->real_escape_string("UPDATE `urls` SET `checked` = ? WHERE `url` = ?");
	
		$stmt = $this->mysqli->prepare($query);
		$stmt->bind_param("is", $checked, $url);
		$stmt->execute();
		
		$stmt->close();

		return;
	}	
	
	
	
	public function check($url)
	{
		return $this->checkLink($url);
	}
	
	public function insert($url)
	{
		return $this->insertLink($url);
	}
	
	public function isChecked($url)
	{
		return $this->isCheckedLink($url);
	}
	
	public function setChecked($url)
	{
		return $this->setCheckedLink($url);
	}	
	
	public function getLink($id)
	{
		return $this->getThisLink($id);
	}
	
}