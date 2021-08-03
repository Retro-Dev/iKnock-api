jQuery(document).ready(function(){        
	jQuery('li img').on('click',function(){
		var src = jQuery(this).attr('src');
		var img = '<img src="' + src + '" class="img-responsive"/>';
		
		//start of new code new code
		var index = jQuery(this).parent('li').index();   
		
		var html = '';
		html += img;                
		html += '<div style="height:25px;clear:both;display:block;">';
		html += '<a class="controls next" href="'+ (index+2) + '">next &raquo;</a>';
		html += '<a class="controls previous" href="' + (index) + '">&laquo; prev</a>';
		html += '</div>';
		
		jQuery('#myModal').modal();
		jQuery('#myModal').on('shown.bs.modal', function(){
			jQuery('#myModal .modal-body').html(html);
			//new code
			jQuery('a.controls').trigger('click');
		})
		jQuery('#myModal').on('hidden.bs.modal', function(){
			jQuery('#myModal .modal-body').html('');
		});
		
		
		
		
   });	
})
        
         
jQuery(document).on('click', 'a.controls', function(){
	var index = jQuery(this).attr('href');
	var src = jQuery('ul.row li:nth-child('+ index +') img').attr('src');             
	
	jQuery('.modal-body img').attr('src', src);
	
	var newPrevIndex = parseInt(index) - 1; 
	var newNextIndex = parseInt(newPrevIndex) + 2; 
	
	if(jQuery(this).hasClass('previous')){               
		jQuery(this).attr('href', newPrevIndex); 
		jQuery('a.next').attr('href', newNextIndex);
	}else{
		jQuery(this).attr('href', newNextIndex); 
		jQuery('a.previous').attr('href', newPrevIndex);
	}
	
	var total = jQuery('ul.row li').length + 1; 
	//hide next button
	if(total === newNextIndex){
		jQuery('a.next').hide();
	}else{
		jQuery('a.next').show()
	}            
	//hide previous button
	if(newPrevIndex === 0){
		jQuery('a.previous').hide();
	}else{
		jQuery('a.previous').show()
	}
	
	
	return false;
});