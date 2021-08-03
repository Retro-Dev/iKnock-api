<?php //echo "<pre>"; print_r($data); exit(); ?>
@include('tenant.include.header')
@include('tenant.include.sidebar')
<div class="right_col" role="main">
    <div class="row nomargin justify-content-md-cente">

        <div class="col-md-12">
            <div class="container">
                <div class="stepwizard">
                    <div class="stepwizard-row setup-panel">
                        <div class="stepwizard-step col-xs-3">
                            <a href="#step-1" type="button" class="btn step-1 btn-success btn-circle">1</a>
                            <p><small style="position: relative;
    left: 4%;">Upload File</small></p>
                        </div>
                        <div class="stepwizard-step col-xs-3">
                            <a href="#step-2" type="button" class="btn step-2 btn-default btn-circle disabled"
                               disabled="disabled">2</a>
                            <p><small>Template</small></p>
                        </div>

                    </div>
                </div>
                <div class="panel panel-primary setup-content" id="step-1">
                    <div class="panel-heading">
                        <h3> Locate And Upload Your File</h3>
                        <p>In the following step, we will walk through importing your lead files. We've
                            included a how-to video for you to refrence before you begin
                        </p>
                        <br>
                        <p>Please note. We accepts the following formats only:XLS,XLSX,CSV</p>
                    </div>
                    <div class="error_form"></div>
                    <div class="panel-body">
                        <form enctype="" role="form" id="frm_step-1" action="{{URL::to('/tenant/lead/wizard/upload')}}">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <label class="control-label">Select file</label>
                                <input type="file" required="required" class="input" name="file"
                                       accept=".xls,.xlsx,.csv"/>
                            </div>
                            <div class="show-error" style="color:#a94442;font-weight: bold;"></div>
                        </form>
                        <button class="btn b2 nextBtn pull-right" type="submit">Next</button>
                    </div>
                </div>
                <div class="panel panel-primary setup-content" id="step-2">

                    <div class="row">
                        <div class="col-md-5">
                            <div class="panel-heading">
                                <h3>Choosing Your File Source And Destination</h3>
                                <p>In this step, we will choose the source of your leads and where they will go.<br>
                                    Choose
                                    the list or group from available options, if you are creating a new list <br>or
                                    group,
                                    simply select the[+] button and name it. than choose it.
                                </p>
                                <div class="error_form_2"></div>
                            </div>

                            <div class="panel-body">
                                <form role="form" id="frm_step-2" action="{{URL::to('/tenant/lead/wizard/template')}}">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="field_id" class="field_id" value=""/>
                                    <div id="loader" class="text-center" style="display: none;position: absolute;
                                    top: 200px;z-index: 9999;left: 451px;">
                                        <img src="{{asset('assets/images/load.gif')}}"/>
                                    </div>
                                    <div class="form-group templates">
                                        @if(count($data['template']))
                                            <label class="template">Select Lead Upload Template</label>
                                            <select class="form-control selectbox select-template selectpicker input"
                                                    data-live-search="true" name="template_id">
                                                <option  disabled selected>Nothing Selected</option>
                                                @foreach ($data['template'] as $template)
                                                    <option data-tokens="{{ $template->id }}"
                                                            value="{{ $template->id }}">{{ $template->title }} </option>
                                                @endforeach
                                            </select>
                                            @else
                                            <select class="form-control selectbox select-template selectpicker input" name="lead_type">
                                                <option value="" disabled selected>No Template Found</option>
                                            </select>
                                        @endif
                                    </div>

                                    
                                    <div class="form-group create_temp" style="display: none;">
                                        <div class="new_temp">
                                            <label>Create New Template</label>
                                            <input type="text" class="input " name="template" id="template"/>
                                        </div>
                                    </div>
                                    <button class="btn b2 nextBtn pull-right template_fields" type="button"
                                            style="display:none;">Save
                                    </button>
                                <a href="{{ URL::to('tenant/template/add') }}">   <button type="button" class="btn b2 mt-15">Create New Template</button></a>
                                  <!--  <button type="button" class="btn b2 cancel">Cancel</button>
                                    <button type="button" class="btn b2 back_button" data-dismiss="modal" style="display:none;">Back</button> -->
                                    <div class="form-group templates">
                                        @if(count($data['lead_types']))
                                            <label class="template">Select Lead Type</label>
                                            <select class="form-control selectbox input selectpicker"
                                                    data-live-search="true" name="lead_type_id">
                                                <option disabled selected>Nothing Selected</option>
                                                @foreach ($data['lead_types'] as $lead_types)
                                                    <option data-tokens="{{ $lead_types->id }}"
                                                            value="{{ $lead_types->id }}">{{ $lead_types->title }} </option>
                                                @endforeach
                                            </select>
                                            @else
                                            <select class="form-control selectbox selectpicker input" name="lead_type">
                                                <option value=""  >No Lead Type Found</option>
                                            </select>
                                        @endif
                                    </div>

                                    <div class="form-group templates">
                                        @if(count($data['lead_status']))
                                            <label class="template">Select Lead Status</label>
                                            <select class="form-control selectbox input selectpicker"
                                                    data-live-search="true" name="lead_status_id">
                                                <option  disabled selected>Nothing Selected</option>
                                                @foreach ($data['lead_status'] as $lead_status)
                                                    <option data-tokens="{{ $lead_status->id }}"
                                                            value="{{ $lead_status->id }}">{{ $lead_status->title }} </option>
                                                @endforeach
                                            </select>
                                            @else
                                            <select class="form-control selectbox selectpicker input" name="lead_status_id">
                                                <option value="">No Lead Type Found</option>
                                            </select>
                                        @endif
                                    </div>
                                </form>
                            </div>        

                        </div> <!-- Col End -->

                        <div class="col-md-7" id="step-3">
                            <form role="form" id="frm_step-3" method="post">
                            <input type="hidden" class="lead_type_id" name="lead_type_id">
                            <input type="hidden" class="lead_status" name="lead_status_id">
                                {{ csrf_field() }}
                                <input type="hidden" class="submit_url"
                                       value="{{ action('LeadController@wizardFields') }}"/>
                                <input type="hidden" class="redirect_url" value="{{ URL::to('tenant/lead/') }}">
                                <input type="hidden" name="template_id" id="template_id" class="template_id" value=""/>
                                <input type="hidden" name="field_id" class="field_id" value=""/>

                                <div class="panel-heading show-heading" style="display:none;">
                                    <h3>Map your Field</h3>
                                    <p>Map your fields by selecting your files headers
                                        for the appropriate field on the right.
                                    </p>
                                </div>
                                @include('tenant.error')
                                <div class="panel-body field_head">
                                    <div class="form-group">
                                        <label>{{config('constants.LEAD_TITLE_DISPLAY')}}</label>
                                        <div class="lead_name_ctn">
                                            <select class="form-group form-control select lead_name field_head change_fields selectpicker" data-value="lead_name" multiple="multiple" data-max-options="4"  name="lead_name[]" >
                                                <option value=""> -- Select Options -- </option>
                                            </select>
                                        </div>
                                    </div>
                                  
                                    <!-- <div class="form-group">
                                        <label>Lead Type</label>
                                        <div class="lead_name_ctn">
                                            <select class="form-group form-control select field_head selectpicker"  name="lead_type">
                                                <option value=""> -- Select Options -- </option>
                                            </select>
                                        </div></div> -->
                                        <!-- <div class="form-group">
                                        <label>Lead Status</label>
                                        <div class="lead_name_ctn">
                                            <select class="form-group form-control select field_head selectpicker"  name="lead_status">
                                                <option value=""> -- Select Options -- </option>
                                            </select>
                                        </div></div> -->
                                    <div class="form-group">
                                        <label>Address</label>
                                        <div class="lead_name_ctn">
                                            <select class="form-group form-control select field_head change_fields selectpicker" data-value="address" name="address" >
                                                <option value=""> -- Select Options -- </option>
                                            </select>
                                        </div></div>
                                    <div class="form-group">
                                        <label>City</label>
                                        <div class="lead_name_ctn">
                                            <select class="form-group form-control select field_head change_fields selectpicker" data-value="city" name="city" >
                                                <option value=""> -- Select Options -- </option>
                                            </select>
                                        </div> </div>
                                    <div class="form-group">
                                        <label>County</label>
                                        <div class="lead_name_ctn">
                                            <select class="form-group form-control select field_head change_fields selectpicker" data-value="county" name="county" >
                                                <option value=""> -- Select Options -- </option>
                                            </select>
                                        </div> </div>
                                    <div class="form-group">
                                        <label>State</label>
                                        <div class="lead_name_ctn">
                                            <select class="form-group form-control select custom_fields field_head change_fields selectpicker" data-value="state" name="state" > 
                                                <option value=""> -- Select Options -- </option>
                                            </select>
                                        </div> </div>
                                    <div class="form-group">
                                        <label>Zip Code</label>
                                        <div class="lead_name_ctn">
                                            <select class="form-group form-control select field_head change_fields selectpicker" data-value="zip_code" name="zip_code" >
                                                <option value=""> -- Select Options -- </option>
                                            </select>
                                        </div>  </div>

                                        <div class="form-group">
                                        <label>Foreclosure Date</label>
                                        <div class="lead_name_ctn">
                                            <select class="form-group form-control select field_head change_fields selectpicker" data-value="foreclosure_date" name="foreclosure_date" >
                                                <option value=""> -- Select Options -- </option>
                                            </select>
                                        </div>  </div>
                                        <div class="form-group">
                                        <label>Admin Notes</label>
                                        <div class="lead_name_ctn">
                                            <select class="form-group form-control select field_head change_fields selectpicker" data-value="admin_notes" name="admin_notes" >
                                                <option value=""> -- Select Options -- </option>
                                            </select>
                                        </div>  </div>
                                    <div id="fix_field"></div>
                                    <div id="demo3">
                                    </div>
                    
                                    <div class="form-group" id="demo">
                                    </div>
                                    <div class="row" style="margin:0px">
                                        <!-- Trigger the modal with a button -->
                                        <div class="col-md-12 show-buttons" style="display:none;">
                                            <button type="button" class="btn b2 add_field" data-toggle="modal"
                                                    data-target="#myModal" data-backdrop="static" data-keyboard="false">Add field
                                            </button>
                                            <button class="btn b2 nextBtn clear" type="reset">Clear</button>
                                            <button type="button" class="btn b2 cancel">Cancel</button>
                                            <button class="btn b2 pull-right import_wizard" id="submit" type="submit">
                                                Submit
                                            </button>
                                        </div>
                                    </div>
                                    <!-- Modal -->
                                    <div class="modal fade" id="myModal" role="dialog">
                                        <div class="modal-dialog">
                                            <!-- Modal content-->
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal">&times;
                                                    </button>
                                                    <h4 class="modal-title">Add Field</h4>
                                                </div>
                                                <div class="modal-body">
                                               
                                                    <div class="form-group">
                                                    <div id="query_form"></div>
                                                        <label>Label Title</label>
                                                        <input type="text" class="input_label input" name="">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn b2 save_field"
                                                            data-dismiss="modal">Save
                                                    </button>
                                                    <button type="button" class="btn b2 modal-cancel" data-dismiss="modal">Cancel
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Modal -->
                                </div>
                            </form>

                        </div> <!-- Col End -->
                    </div>

                </div>

            </div>

        </div>

    </div>
