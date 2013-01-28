<?PHP

add_action("admin_menu", "interactive_posts_wordpress_editor_make");
add_action('admin_enqueue_scripts', 'interactive_posts_editor_javascript' );

function interactive_posts_editor_javascript($hook) {

	global $post;
	
	if($post->post_type=="interactive_posts"){

		if( 'post.php' != $hook )
			return;

		wp_enqueue_script( 'interactive_posts_tinyMCE', plugins_url('/js/tinymce/jscripts/tiny_mce/tiny_mce.js', __FILE__), array('jquery'));
		wp_enqueue_script( 'interactive_posts_tinyMCE_start', plugins_url('/js/tinymce_start.js', __FILE__), array('jquery'));
	
		$type = get_post_meta($post->ID, "interactive_post_type");
		
		if(count($type)==0)
			return;
		
		$type = $type[0];
				
		wp_enqueue_script( 'interactive_posts_editor_' . $type, plugins_url('/interactions/' . $type . '/js/admin/index.js', __FILE__), array('jquery'));
		wp_register_style( 'interactive_posts_css_' . $type, plugins_url('/interactions/' . $type . '/css/admin/index.css', __FILE__) );
		wp_enqueue_style( 'interactive_posts_css_' . $type );
	
	}
	
}

function interactive_posts_wordpress_editor_make()
{

	add_meta_box("interactive_postswordpress_editor", "Interactive Posts Editor", "interactive_posts_wordpress_editor", "interactive_posts");
	
}

function interactive_posts_wordpress_editor(){

	global $post;
	
	if($_REQUEST['post_type']=="interactive_posts"){
	
		$interactions = opendir(dirname(__FILE__) . "/interactions");
		
		echo "<p>When creating a new Interactive Post, please choose a type of interaction</p><select name='interactive_post_type'>";
		
		while($file = readdir($interactions)){
		
			if($file!="."&&$file!=".."){
			
				include "interactions/" . $file . "/index.php";
			
				echo "<option value='" . $file . "'>" .  call_user_func($file . "_name") . "</option>";
			
			}
		
		}
		
		echo "</select>";
	
	}else{
	
		$type = get_post_meta($post->ID, "interactive_post_type");
		$type = $type[0];
		
		global $wpdb;
		
		$table_name = $wpdb->prefix . "interactive_posts_elements";
	
		$data = $wpdb->get_results("select * from " . $table_name . " where post_id=" . $post->ID . " order by id ASC", OBJECT);
		
		echo "<div>";
		
		if(count($data)===0){
		
			include dirname(__FILE__) . "/interactions/" . $type . "/index.php";
			$func = $type . "_setup";
			
			echo "<p>This interaction is a " . str_replace("_"," ",$type) . "</p>";
			
			$func();
		
		}else{
		
			include dirname(__FILE__) . "/interactions/" . $type . "/index.php";
			
			$func = $type . "_edit";
		
			echo "<p>This interaction is a " . str_replace("_"," ",$type) . "</p>";	

			$func($data);
		
		}
	
	}
	
}

?>