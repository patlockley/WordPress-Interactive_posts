<?PHP

	add_action('init', 'interactive_posts_wordpress_custom_page_type_create');

	function interactive_posts_wordpress_custom_page_type_create() 
	{
	  $labels = array(
		'name' => _x('Interactive Posts', 'post type general name'),
		'singular_name' => _x('Interactive Post', 'post type singular name'),
		'add_new' => _x('Add New', 'interactive_post'),
		'add_item' => __('Add New '),
		'edit_item' => __('Edit an Interactive Post'),
		'item' => __('New Interactive Post'),
		'view_item' => __('View Interactive Posts'),
		'search_items' => __('Search Interactive Posts'),
		'not_found' =>  __('No Interactive Posts found'),
		'not_found_in_trash' => __(	'No Interactive Posts found in Trash'), 
		'parent_item_colon' => '',
		'menu_name' => 'Interactive Posts'

	  );
	  
	  $args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true, 
		'show_in_menu' => true, 
		'query_var' => true,
		'menu_item' => plugin_dir_url(__FILE__) . "logo.jpg",
		'_edit_link' => 'post.php?post=%d',	
		'rewrite' => true,
		'capability_type' => 'post',
		'has_archive' => true, 
		'hierarchical' => false,
		'menu_position' => null,
		'rewrite' => true,
		'description' => 'A Collection of terms which which to search for resources with',
		'supports' => array('title')
	  ); 
	  register_post_type('interactive_posts',$args);

	}

?>