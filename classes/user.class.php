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
		
		//Creating user data folder
		if(!mkdir(dirname(__FILE__)."/../data/users/$id")) {
			error_log("User $id may is in db but doesn't have data folder!");
			die("Error creating user data folder!");
		}
				
		$user = new User($id, $name, $nlevel, $email);			
		
		return $user;
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
	
	/*
	 * Retrieve from database
	 */
	public static function retrieve($id) {
	
		$sql = "SELECT id, name, nlevel, email FROM users WHERE id=:id";
	
		global $db;
		
		$stmt = $db->prepare($sql);
		
		try {
			$stmt->bindParam(':id', $id);
			$stmt->execute();				
		} catch(PDOException $e) {
			error_log ("Error: ".$e->getMessage());
			print "Error getting user!";
		}
		
		$count = $stmt->rowCount();
		
		if($count > 1)
			throw new Exception("Something wrong with user database! Found more than one accounts with id.");
		
		if($count == 0)
			return false;
		
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		
		return new User( $result['id'],
				$result['name'],
				$result['nlevel'],
				$result['email']
				);									
		
	}
	
	public function update($name, $password, $level, $email) {
		
		$update_name=false;
		$update_password=false;
		$update_nlevel=false;
		$update_email=false;		
		
		if( isset($name) && $name!="" && $name != $this->name ) $update_name=true;
		if( isset($password) && $password!="" ) $update_password=true;
		if( isset($level) && isset(SiteMap::$UserLevels[$level]) &&  $this->nlevel != SiteMap::$UserLevels[$level]) $update_nlevel=true;    
		if( isset($email) && $email!="" && $this->email != $email) $update_email=true;
		
		if($update_name==false && $update_password==false && $update_nlevel==false && $update_email==false) {
			throw new Exception("Nothing to update!");
		}
		
		$sql = "UPDATE users SET";
		$comma=0;
		
		if($update_name) {
			$comma++;
			$sql.=" name=:name";
		}
		if($update_password) {
			if($comma>0) $sql.=",";$comma++;
			$sql.=" password=:password";			
		}
		if($update_nlevel) {
			if($comma>0) $sql.=","; $comma++;
			$sql.=" nlevel=:nlevel";
		}
		if($update_email) {
			if($comma>0) $sql.=","; $comma++;
			$sql.=" email=:email";
		}

		$sql.=" WHERE id=:id";
		
		global $db;		
		
		try {
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':id', $this->id);
			if($update_name) $stmt->bindParam(':name', $name);
			if($update_password) $stmt->bindParam(':password', password_hash($password, PASSWORD_DEFAULT));
			if($update_nlevel) $stmt->bindParam(':nlevel', SiteMap::$UserLevels[$level]);
			if($update_email)$stmt->bindParam(':email', $email);
			$stmt->execute();
		} catch(PDOException $e) {
			error_log ("Error: ".$e->getMessage());
			print "Error updating user!";
			die();
		}
		
		return;
		
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
		
		//Moving user folder to trash (maybe delete later)
		rename(dirname(__FILE__)."/../data/users/$this->id", dirname(__FILE__)."/../data/trash/$this->id");

		return;
				
	}
	
	public function hasAccess($level) { 
		if(! isset(SiteMap::$UserLevels[$level]))
			throw new Exception("Unknown user level!");
		
		$nlevel = SiteMap::$UserLevels[$level];
		
		if( $nlevel <= $this->nlevel )
			return true;

		return false;
	}
	
	public function hasId($id) {
		if($id==$this->id)
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
				throw new Exception("Invalid email!");
			
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
	
	public function mail($subject, $text) {
		
		
	}
	
	
	/* Table Header Row */
	public static function tableHeader($edit=false) {
		
		$r="";
		
		$r.="<tr class=\"userheader\"><th>ID</th><th>Name</th><th>Password</th><th>Access Level</th><th>EMail</th>";
		
		if($edit) 
			$r.="<th>Update</th><th>Delete</th>";
		
		$r.="</tr>".PHP_EOL;
			
		return $r;
		
	}
	
	/* Table Row */
	public function tableData($edit=false) {
			
		$r="";
		
		//Make table row with user data
		if(!$edit) {
			
			$r.="<tr>";
			$r.="<td>".$this->id."</td>";
			$r.="<td>".$this->name."</td>";
			$r.="<td>[Secret]</td>";
			$level = array_search($this->nlevel, SiteMap::$UserLevels);
			$r.="<td>".$level."</td>";
			$r.="<td>".$this->email."</td>";
			$r.="</tr>".PHP_EOL;
			
			return $r;
		}
		
		//Make table row with form to adjust user data
		if($edit) {
		
			$r= "<tr>";
			$r.="<td>".$this->id."</td>";
			$r.="<td><input id=\"name_".$this->id."\" name=\"name_".$this->id."\" type=\"text\" value=\"".$this->name."\"></td>";
			$r.="<td><input id=\"password_".$this->id."\" name=\"password_".$this->id."\" type=\"password\" value=\"\"></td>";
			$r.="<td><select id=\"nlevel_".$this->id."\" name=\"nlevel_".$this->id."\">".PHP_EOL;
			foreach(SiteMap::$UserLevels as $level => $nlevel) {
				$r.="<option value=\"".$nlevel."\"".($this->nlevel == $nlevel ?  " selected" : "").">".$level."</option>".PHP_EOL;
			}
			$r.="</select>".PHP_EOL;
			$r.="<td><input id=\"email_".$this->id."\" name=\"email_".$this->id."\" type=\"text\" value=\"".$this->email."\"></td>".PHP_EOL;
			$r.="<td><input id=\"update_".$this->id."\" name=\"update_".$this->id."\" type=\"submit\" value=\"Update\"></td>";
			$r.="<td><input id=\"delete_".$this->id."\" name=\"delete_".$this->id."\" type=\"submit\" value=\"Delete\" onclick=\"return sure();\"></td>";
			
			return $r;
		}
		
		return;
	}

	public static function handle($requestdata) {
		
		foreach($requestdata as $name => $value) 
			//delete?
			if(strncmp($name, "delete", 6)==0 && $value=="Delete") {
				
				$id=substr($name, 7, strlen($name)-7);											

				echo "<div class=\"message\">Deleting user with id $id</div><br/>".PHP_EOL;
				$user = User::retrieve($id);								
				$user->delete();				
				return $user;
			} else 
			//update?		
			if(strncmp($name, "update", 6)==0 && $value=="Update") {
				
				$id=substr($name, 7, strlen($name)-7);
				echo "<div class=\"message\">Updating user with id $id</div><br/>".PHP_EOL;
				$user = User::retrieve($id);

				$name=$requestdata['name_'.$id];
				$password=$requestdata['password_'.$id];
				$level = array_search($requestdata['nlevel_'.$id], SiteMap::$UserLevels);
				$email=$requestdata['email_'.$id];		
				
				$user->update($name, $password, $level, $email);
				
				return $user;
			} else 
			//add
			if($name=="add" && $value=="Add") {

				$name=$requestdata['name'];
				$password=$requestdata['password'];
				$level = array_search($requestdata['nlevel'], SiteMap::$UserLevels);
				$email=$requestdata['email'];
		
				echo "<div class=\"message\">Adding user $name</div><br/>".PHP_EOL;

				$user = User::create($name, $password, $level, $email);
				
				return $user;
			} else
			//register
			if($name=="register" && $value=="Register") {
				$name=$requestdata['name'];
				$password=$requestdata['password'];
				$level = array_search(0, SiteMap::$UserLevels);
				$email=$requestdata['email'];
				
				$user = User::create($name, $password, $level, $email);
				
				return $user;
			}
			
			return false;
		
	} 
	
	public static function getIds() {
		$sql = "SELECT id FROM users ORDER BY id;";
		
		global $db;
		
		$stmt = $db->prepare($sql);
		
		try {
			$stmt->execute();
		} catch(PDOException $e) {
			error_log ("Error: ".$e->getMessage());
			print "Error getting user!";
		}
		
		$count = $stmt->rowCount();
				
		if($count == 0)
			return false;
		
		$r=array();
		
		while($result = $stmt->fetch(PDO::FETCH_ASSOC))
			$r[]=$result['id'];
		
		return $r;
		
	}
	
	public static function form($level=false, $submitid="submit", $submitvalue="Submit") {
		
		$r="";
		
		$r.="<table>".PHP_EOL;
		$r.="<tr><th>Name</th><td><input id= \"name\" type=\"text\" name=\"name\" required/></td></tr>".PHP_EOL;
		$r.="<tr><th>E-Mail</th><td><input id=\"email\" type=\"email\" name=\"email\" size=\"30\" required /></td></tr>".PHP_EOL;
		$r.="<tr><th>Password</th><td><input id=\"password\" type=\"password\" name=\"password\" required /></td></tr>".PHP_EOL;
		$r.="<tr><th>Verify Password</th><td><input id=\"password2\" type=\"password\" name=\"password2\" required /></td></tr>".PHP_EOL;
		if($level) {
		$r.="<tr><th>Level</th><td><select id=\"nlevel\" name=\"nlevel\">".PHP_EOL;
			foreach(SiteMap::$UserLevels as $level => $nlevel)
				$r.="<option value=\"".$nlevel."\">".$level."</option>".PHP_EOL;
			$r.="</select></td></tr>".PHP_EOL;			
		}
		$r.="</table>".PHP_EOL;	
		$r.="<br/>".PHP_EOL;
		$r.="<input id=\"".$submitid."\" name=\"".$submitid."\" type=\"submit\" value=\"".$submitvalue."\"><br/>".PHP_EOL;
		
		return $r;
	}

	
	
}