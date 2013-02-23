<?PHP

	function drop_down_choice_track($answer){
	
		return $answer;
		
	}

	function drop_down_choice_ajax(){
	
		global $wpdb;
		
		$table_name = $wpdb->prefix . "interactive_posts_elements";
		
		$wpdb->query( 
			$wpdb->prepare( 
				"
						select keyname FROM " . $table_name . "
						WHERE post_id = %d and
						data = '%s'
				",
						$_REQUEST['post'], 
						$_REQUEST['value']
				)
		);
		
		$data = $wpdb->last_result;
		
		if(isset($data[0]->keyname)){
		
			$wpdb->query( 
				$wpdb->prepare( 
					"
							select data FROM " . $table_name . "
							WHERE post_id = %d and
							keyname = '%s'
					",
							$_REQUEST['post'], 
							str_replace("option","feedback",$data[0]->keyname)
					)
			);	
			
			$data = $wpdb->last_result;
		
			if(isset($data[0]->data)){
			
				echo $data[0]->data;
			
			}
		
		}
		
	}

	function drop_down_choice_before_question(){
	
		global $post, $wpdb;
		
		$table_name = $wpdb->prefix . "interactive_posts_elements";
	
		echo "<textarea name='before_interaction'>";
		
		$data = $wpdb->get_results("select * from " . $table_name . " where post_id=" . $post->ID . " and keyname = 'before_interaction'", OBJECT);
		
		if(isset($data[0]->data)){
		
			echo $data[0]->data;
		
		}
		
		echo "</textarea>";
	
	}

	function drop_down_choice_post_handle($post_id){
	
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
						INSERT INTO " . $table_name . "(post_id, keyname, data)VALUES(%d,'%s','%s')
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
		
		if($_POST['interactive_post_type_add']=="on"){
		
			$wpdb->query( 
				$wpdb->prepare( 
					"
							INSERT INTO " . $table_name . "(post_id, keyname, data)VALUES(%d,'%s','%s')
					",
							$post_id, 'interactive_posts_element_' . $counter . '_option', '' 
					)
			);
			
			$wpdb->query( 
				$wpdb->prepare( 
					"
							INSERT INTO " . $table_name . "(post_id, keyname, data)VALUES(%d,'%s','%s')
					",
							$post_id, 'interactive_posts_element_' . $counter . '_feedback', '' 
					)
			);
			
		}
	
	}

	function drop_down_choice_name(){
	
		return "Drop down choice";
	
	}
	
	function drop_down_choice_display(){
	
		global $post, $wpdb;
		
		$table_name = $wpdb->prefix . "interactive_posts_elements";
	
		$q_data = $wpdb->get_results("select * from " . $table_name . " where post_id=" . $post->ID . " and keyname = 'before_interaction'", OBJECT);
		
		if(count($q_data)!==0){
		
			$output = $q_data[0]->data;
		
			echo $output;
		
		}
	
		$data = $wpdb->get_results("select * from " . $table_name . " where post_id=" . $post->ID . " and keyname like '%_option%'", OBJECT);
		
		echo "<select onchange='interactive_posts_change(" . $post->ID . ",\"drop_down_choice\")' id='drop_down_choice'>";
	
		echo "<option>Choose an answer</option>";
	
		foreach($data as $entry){
	
			$entry = $entry->data;
	
			if(trim($entry)!=""){
	
				echo "<option value='" . $entry . "'>";
			
				echo $entry;
			
				echo "</option>";
				
			}
		
		}
		
		echo "</select>";
		
		echo "<div id='drop_down_choice_feedback'></div>";
	
	}
	
	function drop_down_choice_setup(){
	
		drop_down_choice_before_question();
		$func = "drop_down_choice_html";
		echo $func("interactive_posts_element_1") . $func("interactive_posts_element_2") . $func("interactive_posts_element_3");
		?><label>Add new option</label><input type="checkbox" name="interactive_post_type_add"  /><?PHP	
		
	}
	
	function drop_down_choice_edit($data){
	
		drop_down_choice_before_question();
		
		while($set = array_shift($data)){
				
			if(strpos($set->keyname,"_option")!==FALSE){
				
				drop_down_choice_html_build_option($set->keyname, $set->data);
					
			}else{
				
				drop_down_choice_html_build_feedback($set->keyname, $set->data);
				drop_down_choice_remove_option($set->keyname);	
			}
			
		}
		
		?><label>Add new option</label><input type="checkbox" name="interactive_post_type_add"  /><?PHP	
	
	}
	
	function drop_down_choice_remove_option($id){
	
		?><p><a onclick="javascript:jQuery(this.parentNode.previousSibling).remove();jQuery(this).remove()">Remove this option</a></p><?PHP
	
	}
	
	function drop_down_choice_html($id, $value = NULL){
	
		?><div><h2 onclick="interactive_posts_toggle(this)"><strong>-</strong> Option</h2><div><p>Enter an option</p><input type="text" name="<?PHP echo $id; ?>_option" /></div><div><p>Enter the feedback</p><textarea id="<?PHP echo $id; ?>" name="<?PHP echo $id; ?>_feedback" rows="10" cols="100"></textarea></p></div></div><?
	
	}
	
	function drop_down_choice_html_build_option($id, $value = NULL){
	
		?><div class="option_holder"><h2 onclick="interactive_posts_toggle(this)"><strong><?PHP
		
		if($value!==""){
		
			echo "+";
			
		}else{
		
			echo "-";
		
		}
		
		?></strong> Option <?PHP
		
		if($value!==""){

			echo " : " . $value; 
			
		}
		
		?></h2><div <?PHP
		
		if($value!==""){
		
			?> 	class="interactive_hidden" <?PHP
			
		}
		
		?> ><p>Enter an option</p><input type="text" name="<?PHP echo $id; ?>" value="<?PHP echo $value; ?>" /></div><?PHP
		
	}
	
	function drop_down_choice_html_build_feedback($id, $value = NULL){
	
		?><div <?PHP
		
		if($value!==""){
		
			?> 	class="interactive_hidden" <?PHP
			
		}
		
		?> ><p>Enter the feedback</p><textarea style="width:100%" id="<?PHP echo $id; ?>" name="<?PHP echo $id; ?>"><?PHP echo $value; ?></textarea></div></div><?PHP
	
	}

?>