$(document).ready(function(){

    $('.add-link').click(function(){
        var url = $(this).data('href')
        window.location.href = url;
    })

    $("#e2").daterangepicker({
        datepickerOptions : {
            numberOfMonths : 2
        }
    });

    // $(document).on('click','.hide_show_table',function(){
    //     var class_name = $(this).data('id'); 
    //     if( $(this).is(':checked') ){
    //          $(document).find('.' + class_name).show();   
    //     }else{
    //       $(document).find('.' + class_name).hide(); 
    //     }
    // })

});

