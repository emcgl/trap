<?php

include_once dirname(__FILE__)."/../includes/database.php";
include_once dirname(__FILE__)."/user.class.php";
include_once "uploadexception.class.php";

//Transcriptomic Age Calculation Job
class Job {
	
	private $id;
	private $uid;
	private $name;

	private $expressiontype;
	private static $expressiontypes = array(
		"illumina"=>"Illumina",
		"gene"=>"Gene"
	);	
	
	private $expressionfile;
	
	private $predictortype;
	private static $predictortypes = array(
		"general"=>"General Predictor",
		"scaled"=>"Scaled Predictor"
	);
	
	private $agefile;
	
	private $status;

	public static $statuses = array(
			"defining",
			"defined",
			"scheduled",
			"running",
			"halt",
			"finished",
			"halted"
	);
		
	private function Job($id, $uid, $name, $expressiontype, $expresionfile, $predictortype, $agefile, $status) {
		$this->id=$id;
		$this->uid=$uid;
		$this->name=$name;		
		$this->expressiontype=$expressiontype;
		$this->expressionfile=$expresionfile;		
		$this->predictortype=$predictortype;		
		$this->agefile=$agefile;
		$this->status=$status;		
	}
	
	public static function define($uid, $name, $expressiontype, $expressionfile, $predictortype, $agefile) {
				
		//Have all job param's?
		if(!isset($uid) || !isset($name) || !isset($expressiontype) || !isset($expressionfile) || !isset($predictortype) || !isset($agefile))
			throw new InvalidArgumentException("Provide all parameters for new Job instance!");
	
		//Valid expressiontype?
		if(!array_key_exists($expressiontype, Job::$expressiontypes)) {
			$valids="[";
			foreach(array_values(Job::$expressiontypes) as $val) {$valids.=" ".$val;}
			$valids.="]";
			throw new Exception("Expressiontype must be one of:".$valids);
		}
		
		//Valid predictortype?
		if(!array_key_exists($predictortype, Job::$predictortypes)) {
			$valids="[";
			foreach(array_values(Job::$predictortypes) as $val) {$valids.=" ".$val;}
			$valids.="]";
			throw new Exception("Predictortype must be in :".$valids);
		}

		//Predictor type 'scaled' requires age file (general not!)
		if($predictortype=="scaled") {
			if($agefile!="")
				throw Exception("Predictor type scaled doesn't require age file! '$agefile' is ignored!");
		} elseif($predictortype=="general") {
			if($agefile=="")
				throw Exception("Predictor type general requires age file!");
		}

		global $db;
	
		//Is there a job with same uid and name?
		$count=0;
		$sql = "SELECT * FROM jobs WHERE uid=:uid AND name=:name";
		try {
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':uid', $uid);
			$stmt->bindParam(':name', $name);
			$stmt->execute();
			$count = $stmt->rowCount();
		} catch(PDOException $e) {
			error_log ("Error: ".$e->getMessage());
			print "Error checking duplicate uid AND name!";
		}
		if($count > 0)
			throw new InvalidArgumentException("Job with similar name already exists for this user! Please choose other name!");

		//File check stuff should become here!
		$status="defining";
		
