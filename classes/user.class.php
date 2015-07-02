<?php

include_once dirname(__FILE__)."/../includes/database.php";
include_once dirname(__FILE__)."/../sitemap.php";

class User
{		
	private $id;
	private $name;
	private $nlevel; //This contains the numeric level (see SiteMap for names)
	private $email;
	
	private function User($id, $name, $nlevel, $email) {
		$this->id=$id;
		$this->name=$name;
		$this->nlevel=$nlevel;
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
		
		$nlevel = SiteMap::$UserLevels[$level];
		
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
			throw new InvalidArgumentException("Name or email adress already in use! Please choose other!");
		
		//2do: Check Email!
		
		$id=-1;
		
		//Add user and return instance
		$sql = "INSERT INTO users (name, password, nlevel, email) VALUES (:name, :password, :nlevel, :email)";
		try {
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':name', $name);
			$stmt->bindParam(':password', password_hash($password, PASSWORD_DEFAULT));
			$stmt->bindParam(':nlevel', $nlevel);
			$stmt->bindParam(':email', $email);
			$stmt->execute();	
		} catch(PDOException $e) {
			error_log ("Error: ".$e->getMessage());
			print "Error inserting new user!";
			die();
		}
		
		$id = $db->lastInsertId();
		
		$user = new User($id, $name, $nlevel, $email);
		
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
		
		$sql = "SELECT id, name, password, nlevel, email FROM users WHERE name=:name";
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
						         $result['nlevel'], 
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
		
		$nlevel = SiteMap::$UserLevels[$level];
				
		if( $nlevel <= $this->nlevel )
			return true;

		return false;
	}
	
	public function setAccess($level) {
		
		if(! isset(SiteMap::$UserLevels[$level]))
			throw new Exception("Unknown user level!");

		$nlevel = SiteMap::$UserLevels[$level];
		
		global $db;
		
		$sql = "UPDATE users SET nlevel=:nlevel WHERE id=:id";
		try {
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':id', $this->id);
			$stmt->bindParam(':nlevel', $nlevel);
			$stmt->execute();
		} catch(PDOException $e) {
			error_log ("Error: ".$e->getMessage());
			print "Error updating user in database!";
			die();
		}
		
		$this->nlevel = $nlevel;
		
		return true;
	}
	
	public function generateValidationCode() {
		$code=md5("thisisasecret".$this->name.$this->email);
		return $code;
	}
	
	/*
	 * Sets user's new level and returns true on valid code
	 */
	public static function confirmValidationCode($email, $code) {

		global $db;
		
		$sql = "SELECT id, name, nlevel FROM users WHERE email=:email";
		
		try {
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':email', $email);
			$stmt->execute();		

			$count = $stmt->rowCount();
			
			if($count > 1)
				throw new Exception("Something wrong with user database! Found more than one accounts with name.");
				
			if($count == 0)
				throw Exception("Invalid email!");
			
			$result = $stmt->fetch(PDO::FETCH_ASSOC);						
			
			$vcode = md5("thisisasecret".$result['name'].$email);			
			
			if($code == $vcode) {

				$user = new User( $result['id'], 
							      $result['name'], 
						          $result['nlevel'], 
						          $email 
				);
				
				if($user->hasAccess("user")) 
					throw new Exception("User already has login access level.");

				$user->setAccess("user");
										
				return true;	
			}
							
		} catch (Exception $e) {
			error_log ("Error: ".$e->getMessage());
			throw new Exception("Error validating code");
		}

		return false;
	}
}