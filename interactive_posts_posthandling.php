<?PHP

add_action("save_post", "interactive_posts_wordpress_create");
add_action("before_delete_post", "interactive_posts_wordpress_delete"); 

function interactive_posts_wordpress_create($post_id)
{

	$data = get_post($post_id);
	
	if($data->post_type=="interactive_posts"){

		if(count($_POST)!==0){
		
			if(count(get_post_meta($post_id, "interactive_post_type"))===0){
			
				update_post_meta($post_id, "interactive_post_type", $_POST["interactive_post_type"]);
				
			}else{
			
				$type = get_post_meta($post_id, "interactive_post_type");
				$type = $type[0];
				
				include dirname(__FILE__) . "/interactions/" . $type . "/index.php";
				
				$func = $type . "_post_handle";
				
				$func($post_id);
				
			}
		
		}
	
	}

}


function interactive_posts_wordpress_delete($post_id){

	$data = get_post($post_id);
	
	if($data->post_type=="xerte_online"){

		$wp_dir = wp_upload_dir();
	
		if(file_exists($wp_dir['basedir'] . "/xerte-online/" . $post_id . "/" )){
		
			$dir = opendir($wp_dir['basedir'] . "/xerte-online/" . $post_id . "/");
			while($file = readdir($dir)){
			
				if($file!="."&&$file!=".."){
				
					if(!is_dir($wp_dir['basedir'] . "/xerte-online/" . $post_id . "/" . $file)){
				
						unlink($wp_dir['basedir'] . "/xerte-online/" . $post_id . "/" . $file);
					
					}
				
				}
			
			}
			
			
			$dir = opendir($wp_dir['basedir'] . "/xerte-online/" . $post_id . "/media/");

			while($file = readdir($dir)){
			
				if($file!="."&&$file!=".."){
				
					if(!is_dir($wp_dir['basedir'] . "/xerte-online/" . $post_id . "/media/" . $file)){
				
						unlink($wp_dir['basedir'] . "/xerte-online/" . $post_id . "/media/" . $file);
					
					}
				
				}
			
			}
			
			rmdir($wp_dir['basedir'] . "/xerte-online/" . $post_id . "/media/");
			rmdir($wp_dir['basedir'] . "/xerte-online/" . $post_id . "/");
			
		}
	
	}

}

?>