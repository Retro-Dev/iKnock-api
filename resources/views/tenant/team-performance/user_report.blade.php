<?php //echo "<pre>"; print_r($data); die(); ?>
@include('tenant.include.header')
@include('tenant.include.sidebar')
<style type="text/css">
   .bootstrap-select > .dropdown-toggle, input[type="date"] {
   padding: 0px;
   border: none;
   border-radius: 0px;
   background: #113f85;
   color: white !important;
   padding: 6px 20px;
   font-weight: bold;
   overflow: hidden;
   white-space: nowrap;
   text-overflow: ellipsis;
   }
   .summary, .b1{
       margin-top:0px;
   }
   ::placeholder {
  color: #fff !important;
  opacity: 1; /* Firefox */
}
::-webkit-input-placeholder { /* Edge */
   color: #fff !important;
}

:-ms-input-placeholder { /* Internet Explorer */
   color: #fff !important;
}
</style>
<div class="right_col" role="main">
   <div class="row" id="content-heading">
      <!--content-heading here-->
      <div class="col-md-8">
         <div class="dropdown">
            <button class="btn  dropdown-toggle add-bt" type="button" data-toggle="dropdown" style="background: transparent;">
            <span class="cust-head">User Lead Report</span>
            <span class="caret" style="color:black;padding-bottom:10px;"></span></button>
            <ul class="dropdown-menu link-menu">
               <li><a href="{{ URL::to('/tenant/dashboard') }}">Dashboard</a></li>
               <li><a href="{{ URL::to('/tenant/team-performance/comm_report') }}">Commission Report</a></li>
               <li><a href="{{ URL::to('/tenant/team-performance/team_report') }}">Team Report</a></li>
               <li><a href="{{ URL::to('/tenant/team-performance/user_report') }}">User Lead Report</a></li>
            </ul>
         </div>
      </div>
      
      <div class="col-md-3 text-right" style="margin-top:6px;">
          <button class="b1 toggle-btn"><i class="fa fa-filter"></i> Filter</button>
      </div>
      
      <div class="col-md-1 text-right" style="margin-top:6px;">
          <button class="b1" id="export-btn">Export</button>
      </div>
      
   </div>
   <div class="row show-toggle" id="content-heading" style="display:none;">
         <form class="comm_form">
            <div class="col-md-2">
               <div class="form-group">
                  <label>Start Date</label>
                  <!--<input type="date" name="start_date" class="startDate">-->
                  <input type="text" placeholder="Start Date" id="datepicker" name="start_date" class="startDate date form-control">
               </div>
            </div>
            <!-- Col End -->
            
            <div class="col-md-2">
               <div class="form-group">
                  <label>End Date</label>
                  <!--<input type="date" name="end_date" class="endDate">-->
                  <input type="text" id="datepicker2" placeholder="End Date" name="end_date" class="endDate date form-control">
               </div>
            </div>
            <!-- Col End -->
            
            <div class="col-md-2 form-group">
                <label>Select User</label>
               @if(count($data['agent']))
               <select class="form-control agents_list selectpicker"
                  data-live-search="true" name="target_user_id" value=""
                  data-actions-box="true" title="Select User" multiple>
                  @foreach($data['agent'] as $agent )
                  <option value="{{ $agent->id }}">{{$agent->first_name}}</option>
                  @endforeach
               </select>
               @else
               <select class="form-control  agents_list selectpicker"
                  data-live-search="true" name="target_user_id" value=""
                  data-actions-box="true" title="Select User" multiple>
                  <option value="" class="disabled" disabled="disabled">No User Found</option>
               </select>
               @endif
            </div>
            <div class="col-md-2 form-group">
                <label>Select Status</label>
               @if(count($data['status']))
               <select class="form-control status_list selectpicker"
                  data-live-search="true" name="status_id" value=""
                  data-actions-box="true" title="Select Status" multiple>
                  @foreach($data['status'] as $status )
                  <option value="{{ $status->id }}">{{$status->title}}</option>
                  @endforeach
               </select>
               @endif
            </div>
            <div class="col-md-2 form-group">
                <label>Select Lead Type</label>
               @if(count($data['type']))
               <select class="form-control type_list selectpicker"
                  data-live-search="true" name="type_id" value="" data-actions-box="true"
                  title="Select Lead Type" multiple>
                  @foreach($data['type'] as $type )
                  <option value="{{ $type->id }}">{{$type->title}}</option>
                  @endforeach
               </select>
               @endif
            </div>
            <div class="col-md-2 form-group">
                <label style="visibility:hidden;">Submit</label><br/>
               <button class="b1 save"><i class="fas fa-paper-plane"></i></button>
            </div>
         </form>
   </div>
   <hr class="border">
   <!--content-heading-end-->
   <div class="row" id="pg-content">
      <div id="container"></div>
      <div class="col-md-12">
         <div class="table-responsive mt-20">
            <table class="table table-bordered">
               <thead>
               </thead>
               <tbody>
               </tbody>
            </table>
         </div>
      </div>
   </div>
   <!--content-table-end-->
</div>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/modules/export-data.js"></script>
<script src="https://code.highcharts.com/modules/data.js"></script>
<script src="https://code.highcharts.com/modules/drilldown.js"></script>
<script>
  $( function() {
    $( "#datepicker" ).datepicker();
  } );
  $( function() {
    $( "#datepicker2" ).datepicker();
  } );
  </script>