</div>
<script src="{{asset('assets/js/tenant-js/wizard.js')}}"></script>
<script>

    //getTemplateFields();
    function repl_str(data) {
        if (data.length > 0)
        {
            return  data.replace(/#|_/g,' ');
        }else {
            return data;
        }
    }
    
    function getTemplateFields(save = false) {
        var formData = $('#frm_step-2').serialize();
        $.ajax(
            {
                type: "POST",
                url: $('#frm_step-2').attr('action'),
                data: formData,
                beforeSend: function () {
                
                },
                success: function (res) {

                    if (res.code == 200) {
                        $('.show-buttons, .show-heading').show();

                        var new_template_fields = res.data.custom_fields;
                        var get_template_id = $("[name='template_id']").val();
                        var fix_template_fields = res.data.custom_fields;
                        if (fix_template_fields.length > 0) {
                            $("#fix_field").html('');
                            fix_template_fields = fix_template_fields
                            for (var j = 0; j < fix_template_fields.length; j++) {
                                var field_html = '';
                                field_html += '<div class="col-md-6 hide_field field_head" id="hide' + fix_template_fields[j].id + '"> ';
                                field_html += '<div class="form-group margintop">';
                                field_html += '<lable style="font-weight:bold;">' + repl_str(fix_template_fields[j].key) + ' <a class="btn import" data-id="' + fix_template_fields[j].id + '" style="color:red !important;font-size: 14px;position: absolute;right: 0px;top: 19px;">x</a></label>';
                                field_html += ' <select data-max-options="4" data-value="' + fix_template_fields[j].id + '" name = "custom_field[' + fix_template_fields[j].id + '][]" class="form-group form-control select selectpicker change_fields" multiple="multiple">';
                                $("#fix_field").append(field_html);
                            }
                        }
                       else {
                            $("#fix_field").html('');
                        }
                        var field_index = 0;
                    file_header = res.data.file_header;
                    if (file_header.length > 0) {
                        var options_html = '';
                        options_html += '<option hidden disabled class="nothing">Nothing Selected</option>';
                        for (var i = 0; i < file_header.length; i++) {
                            options_html += '<option value="' + [i] + '">' + file_header[i] + '</option>';
                        }
                        $('.field_head').find('select').html(options_html);

                        
                        // if(save != true)
                        // {
                        //     console.log('Savee',save);
                             $(".selectpicker").selectpicker("refresh");
                        // }
   
                        
                    }

                    var template_id = res.data.template_id;
                    $('.template_id').val(template_id);

                    var record = res.data.template_fields;
                    for (var i = 0; i < record.length; i++) {
                        var select_name = record[i].field;
                        var select_value = record[i].index;
                        var lead_values = select_value;
                        if(select_value)
                            var lead_values = select_value.split(",");
                        
                        if(lead_values.length == 1){
                          var  lead_values = select_value;
                        }
                        
                            $("select[name=" + select_name + "]").selectpicker('val', lead_values);
                                             

                        
                        $("select[name='custom_field" + "[" + select_name + "][]']").selectpicker('val', lead_values);
                       
                            
                        if(select_name == 'lead_name')
                        {
                            // if(save != true)
                            // {
                                $(".lead_name").selectpicker('val', lead_values); 
                            // }
                            
                        }
                       

                    }
                    }
            


                }


            });
            
    }
    $(document).on('change', '.bootstrap-select .change_fields', function () {
            var my_val_arr = $(this).selectpicker('val');
            var new_field_id = $(this).data("value");
            var get_template_id = $("[name='template_id']").val();
           
            if(Array.isArray(my_val_arr)){
                var val_ids = my_val_arr.join(',');
            }else {
                var val_ids = my_val_arr;
            }
            var data = {field_id:new_field_id, indexs:val_ids,template_id:get_template_id};
            ajaxCall('POST', base_url + "/tenant/template/field/index/update", data);
            console.log("new_field_id",data);
        });
        $(document).on('change', '.bootstrap-select .select-template', function () {
            getTemplateFields();
        })
        
    $(document).ready(function () {

        $('select').on('show.bs.select',function(){
            console.log("herer");
            $(".lead_name option[value='']").hide();
        })

        $('select').trigger('change');
        //getTemplateFields();
        $('select[name="lead_type_id"]').on('change', function () {
            if ($(this).val() != '') {
                var id = $(this).val();
                $('.lead_type_id').val(id);
            }
        })

        $('select[name="lead_status_id"]').on('change', function () {
            if ($(this).val() != '') {
                var id = $(this).val();
                console.log("id here", id);
                $('.lead_status').val(id);
            }
        })
        //Delete Function

        $(document).on('click', '.import', function (e) {
            var id = $(this).data("id");
            var get_template_id = $("[name='template_id']").val();
            console.log('id', id);
            console.log('template_id', get_template_id);
            var choice = confirm('Do you really want to delete this field?');
            if (choice === true) {

                let deleteRecord = "{{ URL::to('/tenant/template/field/delete') }}" + "/" + id;
                var data = {id: id, template_id: get_template_id};
                ajaxCall('POST', deleteRecord, data, {});
                $('#hide' + id).hide();
            }
            return false;
        });


        $('.cancel').click(function () {
            location.reload();
        });
        $('.modal-cancel').click(function () {
            $('#query_form').html('');
            $('.input_label').val("");
        });
        $('.save_field').removeAttr('data-dismiss');

        // $('.input_label').keyup(function () {
        //     if ($(this).val().length >= 1) {
        //         $('.save_field').attr('data-dismiss', 'modal');
        //     }
        // })

        $('.save_field').on('click', function () {
            var input_label = $('.input_label').val();
            var input_type = $('.input_type').val();
            var get_template_id = $("[name='template_id']").val();


            var data = {query: input_label, type: 'lead_detail', template_id: get_template_id};
            ajaxCall('POST', base_url + "/tenant/query/create", data).then(function (res) {
                if (res.code == 200) {
                    var new_field_id = res.data.id;
                    $('#myModal').modal('toggle');
                    $('.field_id').val(new_field_id);
                    $('.input_label').val("");
                    $('#query_form').html('');
                    var save = true;
                    getTemplateFields();
                  
                }
              else {
                    $('.save_field').removeAttr('data-dismiss');
                    var record = res.data;
                    console.log("here", record);
                    var error = '';
                   error += '<div class="alert alert-danger">';
                    error += '<ul>';

                    var messages = res.data[0];
                    for (message in messages) {
                        error += '<li>' + messages[message] + '</li>';
                    }
                    
                    error += '</ul>';
                    error += '</div>';
                    $('#query_form').html(error);
                  

                }

            })

        })


        $('.save_temp').on('click', function () {

            $('.templates').hide();
            $('.create_temp').show();
            $('.back_button').show();
            $('.save_temp').hide();
            $('.selectpicker').val('');
            $('.error_form_2').hide();
            $('.template_fields').show();

        })

        $('.back_button').on('click', function () {
            $('.templates').show();
            $('.create_temp').hide();
            $('.back_button').hide();
            $('.save_temp').show();
            $("#template").val("");

            $('.error_form_2').hide();
            $('.selectpicker').selectpicker('refresh');

        })

        $('.clear').on('click', function () {
            $(".select").val('').trigger('change');
            var get_template_id = $("[name='template_id']").val();
            data = {template_id:get_template_id};
            ajaxCall('POST', base_url + "/tenant/template/field/clear/indexes", data);

        })

        $('.template_fields').on('click', function () {
            getTemplateFields();
      
        })


    })
</script>
@include('tenant.include.footer')
{{--</html>--}}