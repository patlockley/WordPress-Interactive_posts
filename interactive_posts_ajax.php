<?PHP
	
add_action('wp_ajax_interactive_posts', 'interactive_posts_ajax');
add_action('wp_ajax_nopriv_interactive_posts', 'interactive_posts_ajax');

function interactive_posts_track($post_id, $answer, $user){

	global $wpdb;
	
	$table_name = $wpdb->prefix . "interactive_posts_results";

	$wpdb->query( 
		$wpdb->prepare( 
			"
					INSERT INTO " . $table_name . "(post_id, user_id, data, submitted)VALUES(%d,'%s','%s', %d)
			",
					$post_id, $user, $answer, time() 
			)
	);

}

function interactive_posts_ajax()
{
	
	if(wp_verify_nonce($_REQUEST['nonce'], 'interactive_posts_nonce')){

		$post = get_post($_REQUEST['post']);
		
		if($post->post_type=="interactive_posts"){
		
			include "interactions/" . $_REQUEST['type'] . "/index.php";
			$func = $_REQUEST['type'] . "_ajax";
			
			if(get_post_meta($post->ID, "first_answer",true)=="on"){

				if(get_post_meta($post->ID, "track",true)=="on"){
				
					global $wpdb;
	
					$table_name = $wpdb->prefix . "interactive_posts_results";

					$wp_user = wp_get_current_user();

					$data = $wpdb->query("select data from " . $table_name . " where post_id = " . $post->ID . " and user_id = " . $wp_user->ID); 
			
					if(is_array($data)&&count($data)==0){
					
						$inner_func = $_REQUEST['type'] . "_track";
						
						$answer = $inner_func($_REQUEST['value']);
						
						interactive_posts_track($post->ID, $answer, $wp_user->ID);
						
					}else if($data==0){
					
						$inner_func = $_REQUEST['type'] . "_track";
						
						$answer = $inner_func($_REQUEST['value']);
			
						interactive_posts_track($post->ID, $answer, $wp_user->ID);
		
					}
							
				}
				
			}else{	
			
				if(get_post_meta($post->ID, "track",true)=="on"){
			
					$wp_user = wp_get_current_user();
					
					$inner_func = $_REQUEST['type'] . "_track";
						
					$answer = $inner_func($_REQUEST['value']);
			
					interactive_posts_track($post->ID, $answer, $wp_user->ID);
					
				}
			
			}
			
			$func($_REQUEST['value']);
			
		}
		
	}
	
	die();
	
}

?>