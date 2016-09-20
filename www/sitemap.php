<?php

class SiteMap {
	
	public static $DefaultLevel = "public";
	
	public static $UserLevels = array(
		"public" => 0,
		"unvalidated" => 5,
		"user" => 50,
		"admin" => 100
	);
	
	public static $StartPage = "welcome";
	
	public static $Pages = array(
		//Default
		array(
			'name'  => "welcome",
			'title' => "Welcome",
			'level' => "public"				
		),
		array(
			'name'  => "register",
			'title' => "Register User",
			'level' => "public"
		),	
		array(
			'name'  => "validate",
			'title' => "Validate",
			'level' => "public"
		),				
		array(
			'name'  => "login",
			'title' => "Login",
			'level' => "public"
		),
		array(
			'name'  => "main",
			'title' => "Main Page",
			'level' => "user"
		),
		array(
			'name'  => "tutorial",
			'title' => "Tutorial",
			'level' => "user"
		),
		array(
			'name'  => "citation",
			'title' => "Citing Software",
			'level' => "user"
		),
		//Logout
		array(
			'name' =>  "logout",
			'title' => "Logout",				
			'level' => "user"
		),
		//Administrate Users		
		array(
			'name' =>  "useredit",
			'title' => "User Edit",
			'level' => "user"
			),
		array(
			'name' =>  "useradmin",
			'title' => "User Administration",				
			'level' => "admin"
		),
		array(
			'name'  => "adduser",
			'title' => "Add User",
			'level' => "admin"
		),
		//Administrate Jobs
		array(
			'name'  => "jobadmin",
			'title' => "Job Administration",
			'level' => "user"
		),
		array(
			'name'  => "definejob",
			'title' => "Define New Job",
			'level' => "user"
		)
	);

}

?>