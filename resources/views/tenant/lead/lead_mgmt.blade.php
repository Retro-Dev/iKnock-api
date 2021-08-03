<?php  //echo "<pre>"; print_r($data);exit;?>
@include('tenant.include.header')
<style>
    .active_page {
        background-color: #337ab7 !important;
        color: white !important;
    }

    #constrainer2 .table {
        width: auto;
        overflow-x: scroll;

        display: inline-block;
        white-space: nowrap;

    }
    

    #redirect2 {
        cursor: pointer;
    }

    .down_arrow{
        position: relative;
        left: 10px;
    }

    .HideArrow span{
        display: none;
    }
    .ShowArrow span{
        display: inline;
    }

    .mx li{
        margin-right:10px;
    }
    .check-menu li span{
        text-transform:capitalize;
    }
.bootstrap-select>.dropdown-toggle, .select{
    padding:9px;
    font-family: 'FontAwesome', 'Second Font name'
}
select {
  font-family: 'FontAwesome', 'Second Font name'
}

.hide-th, .dynamicHide{
    display:none;
}

.show-th, .dynamicShow{
    display:table-cell;
}
</style>
@include('tenant.include.sidebar')
<!-- page content -->
<div class="right_col" role="main">

<div class="row" id="content-heading">
    <div class="col-md-4">
        <h1 class="cust-head">Lead Management</h1>
    </div>
    <div class="col-md-8 text-right pull-right">
        <section class=" text-right  show_all" style="display:none;">
            <button class="btn btn-primary" data-toggle="modal" data-target=".bd-example-modal-lg" data-backdrop="static" data-keyboard="false">Bulk Action</button>
            <button class="btn btn-primary check_delete">Delete</button>
        </section>
        <div class="dropdown text-right pull-right" style="margin-bottom:20px;">
            <a class="lead_map_url" href="{{URL::to('/tenant/leads/map?')}}"><button class="btn btn-primary" type="button">Leads Map View</button></a>
            <a href="{{URL::to('/tenant/lead-default-order')}}"><button class="btn btn-primary" type="button">Lead View Setup</button></a>
            <button class="btn filter-toggle btn-primary" type="button"><i class="fa fa-filter"></i></button>
                <button type="button" class="btn btn-primary" id="export-btn" data-export="false" data-clicked="false" > Export </button>
                <button type="button" class="btn btn-primary" id="export-history"> Export History </button>
                <button class="btn btn-primary setting-dropdown dropdown-toggle" type="button" data-toggle="dropdown" disabled><i
                            class="fa fa-cog"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-right check-menu"></ul>
        </div>
    </div>
</div>
<!-- Filter -->
<div class="row filter-show" >
    
<form class="form_search">
                <input type="hidden" name="status_id" class="id" value=""/>
                <input type="hidden" name="assign_id" class="id" value=""/>
                <input type="hidden" name="type_id" class="id" value=""/>
                <input type="hidden" name="col_title" class="title" value=""/>
                <input type="hidden" name="col_type" class="column_name" value=""/>
                <input type="hidden" name="" class="page-number" value="1"/>

        <div class="col-md-2">
            <div class="form-group margintop">
                    <input class="form-control search" type="search" placeholder="Search..." aria-label="Search" name="search">
            </div>
    </div> <!-- Col End -->

    <div class="col-md-2" id="all-users">
        <div class="form-group">
            <label>Select User</label>
            <select class="form-control selectpicker lead_users" data-live-search="true"
                        name="user_ids" value="" data-actions-box="true" multiple>
                        
                    @foreach ($data['agent'] as $agent)
                        <option data-tokens="{{ $agent->title }}"
                                value="{{ $agent->id }}">{{ $agent->first_name }} </option>
                    @endforeach
            </select>
                            
         </div>
    </div> <!-- Col End -->

    <div class="col-md-2">
            <div class="form-group">
            <label>Select Date Range</label>
            <input type="text" id="e2" name="e2" class="input date_range1" value="select date" name="date_range">
            </div>
    </div> <!-- Col End -->

    <div class="col-md-2">
        <div class="form-group">
            <label>Lead Type</label>
            <select class="form-control selectpicker lead_types" data-live-search="true"
                        name="lead_type_id" value="" >
                        <option value="" selected>All Lead Type</option>
                    @foreach ($data['type'] as $type)
                        <option data-tokens="{{ $type->title }}"
                                value="{{ $type->id }}">{{ $type->title }} </option>
                    @endforeach
            </select>
                            
         </div>
    </div> <!-- Col End -->

    <div class="col-md-2">
        <div class="form-group">
            <label>Lead Upload Template</label>
            <select class="form-control selectpicker" data-live-search="true"
                        name="template_id" value="" >
                        <option value="" selected>All Templates</option>
                    @foreach ($data['templates'] as $template)
                        <option data-tokens="{{ $template->title }}"
                                value="{{ $template->id }}">{{ $template->title }} </option>
                    @endforeach
            </select>
                            
         </div>
    </div> <!-- Col End -->
    <div class="col-md-2">
    <label>Select Status</label>
    <div class="navbar yamm">
  
  <button class="dropdown status-dropdown">
      <a href="#" class="dropdown-toggle" data-toggle="dropdown">Select Status <i class="fas fa-caret-down" style="float:right"></i></a>
      <ul class="dropdown-menu status-menu">
          <li>
          <div class="table-wrapper-scroll-y-2 yamm-content">
                <div class="row">    
                <table class="show-table yamm-content table" style="">
                    <div class="btn-group btn-group-sm btn-block"><button type="button" class="actions-btn select-all btn btn-default">Select All</button><button type="button" class="actions-btn deselect-all btn btn-default">Deselect All</button></div>
                    <tbody class="status_table status_table2"> </tbody>
                </table>
            </div>
            </div>  
          </li>
      </ul>
  </button>

