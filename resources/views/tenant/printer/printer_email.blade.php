<?php //print_r($data);exit(); ?>
@include('tenant.include.header')
@include('tenant.include.sidebar')

<div class="right_col" role="main">
    <div class="row" id="content-heading">
        <!--content-heading here-->
        <div class="col-md-10">
            <h1 class="cust-head">Printer Email</h1>
        </div>



        <div class="col-md-2 text-right">
            <a href="{{ URL::to('/tenant/printer_email/create') }}" class="btn btn-info add-bt">Add</a>

        </div>
    </div>
    <hr class="border">
    <!--content-heading-end-->

    <div class="row" id="pg-content">
        <!--content-table here-->
        <table class="table table-striped jambo_table" id="scroll">
            <thead>
            <tr class="headings">
                <td class="text-left">S.No</td>
                <td class="text-left">Printer Email</td>
                <td class="text-center">Action</td>
            </tr>
            </thead>

            <tbody class="printer_email">


            </tbody>

        </table>

    </div>
    <!--content-table-end-->
</div>
<script src="{{asset('assets/js/tenant-js/lead_type.js')}}"></script>
<script type="text/javascript">
    $(document).ready(function()
    {
      
        $(document).on('click','.delete',function(){

            var choice = confirm('Do you really want to delete this field?');
            if (choice === true) 
            {
                var remove_email =  $(this).attr('id');
                var email_length = $(this).data('length');
                var  new_emails = [];

                var texts = $(".printer_emails").map(function() {
                    return $(this).text();
                    }).get();
                var final_arr = jQuery.grep(texts, function(value) {
                    return value != remove_email;
                    });
                final_arr = final_arr.join(',');
                var is_delete = 1;
                data = {printer_email_address:final_arr,is_delete:is_delete}
                ajaxCall('POST',base_url + "/tenant/printer/email/update",data);
               location.reload();
            }
            return false;
    })
            ajaxCall('POST',base_url + "/tenant/user/profile",{}).then(function(res)
            {
                var record = res.data;
                if(record.printer_email_address.length)
                {
                    var new_printer_email = record.printer_email_address;
                    var printer_email_length = new_printer_email.length;
                
                    for(y=0;y<new_printer_email.length;y++)
                    {
                        var printer_thead = '';
                            printer_thead += '<tr>';
                        for (var z=0; z<new_printer_email.length;z++)
                        {
                        
                            printer_thead += '<td>'+[z + 1] +'</td>';
                            printer_thead += '<td class="text-left printer_emails">'+new_printer_email[z] +'</td>';
                            printer_thead += '<td  class="delete" data-length="'+printer_email_length+'" style="text-align:center;" id="'+new_printer_email[z] +'"><i style="color:#d11a2a;" class="far fa-trash-alt"></i></td>';
                            printer_thead += '</tr>';
                            
                        }

                    }
                                
                    $('.printer_email').html(printer_thead);

                }
                else
                { 
                    var printer_thead = '';
                    printer_thead += '<tr>';
                    printer_thead += '<td colspan ="100" class="text-center"> No record found </td>';
                    printer_thead += '</tr>';
                    $('.printer_email').html(printer_thead);

                }
                
              
            })

    })
</script>

@include('tenant.include.footer')
