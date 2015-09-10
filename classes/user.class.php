<?php

include_once "job.class.php";
include_once dirname(__FILE__)."/../includes/database.php";
include_once dirname(__FILE__)."/../sitemap.php";
include_once dirname(__FILE__)."/../config.php";

class User
{		
	private $id;
	private $username;
	private $name;
	private $email;
	private $jobtitle;
	private $affiliation;
	private $nlevel; //This contains the numeric level (see SiteMap for names)
		
	private function User($id, $username, $name, $email, $jobtitle, $affiliation, $nlevel) {
		$this->id=$id;
		$this->username=$username;
		$this->name=$name;
		$this->email=$email;
		$this->jobtitle=$jobtitle;
		$this->affiliation=$affiliation;
		$this->nlevel=$nlevel;
	}
	                  
	public static function create($username, $password, $name, $email, $jobtitle, $affiliation, $level) {
			
		//Have all user param's?
		if(!isset($username) || !isset($password) || !isset($name) || !isset($email) || !isset($jobtitle) || !isset($affiliation) || !isset($level)  ) 
			throw new InvalidArgumentException("Please provide username, password, name, email, jobtitle, affiliation and level");

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
		$sql = "SELECT * FROM users WHERE username=:username OR email=:email";
		try {
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':username', $username);
			$stmt->bindParam(':email', $email);
			$stmt->execute();
			$count = $stmt->rowCount();
		} catch(PDOException $e) {
			error_log ("Error: ".$e->getMessage());
			print "Error checking duplicate username or email!";		
		} 	
		if($count > 0) 
			throw new InvalidArgumentException("Username or email adress already in use! Please choose other!");
		
		//2do: Check Email!
		
		$id=-1;
		
		//Add user and return instance
		$sql = "INSERT INTO users (username, password, name, email, jobtitle, affiliation, nlevel) VALUES (:username, :password, :name, :email, :jobtitle, :affiliation, :nlevel)";
		try {
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':username', $username);
			$stmt->bindParam(':password', password_hash($password, PASSWORD_DEFAULT));
			$stmt->bindParam(':name', $name);
			$stmt->bindParam(':email', $email);
			$stmt->bindParam(':jobtitle', $jobtitle);
			$stmt->bindParam(':affiliation', $affiliation);
			$stmt->bindParam(':nlevel', $nlevel);
			
