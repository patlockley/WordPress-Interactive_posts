function interactive_posts_check(post_id, type, value, number){
	
	words = new Array();
	
	for(x=0;x<number;x++){
	
		words.push(document.getElementById("interactive_post_" + x).value);
	
	}	
	
	jQuery(document).ready(function($) {
	
		var data = {
			action: "interactive_posts",
			type: type,
			post:post_id,
			value: words,
			nonce: interactive_posts.answerNonce
		};
		
		jQuery.post(interactive_posts.ajaxurl, data, function(response) {
			document.getElementById(type + "_feedback").innerHTML = response;
		});
	});
	

}