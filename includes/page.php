<?php

/*
 * Common page functionality
 */

include_once dirname(__FILE__)."/../sitemap.php";
include_once dirname(__FILE__)."/../classes/user.class.php";

class Page {
	
	//Return link if user has access according to sitemap
	static function link($page, $user="") {
		
		$key = array_search( $page, array_column(SiteMap::$Pages, 'name') );

		if(is_bool($key) && $key==false) {
			throw new Exception("Page not available!");
		}
		
		$pagedata = SiteMap::$Pages[$key];
		
		if(!isset($user) || $user=="" || $user->hasAccess($pagedata['level'])) {
			return "<a class=\"menu\" href=\"/index.php?page=$page\">".$pagedata['title']."</a>";
		} else
			return "";
	}
}

?>