<script type="text/javascript">
   $(document).ready(function(){

   
   function ColChart(user_names, status) {
       console.log("status", status);
       Highcharts.chart('container', {
           chart: {
               type: 'column'
           },
           title: {
               text: 'Stacked column chart'
           },
           xAxis: {
               categories: user_names,
               title: {
                   text: 'Users Lead Report',
               },
           },
           yAxis: {
               min: 0,
               max: 200,
               title: {
                   text: ''
               },
               stackLabels: {
                   enabled: true,
                   style: {
                       fontWeight: 'bold',
                       color: ( // theme
                           Highcharts.defaultOptions.title.style &&
                           Highcharts.defaultOptions.title.style.color
                       ) || 'gray'
                   }
               }
           },
           legend: {
               align: 'center',
               x: 10,
               verticalAlign: 'bottom',
               y: 25,
               maxHeight:40,
               floating: true,
               backgroundColor: Highcharts.defaultOptions.legend.backgroundColor || 'white',
               borderColor: '#CCC',
               borderWidth: 0,
               shadow: false,
               
           },
           tooltip: {
               headerFormat: '<b>{point.x}</b><br/>',
               pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
           },
           plotOptions: {
               column: {
                   stacking: 'normal',
                   dataLabels: {
                       enabled: true
                   }
               }
           },
           series: status
          
       });
   }
   
   function ajaxRequest(method, url, data = {}) {
       ajaxCall(method, url, data).then(function(res) {
   
           if (res.code == 200) {
               var record = res.data;
               var user_names = record.user_names;
               var status = record.status;
               ColChart(user_names, status);
               if (user_names.length > 0) {
                   var thead = '';
                   thead += '<tr>';
                   thead += '<th>S.No</th>';
                   thead += '<th>Status</th>';
                   for (var i = 0; i < user_names.length; i++) {
   
                       thead += '<th style="text-transform:capitalize;">' + user_names[i] + '</th>';
   
   
                   }
                   thead += '</tr>';
                   $('thead').html(thead);
   
   
               }
   
               if (status.length > 0) {
                   var tbody = '';
                   var index = 1;
   
                   for (var i = 0; i < status.length; i++) {
                       tbody += '<tr>';
                       tbody += '<td>' + index + '</td>';
                       tbody += '<td>' + status[i].name + '</td>';
   
                       if (status[i].data.length > 0) {
                           for (var a = 0; a < user_names.length; a++) {
                               if (status[i].data[a]) {
                                   tbody += '<td>' + status[i].data[a] + '</td>';
                               } else {
                                   tbody += '<td>0</td>';
                               }
   
                           }
                       }
   
                       index++;
                       tbody += '</tr>';
                   }
   
                   $('tbody').html(tbody);
   
               }
           }
   
       })
   
   }
   // const monthControl = document.querySelector('input[type="month"]');
   // const date = new Date()
   // const month = ("0" + (date.getMonth() + 1)).slice(-2)
   // const year = date.getFullYear()
   // monthControl.value = `${year}-${month}`;
       var data = {
           
       };
   ajaxRequest('GET', base_url + "/tenant/lead/status/user/report", data);
   
   
   $('.save').click(function (e) {
               e.preventDefault();
               var user_id = $('.agents_list').selectpicker('val');
               if (Array.isArray(user_id)) {
                   user_id = user_id.join();
               }
               var status_id = $('.status_list').selectpicker('val');
               if (Array.isArray(status_id)) {
                   status_id = status_id.join();
               }
               var type_id = $('.type_list').selectpicker('val');
               if (Array.isArray(type_id)) {
                   type_id = type_id.join();
               }
               var start_date = $('.startDate').val();
               var end_date = $('.endDate').val();
               
        
               if(new Date(end_date) < new Date(start_date))
                {
                    alert("Please ensure that the End Date is greater than or equal to the Start Date.");
                    return false;
                }
               
               var data = {target_user_id: user_id, status_id: status_id, type_id: type_id,start_date:start_date,end_date:end_date};
               ajaxRequest('GET', base_url + "/tenant/lead/status/user/report", data);
              
    })
           
    $(document).on('click', '#export-btn', function () {
          var user_id = $('.agents_list').selectpicker('val');
               if (Array.isArray(user_id)) {
                   user_id = user_id.join();
               }
               var status_id = $('.status_list').selectpicker('val');
               if (Array.isArray(status_id)) {
                   status_id = status_id.join();
               }
               var type_id = $('.type_list').selectpicker('val');
               if (Array.isArray(type_id)) {
                   type_id = type_id.join();
               }
               var start_date = $('.startDate').val();
               var end_date = $('.endDate').val();
               
               if(new Date(end_date) < new Date(start_date))
                {
                    alert("Please ensure that the End Date is greater than or equal to the Start Date.");
                    return false;
                }
               
             var data = {target_user_id: user_id, status_id: status_id, type_id: type_id,start_date:start_date,end_date:end_date,export: true};
            console.log('data', data);
            var qString = $.param(data);
            var url = "{{URL::to('tenant/lead/status/user/report?')}}" + qString;
            document.location.href = url;
            
    });
   })
   
   $('.toggle-btn').click(function(){
       $('.show-toggle').fadeToggle();
   })
   
   
</script>
@include('tenant.include.footer')