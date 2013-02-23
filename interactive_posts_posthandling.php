<?PHP

add_action("save_post", "interactive_posts_wordpress_create");

function interactive_posts_wordpress_create($post_id)
{

	$data = get_post($post_id);
	
	if($data->post_type=="interactive_posts"){

		if(count($_POST)!==0){
			
			if(wp_verify_nonce($_POST['interactive_posts_edit'],'interactive_posts_edit') ){
		
				update_post_meta($post_id, "logged_in", $_REQUEST["logged_in"]);
				update_post_meta($post_id, "track",$_REQUEST["track"]);
				update_post_meta($post_id, "first_answer",$_REQUEST["first_answer"]);
			
				if(count(get_post_meta($post_id, "interactive_post_type"))===0){
				
					update_post_meta($post_id, "interactive_post_type", $_POST["interactive_post_type"]);
					
				}else{
				
					$type = get_post_meta($post_id, "interactive_post_type");
					$type = $type[0];
					
					include dirname(__FILE__) . "/interactions/" . $type . "/index.php";
					
					$func = $type . "_post_handle";
					
					$func($post_id);
					
				}
			
			}else{
			
				print 'Sorry, your nonce did not verify.';
				exit;
			
			}
		
		}
	
	}

}

?>