			$stmt->execute();						
		} catch(PDOException $e) {
			error_log ("Error: ".$e->getMessage());
			print "Error inserting new user!";
			die();
		}
		
		$id = $db->lastInsertId();
	
		global $datafolder;
	
		//Creating user data folder
		if(!mkdir("$datafolder/users/$id")) {
			error_log("User $id may is in db but doesn't have data folder!");
			die("Error creating user data folder!");
		}
				
		$user = new User($id, $username, $name,  $email, $jobtitle, $affiliation, $nlevel);			
		
		return $user;
	}

	public static function login($username, $password) {
	
		global $db;
	
		$sql = "SELECT id, username, password, name, email, jobtitle, affiliation, nlevel FROM users WHERE username=:username";
		try {
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':username', $username);
			$stmt->execute();
	
			$count = $stmt->rowCount();
	
			if($count > 1)
				throw new Exception("Something wrong with user database! Found more than one accounts with name.");
				
			if($count == 0)
				return false;
				
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			
			if($result['nlevel']<50) 
				throw new Exception("User doesn't have correct access level (is the user verified?)");
			
			if( password_verify($password, $result['password']) ) {
				return new User( $result['id'],
						$result['username'],
						$result['name'],
						$result['email'],
						$result['jobtitle'],
						$result['affiliation'],
						$result['nlevel']
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
	
		$sql = "SELECT id, username, name, email, jobtitle, affiliation, nlevel FROM users WHERE id=:id";
	
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
			throw new Exception("Could not find user with id $id.");
		
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		
		return new User( $result['id'],
				$result['username'],
				$result['name'],
				$result['email'],
				$result['jobtitle'],
				$result['affiliation'],
				$result['nlevel']
		);		
		
	}

	public static function retrieveName($id) {
		$user=User::retrieve($id);
		return $user->username;
	}
	          
	public function update($username, $password, $name, $email, $jobtitle, $affiliation, $level) {
		
		$update_username=false;
		$update_password=false;
		$update_name=false; 
		$update_email=false;
		$update_jobtitle=false; 
		$update_affiliation=false;
		$update_nlevel=false;
		
		if( isset($username) && $username!="" && $username != $this->username ) $update_username=true;
		if( isset($password) && $password!="" ) $update_password=true;
		if( isset($name) && $name!="" && $name != $this->name ) $update_name=true;
		if( isset($email) && $email!="" && $this->email != $email) $update_email=true;
		if( isset($jobtitle) && $jobtitle!="" && $this->jobtitle != $jobtitle ) $update_jobtitle=true;
		if( isset($affiliation) && $affiliation!="" && $this->affiliation != $affiliation ) $update_affiliation=true;
		if( isset($level) && isset(SiteMap::$UserLevels[$level]) &&  $this->nlevel != SiteMap::$UserLevels[$level]) $update_nlevel=true;    
		
		if($update_username==false && $update_password==false && $update_name==false && $update_email==false && $update_jobtitle==false && $update_affiliation==false && $update_nlevel==false) {
			throw new Exception("Nothing to update!");
		}
		
		$sql = "UPDATE users SET";
		$comma=0;
		
		if($update_username) {
			$comma++;
			$sql.=" username=:username";
		}
		if($update_password) {
			if($comma>0) $sql.=",";$comma++;
			$sql.=" password=:password";			
		}
		if($update_name) {
		if($comma>0) $sql.=",";$comma++;
			$sql.=" name=:name";
		}
		if($update_email) {
			if($comma>0) $sql.=",";$comma++;
			$sql.=" email=:email";
		}
		if($update_jobtitle) {
			if($comma>0) $sql.=",";$comma++;
			$sql.=" jobtitle=:jobtitle";
		}
		if($update_affiliation) {
			if($comma>0) $sql.=",";$comma++;
			$sql.=" affiliation=:affiliation";
		}
		if($update_nlevel) {
			if($comma>0) $sql.=",";$comma++;
			$sql.=" nlevel=:nlevel";
		}

		$sql.=" WHERE id=:id";
		
		global $db;		
		
		try {
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':id', $this->id);
			if($update_username) $stmt->bindParam(':username', $username);
			if($update_password) $stmt->bindParam(':password', password_hash($password, PASSWORD_DEFAULT));
			if($update_name) $stmt->bindParam(':name', $name);
			if($update_email)$stmt->bindParam(':email', $email);
			if($update_jobtitle)$stmt->bindParam(':jobtitle', $jobtitle);
			if($update_affiliation)$stmt->bindParam(':affiliation', $affiliation);
			if($update_nlevel) $stmt->bindParam(':nlevel', SiteMap::$UserLevels[$level]);
					
			$stmt->execute();
		} catch(PDOException $e) {
			error_log ("Error: ".$e->getMessage());
			print "Error updating user!";
			die();
		}
		
		return;
		
	}
	
	public function delete() {
		
		$jobids = Job::getIds($this);
		
		if(isset($jobids) && $jobids!=false) {

			echo "<div class=\"warning\">Deleting user's jobs..</div><br/>".PHP_EOL;
			
			//Delete jobs
			try {
				foreach($jobids as $id)
					$job=Job::retrieve($id);
					$job->delete();
			} catch(Exception $e) {
				echo "<div class=\"error\">Error deleting account! Can't delete all user's jobs: ".$e->getMessage()."</div><br/>".PHP_EOL;
				return; 
			}
		}
		
		global $db;
		
		//Delete user
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
	
		global $datafolder;
	
		if(rmdir("$datafolder/users/$this->id"))
			return true;
		else 
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
	
	public function hasId($id) {
		if($id==$this->id)
			return true;
		return false;
	}
	
	public function getId() {
		return $this->id;
	}
	
	public function isAdmin() {
		return $this->hasAccess("admin");
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
		$code=md5("thisisasecret".$this->username.$this->email);
		return $code;
	}
	
	/*
	 * Sets user's new level and returns true on valid code
	 */
	public static function confirmValidationCode($email, $code) {

		global $db;
		
		$sql = "SELECT id, username, nlevel FROM users WHERE email=:email";
		
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
			
			$vcode = md5("thisisasecret".$result['username'].$email);			
						
			if($code == $vcode) {								
				
				if(!$user = User::retrieve($result['id'] ))
					throw new Exception("Can't find registered (unvalidated) user!"); 			
				
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
		
	/* Table Header Row */
	public static function tableHeader($edit=false) {
		
		$r="";
		
		$r.="<tr class=\"userheader\"><th>ID</th><th>Username</th><th>Password</th><th>Name</th><th>EMail</th><th>Job Title</th><th>Affiliation</th><th>Access Level</th>";
		
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
			$r.="<td>".$this->username."</td>";
			$r.="<td>[Secret]</td>";
			$r.="<td>".$this->name."</td>";
			$r.="<td>".$this->email."</td>";
			$r.="<td>".$this->jobtitle."</td>";
			$r.="<td>".$this->affiliation."</td>";
			$level = array_search($this->nlevel, SiteMap::$UserLevels);
			$r.="<td>".$level."</td>";
			
			$r.="</tr>".PHP_EOL;
			
			return $r;
		}
		
		//Make table row with form to adjust user data
		if($edit) {

			//Can't edit current user!
			$loggedin=false;			
			
			if(isset($_SESSION['user']) && $_SESSION['user']->getId() == $this->getId() ) 
				$loggedin=true;
						
			$r= "<tr>";
			$r.="<td>".$this->id."</td>";
			$r.="<td><input id=\"username_".$this->id."\" name=\"username_".$this->id."\" type=\"text\" value=\"".$this->username."\"></td>";
			$r.="<td><input id=\"password_".$this->id."\" name=\"password_".$this->id."\" type=\"password\" value=\"\"></td>";
			$r.="<td><input id=\"name_".$this->id."\" name=\"name_".$this->id."\" type=\"text\" value=\"".$this->name."\"></td>";
			$r.="<td><input id=\"email_".$this->id."\" name=\"email_".$this->id."\" type=\"text\" value=\"".$this->email."\"></td>".PHP_EOL;
			$r.="<td><input id=\"jobtitle_".$this->id."\" name=\"jobtitle_".$this->id."\" type=\"text\" value=\"".$this->jobtitle."\"></td>".PHP_EOL;
			$r.="<td><input id=\"affiliation_".$this->id."\" name=\"affiliation_".$this->id."\" type=\"text\" value=\"".$this->affiliation."\"></td>".PHP_EOL;
			$r.="<td><select id=\"nlevel_".$this->id."\" name=\"nlevel_".$this->id."\">".PHP_EOL;
			foreach(SiteMap::$UserLevels as $level => $nlevel) {
				$r.="<option value=\"".$nlevel."\"".($this->nlevel == $nlevel ?  " selected" : "").">".$level."</option>".PHP_EOL;
			}
			$r.="</select>".PHP_EOL;
			$r.="<td><input id=\"email_".$this->id."\" name=\"email_".$this->id."\" type=\"text\" value=\"".$this->email."\"></td>".PHP_EOL;
			$r.="<td><input id=\"update_".$this->id."\" name=\"update_".$this->id."\" type=\"submit\" value=\"Update\" ".($loggedin ? "disabled" : "")."></td>";
			$r.="<td><input id=\"delete_".$this->id."\" name=\"delete_".$this->id."\" type=\"submit\" value=\"Delete\" onclick=\"return sure('Are you sure? User and jobs will be deleted!');\" ".($loggedin ? "disabled" : "")."></td>";
			
			return $r;
		}
		
		return;
	}

	public static function handle($requestdata) {
		
		foreach($requestdata as $name => $value) 
			//delete?
			if(strncmp($name, "delete", 6)==0 && $value=="Delete") {
				
				$id=substr($name, 7, strlen($name)-7);											

				if(isset($_SESSION['user']) && $_SESSION['user']->getId() == $id)
					throw new Exception("Can't delete current user!");
				
				echo "<div class=\"message\">Deleting user with id $id..</div><br/>".PHP_EOL;
				$user = User::retrieve($id);								
				$user->delete();				
				return $user;
			} else 
			//update?		
			if(strncmp($name, "update", 6)==0 && $value=="Update") {
				
				$id=substr($name, 7, strlen($name)-7);

				if(isset($_SESSION['user']) && $_SESSION['user']->getId() == $id)
					throw new Exception("Can't update current user!");
				
				echo "<div class=\"message\">Updating user with id $id</div><br/>".PHP_EOL;

				$user = User::retrieve($id);
				
				$username=$requestdata['username_'.$id];
				$password=$requestdata['password_'.$id];
				$name=$requestdata['name_'.$id];
				$email=$requestdata['email_'.$id];
				$jobtitle=$requestdata['jobtitle_'.$id];
				$affiliation=$requestdata['affiliation_'.$id];
				$level = array_search($requestdata['nlevel_'.$id], SiteMap::$UserLevels);
				
				$user->update($username, $password, $name, $email, $jobtitle, $affiliation, $level);
				
				return $user;
			} else 
			//add
			if($name=="add" && $value=="Add") {

				$username=$requestdata['username'];
				$password=$requestdata['password'];
				$name=$requestdata['name'];
				$email=$requestdata['email'];
				$jobtitle=$requestdata['jobtitle'];
				$affiliation=$requestdata['affiliation'];
				$level = array_search($requestdata['nlevel'], SiteMap::$UserLevels);
				
				echo "<div class=\"message\">Adding user $name</div><br/>".PHP_EOL;
				try {
					$user = User::create($username, $password, $name, $email, $jobtitle, $affiliation, $level);
					return $user;
				} catch(Exception $e) {
					echo "<div class=\"error\">Problem: ".$e->getMessage()."</div>".PHP_EOL;
				} 

				return false;
				
			} else
			//register
			if($name=="register" && $value=="Register") {
				$username=$requestdata['username'];
				$password=$requestdata['password'];
				$name=$requestdata['name'];
				$email=$requestdata['email'];
				$jobtitle=$requestdata['jobtitle'];
				$affiliation=$requestdata['affiliation'];
				$level = "unvalidated"; //array_search(0, SiteMap::$UserLevels);
								
				$user = User::create($username, $password, $name, $email, $jobtitle, $affiliation, $level);
				
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
		
		$r.="<table class=\"frmtbl\">".PHP_EOL;
		$r.="<tr><th>User Name</th><td><input id=\"username\" type=\"text\" name=\"username\" size=\"30\" required/></td></tr>".PHP_EOL;
		$r.="<tr><th>Password</th><td><input id=\"password\" type=\"password\" name=\"password\" size=\"30\" required /></td></tr>".PHP_EOL;
		$r.="<tr><th>Verify Password</th><td><input id=\"password2\" type=\"password\" name=\"password2\" size=\"30\" required /></td></tr>".PHP_EOL;
		$r.="<tr><th>Name</th><td><input id=\"name\" type=\"text\" name=\"name\" size=\"30\" required/></td></tr>".PHP_EOL;
		$r.="<tr><th>E-Mail</th><td><input id=\"email\" type=\"email\" name=\"email\" size=\"30\" required /></td></tr>".PHP_EOL;
		$r.="<tr><th>Job Title</th><td><input id=\"jobtitle\" type=\"text\" name=\"jobtitle\" size=\"30\" required /></td></tr>".PHP_EOL;
		$r.="<tr><th>Affiliation</th><td><input id=\"affiliation\" type=\"text\" name=\"affiliation\" size=\"30\" required /></td></tr>".PHP_EOL;
		if($level) {
		$r.="<tr><th>Level</th><td><select id=\"nlevel\" name=\"nlevel\">".PHP_EOL;
			foreach(SiteMap::$UserLevels as $level => $nlevel)
				$r.="<option value=\"".$nlevel."\">".$level."</option>".PHP_EOL;
			$r.="</select></td></tr>".PHP_EOL;			
		}
		$r.="</table>".PHP_EOL;	
		$r.="<br/>".PHP_EOL;
		$r.="<input id=\"".$submitid."\" name=\"".$submitid."\" class=\"btn\" type=\"submit\" value=\"".$submitvalue."\"><br/>".PHP_EOL;
		
		return $r;
	}

	
	
}
