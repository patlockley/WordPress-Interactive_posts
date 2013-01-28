<?PHP

	function single_question_ajax(){
	
		global $wpdb;
		
		$table_name = $wpdb->prefix . "interactive_posts_elements";
		
		$wpdb->query( 
			$wpdb->prepare( 
				"
						select data FROM " . $table_name . "
						WHERE post_id = %d and
						data like '%s'
				",
						$_REQUEST['post'], 
						"%_element_%"
				)
		);
		
		$data = $wpdb->last_result;
		
		$answer = unserialize($data[0]->data);
		
		if($answer[1]==$_REQUEST['value']){
		
			$feedback = unserialize($data[1]->data);
			echo $feedback[1];
		
		}else{
		
			$feedback = unserialize($data[2]->data);
			echo $feedback[1];
			
		}
		
	}

	function single_question_before_question(){
	
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

	function single_question_post_handle($post_id){
	
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

	function single_question_name(){
	
		return "Single question";
	
	}
	
	function single_question_display(){
	
		global $post, $wpdb;
		
		$table_name = $wpdb->prefix . "interactive_posts_elements";
	
		$q_data = $wpdb->get_results("select * from " . $table_name . " where post_id=" . $post->ID . " and data like '%before_interaction%'", OBJECT);
		
		if(count($q_data)!==0){
		
			$output = unserialize($q_data[0]->data);
		
			echo $output[1];
		
		}
	
		$data = $wpdb->get_results("select * from " . $table_name . " where post_id=" . $post->ID . " and data like '%_option%'", OBJECT);
	
		foreach($data as $entry){
		
			$entry = unserialize($entry->data);
	
			echo "<p>";
	
			echo "<input type='textbox' id='answer' />";
			echo "<a onclick='interactive_posts_check(" . $post->ID . ",\"single_question\",\"" . $entry[0] . "\")' >Check answer</a>";
	
			echo "</p>";
		
		}
		
		echo "<div id='single_question_feedback'></div>";
	
	}
	
	function single_question_setup(){
	
		single_question_before_question();
		$func = "single_question_html";
		echo $func("interactive_posts_element_1");
		
	}
	
	function single_question_edit($data){
	
		single_question_before_question();
		
		while($set = array_shift($data)){
			
			$interaction = unserialize($set->data);
				
			if(strpos($interaction[0],"_option")!==FALSE){
				
				single_question_html_build_option($interaction[0], $interaction[1]);
					
			}else{
			
				if(strpos($interaction[0],"_correct")!==FALSE){
				
					single_question_html_build_feedback_correct($interaction[0], $interaction[1]);
					
				}else if(strpos($interaction[0],"_incorrect")!==FALSE){
				
					single_question_html_build_feedback_incorrect($interaction[0], $interaction[1]);
							
				}
				
			}
			
		}
		
	}
	
	function single_question_html($id, $value = NULL){
	
		?><div><h2 onclick="interactive_posts_toggle(this)"><strong>-</strong> Option</h2><div><p>Enter the answer</p><input type="text" name="<?PHP echo $id; ?>_option" /></div>
		<div><p>Enter the feedback if correct</p><textarea id="<?PHP echo $id; ?>_correct" name="<?PHP echo $id; ?>_feedback_correct" rows="10" cols="100"></textarea></p></div>
		<div><p>Enter the feedback if incorrect</p><textarea id="<?PHP echo $id; ?>_incorrect" name="<?PHP echo $id; ?>_feedback_incorrect" rows="10" cols="100"></textarea></p></div></div><?
	
	}
	
	function single_question_html_build_option($id, $value = NULL){
	
		?><div><p>Enter the answer</p><input type="text" name="<?PHP echo $id; ?>" value="<?PHP echo $value; ?>" /></div><?PHP
		
	}
	
	function single_question_html_build_feedback_correct($id, $value = NULL){
	
		?><div><p>Enter the feedback if correct</p><textarea id="<?PHP echo $id; ?>_correct" name="<?PHP echo $id; ?>_correct" rows="10" cols="100"><?PHP echo $value; ?></textarea></div><?PHP
		
	}

	function single_question_html_build_feedback_incorrect($id, $value = NULL){
	
		?><p>Enter the feedback if incorrect</p><textarea id="<?PHP echo $id; ?>_incorrect" name="<?PHP echo $id; ?>_incorrect" rows="10" cols="100"><?PHP echo $value; ?></textarea></div><?PHP
	
	}

?>