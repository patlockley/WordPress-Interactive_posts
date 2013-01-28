<?PHP

	/*
	Plugin Name: Interactive Posts 
	Description: Interactive Posts plugin to allow for interaction in a post
	Version: 0.01
	Author: pgogy
	Plugin URI: http://www.pgogy.com/code/xerte-online
	Author URI: http://www.pgogy.com
	License: GPL
	*/

	require_once("interactive_posts_editor.php");
	require_once("interactive_posts_ajax.php");
	require_once("interactive_posts_display.php");
	require_once("interactive_posts_custompost.php");
	require_once("interactive_posts_posthandling.php");
	
	register_activation_hook( __FILE__, 'interactive_posts_activate' );
	
	register_deactivation_hook( __FILE__ , 'xerteonline_deactivate');
	
	function interactive_posts_activate(){
	
		global $interactive_posts_db_version, $wpdb;
		
		$interactive_posts_db_version = "0.1";
		
		$table_name = $wpdb->prefix . "interactive_posts_elements";
			  
		$sql = "CREATE TABLE " . $table_name . " (
			  id bigint(20) NOT NULL AUTO_INCREMENT,
			  post_id  bigint(20),
			  data varchar(1000),
			  UNIQUE KEY id(id)
			);";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		   
		add_option("interactive_posts_db_version", $interactive_posts_db_version);
		
		// DATABASE ADDED
	
	}

?>