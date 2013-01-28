<?PHP

	function fill_in_the_blanks_ajax(){
	
		global $wpdb;
		
		$table_name = $wpdb->prefix . "interactive_posts_elements";
		
		$wpdb->query( 
			$wpdb->prepare( 
				"
						select data FROM " . $table_name . "
						WHERE post_id = %d
				",
						$_REQUEST['post']
				)
		);
		
		$data = $wpdb->last_result;
		
		$replace = unserialize($data[2]->data);
		
		$words = explode(" ", strip_tags($replace[1]));
		
		$text = unserialize($data[1]->data);
		
		$text = $text[1];
		
		$score = 0;
		
		$max = count($words);
		
		while($word = array_shift($words)){
		
			$compare = array_shift($_REQUEST['value']);
			
			if($word!=$compare){
				
				$text = str_replace($word, "<em>" . $word . "</em>", $text);
				
				$score--;
			
			}else{
			
				$score++;
			
				$text = str_replace($word, "<strong>" . $word . "</strong>", $text);
			
			}
		
		}
		
		echo "<p>" . $text . "</p>";
		
		echo "<p>You scored " . $score . " out of " . $max . "</p>";
		
		if($score == $max){
		
			$feedback = unserialize($data[3]->data);
			echo "<p>" . $feedback[1] . "</p>";
		
		}else{
		
			$feedback = unserialize($data[4]->data);
			echo "<p>" . $feedback[1] . "</p>";
			
		}
		
	}

	function fill_in_the_blanks_before_question(){
	
		global $post, $wpdb;
		
		$table_name = $wpdb->prefix . "interactive_posts_elements";
	
		echo "<textarea name='before_interaction'>";
		
		$data = $wpdb->get_results("select * from " . $table_name . " where post_id=" . $post->ID . " and data like '%before_interaction%'", OBJECT);
		
		if(count($data)!==0){
		
			$output = unserialize($data[0]->data);
		
			echo $output[1];
		
		}
		
		echo "</textarea>";
	
	}

	function fill_in_the_blanks_post_handle($post_id){
	
		global $wpdb;

		$table_name = $wpdb->prefix . "interactive_posts_elements";
		
		$wpdb->query( 
			$wpdb->prepare( 
				"
						DELETE FROM " . $table_name . "
						WHERE post_id = %d
				",
						$post_id 
				)
		);
		
		$counter = 0;
		
		$wpdb->query( 
			$wpdb->prepare( 
				"
						INSERT INTO " . $table_name . "(post_id, data)VALUES(%d,'%s')
				",
						$post_id, serialize(array("before_interaction", $_POST['before_interaction'])) 
				)
		);
		
		foreach($_POST as $key => $value){
		
			if(strpos($key, "interactive_posts")!==FALSE){
			
				$wpdb->query( 
					$wpdb->prepare( 
						"
								INSERT INTO " . $table_name . "(post_id, data)VALUES(%d,'%s')
						",
								$post_id, serialize(array($key, $value)) 
						)
				);
				
				$counter++;
			
			}
		
		}
		
		if($_POST['interactive_post_type_add']=="on"){
		
			$wpdb->query( 
				$wpdb->prepare( 
					"
							INSERT INTO " . $table_name . "(post_id, data)VALUES(%d,'%s')
					",
							$post_id, serialize(array('interactive_posts_element_' . $counter . '_option', '')) 
					)
			);
			
			$wpdb->query( 
				$wpdb->prepare( 
					"
							INSERT INTO " . $table_name . "(post_id, data)VALUES(%d,'%s')
					",
							$post_id, serialize(array('interactive_posts_element_' . $counter . '_feedback', '')) 
					)
			);
			
		}
	
	}

	function fill_in_the_blanks_name(){
	
		return "Fill in the blanks";
	
	}
	
	function fill_in_the_blanks_display(){
	
		global $post, $wpdb;
		
		$table_name = $wpdb->prefix . "interactive_posts_elements";
	
		$q_data = $wpdb->get_results("select * from " . $table_name . " where post_id=" . $post->ID . " and data like '%before_interaction%'", OBJECT);
		
		$blanks = new StdClass;
	
		$data = $wpdb->get_results("select * from " . $table_name . " where post_id=" . $post->ID, OBJECT);
	
		foreach($data as $entry){
		
			$entry = unserialize($entry->data);
			
			$blanks->{$entry[0]} = $entry[1];
		
		}
		
		$replace = explode(" ", strip_tags($blanks->interactive_posts_text_remove));
		
		$text = strip_tags($blanks->interactive_posts_text);
		
		$counter = 0;
		
		echo "<p>";
	
		while($word = array_shift($replace)){
		
			$text = preg_replace("$\b" . trim($word) . "\b$", "<input type='text' id='interactive_post_" . $counter++ . "' />", $text);
		
		}
		
		echo $text;
		
		echo "</p>";
		
		echo "<a onclick='interactive_posts_check(" . $post->ID . ",\"fill_in_the_blanks\",\"" . $entry[0] . "\"," . $counter . ")' >Check answer</a>";
	
		echo "<div id='fill_in_the_blanks_feedback'></div>";
	
	}
	
	function fill_in_the_blanks_setup(){
	
		fill_in_the_blanks_before_question();
		$func = "fill_in_the_blanks_html";
		echo $func("interactive_posts_text");
		
	}
	
	function fill_in_the_blanks_edit($data){
	
		fill_in_the_blanks_before_question();
		
		while($set = array_shift($data)){
			
			$interaction = unserialize($set->data);
				
			if(strpos($interaction[0],"_text_remove")!==FALSE){
				
				fill_in_the_blanks_html_build_text_remove($interaction[0], $interaction[1]);
					
			}else if(strpos($interaction[0],"_text_feedback_correct")!==FALSE){
				
				fill_in_the_blanks_html_build_feedback_correct($interaction[0], $interaction[1]);
				
			}else if(strpos($interaction[0],"_text_feedback_incorrect")!==FALSE){
				
				fill_in_the_blanks_html_build_feedback_incorrect($interaction[0], $interaction[1]);
				
			}else if(strpos($interaction[0],"_text")!==FALSE){
			
				fill_in_the_blanks_html_build_text($interaction[0], $interaction[1]);
			
			}
			
		}
		
	}
	
	function fill_in_the_blanks_html($id, $value = NULL){
	
		?><div><h2 onclick="interactive_posts_toggle(this)"><strong>-</strong> Option</h2><div><p>Enter the text you wish to use</p><textarea name="<?PHP echo $id; ?>"></textarea>
		<p>Enter the words </p><textarea name="<?PHP echo $id; ?>_remove"></textarea></div>
		<div><p>Enter the feedback if correct</p><textarea id="<?PHP echo $id; ?>_correct" name="<?PHP echo $id; ?>_feedback_correct" rows="10" cols="100"></textarea></p></div>
		<div><p>Enter the feedback if incorrect</p><textarea id="<?PHP echo $id; ?>_incorrect" name="<?PHP echo $id; ?>_feedback_incorrect" rows="10" cols="100"></textarea></p></div></div><?
	
	}
	
	function fill_in_the_blanks_html_build_text($id, $value = NULL){
	
		?><div><p>Enter the text you wish to use</p><textarea name="<?PHP echo $id; ?>"><?PHP echo $value; ?></textarea><?PHP
		
	}
	
	
	function fill_in_the_blanks_html_build_text_remove($id, $value = NULL){
	
		?><p>Enter the words </p><textarea name="<?PHP echo $id; ?>"><?PHP echo $value; ?></textarea></div><?PHP
		
	}
	
	function fill_in_the_blanks_html_build_feedback_correct($id, $value = NULL){
	
		?><div><p>Enter the feedback if correct</p><textarea id="<?PHP echo $id; ?>" name="<?PHP echo $id; ?>" rows="10" cols="100"><?PHP echo $value; ?></textarea><?PHP
		
	}

	function fill_in_the_blanks_html_build_feedback_incorrect($id, $value = NULL){	
		
		?><p>Enter the feedback if incorrect</p><textarea id="<?PHP echo $id; ?>" name="<?PHP echo $id; ?>" rows="10" cols="100"><?PHP echo $value; ?></textarea></div><?PHP
	
	}

?>