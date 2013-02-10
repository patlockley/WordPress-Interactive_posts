function interactive_posts_toggle(element){

	div = element.parentNode;

	if(element.childNodes[0].innerHTML=="-"){
		
		for(x=1;x<div.childNodes.length;x++){
		
			div.childNodes[x].style.display = "none";
		
		}
		
		element.childNodes[0].innerHTML="+"

	}else{
	
		for(x=1;x<div.childNodes.length;x++){
		
			div.childNodes[x].style.display = "block";
		
		}
		
		element.childNodes[0].innerHTML="-";
	
	}

}