@include('tenant.include.header')
@include('tenant.include.sidebar')
<style type="text/css">
    .bootstrap-select > .dropdown-toggle {
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

</style>

<div class="right_col" role="main">
    <div class="row" id="content-heading">
        <!--content-heading here-->
        <div class="col-md-3">
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle add-bt" type="button" data-toggle="dropdown"
                        style="background: transparent;">
                    <span class="cust-head">Commission Report</span>
                    <span class="caret" style="color:black;padding-bottom:10px;"></span></button>
                    <ul class="dropdown-menu link-menu">
                    <li><a href="{{ URL::to('/tenant/dashboard') }}">Dashboard</a></li>
                    <li><a href="{{ URL::to('/tenant/team-performance/comm_report') }}">Commission Report</a></li>
                    <li><a href="{{ URL::to('/tenant/team-performance/team_report') }}">Team Report</a></li>
                    <li><a href="{{ URL::to('/tenant/team-performance/user_report') }}">User Lead Report</a></li>
                </ul>
            </div>
        </div>
        <div class="col-md-9">
            <form class="comm_form">

                <div class="col-md-2 form-group">

                    <select class="form-control summary duration" name="time_slot">
                        <option disabled="disabled" selected="selected">Select Time</option>
                        <option value="all_time">All Time</option>
                        <option value="today">Today</option>
                        <option value="yesterday">Yesterday</option>
                        <option value="week">This Week</option>
                        <option value="last_week">Last week</option>
                        <option value="month">This Month</option>
                        <option value="last_month">Last month</option>
                        <option value="year">This Year</option>
                        <option value="last_year">Last year</option>
                    </select>

                </div>
                <div class="col-md-3 form-group">

                    @if(count($data['agent']))


                        <select class="form-control summary agents_list selectpicker"
                                data-live-search="true" data-actions-box="true" title="Select User" name="target_user_id" value="" multiple>

                            @foreach($data['agent'] as $agent )

                                <option value="{{ $agent->id }}">{{$agent->first_name}}</option>
                            @endforeach
                        </select>

                        @else
                                <select class="form-control summary type_list selectpicker"
                                data-live-search="true" data-actions-box="true" title="Select User" name="type_id"
                                value="" multiple>

                                <option value="" class="disabled" disabled="disabled">No User Found</option>
                        </select>

                    @endif

                </div>

                <div class="col-md-3 form-group">

                    @if(count($data['type']))


                        <select class="form-control summary type_list selectpicker" data-live-search="true"
                                name="type_id" value="" data-actions-box="true" title="Select Lead Type" multiple>
                            @foreach($data['type'] as $type )
                                <option value="{{ $type->id }}">{{$type->title}}</option>
                            @endforeach
                        </select>

                        @else
                                <select class="form-control summary type_list selectpicker"
                                data-live-search="true" data-actions-box="true" title="Select Lead Type" name="type_id"
                                value="" multiple>

                                <option value="" disabled="disabled">No Type Found</option>
                        </select>

                    @endif

                </div>


                <div class="col-md-2 form-group">

                    <select class="form-control summary value" name="type">
                        <option disabled="disabled" selected="selected">Select Amount</option>
                        <option value="percentage">Percentage</option>
                        <option value="amount"> Dollar</option>
                    </select>

                </div>
                <div class="col-md-1">

                    <button type="button" class="b1 save" id="filter_save"><i class="fas fa-paper-plane"></i></button>
                </div>
                <div class="col-md-1">

                    <button type="button" class="b1" id="export-btn">Export</button>
                </div>
            </form>
        </div>

    </div>
    <hr class="border">
    <!--content-heading-end-->

    <table class="table table-striped jambo_table" id="scroll" id="">
        <thead>
        <tr class="headings">
            <td class="text-left">S.no</td>
            <td class="text-left">Users</td>
            <td class="text-left">Commission</td>
        </tr>
        </thead>
        <tbody class="team_comm"></tbody>
        <tfoot class="tfoot">
        <tr>
            <td><b>Total</b></td>
            <td></td>
            <td class="commission_count text-left" id="commission_new_count"></td>
        </tr>
        </tfoot>

    </table>
    <!--content-table-end-->
</div>
<script>

    $(document).ready(function () 
    {


        var columns = ['status_agent_name', 'commission_count'];
        loadGridWitoutAjax('GET', base_url + "/tenant/user/lead/status/report", {}, {}, columns, '.team_comm', 'result', false);

        ajaxCall('GET', base_url + "/tenant/user/lead/status/report").then(function (res) {

            var commission_new_count = '';
                commission_new_count += res.data.commission_count;

                $('#commission_new_count').html(commission_new_count);


        })

        $('.save').click(function (e) {
            e.preventDefault();
            var user_id = $('.agents_list').selectpicker('val');
            if(Array.isArray(user_id)) {
                user_id = user_id.join();
            }

            var type_id = $('.type_list').selectpicker('val');
            if(Array.isArray(type_id)) {
                type_id = type_id.join();
                
            }

            var type = $('.value').val();

            var time_slot = $('.duration').val();
            var data = {target_user_id: user_id, type_id: type_id,type:type, time_slot: time_slot};
            
            loadGridWitoutAjax('GET', base_url + "/tenant/user/lead/status/report", data, {}, columns, '.team_comm', 'result',false);

            ajaxCall('GET', base_url + "/tenant/user/lead/status/report", data).then(function (res) {

                var commission_new_count = '';
                commission_new_count += res.data.commission_count;
                $('#commission_new_count').html(commission_new_count);


            })

        })

        $(document).on('click', '#export-btn', function () {
            //$(this).data('export',true);


            var user_id = $('.agents_list').selectpicker('val');
            if (Array.isArray(user_id)) {
                user_id = user_id.join();
            }

            var type_id = $('.type_list').selectpicker('val');
            if (Array.isArray(type_id)) {
                type_id = type_id.join();

            }

            var type = $('.value').val();

            var time_slot = $('.duration').val();
            var data = {target_user_id: user_id, type_id: type_id, type: type, time_slot: time_slot, export: true};
            console.log('data', data);
            var qString = $.param(data);
            var url = "{{URL::to('tenant/user/lead/status/report?')}}" + qString;
            document.location.href = url;
            //ajaxCall('GET', base_url + "/tenant/user/lead/status/report", data)

        });

    })
</script>
@include('tenant.include.footer')
