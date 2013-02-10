<?PHP

	function single_question_track($answer){
	
		return $answer;
	
	}

	function single_question_ajax(){
	
		global $wpdb;
		
		$table_name = $wpdb->prefix . "interactive_posts_elements";
		
		$wpdb->query( 
			$wpdb->prepare( 
				"
						select data FROM " . $table_name . "
						WHERE post_id = %d and
						keyname like '%s'
				",
						$_REQUEST['post'], 
						"%_option"
				)
		);	
		
		$data = $wpdb->last_result;
		
		if($data[0]->data==$_REQUEST['value']){
		
			$data = $wpdb->query( 
			$wpdb->prepare( 
				"
						select data FROM " . $table_name . "
						WHERE post_id = %d and
						keyname like '%s'
				",
						$_REQUEST['post'], 
						"%\_correct"
				)
			);
			
			$data = $wpdb->last_result;
			
			echo $data[0]->data;
		
		}else{
		
			$data = $wpdb->query( 
			$wpdb->prepare( 
				"
						select data FROM " . $table_name . "
						WHERE post_id = %d and
						keyname like '%s'
				",
						$_REQUEST['post'], 
						"%\_incorrect"
				)
			);
			
			$data = $wpdb->last_result;
			
			echo $data[0]->data;
			
		}
		
	}

	function single_question_before_question(){
	
		global $post, $wpdb;
		
		$table_name = $wpdb->prefix . "interactive_posts_elements";
	
		echo "<textarea name='before_interaction'>";
		
		$data = $wpdb->get_results("select * from " . $table_name . " where post_id=" . $post->ID . " and keyname = 'before_interaction'", OBJECT);
		
		if(count($data)!==0){
		
			echo $data[0]->data;
		
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
						INSERT INTO " . $table_name . "(post_id, keyname,  data)VALUES(%d,'%s','%s')
				",
						$post_id, "before_interaction", $_POST['before_interaction']
				)
		);
		
		foreach($_POST as $key => $value){
		
			if(strpos($key, "interactive_posts")!==FALSE){
			
				$wpdb->query( 
					$wpdb->prepare( 
						"
								INSERT INTO " . $table_name . "(post_id, keyname, data)VALUES(%d,'%s','%s')
						",
								$post_id, $key, $value
						)
				);
				
				$counter++;
			
			}
		
		}
		
	}

	function single_question_name(){
	
		return "Single question";
	
	}
	
	function single_question_display(){
	
		global $post, $wpdb;
		
		$table_name = $wpdb->prefix . "interactive_posts_elements";
	
		$q_data = $wpdb->get_results("select * from " . $table_name . " where post_id=" . $post->ID . " and keyname = 'before_interaction'", OBJECT);
		
		if(count($q_data)!==0){
		
			echo $q_data[0]->data;
		
		}
	
		$data = $wpdb->get_results("select * from " . $table_name . " where post_id=" . $post->ID . " and keyname like '%_option%'", OBJECT);
	
		foreach($data as $entry){
	
			echo "<p>";
	
			echo "<input type='textbox' id='answer' />";
			echo "<a onclick='interactive_posts_check(" . $post->ID . ",\"single_question\",\"" . $entry->keyname . "\")' >Check answer</a>";
	
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
				
			if(strpos($set->keyname,"_option")!==FALSE){
				
				single_question_html_build_option($set->keyname, $set->data);
					
			}else{
			
				if(strpos($set->keyname,"_correct")!==FALSE){
				
					single_question_html_build_feedback_correct($set->keyname, $set->data);
					
				}else if(strpos($set->keyname,"_incorrect")!==FALSE){
				
					single_question_html_build_feedback_incorrect($set->keyname, $set->data);
							
				}
				
			}
			
		}
		
	}
	
	function single_question_html($id, $value = NULL){
	
		?><div><div><p>Enter the answer</p><input style="width:100%" type="text" name="<?PHP echo $id; ?>_option" /></div>
		<div><p>Enter the feedback if correct</p><textarea style="width:100%" id="<?PHP echo $id; ?>_correct" name="<?PHP echo $id; ?>_feedback_correct" rows="10" cols="100"></textarea></p></div>
		<div><p>Enter the feedback if incorrect</p><textarea style="width:100%" id="<?PHP echo $id; ?>_incorrect" name="<?PHP echo $id; ?>_feedback_incorrect" rows="10" cols="100"></textarea></p></div></div><?
	
	}
	
	function single_question_html_build_option($id, $value = NULL){
	
		?><div><p>Enter the answer</p><input type="text" style="width:100%" name="<?PHP echo $id; ?>" value="<?PHP echo $value; ?>" /></div><?PHP
		
	}
	
	function single_question_html_build_feedback_correct($id, $value = NULL){
	
		?><div><p>Enter the feedback if correct</p><textarea style="width:100%" id="<?PHP echo $id; ?>_correct" name="<?PHP echo $id; ?>_correct" rows="10" cols="100"><?PHP echo $value; ?></textarea></div><?PHP
		
	}

	function single_question_html_build_feedback_incorrect($id, $value = NULL){
	
		?><p>Enter the feedback if incorrect</p><textarea style="width:100%" id="<?PHP echo $id; ?>_incorrect" name="<?PHP echo $id; ?>_incorrect" rows="10" cols="100"><?PHP echo $value; ?></textarea></div><?PHP
	
	}

?>