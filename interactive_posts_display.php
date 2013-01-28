<?PHP

add_action('wp_enqueue_scripts', 'interactive_posts_display_javascript' );

function interactive_posts_display_javascript($hook) {

	global $post;
	
	if($post->post_type=="interactive_posts"){
	
		$type = get_post_meta($post->ID, "interactive_post_type");
				
		$type = $type[0];
		
		wp_register_style( 'interactive_posts_css_' . $type, plugins_url('/interactions/' . $type . '/css/display/index.css', __FILE__) );
		wp_enqueue_style( 'interactive_posts_css_' . $type );
				
		wp_enqueue_script( 'interactive_posts_editor_' . $type, plugins_url('/interactions/' . $type . '/js/display/index.js', __FILE__), array('jquery'));
		
		// embed the javascript file that makes the AJAX request
		wp_enqueue_script( 'interactive_posts_editor_ajax', plugin_dir_url( __FILE__ ) . 'js/interactive_posts.js', array( 'jquery' ) );
		 
		// declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
		wp_localize_script( 'interactive_posts_editor_ajax', 'interactive_posts', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'answerNonce' => wp_create_nonce( 'interactive_posts_nonce' ) ) );
		
	}
	
}

add_filter("the_content", "interactive_posts_wordpress_display");

function interactive_posts_wordpress_display($content)
{

	global $post;

	if($post->post_type=="interactive_posts"){
	
		$type = get_post_meta($post->ID, "interactive_post_type");
		$type = $type[0];
		
		global $wpdb;
		
		$table_name = $wpdb->prefix . "interactive_posts_elements";
	
		$data = $wpdb->get_results("select * from " . $table_name . " where post_id=" . $post->ID, OBJECT);
		
		if(count($data)===0){
			
			echo "<p>This Interactive Post has no content</p>";
		
		}else{
		
			include "interactions/" . $type . "/index.php";
			$func = $type . "_display";
			$func();
		
		}
	
	}else{
	
		return $content;
	
	}

}

?>