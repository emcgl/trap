<?php

include_once dirname(__FILE__)."/../includes/database.php";
include_once dirname(__FILE__)."/../sitemap.php";

class User
{		
	private $id;
	private $name;
	private $level;
	private $email;
	
	private function User($id, $name, $level, $email) {
		$this->id=$id;
		$this->name=$name;
		$this->level=$level;
		$this->email=$email;
	}
	
	public static function create($name, $password, $level, $email) {
			
		//Have all user param's?
		if(!isset($name) || !isset($password) || !isset($level) || !isset($email) ) 
			throw new InvalidArgumentException("Please provide name, password level and email for new user!");

		//Valid level?
		if(!isset(SiteMap::$UserLevels[$level])) {
			$valids="[";
			foreach(array_keys(SiteMap::$UserLevels) as $val) {$valids.=" ".$val;}
			$valids.="]";
			throw new InvalidArgumentException("Please provide defined level: ".$valids);
		}
		
		global $db;
		
		//Is there a user with same name or email?
		$count=0;
		$sql = "SELECT * FROM users WHERE name=:name OR email=:email";
		try {
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':name', $name);
			$stmt->bindParam(':email', $email);
			$stmt->execute();
			$count = $stmt->rowCount();
		} catch(PDOException $e) {
			error_log ("Error: ".$e->getMessage());
			print "Error checking duplicate name or email!";		
		} 	
		if($count > 0) 
			throw new InvalidArgumentException("Name or email adress already in use! Please choose other");
		
		//2do: Check Email!
		
		$id=-1;
		
		//Add user and return instance
		$sql = "INSERT INTO users (name, password, level, email) VALUES (:name, :password, :level, :email)";
		try {
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':name', $name);
			$stmt->bindParam(':password', password_hash($password, PASSWORD_DEFAULT));
			$stmt->bindParam(':level', SiteMap::$UserLevels[$level]);
			$stmt->bindParam(':email', $email);
			$stmt->execute();	
		} catch(PDOException $e) {
			error_log ("Error: ".$e->getMessage());
			print "Error inserting new user!";
			die();
		}
		
		$id = $db->lastInsertId();
		
		$user = new User($id, $name, $level, $email);
		
		return $user;
	}

	public function delete() {
		
		global $db;
		
		$sql = "DELETE FROM users WHERE id=:id";
		try {
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':id', $this->id);
			$stmt->execute();
		} catch( PDOException $e) {
			error_log ("Error: ".$e->getMessage());
			print "Error deleting user!";
			die();
		}
		
		return;
				
	}
	
	public static function login($name, $password) {
		
		global $db;
		
		$sql = "SELECT id, name, password, level, email FROM users WHERE name=:name";
		try {
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':name', $name);
			$stmt->execute();

			$count = $stmt->rowCount();

			if($count > 1)
				throw new Exception("Something wrong with user database! Found more than one accounts with name.");
			
			if($count == 0)
				return false;
							
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			
			if( password_verify($password, $result['password']) ) {
				return new User( $result['id'], 
								 $result['name'], 
						         $result['password'], 
						         $result['level'], 
						         $result['email'] 
						       );
			}
			
		} catch(PDOException $e) {
			error_log ("Error: ".$e->getMessage());
			print "Error checking duplicate name or email!";
		}		
		
		return false;
	}
	
	public function hasAccess($level) {
		if(! isset(SiteMap::$UserLevels[$level]))
			throw new Exception("Unknown user level!");
		
		$numlevel = SiteMap::$UserLevels[$level];		
		
		if( $numlevel >= SiteMap::$UserLevels[$level] )
			return true;

		return false;
	}
	
}