</div>        
    </div> <!-- Col End -->

</div> <!-- Row End -->

<!-- End Filter -->

    <div class="row">
        <div class="col-md-12">
            <!-- Modal Start -->
            <!-- Modal -->
<div class="modal fade bd-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <div class="form-group">
                <label>Current Status</label>
                <select class="form-control modal_status selectbox selectpicker input" data-live-search="true" name="status_id">
                    <option value="" selected="selected">Nothing Selected</option>
                    @foreach ($data['status'] as $status)
                    <option data-tokens="{{ $status->id }}" value="{{ $status->id }}">{{ $status->title }} </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="exampleFormControlSelect1">Assign to</label>
                <select class="form-control modal_agent selectbox selectpicker input" data-live-search="true" name="assign_id">
                    <option value="" selected="selected">Nothing Selected</option>
                    @foreach ($data['agent'] as $agent)
                    <option data-tokens="{{ $agent->id }}" value="{{ $agent->id }}">{{ $agent->first_name }} </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="exampleFormControlSelect1">Is Expired</label>
                <select class="form-control modal_type selectbox selectpicker input is_expired" data-live-search="true" name="is_expired" value="">
                    <option value="" selected="selected">Nothing Selected</option>
                    <option value="0">No</option>
                    <option value="1">Yes</option>
                </select>
            </div>
            <div class="form-group">
                <button class="btn btn-default b1 check_update">Update</button>
            </div>
      </div>
    </div>
  </div>
</div>
            <!-- Modal End -->
            @include('tenant.error')
            </form>
            <div class="" id="constrainer2">
                <table id="table4"
                       class="table table-striped jambo_table bulk_action lead_mg table_ld table-responsive"
                       id="example" cellspacing="0" width="100%">
                    <thead>
                    <tr class="headings">

                    </tr>
                    </thead>
                   
                    <tbody class="scroll">

                    </tbody>
                
                </table>
                <div class="pagination_cont"></div>
    
   
    <!--<div id="loader" class="text-center" style="display: none;position: absolute;-->
    <!--                                top: 200px;z-index: 9999;left: 451px;">-->
    <!--                                    <img src="{{asset('assets/images/load.gif')}}"/>-->
    <!--                                </div>-->
</div>

            </div>
        </div>
    </div>
</div>
<!-- /page content -->

<!--footer-->

<script src="{{asset('assets/js/tenant-js/lead_mgmt.js')}}">
</script>
<script src="http://malsup.github.io/jquery.blockUI.js"></script>
<script type="text/javascript">

