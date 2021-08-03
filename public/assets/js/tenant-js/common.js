var table;
var api_response_collection = [];

$.ajaxSetup({
    headers: {

        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});


window.onload = function() {


    // Select your input element.
    var number = document.getElementById('input-number');

    // Listen for input event on numInput.
    number.onkeydown = function(e) {
        if (!((e.keyCode > 95 && e.keyCode < 106) ||
                (e.keyCode > 47 && e.keyCode < 58) ||
                e.keyCode == 8)) {
            return false;
        }
    }

}


$(document).ready(function() {

    $('button, .ajax-button, .submit').addClass('b1').removeClass('b2');
    $('.btn-info').addClass('b1').removeClass('add-bt');
    $('.select-all, .deselect-all').removeClass('b1');

    $('form').submit(function(e) {

        var redirect_url = $('.redirect_url').val();
        redirect_url = typeof redirect_url == 'undefined' ? window.location.href : redirect_url;
        e.preventDefault();
        var formData = new FormData(this);
        $.ajax({
            type: "POST",
            url: $('.submit_url').val(),
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function() {
                $(this).attr('disabled', 'disabled');
                $('.clear').attr('disabled', 'disabled');
                $('.add_field').attr('disabled', 'disabled');
                $('.btn-circle').attr('disabled', 'disabled');
                $('.import_wizard').attr('disabled', 'disabled');

                $('.btn-circle').off('click');
                $('.error').hide();
                $("#loader").show();

                $(".ajax-button").prop('disabled', true);

            },
            success: function(res) {

                $("#loader").hide();
                // $("input, select, textarea, button").prop("disabled", false);

                $(this).removeAttr('disabled');
                if (res.code == 200) {
                    $("#loader").hide();
                    success_html = '<li>' + res.message + '</li>';
                    $('.success').html(success_html);
                    $('.success').show();
                    $(".ajax-button").prop('disabled', true);
                    setTimeout(function() {
                        window.location.href = redirect_url;
                    }, 1000)

                } else {
                    $("#loader").hide();
                    $(".ajax-button").prop('disabled', false);
                    let error_html = '';

                    var messages = res.data[0];
                    for (message in messages) {
                        error_html += '<li>' + messages[message] + '</li>';
                    }
                    $('.error').html(error_html);

                    $('.error').show();
                    $('.add_field').removeAttr('disabled');

                    $('.import_wizard').removeAttr('disabled');
                    $('.clear').removeAttr('disabled');
                }
            }
        });

    })

    $(document).on('click', '.link', function() {
        var id = $(this).attr('id');
        var data = {
            id: id
        };
        ajaxCall('POST', base_url + "/tenant/agent/reset/" + id, data).then(function(res) {
            if (res.code == 200) {

                success_html = '<li>' + res.message + '</li>';
                $('.success').html(success_html);
                $('.success').show();
                setTimeout(function() {
                    $(".success").hide('blind', {}, 500)
                }, 2000);
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

        return false;
    })


    $(document).on('click', '#redirect2', function(e) {

        if (window.event.target.name == 'lead_ids') {

        } else {

            var id = $(this).closest('tr').find('input[type="checkbox"]').val();

            var getUrl = window.location + '/edit/' + id;

            // var redirect_url = getUrl .protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[1]+'/lead/edit/'+id;
            let redirect_url = getUrl;
            window.location.href = redirect_url;
        }

    })

    //redirect edit page
    $(document).on('click', '.redirect', function(e) {


        e.preventDefault();
        let redirect_url = $(this).data('href');


        window.location.href = redirect_url;



    });

    //delete media script
    $(document).on('click', '._delete_media', function() {

        var msg = confirm("Are you sure you want to continue?");
        if (msg) {
            var mediaID = $(this).data('mediaid');
            var get_all_media_id = $('.delete_media').val();
            if (get_all_media_id == '') {
                $('.delete_media').val(mediaID);
            } else {
                $('.delete_media').val(get_all_media_id + ',' + mediaID);
            }
            $(this).parent().hide('slow');
        } else {
            return false
        }
    })

});

function getEditRecord(method, url, data = {}, headers = {}, columns = [], element = "tbody") {
    ajaxCall(method, url, data = {}, headers = {}).then(function(res) {

        if (res.code == 200) {
            let record = res.data;

            for (var c = 0; c < columns.length; c++) {

                if (columns[c] == 'media') {

                    var ulHtml = '<ul class="navbar-nav cust-nav" style="padding-left:0px;">';
                    if (record[columns[c]].length > 0) {
                        for (var i = 0; i < record[columns[c]].length; i++) {
                            ulHtml += '<li>';
                            if (record[columns[c]][i].media_type != "image") {

                                ulHtml += '<a href="' + record[columns[c]][i].path + '" target="_blank"><img src="' + record[columns[c]][i].thumb + '" style="width: 100px;height: 100px;" class="image-url"></a>';
                                ulHtml += '<a data-mediaId="' + record[columns[c]][i].id + '" class="btn cross _delete_media" style="color:black !important;font-size: 20px;">x</a>';
                                ulHtml += '</li>';
                            } else {
                                ulHtml += '<img src="' + record[columns[c]][i].thumb + '" style="width: 100px;height: 100px;" class="image-url">';
                                ulHtml += '<a data-mediaId="' + record[columns[c]][i].id + '" class="btn cross _delete_media" style="color:black !important;font-size: 20px;">x</a>';
                                ulHtml += '</li>';
                            }
                        }
                        $('.view_image').html(ulHtml);
                        console.log("ulHtml", ulHtml);
                    }
                    ulHtml += '</ul>';
                } else if (columns[c] == 'image_url') {
                    var ulHtml = '<ul class="navbar-nav" style="float:none;">';
                    ulHtml += '<img src="' + record[columns[c]] + '" style="border-radius:50%;width:200px;height:200px;margin: 0 auto;" class="img-responsive">';
                    ulHtml += '<a data-mediaId="' + record[columns[c]].id + '" class="btn cross _delete_media" style="color:black !important;font-size: 20px;">x</a>';
                    ulHtml += '</li>';
                    $('.view_image').html(ulHtml);
                    ulHtml += '</ul>';

                } else if (columns[c] == 'nav_user_name') {

                    var new_user_image = record.image_url;
                    var new_name = record.name;
                    var aHtml = '';
                    aHtml += '<a href="javascript:;" class="user-profile dropdown-toggle" data-toggle="dropdown" aria-expanded="false"> <img src="' + new_user_image + '" alt="">' + new_name + '<span class=" fa fa-angle-down" style="display:inline-block;padding-left:10px;"></span> </a>';
                    $('.view_user').prepend(aHtml);


                } else if (columns[c] == 'comm_target_id') {
                    $('.target_id').val(record.user_id);

                } else if (columns[c] == 'old_status') {
                    var old_status_id = record.status.id;
                    $('select[name="status_id"] option[value="' + old_status_id + '"]').attr('selected', 'selected')

                    $('.selectpicker').selectpicker('refresh')
                } else if (columns[c] == 'is_expired') {
                    var is_expired = record.is_expired;
                    $('select[name="is_expired"] option[value="' + is_expired + '"]').attr('selected', 'selected')

                    $('.selectpicker').selectpicker('refresh')
                } else if (columns[c] == 'old_type') {
                    var old_type_id = record.type.id;

                    $('select[name="type_id"] option[value="' + old_type_id + '"]').attr('selected', 'selected')

                    $('.selectpicker').selectpicker('refresh')
                } else if (columns[c] == 'assignee') {
                    var old_assignee_id = record.assignee.id;
                    $('select[name="target_id"] option[value="' + old_assignee_id + '"]').attr('selected', 'selected')

                    $('.selectpicker').selectpicker('refresh')
                } else if (columns[c] == 'user_status_id') {


                    var user_status_id = record.user_status_id;
                    $('select[name="user_status_id"] option[value="' + user_status_id + '"]').attr('selected', 'selected')

                    $('.selectpicker').selectpicker('refresh')
                } else if (columns[c] == 'user_group_id') {
                    var new_user_type = record.user_group_id;

                    $('select[name="user_group_id"] option[value="' + new_user_type + '"]').attr('selected', 'selected');
                    $('.selectpicker').selectpicker('refresh');
                } else if (columns[c] == 'template_title') {
                    var new_template_title = record[c].title;


                    $('[name="' + columns[c] + '"]').val(new_template_title);
                } else if (columns[c] == 'new_commission_event') {
                    var new_comm_title = record.commission_event;

                    $('select option[value="' + new_comm_title + '"]').attr('selected', 'selected');
                    $('.selectpicker').selectpicker('refresh');
                }

                // else if(columns[c] == 'month')
                // {
                //     var new_month = record.month;
                //     console.log("hey", new_month);

                //     $('[name="' + columns[c] + '"]').val(new_month);
                // }
                else {


                    $('[name="' + columns[c] + '"]').val(record[columns[c]]);
                }

            }
        } else {
            alert(res.message);
        }
    });
}

function ajaxCall(method, url, data = {}, headers = {}, async = true) {

    return new Promise(function(resolve, reject) {
        $.ajax({
            type: method,
            url: url,
            data: data,
            headers: headers,
            async: async,
        beforeSend: function() {
        
              
            },
            success: function(res) {
                 
                if (res.code >= 200 || res.code <= 300) {
                    resolve(res);

                } else {
                    console.log("res",res);
                    let error_html = '';
                    var messages = res.data[0];
                    for (message in messages) {
                        error_html += '<li>' + messages[message] + '</li>';

                    }
                    $('.error').html(error_html);
                    $('.error').show();
                    $(".delete").prop('disabled', true);
                }


            }
        });
    })
}

function loadSelectBox(method, url, params = {}, headers = {}, columns = []) {
    var options_html = '';
    ajaxCall(method, url, params, headers).then(function(res) {
        if (res.code == 200) {
            var record = res.data;

            if (record.length > 0) {
                for (var i = 0; i < record.length; i++) {
                    var new_count = record[i].lead_count;
                    var new_color = record[i].color_code;
                    var new_id = record[i].id;
                    var lead_per = record[i].lead_percentage;
                    options_html += '<option value="' + record[i].id + '">' + record[i].title + ' ' + lead_per + '% <span class="unicode">' + new_count + '</span></option>';
                }
                $('[name="lead_type_status"]').append(options_html);
                $('.selectpicker').selectpicker('refresh')
            }
        }
    });
}

// ajax call
function loadGridWitoutAjax(method, url, params = {}, headers = {}, columns = [], element = 'tbody', readData = '', redirect = true, pagination = false, check = true, filtered, indexing = true, api_resp = '') {
console.log("api_page_size",page_size);
    return new Promise(function(resolve, reject) {
        ajaxCall(method, url, params, headers).then(function(res) {

            if (res.code == 200) {
                if(api_resp == "lead_management" ){
                    var newLocalArray = [];
                    $('.setting-dropdown').removeAttr("disabled");
                    
                }
                var totalRecord = res.recordsTotal;
                if(api_resp){
                    api_response_collection[api_resp] = totalRecord;                    
                }
                //console.log("totalRecord",totalRecord);
                var tbodyHtml = '';
                if (readData == '') {
                    var record = res.data;

                } else {
                    var record = res.data[readData];

                }


                if (record.length > 0) {
                    
                        if(localStorage.getItem("myNewArray")  != null)
                        {
                            var localArray = JSON.parse(localStorage.getItem("myNewArray"));
                        }
                                    
                        else
                        {
                            var localArray = [];
                        }

                    if (pagination == false) {
                        var index = 1;
                    } else {
                        var pagination_meta = res.meta;

                        
                        var index = ((page_size * (pagination_meta.current_page - 1)) + 1);

                        $('#checkAll').click(function() {


                            if ($(this).is(':checked')) {
                                $(".chkboxes").prop("checked", true);
                                $('.show_all').css('display', 'inline-block');
                                $('.setting').addClass('col-md-2').removeClass('col-md-6');

                                //         $("#txtAge").dialog({
                                //             close: function() {
                                //                 $('.chkboxes').prop('checked', false);
                                //                 $('#checkAll').prop('checked', false);
                                //                 $('.show_all').hide();
                                var newDiv = '';
                                newDiv += '<div class="col-md-4 text-right show_all" style=""></div>';
                                $('.new_div').html(newDiv);

                                //         });

                            } else {
                                $(".show_all").hide();
                                $('.setting').removeClass('col-md-2');
                                $(".chkboxes").prop("checked", false);
                            }

                        });


                    }

                    for (var i = 0; i < record.length; i++) {


                        if (redirect == true) {


                            tbodyHtml += '<tr class="redirect" data-href="' + window.location.href + '/edit/' + record[i].id + '" id="' + record[i].id + '">';
                        } else {


                            tbodyHtml += '<tr id="' + record[i].id + '">';

                        }

                        if (pagination == true) {

                            var lead_id = res.data[i].id;
                            var checkbox = '<input type="checkbox" class="chkboxes abc"  id="checkbox' + lead_id + '" name="lead_ids" value="' + lead_id + '">';

                            tbodyHtml += '<tr id="redirect2">';
                            tbodyHtml += '<td>' + checkbox + '</td>';

                            $(document).on('click', '.abc', function(e) {

                                if ($(this).is(':checked')) {


                                    $('.show_all').css('display', 'inline-block');

                                }

                                if ($('.abc:checked').length == 0) {
                                    $('.show_all').hide();
                                    $("#txtAge").dialog('close');


                                }

                            })

                        }

                        if (indexing == true) {
                            tbodyHtml += '<td>' + index + '</td>';
                        }
                        

                        for (var c = 0; c < columns.length; c++) {
                            
                            if(api_resp == "lead_management" ){
                                
                                    
                                let hidetr = localArray.includes(columns[c])
                                
                                if(hidetr){
                                    var dynamicShow = 'dynamicHide';
                                }
                                else{
                                    var dynamicShow = 'dynamicShow';
                                    
                                }
                                    
                                }


                            if (columns[c] == 'color_code') {
                                // < iclass="fas fa-circle" style="color:;"></i>
                                tbodyHtml += '<td id="' + columns[c] + '" class="' + columns[c] + ' text-left"><i class="fas fa-circle" style="color:' + record[i][columns[c]] + ';"></i>' + record[i][columns[c]] + '</td>';
                            } else if (columns[c] == 'latitude')

                            {
                                tbodyHtml += '<td id="' + columns[c] + '" class="' + columns[c] + ' text-left">' + record[i].coordinate.latitude + '</td>';
                            } else if (columns[c] == 'longitude') {

                                tbodyHtml += '<td id="' + columns[c] + '" class="' + columns[c] + ' text-left">' + record[i].coordinate.longitude + '</td>';

                            } else if (columns[c] == 'field') {


                                tbodyHtml += '<td style="text-align:center;"><a style="padding-right:2px;" href="' + base_url + '/tenant/template/update/' + record[i].template_id + '/' + record[i].field + '"><i class="fas fa-edit edit"></i></a> <i style="color:#d11a2a;" class="far fa-trash-alt delete" id="' + record[i].field + '"></i></td>';
                            } else if (columns[c] == 'actions') {

                                tbodyHtml += '<td style="text-align:center;"><a style="padding-right:2px;" href="' + base_url + '/tenant/lead-default/edit/' + record[i].id + '/"><i class="fas fa-edit edit"></i></a> <i style="color:#d11a2a;" class="far fa-trash-alt delete" id="' + record[i].id + '"></i></td>';
                            }
                            // else if (columns[c] == 'template_name')
                            // {

                            //     tbodyHtml += '<td name="common" style="text-align:center;"><a style="padding-right:2px;" href="'+base_url+'/tenant/lead-default/edit/'+record[i].id+'/"><i class="fas fa-edit edit"></i></a> <i style="color:#d11a2a;" class="far fa-trash-alt delete" name="common" id="'+ record[i].id+'"></i></td>';
                            // }
                            else if (columns[c] == 'is_expired') {

                                var is_expired = record[i].is_expired;

                                if (is_expired == 1) {

                                    tbodyHtml += '<td  class="' + columns[c] + ' text-left '+dynamicShow+'">Yes</td>';
                                } else {
                                    tbodyHtml += '<td class="' + columns[c] + ' text-left '+dynamicShow+'">No</td>';
                                }


                            } else if (columns[c] == 'user_id') {

                                var new_user_id = res.data[i].id;
                                var new_status_user_id = res.data[i].user_status_id;

                                if (new_status_user_id == 0) {
                                    tbodyHtml += '<td id="' + new_user_id + '" class="unlink" style="text-decoration:none;">---</td>';
                                    $('.un-link').html('Password Reset Link');
                                } else {
                                    tbodyHtml += '<td id="' + new_user_id + '" class="link">Sent</td>';

                                }


                            } else if (columns[c] == 'template-action') {

                                var new_user_id = res.data[i].id;
                                var new_status_user_id = res.data[i].user_status_id;

                                tbodyHtml += '<td id="' + new_user_id + '" class="delete text-center"><i style="color:#d11a2a;" class="far fa-trash-alt"></i></td>';
                            } else if (columns[c] == 'lead_count') {

                                var new_count = record[i].lead_count;
                                var new_color = record[i].color_code;

                                tbodyHtml += '<td id="' + columns[c] + '" class="' + columns[c] + ' text-left lead_count_' + new_id + '">' + new_count + '  <i class="fas fa-map-marker-alt" style="color:' + new_color + ';"></i></td>';

                            } else if (columns[c] == 'lead_percentage') {
                                var new_id = record[i].id;

                                var lead_per = record[i].lead_percentage;
                                tbodyHtml += '<td id="' + lead_per + '" class="' + lead_per + ' text-left lead_percentage_' + new_id + '   ">' + lead_per + '%</td>';

                            } else if (columns[c] == 'query') {
                                var new_query = record[i].query;


                                tbodyHtml += '<td>' + new_query + '</td>';


                            }
                            else if (columns[c] == 'updated_by') {
                               
                               

                                    tbodyHtml += '<td id="' + columns[c] + '" data-id="' + record[i].id + '" title="' + record[i].updated_by.name + '" class="' + columns[c].split(' ').join('_') + ' text-left '+dynamicShow+'">' + record[i].updated_by.name + '</td>';
                                
                                
                            }
                            else if (columns[c] == 'status_lead_count') {
                                var new_lead = record[i].lead_count;



                                tbodyHtml += '<td id="' + columns[c] + '' + index + '" class="' + columns[c] + ' text-left lead_count_' + new_id + '">' + new_lead + '</td>';

                            } else if (columns[c] == 'status_agent_name') {
                                var new_agent = record[i].agent_name;

                                tbodyHtml += '<td id="' + columns[c] + '' + index + '" class="' + columns[c] + ' text-left lead_count_' + new_id + '">' + new_agent + '</td>';

                            } else if (columns[c] == 'template_key') {
                                var new_key = record[i].key;

                                tbodyHtml += '<td id="' + columns[c] + '' + index + '" class="' + columns[c] + ' text-left">' + new_key.split('_').join(' ') + '</td>';

                            } else if (columns[c] == 'test_title')

                            {

                                var status_title = record[i].title;
                                var status_ids = record[i].id;


                                var legchecked = record.length;
                                if (check == true)

                                {

                                    var default_checked = 'checked';
                                } else {

                                    if (filtered[i] == record[i].id)

                                    {
                                        var default_checked = '';
                                    } else {
                                        var default_checked = 'checked';
                                    }
                                }

                                // tbodyHtml += '<option value="">'+ status_title +'</option>'

                                // $('.lead_type_id').append(tbodyHtml);
                                // $('.selectpicker').selectpicker('refresh')

                                tbodyHtml += '<td class="' + status_title + ' text-left" title="' + status_title + '"><input type="checkbox" name="status_id" value="' + record[i].id + '" id="' + status_ids + '" ' + default_checked + '/>' + status_title + '</td>';
                            } else {
                                if (columns[c].includes('.')) {
                                    var innerKey = columns[c].split('.');
                                    var td_value = '';
                                    for (var k = 0; k < innerKey.length; k++) {
                                        if (k == 0) {
                                            td_value = record[i][innerKey[k]];

                                        } else {
                                            td_value = td_value[innerKey[k]];

                                        }
                                    }
                                } else {
                                    var td_value = record[i][columns[c]];


                                }
                                if (typeof td_value === 'undefined') {
                                    td_value = '---';
                                }
                                

                                tbodyHtml += '<td id="' + columns[c] + '" data-id="' + record[i].id + '" title="' + record[i][columns[c]] + '" class="' + columns[c].split(' ').join('_') + ' text-left '+dynamicShow+'" >' + td_value + '</td>';
                            }
                        }


                        tbodyHtml += '</tr>';
                        index++;

                    }

                    $(element).html(tbodyHtml);

                    //pagination
                    if (pagination) {

                        var pagination_obj = res.meta;
                        var last_page_number = pagination_obj.last_page;

                        if (last_page_number > 1) {
                            var pagination_html = '<nav aria-label="Page navigation example">';
                            pagination_html += '<ul class="pagination">';
                            if (pagination_obj.current_page > 1) {
                                pagination_html += '<li data-page_number="1" class="page-item"><a class="page-link" > << </a></li>';

                            }
                            pagination_html += '<li data-page_number="' + (parseInt(pagination_obj.current_page) - 1) + '" class="page-item"><a class="page-link"> < </a></li>';
                            var index = 1;

                            for (var p = pagination_obj.current_page; p <= last_page_number; p++) {
                                if (index <= page_size) {
                                    if (index == 1) {
                                        var active_class = 'active_page';
                                    } else {
                                        var active_class = '';
                                    }
                                    pagination_html += '<li data-page_number="' + p + '" class="page-item"><a class="' + active_class + '  page-link">' + p + '</a></li>';

                                }

                                index++;

                            }

                            if (pagination_obj.current_page != last_page_number) {
                                pagination_html += '<li data-page_number="' + (parseInt(pagination_obj.current_page) + 1) + '" class="page-item"><a class="page-link"> > </a></li>';

                            }


                            if (pagination_obj.current_page < last_page_number) {
                                pagination_html += '<li data-page_number="' + last_page_number + '" class="page-item"><a class="page-link"> >> </a></li>';
                            }
                            pagination_html += '</ul>';
                            pagination_html += '</nav>';
                            $('.pagination_cont').html(pagination_html)
                        }

                        if (last_page_number == 1) {
                            $('.pagination_cont').html('')
                        }
                    }


                } else {

                    tbodyHtml += '<tr>';
                    tbodyHtml += '<td colspan ="100" class="text-center"> No record found </td>';
                    tbodyHtml += '</tr>';
                    $(element).html(tbodyHtml);
                    $('.pagination_cont').html('')

                }
            } else {
                tbodyHtml += '<tr>';
                tbodyHtml += '<td colspan ="100" class="text-center"> No record found </td>';
                tbodyHtml += '</tr>';
                $(element).html(tbodyHtml);
                $('.pagination_cont').html('')
            }

            resolve(true);
        })
    })
}


//ajax datatable
var ajaxDatatable = (element, source_url, pageLength = 25, columns = [], field = '') => {

    var columnJson = [];
    var tbodyHtml = '';
    var page_count = 1;

    for (var c = 0; c < columns.length; c++) {
        columnJson.push({
            "data": columns[c]
        });

    }
    var ids;
    var action;
    table = $(element).DataTable({
        "processing": true,
        "serverSide": true,
        "ordering": false,
        searching: false,
        "lengthChange": false,
        createdRow: function(row, data, dataIndex) {

            if (field == 'type') {
                $(row).attr('data-href', window.location.href + '/edit/' + data.id + '?type=' + data.type);
                $(row).attr('class', 'redirect');
                $(row).attr('id', data.id);

            } else {
                $(row).attr('data-href', window.location.href + '/edit/' + data.id);
                $(row).attr('class', 'redirect');
                $(row).attr('id', data.id);
            }


        },
        "ajax": {
            url: source_url,
            type: "GET",
            beforeSend: function() {
                //$('button').attr('disbaled','disabled');
            },
            data: function(d) {
                var current_page_index = (d.start / 25) + 1;
                d.current_page_index = current_page_index;
                d.agent_ids =  $('.agent_ids').val();
                d.start_date =  $('.start').val();
                d.end_date =  $('.end').val();
                d.commission_events =  $('.commission_events').val();
                d.order_by = $('.column_name').val();
                d.order_type = $('.title').val();
                $('#current_page_index').val(current_page_index);
                delete d.columns;
            },
            error: function() { // error handling

            }
        },
        drawCallback: function(settings) {
            // other functionality
            //$('button').removeAttr('disbaled');
        },
        lengthMenu: [
            [10, 20, 50, 100, 200],
            [10, 20, 50, 100, 200] // change per page values here
        ],
        pageLength: pageLength, // default record count per page

        "columns": columnJson
    });

}