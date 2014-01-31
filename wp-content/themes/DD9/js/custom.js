$(document).ready( function() {
		$(".secondary.single_project").sticky({
			topSpacing: 36,
			zIndex:2,
			stopper: ".block_container.lower"
		});
		
		/* Scroll to top on load */
		$('html, body').stop().animate({ scrollTop: 0 }, 800);
		
		/* Back to top link */
		$('a.top').click(function(){
			$('html, body').animate({scrollTop : 0}, 800);
			return false;
		});
		
});

$(window).scroll(function(){ 
	if ($(this).scrollTop() > 50){  
	 // x should be from where you want this to happen from top//
		//make CSS changes here
		
		$('body').addClass("scrolled");
	} 
	else{
		//back to default styles
		$('body').removeClass("scrolled");
	}
});