$(document).ready(function() {
let export_in_chunk = "{{config('constants.IS_EXPORT_IN_CHUNK')}}";

$('.bd-example-modal-lg').on('hide.bs.modal', function(e) {
    // do something..
    $('.chkboxes').prop('checked', false);
    $('#checkAll').prop('checked', false);
    $('.show_all').hide();
    var newDiv = '';
    newDiv += '<div class="col-md-4 text-right show_all" style=""></div>';
    $('.new_div').html(newDiv);
    $('.selectpicker').val('').selectpicker('refresh');
})



$(function() {
    $(document).on('click', '.yamm .dropdown-menu', function(e) {
        e.stopPropagation()
    })
})

$('.status-dropdown').removeClass('b1');
var columns = '{!!json_encode($data["columns"])!!}';
var orderable_columns = '{!!json_encode($data["orderable_columns"])!!}';
columns = JSON.parse(columns);
var created_at = 'created_at';
var updated_at = 'updated_at';
orderable_columns = JSON.parse(orderable_columns);

$("select").on("changed.bs.select",
    function(e, clickedIndex, newValue, oldValue) {

        var nothing = $('#all-users .filter-option-inner-inner').text();
        if (nothing == 'Nothing selected') {
            $('#all-users .filter-option-inner-inner').html('All User');
        }

    });
$(".filter-toggle").click(function() {
    $(".filter-show").fadeToggle();
    $('#all-users .filter-option-inner-inner').html('All User');
});
jQuery(function($) {
    $('#table4').checkboxes('range', true);
});


$(document).on('click', '.sort', function() {
    var title = $(this).attr('title');
    var column_name = $(this).data('column');
    $('.title').val(title);
    $('.column_name').val(column_name);

    if (title == 'asc') {
        $(this).hide();
        $('.sort[title="desc"]').show();
    } else {
        $(this).hide();
        $('.sort[title="asc"]').show();
    }
    data = {
        order_by: column_name,
        order_type: title
    }

    loadData('GET', base_url + "/tenant/lead/list?page=1", data, {}, columns, '.table_ld tbody', orderable_columns);

})


loadData('GET', base_url + "/tenant/lead/list?page=1", {}, {}, columns, '.table_ld tbody', orderable_columns);

//ajaxDatatable('#example',base_url + "/tenant/lead/list",10,columns);

$('.search').keyup(function() {
    if ($(this).val().length > 3) {
        searchData('GET', base_url + "/tenant/lead/list", {}, {}, columns, '.table_ld tbody', orderable_columns);

    } else {

        searchData('GET', base_url + "/tenant/lead/list", {}, {}, columns, '.table_ld tbody', orderable_columns);

    }
})

$('.clear_button').click(function() {
    loadData('GET', base_url + "/tenant/lead/list?page=1", {}, {}, columns, '.table_ld tbody', orderable_columns);

})

$('.lead_users').on('change', function() {
    if ($(this).val() != '') {
        searchData('GET', base_url + "/tenant/lead/list", {}, {}, columns, '.table_ld tbody', orderable_columns);
    }
});

$('input[name="e2"]').on('change', function() {
    if ($(this).val() != '') {
        searchData('GET', base_url + "/tenant/lead/list", {}, {}, columns, '.table_ld tbody', orderable_columns);
    }
})

$('select[name="lead_type_id"]').on('change', function() {
    if ($(this).val() != '') {
        $('select[name="template_id"]').selectpicker("val", "");
        searchData('GET', base_url + "/tenant/lead/list", {}, {}, columns, '.table_ld tbody', orderable_columns);
    } else {
        searchData('GET', base_url + "/tenant/lead/list", {}, {}, columns, '.table_ld tbody', orderable_columns);
    }
})


$('select[name="template_id"]').on('change', function() {
    if ($(this).val() != '') {
        var id = $(this).val();
        $('input[name="check-menus"]').click();
        var optionHTML = '';
        data = {
            is_all: 1
        };
        ajaxCall('GET', base_url + "/tenant/template/fields/?template_id=" + id, data).then(function(res) {
            if (res.code == 200) {
                var record = res.data;
                var theadHtML = '';
                var lead_type_columns = [];

                var new_arr = [];

                if (record.length > 0) {
                    theadHtML += '<input type="checkbox" id="checkAll" name="checkAll" class="select_all" style="position:relative;left:8px;top:5px;">';
                    theadHtML += '<td class="text-left">S.no</td>';
                    for (var i = 0; i < record.length; i++) {
                        var custom_fields = record[i].key_map.split(' ').join('_').split('/').join('_');
                        var custom_fields2 = record[i].key_map;
                        lead_type_columns.push(custom_fields2);
                        $('.' + custom_fields).show();
                        $("input[data-id='" + custom_fields + "']:checkbox").prop('checked', true);

                        //theadHtML += '<td class="column-title text-center  ' + record[i].key_map.split(' ').join('_').split('/').join('_') + '" id="' + record[i].key_map + '">' + record[i].key_map.split('_').join(' ') + ' <span class="sort sort_asc" style="cursor:pointer;" title="asc" data-column = "' + record[i].key_map + '"><i class="fas fa-sort-up" style="position:relative;left:10px;"></i></span> <span class="sort sort_desc" title="desc" style="margin-left:-1px;cursor:pointer;position:relative;top:4px;" data-column = "' + record[i].key_map + '" title="desc"><i class="fas fa-sort-down"></i></span></td>';
                        theadHtML += '<td class="column-title text-center  ' + record[i].key_map.split(' ').join('_') + '" id="' + record[i].key_map + '">' + record[i].key_map.split('_').join(' ') + ' <span class="sort sort_asc" style="cursor:pointer;" title="asc" data-column = "' + record[i].key_map + '"><i class="fas fa-sort-up" style="position:relative;left:10px;"></i></span> <span class="sort sort_desc" title="desc" style="margin-left:-1px;cursor:pointer;position:relative;top:4px;" data-column = "' + record[i].key_map + '" title="desc"><i class="fas fa-sort-down"></i></span></td>';

                    }
                    if (lead_type_columns.length > 0) {
                        lead_type_columns = lead_type_columns;

                    } else {
                        lead_type_columns = [];
                    }

                    $('.headings').html(theadHtML);
                    $('.headings #lead_name').html('Mobile App - Classification <span class="sort sort_asc" style="cursor:pointer;" title="asc" data-column = "title"><i class="fas fa-sort-up" style="position:relative;left:10px;"></i></span> <span class="sort sort_desc" title="desc" style="margin-left:-1px;cursor:pointer;position:relative;top:4px;" data-column = "title" title="desc"><i class="fas fa-sort-down"></i></span>');
                    searchData('GET', base_url + "/tenant/lead/list", {}, {}, lead_type_columns, '.table_ld tbody', orderable_columns);

                }
            }
        });

        //})

    } else {
        searchData('GET', base_url + "/tenant/lead/list", {}, {}, columns, '.table_ld tbody', orderable_columns);
    }

})

$(document).on('click', '.page-item', function() {
    var page_number = $(this).data('page_number');
    $('.page-number').val(page_number);
    searchData('GET', base_url + "/tenant/lead/list?page=" + page_number, {}, {}, columns, '.table_ld tbody', orderable_columns);
})

$('.select-all').click(function() {
    $('input[name="status_id"]').prop("checked", true);
    var status_ids = $('input[name="status_id"]:checked').map(function() {
        return this.value;
    }).get();

    if (status_ids.length > 0) {
        status_ids = status_ids.join(',');
    } else {
        status_ids = ''
    }
    var unchecked_ids = $('input[name="status_id"]:not(:checked)').map(function() {
        return this.value;
    }).get();
    var filtered = unchecked_ids.filter(function(el) {
        return el != "";
    });
    data2 = {
        status_ids: status_ids
    };
    
      //searchData('GET', base_url + "/tenant/lead/list", data2, {}, columns, '.table_ld tbody', orderable_columns);
      
      //loadGridWitoutAjax('GET', base_url + "/tenant/lead/list", data2, {}, columns, '.table_ld tbody', orderable_columns);
   
    ajaxCall('GET', base_url + "/tenant/lead/status/list", data2, {}).then(function(res) {
        var record = res.data;
        for (var i = 0; i < record.length; i++) {
            var new_color = record[i].color_code;
            $(".lead_percentage_" + record[i].id).html(record[i].lead_percentage + '%');
        }
        $('.search').val("");
        $('.selectpicker').selectpicker("val", "");
        var dateRange = $("#e2").daterangepicker("clearRange");

    });
     return false
})

$('.deselect-all').on('click',function() {
    $('input[name="status_id"]:checked').prop("checked", false);
    var status_ids = $('input[name="status_id"]:checked').map(function() {
        return this.value;
    }).get();

    if (status_ids.length > 0) {
        status_ids = status_ids.join(',');
    } else {
        status_ids = ''
    }
    var unchecked_ids = $('input[name="status_id"]:not(:checked)').map(function() {
        return this.value;
    }).get();
    var filtered = unchecked_ids.filter(function(el) {
        return el != "";
    });
    data2 = {
        status_ids: status_ids
    };
    //loadGridWitoutAjax('GET', base_url + "/tenant/lead/list", data2, {}, columns, '.table_ld tbody', orderable_columns);
    
    ajaxCall('GET', base_url + "/tenant/lead/status/list", data2, {}).then(function(res) {
        var record = res.data;
        for (var i = 0; i < record.length; i++) {
            var new_color = record[i].color_code;
            $(".lead_percentage_" + record[i].id).html(record[i].lead_percentage + '%');
        }
        $('.search').val("");
        $('.selectpicker').selectpicker("val", "");
        var dateRange = $("#e2").daterangepicker("clearRange");

    });
    return false;
  
})

$(document).on('click', 'input[name="status_id"]', function() {

    var theID = $(this).attr('id');

    var arr = jQuery.makeArray(theID);

    var status_ids = $('input[name="status_id"]:checked').map(function() {
        return this.value;
    }).get();

    if (status_ids.length > 0) {
        status_ids = status_ids.join(',');
    } else {
        status_ids = ''

    }

    var unchecked_ids = $('input[name="status_id"]:not(:checked)').map(function() {
        return this.value;
    }).get();
    var filtered = unchecked_ids.filter(function(el) {
        return el != "";
    });


    //searchData('GET', base_url + "/tenant/lead/list", {}, {}, columns, '.table_ld tbody');


    data2 = {
        status_ids: status_ids
    };

    // loadGridWitoutAjax('GET', base_url + "/tenant/lead/status/list", data2, {}, ['test_title', 'lead_percentage', 'lead_count'], '.status_table', '', false);
    

    ajaxCall('GET', base_url + "/tenant/lead/status/list", data2, {}).then(function(res) {
        var record = res.data;
        for (var i = 0; i < record.length; i++) {

            var new_color = record[i].color_code;
            $(".lead_percentage_" + record[i].id).html(record[i].lead_percentage + '%');
        }
        $('.search').val("");
        $('.selectpicker').selectpicker("val", "");
        var dateRange = $("#e2").daterangepicker("clearRange");

    })

});


$(document).on('click', '#export-btn', function() {
    
    var user_ids_arr = $('.lead_users').selectpicker('val');
    var order_type = $('.title').val();
    var order_by = $('.column_name').val();
    if (user_ids_arr === null) {
        var user_ids = '';
    } else {
        var user_ids = user_ids_arr.join(',');
    }

    var type_ids_arr = $('[name="lead_type_id"]').val();

    if (type_ids_arr === null) {
        var lead_type_id = '';
    } else {
        var lead_type_id = type_ids_arr;
    }

    var lead_ids = $('input[name="lead_ids"]:checked').map(function() {
        return this.value;
    }).get();


    if (lead_ids.length > 0) {
        lead_ids = lead_ids.join(',');
    } else {
        lead_ids = ''

    }

    var search = $('.search').val();

    var status_ids = $('input[name="status_id"]:checked').map(function() {
        return this.value;
    }).get();

    if (status_ids.length > 0) {
        status_ids = status_ids.join(',');
    } else {
        status_ids = ''
    }

    var menu_names = $('input[name="check-menus"]:checked').map(function() {
        return this.value;
    }).get();

    if (menu_names.length > 0) {
        menu_names = menu_names.join(',');

    } else {
        menu_names = ''
    }

    var ignore_column_names = $('input[name="check-menus"]:not(:checked)').map(function() {
        return this.value;
    }).get();

    if (ignore_column_names.length > 0) {
        ignore_column_names = ignore_column_names.join(',');
    } else {
        ignore_column_names = '';
    }

    var dateRange = $("#e2").daterangepicker("getRange");

    if (dateRange === null) {
        var start_date = '';
        var end_date = '';
    } else {
        var start_date = $.datepicker.formatDate('yy-m-d', new Date(dateRange.start));
        var end_date = $.datepicker.formatDate('yy-m-d', new Date(dateRange.end))
    }

    var template_ids = $('[name="template_id"]').val();

    if (template_ids === null) {
        var template_ids = '';
    } else {
        var template_id = template_ids;
    }
    var export_pagination_size = "{{config('constants.EXPORT_PAGE_SIZE')}}";
    var lead_management_total_record = api_response_collection['lead_management'];
    var no_page = lead_management_total_record / export_pagination_size;
    var round_page_no = Math.ceil(no_page);
    
    if (lead_ids.length == 0) {
        
        if(export_in_chunk == true) {
            var total;
        data = {
            search: search,
            user_ids: user_ids,
            start_date: start_date,
            end_date: end_date,
            status_ids: status_ids,
            lead_ids: lead_ids,
            lead_type_id: lead_type_id,
            template_id: template_id,
            menu_names: menu_names,
            ignore_column_names: ignore_column_names,
            order_by: order_by,
            order_type: order_type,
            export: true,
            is_download: 0
        };
        
        for (var i = 1; i <round_page_no; i++) 
        {
            data.page = i;
            total = i;
            ajaxCall('GET', base_url + "/tenant/lead/list", data, {}, async = false);
        }
        
        if(round_page_no == 1)
        {
            data.page = 1;
             
        }
       else{
            data.page = total + 1;

        }
        
        data.is_download = 1;
        
        
         var qString = $.param(data);
                var url = "{{URL::to('tenant/lead/list?')}}"+qString;
         window.open(url,'_self')
        }
        
        else {
            //$("#loader").show();
            data = {
            search: search,
            user_ids: user_ids,
            start_date: start_date,
            end_date: end_date,
            status_ids: status_ids,
            lead_ids: lead_ids,
            lead_type_id: lead_type_id,
            template_id: template_id,
            menu_names: menu_names,
            ignore_column_names: ignore_column_names,
            order_by: order_by,
            order_type: order_type,
            export: true

        };

            sendData(data)
            
        }
    } 
    
    else 
    {
        data = {
            search: search,
            user_ids: user_ids,
            start_date: start_date,
            end_date: end_date,
            status_ids: status_ids,
            lead_ids: lead_ids,
            lead_type_id: lead_type_id,
            template_id: template_id,
            menu_names: menu_names,
            ignore_column_names: ignore_column_names,
            order_by: order_by,
            order_type: order_type,
            export: true

        };
      
        sendData(data);
    }
});

$(document).on('click', '#export-history', function() {
    
    
    var user_ids_arr = $('.lead_users').selectpicker('val');
    var order_type = $('.title').val();
    var order_by = $('.column_name').val();
    if (user_ids_arr === null) {
        var user_ids = '';
    } else {
        var user_ids = user_ids_arr.join(',');
    }

    var type_ids_arr = $('[name="lead_type_id"]').val();

    if (type_ids_arr === null) {
        var lead_type_id = '';
    } else {
        var lead_type_id = type_ids_arr;
    }

    var lead_ids = $('input[name="lead_ids"]:checked').map(function() {
        return this.value;
    }).get();


    if (lead_ids.length > 0) {
        lead_ids = lead_ids.join(',');
    } else {
        lead_ids = ''

    }

    var search = $('.search').val();

    var status_ids = $('input[name="status_id"]:checked').map(function() {
        return this.value;
    }).get();

    if (status_ids.length > 0) {
        status_ids = status_ids.join(',');
    } else {
        status_ids = ''
    }

    var menu_names = $('input[name="check-menus"]:checked').map(function() {
        return this.value;
    }).get();

    if (menu_names.length > 0) {
        menu_names = menu_names.join(',');

    } else {
        menu_names = ''
    }

    var ignore_column_names = $('input[name="check-menus"]:not(:checked)').map(function() {
        return this.value;
    }).get();

    if (ignore_column_names.length > 0) {
        ignore_column_names = ignore_column_names.join(',');
    } else {
        ignore_column_names = '';
    }

    var dateRange = $("#e2").daterangepicker("getRange");

    if (dateRange === null) {
        var start_date = '';
        var end_date = '';
    } else {
        var start_date = $.datepicker.formatDate('yy-m-d', new Date(dateRange.start));
        var end_date = $.datepicker.formatDate('yy-m-d', new Date(dateRange.end))
    }

    var template_ids = $('[name="template_id"]').val();

    if (template_ids === null) {
        var template_ids = '';
    } else {
        var template_id = template_ids;
    }
  
        data = {
            search: search,
            user_ids: user_ids,
            start_date: start_date,
            end_date: end_date,
            status_ids: status_ids,
            lead_ids: lead_ids,
            lead_type_id: lead_type_id,
            template_id: template_id,
            menu_names: menu_names,
            ignore_column_names: ignore_column_names,
            order_by: order_by,
            order_type: order_type,
            export: true,
            is_history_export: 1
        };
        
        
         var qString = $.param(data);
                var url = "{{URL::to('tenant/leads/history/export?')}}"+qString;
         window.open(url,'_self')
        
    
});

function sendData(data = {}){
    
        var qString = $.param(data);
        var url = "{{URL::to('tenant/lead/list?')}}" + qString;
        window.open(url, '_self');
}

$(document).on('click', '.check_update', function() {
    var agent_id = $('.modal_agent').selectpicker('val');
    var status_id = $('.modal_status').selectpicker('val');
    var is_expired = $('.is_expired').selectpicker('val');
    var action = 'update';
    var lead_ids = $('input[name="lead_ids"]:checked').map(function() {
        return this.value;
    }).get();
    if (lead_ids.length > 0) {
        lead_ids = lead_ids.join(',');
    } else {
        lead_ids = ''

    }
    data = {
        lead_ids: lead_ids,
        assign_id: agent_id,
        status_id: status_id,
        is_expired: is_expired,
        action: action
    };
    ajaxCall('POST', base_url + "/tenant/lead/bulk/update", data, {}).then(function(res) {
        if (res.code == 200) {
            //location.reload();
            $('.ui-dialog').hide();
            $('.show_all').hide();
            $('input[name="lead_ids"]').attr('checked', false);
            var newDiv = '';
            newDiv += '<div class="col-md-4 text-right show_all" style=""></div>';
            $('.new_div').html(newDiv);
            var page_number = $('.page-number').val();
            $('.page-number').val(page_number)
            searchData('GET', base_url + "/tenant/lead/list?page=" + page_number, {}, {}, columns, '.table_ld tbody', orderable_columns);

            success_html = '<li>' + res.message + '</li>';
            $('.success').html(success_html);
            $('.success').show();
            setTimeout(function() {
                $(".success").hide('fade', {}, 1000)
            }, 2000);
            //location.reload();
        } else {

            let error_html = '';

            var messages = res.data[0];
            for (message in messages) {
                error_html += '<li>' + messages[message] + '</li>';
            }
            $('.error').html(error_html);

            $('.error').show();
        }
    })
    $('.bd-example-modal-lg').modal('hide');
});

$(document).on('click', '.check_delete', function() {
    var choice = confirm('Do you really want to delete?');
    var action = 'delete';
    var lead_ids = $('input[name="lead_ids"]:checked').map(function() {
        return this.value;
    }).get();

    if (lead_ids.length > 0) {
        lead_ids = lead_ids.join(',');
    } else {
        lead_ids = ''

    }
    if (choice === true) {
        data = {
            lead_ids: lead_ids,
            action: action
        };
        ajaxCall('POST', base_url + "/tenant/lead/bulk/update", data, {}).then(function(res) {
            //location.reload();
            $('.ui-dialog').hide();
            var newDiv = '';
            newDiv += '<div class="col-md-4 text-right show_all" style=""></div>';
            $('.new_div').html(newDiv);

        })

        var page_number = $('.page-number').val();
        searchData('GET', base_url + "/tenant/lead/list?page=" + page_number, {}, {}, columns, '.table_ld tbody', orderable_columns);
    }
    return false;
})

loadGridWitoutAjax('GET', base_url + "/tenant/lead/status/list", {}, {}, ['test_title', 'lead_percentage', 'lead_count'], '.status_table', '', false);

})


