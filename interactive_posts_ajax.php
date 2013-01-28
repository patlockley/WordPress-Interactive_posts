<?PHP
	
add_action('wp_ajax_interactive_posts', 'interactive_posts_ajax');
add_action('wp_ajax_nopriv_interactive_posts', 'interactive_posts_ajax');

function interactive_posts_ajax()
{
	
	if(wp_verify_nonce($_REQUEST['nonce'], 'interactive_posts_nonce')){

		$post = get_post($_REQUEST['post']);
		
		if($post->post_type=="interactive_posts"){
		
			include "interactions/" . $_REQUEST['type'] . "/index.php";
			$func = $_REQUEST['type'] . "_ajax";

			$func($_REQUEST['value']);
			
		}
		
	}
	
	die();
	
}

?>