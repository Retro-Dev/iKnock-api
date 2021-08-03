
@include('tenant.include.header')
@include('tenant.include.sidebar')

<style type="text/css">
    .bootstrap-select>.dropdown-toggle{
     padding: 0px; 
    border: none;
     border-radius: 0px;
    background: #113f85;
    color: white !important;
    padding: 6px 20px;
    font-weight: bold;
}
</style>

<div class="right_col" role="main">
    <div class="row" id="content-heading">
        <!--content-heading here-->
        <div class="col-md-6">
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle add-bt" type="button" data-toggle="dropdown" style="background: transparent;">
                    <span class="cust-head">Team Performance Report</span>
                    <span class="caret" style="color:black;padding-bottom:10px;"></span></button>
                <ul class="dropdown-menu link-menu">
                    <li><a href="{{ URL::to('/tenant/dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ URL::to('/tenant/team-performance/comm_report') }}">Commission Report</a></li>
                    <li><a href="{{ URL::to('/tenant/team-performance/team_report') }}">Team Report</a></li>
                </ul>
            </div>
        </div>
        <div class="col-md-6">
            <form class="comm_form">
                
                <div class="col-md-1"></div>

                <div class="col-md-3 form-group">

                   <select class="form-control summary duration" name="time_slot">
                        <option disabled="disabled" selected="selected">Select Time</option>
                        <option value="today">Today</option>
                        <option value="yesterday">Yesterday</option>
                        <option value="week">Week</option>
                        <option value="last_week">Last week</option>
                        <option value="month">Month</option>
                        <option value="last_month">last month</option>
                        <option value="year">Year</option>
                        <option value="last_year">Last year</option>
                    </select>

                </div>
                <div class="col-md-3 form-group">
                    @if(count($data['agent']))
                    

                    <select class="form-control summary agents_list selectpicker" data-live-search="true" name="target_user_id" value="">
                        <option value="" disabled="disabled" selected="selected">Select User</option>
                        <option value="" >Select all</option>
                        @foreach($data['agent'] as $agent )
                        
                        <option value="{{ $agent->id }}">{{$agent->first_name}}</option>
                         @endforeach
                    </select>
                   
                    @endif

                </div>
                <div class="col-md-3 form-group">

                    @if(count($data['status']))
                    

                    <select class="form-control summary status_list selectpicker" data-live-search="true" name="status_id" value="">
                        <option value="" disabled="disabled" selected="selected">Select Status</option>
                        <option value="" >Select all</option>
                        @foreach($data['status'] as $status )
                       
                        <option value="{{ $status->id }}">{{$status->title}}</option>
                         @endforeach
                    </select>
                   
                    @endif

                </div>
                <div class="col-md-2">
                    
                    <input type="button" name="" class="btn btn-info b1 save" value="Apply">
                </div>
            </form>
        </div>


    </div>
    <hr class="border">
    <!--content-heading-end-->

    <div class="row" id="pg-form">


        <div id="container"></div>
        <script src="https://code.highcharts.com/highcharts.js"></script>
        <script src="https://code.highcharts.com/modules/exporting.js"></script>
        <script src="https://code.highcharts.com/modules/export-data.js"></script>
        <script src="{{asset('assets/js/tenant-js/chart.js')}}"></script>

    </div>
</div>   <!--footer-->
<script>
$(document).ready(function(){

    loadChart('GET',base_url + "/tenant/user/lead/report",{},{});
    
    $('.save').click(function(){

        var user_id = $('.agents_list').selectpicker('val');
        var status_id = $('.status_list').selectpicker('val');
        var time_slot =  $('.duration').val();
        var data = {target_user_id:user_id,status_id:status_id,time_slot:time_slot}; 
        loadChart('GET',base_url + "/tenant/user/lead/report",data,{})

    })  
})

function loadChart(method,url,data,header)
{
    ajaxCall(method,url,data,header).then(function(res){

                if(res.code == 200){
                    var label = [];
                    var color = [];
                    var long_label = [];
                    var value = [];
                    var record = res.data;
                    if(record.length>0){
                        for(var i=0; i<record.length;i++){
                         var label_key = record[i].label;
                         var color_key = record[i].color_code;
                         var title_key = record[i].long_label;
                         var value_key = record[i].value;

                         label.push(label_key);
                         color.push(color_key);
                         long_label.push(title_key);
                         value.push(value_key);
                    }

                   barchart('container',label , color,long_label,value); 

                    }
                    else{
                        $("#container").html("<img style='width:100%;margin:0 auto;' src='{{asset("assets/images/graph.png")}}''>");
                    }
                    
                 }   

        })
}

$(document).ready(function(){
   ajaxCall("GET", base_url + "/tenant/user/lead/status/report", data = {}, headers = {})
})
</script>
@include('tenant.include.footer')
<!--footer-->