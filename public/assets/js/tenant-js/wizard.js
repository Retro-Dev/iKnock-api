var file_header = [];

$(document).ready(function () {



    var navListItems = $('div.setup-panel div a'),
        allWells = $('.setup-content'),
        allNextBtn = $('.nextBtn');

    allWells.hide();


    navListItems.click(function (e) {
        e.preventDefault();
        var $target = $($(this).attr('href')),
            $item = $(this);

        if (!$item.hasClass('disabled')) {
            navListItems.removeClass('btn-success').addClass('btn-default');
            $item.addClass('btn-success');
            allWells.hide();
            $target.show();
            $target.find('input:eq(0)').focus();
        }
    });

    allNextBtn.click(function () {
        var curStep = $(this).closest(".setup-content"),
            curStepBtn = curStep.attr("id"),
            nextStepWizard = $('div.setup-panel div a[href="#' + curStepBtn + '"]').parent().next().children("a"),
            curInputs = curStep.find("input[type='text'],input[type='url'],input[type='file']"),
            isValid = true;

        $(".form-group").removeClass("has-error");
        for (var i = 0; i < curInputs.length; i++) {
            if (!curInputs[i].validity.valid) {
                isValid = false;
                $(curInputs[i]).closest(".form-group").addClass("has-error");
                $('.show-error').html('Please Select the File to Upload');
                
               
            }
        }

        if (isValid)
        {

            if(curStepBtn == 'step-1')
            {
               
                var formData = new FormData();
                $.each($('input[name="file"]')[0].files, function(i, file) {
                    formData.append('file', file);
                });

                $.ajax({
                    type:"POST",
                    url: $('#frm_'+curStepBtn).attr('action'),
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    beforeSend:function(){
                        /*$(this).attr('disabled','disabled');
                        $('.error').hide();*/    

                    },
                    success:function(res){

                        if(res.code == 200)
                        {
                            
                        }

                        if(res.code == 404)
                        {
                            var record = res.data;
                            var error ='';
                                error +='<div class="alert alert-danger">';
                                error +='<ul>';
                        
                                for(var i=0; i<record.length;i++)
                                {
                                    
                                    
                                        error +='<li>'+ record[i].file +'</li>';

                                }
                                error +='</ul>';
                                error +='</div>';

                               
                                $('.error_form').html(error);
                            
                        }
                        else{
                            $('.error_form').hide();
                            nextStepWizard.removeAttr('disabled').trigger('click');
                            nextStepWizard.removeClass('disabled').trigger('click');
                        }
                        
                        

                    }
                });

            }

            else
            {


            
        //         if(curStepBtn == 'step-2')
        //             {
        //                 var formData = $('#frm_'+curStepBtn).serialize();
        //                 $.ajax(
        //                 {
        //                     type:"POST",
        //                     url: $('#frm_'+curStepBtn).attr('action'),
        //                     data: formData,
        //                     beforeSend:function(){
        //                         /*$(this).attr('disabled','disabled');
        //                         $('.error').hide();*/
                        

        //                     },
                    
        //                 success:function(res)


        //                 { 
                            
                        

        //                     if(res.code == 200)
        //                     {
        //                         file_header = res.data.file_header;
        //                         if(file_header.length > 0)
        //                         {
                                   
        //                              var options_html = '<option value="" hidden class="nothing">Nothing Selected</option>';
        //                             for(var i=0; i<file_header.length;i++)
        //                             {
        //                                  options_html += '<option value="'+[i]+'">'+ file_header[i] +'</option>';  
        //                             }  

                                   
                                             
        //                             $('.field_head').append(options_html);
        //                             $('.lead_name').addClass('selectpicker');
        //                             $('.owner').addClass('selectpicker');
        //                             $('.selectpicker').selectpicker();
        //                             $('.step-2').addClass('disabled');
        //                              $('.step-1').addClass('disabled');
                                 
        //                         } 

                                
                             
                                
        //                      var template_id = res.data.template_id;
        //                      console.log('template_id',template_id);
        //                         $('.template_id').val(template_id);

        //                         var record = res.data.template_fields;
        //                              for(var i =0;i<record.length;i++)
        //                         {
        //                             var select_name = record[i].field;
        //                             var select_value = record[i].index;
     
        //                             if(select_name == 'lead_name') 
        //                             {  

        //                                 if(select_value)
        //                                 {
        //                                     var lead_values =  select_value.split(",");
        //                                     $('.lead_name').selectpicker('val',lead_values);
                                            

        //                                 }

        //                             }

        //                             else if(select_name == 'owner'){
        //                                 if(select_value)
        //                                 {
        //                                     var lead_values =  select_value.split(",");
                                          
        //                                     $('.owner').selectpicker('val',lead_values);

        //                                 }

        //                             }

                                    
        //                                  $("select[name="+select_name+"]").selectpicker('val',select_value);

        //                                 var arr =   $("select[name='custom_field"+"["+select_name+"]']").selectpicker('val',select_value);
                                   
                   
        //                          }     

        //                     }


                            

                          
        //                             if(res.code == 404)
        //                         {

        //                             var record = res.data;

        //                             var error ='';
        //                                 error +='<div class="alert alert-danger">';
        //                                 error +='<ul>';
                                
        //                                 for(var i=0; i<record.length;i++)
        //                                 {
                                            
        //                                         error +='<li>'+ record[i].template +'</li>';

        //                                 }
        //                                 error +='</ul>';
        //                                 error +='</div>';

                                        
        //                                 $('.error_form_2').html(error);
        //                                  $('.error_form_2').show();
        //                         }

        //                         else
        //                         {

                                   

        //                             $('.error_form_2').hide();
        //                             nextStepWizard.removeAttr('disabled').trigger('click');
        //                             nextStepWizard.removeClass('disabled').trigger('click');
                                    
        //                              var formData = $('#frm_'+curStepBtn).serialize();
    
        //                         }
        //                 }
        //     });
                                                
        // }



               
            }
        }

    });

    $('div.setup-panel div a.btn-success').trigger('click');


});