function searchData(method, url, data, header, columns, element, orderable_columns) {
    var order_type = $('.title').val();
    var order_by = $('.column_name').val();
    var user_ids_arr = $('.lead_users').selectpicker('val');
    if (user_ids_arr === null) {
        var user_ids = '';
    } else {
        var user_ids = user_ids_arr.join(',');
    }

    var type_ids_arr = $('[name="lead_type_id"]').val();

    if (type_ids_arr === null) {
        var lead_type_id = '';
    } else {
        var lead_type_id = type_ids_arr;
    }


    var exportVal = $('#export-btn').data('export');

    var search = $('.search').val();

    var status_ids = $('input[name="status_id"]:checked').map(function() {
        return this.value;
    }).get();

    if (status_ids.length > 0) {
        status_ids = status_ids.join(',');
    } else {
        status_ids = ''

    }

    var dateRange = $("#e2").daterangepicker("getRange");

    if (dateRange === null) {
        var start_date = '';
        var end_date = '';
    } else {
        var start_date = $.datepicker.formatDate('yy-m-d', new Date(dateRange.start));
        var end_date = $.datepicker.formatDate('yy-m-d', new Date(dateRange.end))
    }

    var template_ids = $('[name="template_id"]').val();

    if (template_ids === null) {
        var template_ids = '';
    } else {
        var template_id = template_ids;
    }

    data = {
        search: search,
        user_ids: user_ids,
        start_date: start_date,
        end_date: end_date,
        status_ids: status_ids,
        lead_type_id: lead_type_id,
        order_by: order_by,
        order_type: order_type,
        export: exportVal
    };
    
    var recursiveEncoded = $.param( data );
    var recursiveDecoded = decodeURIComponent( $.param( data ) );
    
    var lead_map_url = $('a[class=lead_map_url]').attr('href');
    lead_map_url = lead_map_url + recursiveEncoded;

    $('a[class=lead_map_url]').attr('href',lead_map_url)
    
    return loadData(method, url, data, header, columns, element, orderable_columns);

}


