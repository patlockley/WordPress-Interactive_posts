function interactive_posts_change(post_id, type){
	
	jQuery(document).ready(function($) {

		var data = {
			action: "interactive_posts",
			type: type,
			post:post_id,
			value: document.getElementById(type).value,
			nonce: interactive_posts.answerNonce
		};
		
		jQuery.post(interactive_posts.ajaxurl, data, function(response) {
			document.getElementById(type + "_feedback").innerHTML = response;
		});
	});
	

}