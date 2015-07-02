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
		//Default page
		array(
			'name'  => "welcome",
			'level' => "public"				
		),
		//Default page
		array(
			'name'  => "register",
			'level' => "public"
		),	
		//Default page
		array(
			'name'  => "validate",
			'level' => "public"
		),				
		//Login page
		array(
			'name'  => "login",
			'level' => "public"
		),
		//Main menu
		array(
			'name'  => "main",
			'level' => "user"
		),	
		//Logout
		array(
			'name' => "logout",
			'level' => "user"
		),
		//Administrate Users		
		array(
			'name' =>  "useradmin",
			'level' => "admin"
		)
	);

}

?>