function loadData(method, url, data, header, columns, element, orderable_columns) {
   
    var theadHtML = '';
    var optionHTML = '';
    var image_url_asc = '{{asset("assets/images/asc.png")}}';
    var image_url_desc = '{{asset("assets/images/desc.png")}}';
    columns.push('foreclosure_date');
    columns.push('admin_notes');
    columns.push('updated_by');
    columns.push('created_at');
    columns.push('updated_at');
    var myNewArray = columns.filter(function(elem, index, self) {
      return index === self.indexOf(elem);
  });

    if (myNewArray.length > 0) 
    {
        
        console.log("myNewArray",myNewArray)
        
        
        theadHtML += '<input type="checkbox" id="checkAll" name="checkAll" class="select_all" style="position:relative;left:8px;top:5px;">';
        theadHtML += '<td class="text-left">S.no</td>';
        
    
        
        if(localStorage.getItem("myNewArray")  != null){
            localArray = JSON.parse(localStorage.getItem("myNewArray"));
        }
        else{
            localArray = [];
        }
            
            
        for (var index = 0; index < myNewArray.length; index++) 
        {
          
             if(localArray.indexOf(myNewArray[index]) < 0){
                  theadHtML += '<td class="column-title text-center show-th ' + myNewArray[index].split(' ').join('_') + '" id="' + myNewArray[index].split(' ').join('_').split('/').join('_') + '">' + myNewArray[index].split('_').join(' ') + ' <span class="sort sort_asc" style="cursor:pointer;" title="asc" data-column = "' + myNewArray[index] + '"><i class="fas fa-sort-up" style="position:relative;left:10px;"></i></span> <span class="sort sort_desc" title="desc" style="margin-left:-1px;cursor:pointer;position:relative;top:4px;" data-column = "' + myNewArray[index] + '" title="desc"><i class="fas fa-sort-down"></i></span></td>';
                  optionHTML += '<li class="">';
           optionHTML += '<div class="checkbox"><label><input value="' + myNewArray[index] + '" type="checkbox" name="check-menus" data-id="' + myNewArray[index].split(' ').join('_').split('/').join('_') + '" class="hide_show_table" checked>'
            optionHTML += '<span  style="font-size:14px;">' + myNewArray[index].split(' ').join('_') + '</span></label></div>';
            optionHTML += '</li>';
                
            }else{
                   theadHtML += '<td class="column-title text-center hide-th ' + myNewArray[index].split(' ').join('_') + '" id="' + myNewArray[index].split(' ').join('_').split('/').join('_') + '">' + myNewArray[index].split('_').join(' ') + ' <span class="sort sort_asc" style="cursor:pointer;" title="asc" data-column = "' + myNewArray[index] + '"><i class="fas fa-sort-up" style="position:relative;left:10px;"></i></span> <span class="sort sort_desc" title="desc" style="margin-left:-1px;cursor:pointer;position:relative;top:4px;" data-column = "' + myNewArray[index] + '" title="desc"><i class="fas fa-sort-down"></i></span></td>';
                   optionHTML += '<li class="">';
           optionHTML += '<div class="checkbox"><label><input value="' + myNewArray[index] + '" type="checkbox" name="check-menus" data-id="' + myNewArray[index].split(' ').join('_').split('/').join('_') + '" class="hide_show_table">'
             optionHTML += '<span style="font-size:14px;">' + myNewArray[index].split(' ').join('_') + '</span></label></div>';
             optionHTML += '</li>';
            }
           
        }
    }
    $('.headings').html(theadHtML);
    var final = myNewArray.filter(function(item) {
        return !orderable_columns.includes(item.split('.')[0]);
    })

    for (var order = 0; order < final.length; order++) {
        if (final.length > 0) {
            //var unselected = final[order].split(' ').join('_').split('/').join('_');
            var unselected = final[order].split(' ').join('_');
            $('.' + unselected).addClass('HideArrow');
        } else {
            var newClass = '';
        }
    }
    
    $('.headings #title').html('{{config("constants.LEAD_TITLE_DISPLAY")}} <span class="sort sort_asc" style="cursor:pointer;" title="asc" data-column = "title"><i class="fas fa-sort-up" style="position:relative;left:10px;"></i></span> <span class="sort sort_desc" title="desc" style="margin-left:-1px;cursor:pointer;position:relative;top:4px;" data-column = "title" title="desc"><i class="fas fa-sort-down"></i></span>');
    $('.check-menu').html(optionHTML);
    $('.check-menu .title1').html("{{config('constants.LEAD_TITLE_DISPLAY')}}");
    
    $(document).on('click','.hide_show_table',function(){
        var value = $(this).val();
        var class_name = $(this).data('id'); 
        if($(this).prop('checked') == false){
            localArray.push(value)
            $(document).find('.' + class_name).hide()        
            localStorage.setItem("myNewArray", JSON.stringify(localArray));
}
else{
    localArray.splice(localArray.indexOf(value), 1);
    localStorage.setItem("myNewArray", JSON.stringify(localArray));
    $(document).find('.' + class_name).show();
}
       
    })

    return loadGridWitoutAjax(method, url, data, header, myNewArray, element, '', true, true, true, '', true, 'lead_management');

}
</script>
@include('tenant.include.footer')
<!--footer>