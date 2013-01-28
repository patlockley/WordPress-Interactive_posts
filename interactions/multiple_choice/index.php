<?PHP

	function multiple_choice_ajax(){
	
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
						"%" . str_replace("_option","_feedback",$_REQUEST['value']) . "%"
				)
		);
		
		$data = $wpdb->last_result;
		
		if(count($data)!==0){
		
			$output = unserialize($data[0]->data);
		
			print_r($output[1]);
		
		}
		
	}

	function multiple_choice_before_question(){
	
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

	function multiple_choice_post_handle($post_id){
	
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

	function multiple_choice_name(){
	
		return "Multiple choice";
	
	}
	
	function multiple_choice_display(){
	
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
			
			echo "<label>" . $entry[1] . "</label>";
	
			echo "<input type='checkbox' onchange='interactive_posts_change(" . $post->ID . ",\"multiple_choice\",\"" . $entry[0] . "\")' >";
	
			echo "</p>";
		
		}
		
		echo "<div id='multiple_choice_feedback'></div>";
	
	}
	
	function multiple_choice_setup(){
	
		multiple_choice_before_question();
		$func = "multiple_choice_html";
		echo $func("interactive_posts_element_1") . $func("interactive_posts_element_2") . $func("interactive_posts_element_3");
		?><label>Add new option</label><input type="checkbox" name="interactive_post_type_add"  /><?PHP	
		
	}
	
	function multiple_choice_edit($data){
	
		multiple_choice_before_question();
		
		while($set = array_shift($data)){
			
			$interaction = unserialize($set->data);
				
			if(strpos($interaction[0],"_option")!==FALSE){
				
				multiple_choice_html_build_option($interaction[0], $interaction[1]);
					
			}else{
				
				multiple_choice_html_build_feedback($interaction[0], $interaction[1]);
				
			}
			
		}
		
		?><label>Add new option</label><input type="checkbox" name="interactive_post_type_add"  /><?PHP	
	
	}
	
	function multiple_choice_html($id, $value = NULL){
	
		?><div><h2 onclick="interactive_posts_toggle(this)"><strong>-</strong> Option</h2><div><p>Enter an option</p><input type="text" name="<?PHP echo $id; ?>_option" /></div><div><p>Enter the feedback</p><textarea id="<?PHP echo $id; ?>" name="<?PHP echo $id; ?>_feedback" rows="10" cols="100"></textarea></p></div></div><?
	
	}
	
	function multiple_choice_html_build_option($id, $value = NULL){
	
		?><div><h2 onclick="interactive_posts_toggle(this)"><strong><?PHP
		
		if($value!==NULL){
		
			echo "+";
			
		}else{
		
			echo "-";
		
		}
		
		?></strong> Option <?PHP
		
		if($value!==NULL){

			echo " : " . $value; 
			
		}
		
		?></h2><div <?PHP
		
		if($value!==NULL){
		
			?> 	class="interactive_hidden" <?PHP
			
		}
		
		?> ><p>Enter an option</p><input type="text" name="<?PHP echo $id; ?>" value="<?PHP echo $value; ?>" /></div><?PHP
		
	}
	
	function multiple_choice_html_build_feedback($id, $value = NULL){
	
		?><div <?PHP
		
		if($value!==""){
		
			?> 	class="interactive_hidden" <?PHP
			
		}
		
		?> ><p>Enter the feedback</p><textarea id="<?PHP echo $id; ?>" name="<?PHP echo $id; ?>" rows="10" cols="100"><?PHP echo $value; ?></textarea></div></div><?PHP
	
	}

?>