		//Put job in database
		$sql = "INSERT INTO jobs (uid, name, expressionfile, expressiontype, predictortype, agefile, status) VALUES (:uid, :name, :expressionfile, :expressiontype, :predictortype, :agefile, :status)";
		try {
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':uid', $uid);
			$stmt->bindParam(':name', $name);
			$stmt->bindParam(':expressionfile', $expressionfile);
			$stmt->bindParam(':expressiontype', $expressiontype);
			$stmt->bindParam(':predictortype', $predictortype);
			$stmt->bindParam(':agefile', $agefile);
			$stmt->bindParam(':status', $status);
			$stmt->execute();
		} catch(PDOException $e) {
			error_log ("Error: ".$e->getMessage());
			die("Error inserting new job!");
		}
		
		$id = $db->lastInsertId();
		
		//Creating job data folder
		if(!mkdir(dirname(__FILE__)."/../data/users/$uid/$id")) 
			die("<div class=\"error\">Error creating job data folder! Please inform administrator!</div>");
			

		//From files from $_FILES!
		if(!isset($_FILES) || !isset($_FILES['expressionfile']) || !isset($_FILES['agefile']))
			throw new Exception("Can't find uploaded files!");
		
		$tmpexpressionfile=$_FILES['expressionfile']['tmp_name'];
		$tmpagefile=$_FILES['agefile']['tmp_name'];
		
		if(!isset($expressionfile) || $expressionfile=="")
			throw new Exception("Can't locate expressionfile!");		
								
		if(!move_uploaded_file($tmpexpressionfile, dirname(__FILE__)."/../data/users/$uid/$id/expression.file"))
			throw new Exception("Can't move expressionfile!");
		
		//If needed, check age file and le move
		if($predictortype=="general") {
			if(!isset($tmpagefile) || $tmpagefile=="")
				throw new Exception("Can't locate agefile!");
			if(!move_uploaded_file($tmpagefile, dirname(__FILE__)."/../data/users/$uid/$id/age.file"))
				throw new Exception("Can't move agefile!");
		} else {
			$agefile="";
		}
		
		//Set status to defined!
		$status="defined";

		//Put job in database
		$sql = "UPDATE jobs SET status=:status WHERE id=:id";
		try {
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':status', $status);
			$stmt->bindParam(':id', $id);
			$stmt->execute();
		} catch(PDOException $e) {
			error_log ("Error: ".$e->getMessage());
			die("Error updating status of new job!");
		}
		
		return new Job($id, $uid, $name, $expressiontype, $expressionfile, $predictortype, $agefile, $status);

	}
	
	public static function retrieve($id) {
	
		global $db;				
		
		$sql = "SELECT id, uid, name, expressionfile, expressiontype, predictortype, agefile, status FROM jobs WHERE id=:id";
	
		$stmt = $db->prepare($sql);
	
		try {
			$stmt->bindParam(':id', $id);
			$stmt->execute();
		} catch(PDOException $e) {
			error_log ("Error: ".$e->getMessage());
			print "Error getting job!";
		}
	
		$count = $stmt->rowCount();
	
		if($count > 1)
			throw new Exception("Something wrong with jobs database! Found more than one jobs with same id.");
	
		if($count == 0)
			return false;
	
		$result = $stmt->fetch(PDO::FETCH_ASSOC);
		
		return new Job($result['id'], 
					   $result['uid'], 
				       $result['name'], 
				       $result['expressiontype'], 
				       $result['expressionfile'], 
				       $result['predictortype'], 
				       $result['agefile'], 
				       $result['status']
		); 	
	}

	public static function getIds($user) {
	
		global $db;
		
		$sql = "SELECT id FROM jobs WHERE uid=:uid ORDER BY id;";
		
		$stmt = $db->prepare($sql);
	
		try {
			$stmt->bindParam(':uid', $user->getId());
			$stmt->execute();
		} catch(PDOException $e) {
			error_log ("Error: ".$e->getMessage());
			print "Error getting job!";
		}
	
		$count = $stmt->rowCount();
	
		if($count == 0)
			return false;
	
		$r=array();
	
		while($result = $stmt->fetch(PDO::FETCH_ASSOC))
			$r[]=$result['id'];
	
		return $r;
	
	}
	public static function getAllIds($user) {
	
		global $db;
	
		if(!$user->isAdmin())
			throw new Exception("Can't provide all ids! User not admin!");
	
		$sql = "SELECT id FROM jobs ORDER BY id;";
	
		$stmt = $db->prepare($sql);
	
		try {
			if($user->isAdmin()) {
				$stmt->bindParam(':uid', $user->getId());
			}
			$stmt->execute();
		} catch(PDOException $e) {
			error_log ("Error: ".$e->getMessage());
			print "Error getting job!";
		}
	
		$count = $stmt->rowCount();
	
		if($count == 0)
			return false;
	
		$r=array();
	
		while($result = $stmt->fetch(PDO::FETCH_ASSOC))
			$r[]=$result['id'];
	
		return $r;
	
	}
	
	public function isOwnedBy($user) {
		
		if($user->hasId($this->uid)) return true;
		else return false;
		
	}

	
	public function halt() {
		if($this->status!="scheduled" && $this->status!="running")
			throw new Exception("Can't halt job that is neither scheduled or running!");
		
		if(isset($_SESSION['user']))
			$user=$_SESSION['user'];
		else
			throw new Exception("Can't halt without logged in!");

		if(!($user->isAdmin() || $user->hasId($this->uid)))
			throw new Exception("You don't have permission to halt this job!");

		global $db;
		
		//Set status to halt!
		$status="halt";
		
		$sql = "UPDATE jobs SET status=:status WHERE id=:id";
		try {
			$stmt = $db->prepare($sql);
			$stmt->bindParam(':status', $status);
			$stmt->bindParam(':id', $this->id);
			$stmt->execute();
			$this->status="halt";
		} catch( PDOException $e) {
			error_log ("Error: ".$e->getMessage());
			print "Error halting job!";
			die();
		}
		
		return true;
		
	}

	public function delete() {
	
		if(isset($_SESSION['user']))
			$user=$_SESSION['user'];
		else
			throw new Exception("Can't delete without logged in!");
		
		if(!($user->isAdmin() || $user->hasId($this->uid)))
			throw new Exception("You don't have permission to delete this job!");
		
		//2do Mutex!! (depends on sheduling / background system - unable to delete defined is safe for now!)
		if($this->status=="defined")
			throw new Exception("Can't delete defined jobs! Please halt or wait for finish!");			
		if($this->status=="scheduled")
			throw new Exception("Can't delete scheduled jobs! Please halt or wait for finish!");				
		if($this->status=="running")
			throw new Exception("Can't delete running jobs! Please halt or wait for finish!");
		if($this->status=="halt")
			throw new Exception("The job is in process of halting, can't delete yet! Please wait for your job to be halted!");
				
		if($this->isFinished() || $this->isHalted()) {
		
			global $db;	
				
			$sql = "DELETE FROM jobs WHERE id=:id";
			try {
				$stmt = $db->prepare($sql);
				$stmt->bindParam(':id', $this->id);
				$stmt->execute();
			} catch( PDOException $e) {
				error_log ("Error: ".$e->getMessage());
				print "Error deleting job!";
				die();
			}
	
			//Moving job folder to trash inside user folder(maybe delete later)
			if(!file_exists(dirname(__FILE__)."/../data/trash/$this->uid"))
				mkdir(dirname(__FILE__)."/../data/trash/$this->uid");
			rename(dirname(__FILE__)."/../data/users/$this->uid/$this->id", dirname(__FILE__)."/../data/trash/$this->uid/$this->id");
		} else {
			throw new Exception("Only can delete finished or halted jobs!");
		}
		
		return true;
	
	}

	
	public function isScheduled() {
		if($this->status=="scheduled")
			return true;
		return false;
	}
	
	public function isRunning() {
		if($this->status=="running")
			return true;
		return false;
	}
	
	public function isFinished() {
		if($this->status=="finished")
			return true;
		return false;
	}
	public function isHalted() {
		if($this->status=="halted")
			return true;
		return false;
	}
	
	/* Table Header Row */
	public static function tableHeader($edit=false, $admin=false) {
	
		$r="";
	
		$r.="<tr class=\"jobheader\"><th>ID</th>";
		
		if($admin)		
			$r.="<th>User</th>";
			
		$r.="<th>Job Name</th><th>Expression Type</th><th>Expression File</th><th>Predictor Type</th><th>Age File</th><th>Status</th>";
	
		if($edit) {
			$r.="<th>Halt</th>";				
			$r.="<th>Results</th>";
			$r.="<th>Delete</th>";
		}else{
			$r.="<th>Results</th>";
		}
			
		$r.="</tr>".PHP_EOL;
			
		return $r;
	
	}
	
	/* Table Row */
	public function tableData($edit=false, $admin=false) {
			
		$r="";
	
		//Make table row with user data
		if(!$edit) {
				
			$r.="<tr>";
			$r.="<td>".$this->id."</td>";
			
			if($admin) {
				$username = User::retrieveName($this->uid);
				$r.="<td>".$username."</td>";
			}			
			
			$r.="<td>".$this->name."</td>";
			$r.="<td>".Job::$expressiontypes[$this->expressiontype]."</td>";
			$r.="<td>".$this->expressionfile."</td>";
			$r.="<td>".Job::$predictortypes[$this->predictortype]."</td>";
			$r.="<td>".$this->agefile."</td>";
			$r.="<td ".($this->status=="running" ? "class=\"runningstatus\"" : "class=\"staticstatus\"").">".$this->status."</td>";
			$r.="<td><input id=\"results_".$this->id."\" name=\"results_".$this->id."\" type=\"submit\" value=\"Results\" ".($this->isFinished() ? "" : "disabled")."></td>";
			$r.="</tr>".PHP_EOL;
				
			return $r;
		} else {
			
			$r= "<tr>";
			$r.="<td>".$this->id."</td>";
			
			if($admin) {
				$username = User::retrieveName($this->uid);
				$r.="<td>".$username."</td>";
			}
			
			$r.="<td>".$this->name."</td>";
			$r.="<td>".Job::$expressiontypes[$this->expressiontype]."</td>";
			$r.="<td>".$this->expressionfile."</td>";
			$r.="<td>".Job::$predictortypes[$this->predictortype]."</td>";
			$r.="<td>".$this->agefile."</td>";
			$r.="<td ".($this->status=="running" ? "class=\"runningstatus\"" : "class=\"staticstatus\"").">".$this->status."</td>";		
						
			$r.="<td><input id=\"halt_".$this->id."\" name=\"halt_".$this->id."\" type=\"submit\" value=\"Halt\" ".($this->isRunning() ? "" : "disabled")."></td>";
			$r.="<td><input id=\"results_".$this->id."\" name=\"results_".$this->id."\" type=\"submit\" value=\"Results\" ".($this->isFinished() ? "" : "disabled")."></td>";
			$r.="<td><input id=\"delete_".$this->id."\" name=\"delete_".$this->id."\" type=\"submit\" value=\"Delete\" onclick=\"return sure('Are you sure? Job will be deleted!');\"".( ($this->isFinished() || $this->isHalted()) ? "" : "disabled")."></td>";			
				
			return $r;
		}
	
		return;
	}

	public static function handle($requestdata) {
	
		foreach($requestdata as $name => $value)
			if(strncmp($name, "delete", 6)==0 && $value=="Delete") {
	
				$id=substr($name, 7, strlen($name)-7);
	
				echo "<div class=\"message\">Deleting job with id $id..</div><br/>".PHP_EOL;
				$job = Job::retrieve($id);
				
				if(isset($_SESSION['user']))					
					$user=$_SESSION['user'];
				else
					throw new Exception("Need to login for this action!");
				
				if(($job->isFinished() || $job->isHalted()) && ($user->isAdmin() || $user->hasId($job->uid))) {				
					$job->delete();
					return $job;
				} else {
					throw new Exception("Deleting this job is not allowed or not possible!");
				}
			
			} elseif(strncmp($name, "halt", 4)==0 && $value=="Halt") {
	
					$id=substr($name, 5, strlen($name)-5);
					echo "<div class=\"message\">Halting job with id $id</div><br/>".PHP_EOL;
					
					$job = Job::retrieve($id);
					
					if(isset($_SESSION['user']))
						$user=$_SESSION['user'];
					else 
						throw new Exception("Need to login for this action!");
	
					if($job->isRunning() && ($user->isAdmin() || $user->hasId($job->uid )) ) {
						$job->halt();
						return $job;
					} else {
						throw new Exception("Halting this job is not allowed or possible!"); 
					}								
			} elseif(strncmp($name, "results", 7)==0 && $value=="Results") { 

					$user=null;
				
					if(isset($_SESSION['user']))
						$user=$_SESSION['user'];
					else
						throw new Exception("Can't download new job, first login!");
				
					$id=substr($name, 8, strlen($name)-8);		
					
					$job = Job::retrieve($id);
					
					echo "<div class=\"message\">Download results of job '".$job->name."' with id ".$job->id."</div><br/>".PHP_EOL;

					echo "<br/><div><a href=\"/index.php?download&uid=$job->uid&jid=$job->id\">If download won't start automatically, click here!</a></div><br/>".PHP_EOL;
					
					echo Page::link("main", $user);
					
					echo "<script type=\"text/javascript\">".PHP_EOL;
					echo "window.location = \"/index.php?download=job&uid=$job->uid&jid=$job->id\";".PHP_EOL;
					echo "</script>";
										
					return $job;
			} elseif($name=="add" && $value=="Submit") {			
				
					if(isset($_SESSION['user']))
						$user=$_SESSION['user'];
					else 
						throw new Exception("Can't define new job, first login!");
					
					$uid = $user->getId();
					
					if(!$user->hasAccess("user"))
						throw new Exception("Not enough rights to define job!");

					if($_FILES['expressionfile']['error'] != UPLOAD_ERR_OK)
						throw new UploadException($_FILES['expressionfile']['error']);
					
					$name=$requestdata['name'];
					$expressiontype=$requestdata['expressiontype'];
					
					if(!isset($_FILES['expressionfile']))
						throw new Exception("Expression file not provided!");
					else
						$expressionfile=$_FILES['expressionfile']['name'];
					
					$predictortype=$requestdata['predictortype'];
					
					if($predictortype == "general") {
						
						if($_FILES['agefile']['error'] != UPLOAD_ERR_OK) 
							throw new UploadException($_FILES['agefile']['error']);

						$agefile=$_FILES['agefile']['name'];
					} else {
						$agefile="";
					}
					
					$job = Job::define($uid, $name, $expressiontype, $expressionfile, $predictortype, $agefile);
					
					echo "<div class=\"message\">Job defined!</div><br/>".PHP_EOL;
					
					return $job;
				} 
				
			return false;
	}
	
	public static function form() {
	
		$r="";
	
		$r.="<table>".PHP_EOL;
		$r.="<tr><th>Name</th><td><input id= \"name\" type=\"text\" name=\"name\" required/></td></tr>".PHP_EOL;
		
		$r.="<tr><th>Expression Type</th><td><select id=\"expressiontype\" name=\"expressiontype\" required>".PHP_EOL;
		foreach(Job::$expressiontypes as $type => $name)
			$r.="<option value=\"".$type."\">".$name."</option>".PHP_EOL;
		$r.="</select></td></tr>".PHP_EOL;
		$r.="<tr><th>Expression File</th><td><input type=\"file\" id=\"expressionfile\" name=\"expressionfile\" required></td></tr>".PHP_EOL;
		$r.="<tr><th>Predictor Type</th><td><select id=\"predictortype\" name=\"predictortype\" onchange=\"needAgeFile();\" required>".PHP_EOL;
		foreach(Job::$predictortypes as $type => $name)
			$r.="<option value=\"".$type."\">".$name."</option>".PHP_EOL;
		$r.="</select></td></tr>".PHP_EOL;
		$r.="<tr><th>Age File</th><td><input type=\"file\" id=\"agefile\" name=\"agefile\" required></td></tr>".PHP_EOL;
		$r.="</table>".PHP_EOL;
		$r.="<br/>".PHP_EOL;
		$r.="<input id=\"add\" name=\"add\" type=\"submit\" value=\"Submit\"><br/>".PHP_EOL;
	
		return $r;
	}
	
	public function retrieveOutput() {
		
		$filename = "output.".($this->predictortype == 'general' ? 'general' : 'scaled'  ).".".$this->id.".txt";
		
		error_log($filename);
		
		$file = dirname(__FILE__)."/../data/users/$this->uid/$this->id/$filename";

		error_log($file);
		
		if(!file_exists($file)) { die ("Output file does not exist!"); }
			
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="'.basename($file).'"');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		header('Content-Length: '.filesize($file));
		readfile($file);
		exit;
	
	}
	
	
}
			
