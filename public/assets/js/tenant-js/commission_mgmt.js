$(document).ready(function() {
    $('.add-link').click(function() {
        var url = $(this).data('href')
        window.location.href = url;
    })
    // Date Range Picker Function
    $("#e2").daterangepicker({
        datepickerOptions: {
            numberOfMonths: 2,
            maxDate: null
        }
    });


    //API Call for Listing
    var columns = ['user_name', 'lead_title', 'commission_event', 'commission', 'month'];
    ajaxDatatable('#example', base_url + "/tenant/user/commission/list", 25, columns);

    $('.clear_button').click(function() {

        console.log("here");
        $('.agent_ids').val('');
        $('.start').val('');
        $('.end').val('');
        table.ajax.reload();
    
    })

    //Getting Dates
    $('.date_range').on('change', function() {


        if ($(this).val() != '') {

            var dateRange = $("#e2").daterangepicker("getRange");
            if (dateRange === null) {
                var start_date = '';
                var end_date = '';
            } else {
                var start_date = $.datepicker.formatDate('yy-m-d', new Date(dateRange.start));
                var end_date = $.datepicker.formatDate('yy-m-d', new Date(dateRange.end));

            }
            $('.start').val(start_date);
            $('.end').val(end_date);

            var data = {
                start_date: start_date,
                end_date: end_date,
            };
            table.ajax.reload();
            //loadGridWitoutAjax('GET', base_url + "/tenant/user/commission/list", data, {}, columns, 'tbody.pag', '', true, false, true, '', indexing = false);

        }
    })

    //Getting Agents IDS
    $('.agents').on('change', function() {
        if ($(this).val() != '') {
            var user_ids_arr = $('.agents').selectpicker('val');
            if (user_ids_arr === null) {
                var user_ids = '';
            } else {
                var user_ids = user_ids_arr.join(',');
            }
            var data = {
                agent_ids: user_ids,
            };
            $('.agent_ids').val(user_ids);
            table.ajax.reload();
            //var qString = $.param(data);
            //ajaxDatatable('#example', base_url + "/tenant/user/commission/list?"+qString, 10, columns);
            //loadGridWitoutAjax('GET', base_url + "/tenant/user/commission/list", data, {}, columns, 'tbody.pag', '', true, false, true, '', indexing = false);
        }
    })

    //Getting Commission Events IDS
    $('.commissions').on('change', function() {
        if ($(this).val() != '') {
            var commission_events_arr = $('.commissions').selectpicker('val');
            if (commission_events_arr === null) {
                var comm_ids = '';
            } else {
                var comm_ids = commission_events_arr.join(',');
            }
            var data = {
                commission_events: comm_ids,
            };
            $('.commission_events').val(comm_ids);
            table.ajax.reload();
            //loadGridWitoutAjax('GET', base_url + "/tenant/user/commission/list", data, {}, columns, 'tbody.pag', '', true, false, true, '', indexing = false);
        }
    })


    //Sort Function
    $('.sort').click(function() {

        var title = $(this).attr('title');
        var column_name = $(this).data('column');
        $('.title').val(title);
        $('.column_name').val(column_name);

        // if(title == 'asc'){
        //      $(this).hide(); 
        //      $('.sort[title="desc"]').show(); 
        // }else
        // {
        //    $(this).hide(); 
        //    $('.sort[title="asc"]').show();      
        // }
        data = {
            order_by: column_name,
            order_type: title
        }
        table.ajax.reload();
        //loadGridWitoutAjax('GET', base_url + "/tenant/user/commission/list", data, {}, columns, 'tbody .pag', '', true, false, true, '', indexing = false);
    })

    // Filter Toggle
    $(".comm-toggle").click(function() {
        $(".filter-show").fadeToggle();
    })

    //Export Function
$(document).on('click','#export-btn',function() {

    var commission_events_arr = $('.commissions').selectpicker('val');
    if (commission_events_arr === null) {
        var comm_ids = '';
    } else {
        var comm_ids = commission_events_arr.join(',');
    }

    var user_ids_arr = $('.agents').selectpicker('val');
    if (user_ids_arr === null) {
        var user_ids = '';
    } else {
        var user_ids = user_ids_arr.join(',');
    }

    var dateRange = $("#e2").daterangepicker("getRange");
    if (dateRange === null) {
        var start_date = '';
        var end_date = '';
    } else {
        var start_date = $.datepicker.formatDate('yy-m-d', new Date(dateRange.start));
        var end_date = $.datepicker.formatDate('yy-m-d', new Date(dateRange.end));

    }

    var data = {
        commission_events: comm_ids,
        agent_ids: user_ids,
        start_date: start_date,
        end_date: end_date,
    };
    var qString = $.param(data);
    var url = base_url + "/tenant/user/commission/export?"+qString;
    window.open(url, '_self');
    return false;
    console.log("data",url);

})

    
});

