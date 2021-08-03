/* Auto hide placeholder on focus */
$("input").each(
 function(){
 $(this).data('holder',$(this).attr('placeholder'));
 $(this).focusin(function(){
 $(this).attr('placeholder','');
 });
 $(this).focusout(function(){
 $(this).attr('placeholder',$(this).data('holder'));
 });
});
/**/
jQuery("#accordion > li").click(function(){

	if(false == jQuery(this).next().is(':visible')) {
		jQuery('#accordion > ol').slideUp(300);
	}
	jQuery(this).next().slideToggle(300);
});

/* sidebar script */
$(document).ready(function () {
 jQuery('#sidebarCollapse').on('click', function () {
 jQuery('#sidebar').toggleClass('active');
 });
});
/* Range Date Picker */
$('#pick').daterangepicker({
    "showISOWeekNumbers": true,
    "timePicker": false,
    "autoUpdateInput": true,
    "locale": {
        "cancelLabel": 'Clear',
        "format": "MMMM DD, YYYY",
        "separator": " - ",
        "applyLabel": "Apply",
        "cancelLabel": "Cancel",
        "fromLabel": "From",
        "toLabel": "To",
        "customRangeLabel": "Custom",
        "weekLabel": "W",
        "daysOfWeek": [
            "Su",
            "Mo",
            "Tu",
            "We",
            "Th",
            "Fr",
            "Sa"
        ],
        "monthNames": [
            "January",
            "February",
            "March",
            "April",
            "May",
            "June",
            "July",
            "August",
            "September",
            "October",
            "November",
            "December"
        ],
        "firstDay": 1
    },
    "linkedCalendars": true,
    "showCustomRangeLabel": false,
    "startDate": 1,
    "endDate": "December 31, 2016 @ h:mm A",
    "opens": "center"
});
/* Init datatable */
$(document).ready(function() {
 jQuery('#storage').DataTable();
} );
/* Download CSV */
/* global $ */
$("#dl").click(function(){
    $("#storage").table2csv('output', {appendTo: '#out'});
    $("#storage").table2csv();
});
/**
// ||||||||||||||||||||||||||||||| \\
//	Drag and Drop code for Upload
// ||||||||||||||||||||||||||||||| \\
**/
(function(){
	var $ = function( elem ){
		if (!(this instanceof $)){
      return new $(elem);
		}
		this.el = document.getElementById( elem );
	};
	window.$ = $;
	$.prototype = {
		onChange : function( callback ){
			this.el.addEventListener('change', callback );
			return this;
		}
	};
})();


var dragdrop = {
	init : function( elem ){
		elem.setAttribute('ondrop', 'dragdrop.drop(event)');
		elem.setAttribute('ondragover', 'dragdrop.drag(event)' );
	},
	drop : function(e){
		e.preventDefault();
		var file = e.dataTransfer.files[0];
		runUpload( file );
	},
	drag : function(e){
		e.preventDefault();
	}
};

/**
// ||||||||||||||||||||||||||||||| \\
//	Code to capture a file (image) 
//  and upload it to the browser
// ||||||||||||||||||||||||||||||| \\
**/
function runUpload( file ) {
	// http://stackoverflow.com/questions/12570834/how-to-preview-image-get-file-size-image-height-and-width-before-upload
	if( file.type === 'image/png'  || 
			file.type === 'image/jpg'  || 
		  file.type === 'image/jpeg' ||
			file.type === 'image/gif'  ||
			file.type === 'image/bmp'  ){
		var reader = new FileReader(),
				image = new Image();
		reader.readAsDataURL( file );
		reader.onload = function( _file ){
			$('imgPrime').el.src = _file.target.result;
			$('imgPrime').el.style.display = 'inline';
		} // END reader.onload()
	} // END test if file.type === image
}

/**
// ||||||||||||||||||||||||||||||| \\
//	window.onload fun
// ||||||||||||||||||||||||||||||| \\
**/
window.onload = function(){
	if( window.FileReader ){
		// Connect the DIV surrounding the file upload to HTML5 drag and drop calls
		dragdrop.init( $('userActions').el );
		//	Bind the input[type="file"] to the function runUpload()
		$('fileUpload').onChange(function(){ runUpload( this.files[0] ); });
	}else{
		// Report error message if FileReader is unavilable
		var p   = document.createElement( 'p' ),
				msg = document.createTextNode( 'Sorry, your browser does not support FileReader.' );
		p.className = 'error';
		p.appendChild( msg );
		$('userActions').el.innerHTML = '';
		$('userActions').el.appendChild( p );
	}
};
/*=========== date picker ================*/
jQuery(document).ready(function() {
  jQuery(".date-picker").datepicker();
});
