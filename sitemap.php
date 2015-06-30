<?php

class SiteMap {
	
	public static $DefaultLevel = "normal";
	
	public static $UserLevels = array(
		"disabled" => 0,
		"normal" => 1,
		"login" => 5,
		"admin" => 100
	);
	
	public static $StartPage = "welcome";
	
	public static $Pages = array(
		//Default page
		array(
			'name' =>  "welcome",
			'level' => "normal"				
		),
		//Login page
		array(
			'name' => "login",
			'level' => "normal"
		),
		//Main menu
		array(
			'name' => "main",
			'level' => "login"
		),	
		//Logout
		array(
			'name' => "logout",
			'level' => "login"
		),
				
		array(
			'name' =>  "useradmin",
			'level' => "admin"
		)
	);

}

?>