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
        <div class="col-md-3">
            <div class="dropdown">
                <button class="btn btn-primary dropdown-toggle add-bt" type="button" data-toggle="dropdown"
                        style="background: transparent;">
                    <span class="cust-head">Team Performance Report</span>
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
                                data-live-search="true" name="target_user_id" value=""
                                data-actions-box="true" title="Select User" multiple>


                            @foreach($data['agent'] as $agent )

                                <option value="{{ $agent->id }}">{{$agent->first_name}}</option>
                            @endforeach
                        </select>

                        @else
                        <select class="form-control summary agents_list selectpicker"
                                data-live-search="true" name="target_user_id" value=""
                                data-actions-box="true" title="Select User" multiple>

                                <option value="" class="disabled" disabled="disabled">No User Found</option>
                        </select>

                    @endif

                </div>
                <div class="col-md-3 form-group">

                    @if(count($data['status']))


                        <select class="form-control summary status_list selectpicker"
                                data-live-search="true" name="status_id" value=""
                                data-actions-box="true" title="Select Status" multiple>

                            @foreach($data['status'] as $status )

                                <option value="{{ $status->id }}">{{$status->title}}</option>
                            @endforeach
                        </select>

                    @endif

                </div>

                <div class="col-md-3 form-group">

                    @if(count($data['type']))


                        <select class="form-control summary type_list selectpicker"
                                data-live-search="true" name="type_id" value="" data-actions-box="true"
                                title="Select Lead Type" multiple>

                            @foreach($data['type'] as $type )

                                <option value="{{ $type->id }}">{{$type->title}}</option>
                            @endforeach
                        </select>

                    @endif

                </div>
                <div class="col-md-1">

                    <button class="b1 save"><i class="fas fa-paper-plane"></i></button>
                </div>
            </form>
        </div>


    </div>
    <hr class="border">
    <table class="table table-striped jambo_table" id="scroll" id="">
        <thead>
        <tr class="headings">
            <td class="text-left">S.no</td>
            <td class="text-left">Users</td>
            <td class="text-left">Knocks</td>
            <td class="text-left">Appointments</td>
            <td class="text-left">Profit</td>
            <td class="text-left">Contract</td>
        </tr>
        </thead>
        <tbody class="team_status"></tbody>
        <tfoot class="tfoot">
        <tr>
            <td><b>Total</b></td>
            <td></td>
            <td class="text-left" id="knock_sum"></td>
            <td class="appointment_class text-left" id="appointment_sum"></td>
            <td class="text-left" id="profit"></td>
            <td class="text-left" id="contract"></td>
        </tr>
        </tfoot>

    </table>


</div>

</div>   <!--footer-->
<script>

    $(document).ready(function () {

        var columns = ['status_agent_name', 'status_lead_count', 'appointment_count','commission_profit_count','commission_contract_count'];
        loadGridWitoutAjax('GET', base_url + "/tenant/user/lead/status/report", {}, {}, columns, '.team_status', 'result', false);

        ajaxCall('GET', base_url + "/tenant/user/lead/status/report").then(function (res) {
            var knock_sum = '';
            var appointment_sum = '';
            var profit_sum = '';
            var contract_sum = '';
            knock_sum += res.data.lead_count;
            appointment_sum += res.data.appointment_count;
            profit_sum += res.data.commission_profit_count;
            contract_sum += res.data.commission_contract_count;

            $('#knock_sum').html(knock_sum);
            $('#appointment_sum').html(appointment_sum);
            $('#profit').html(profit_sum);
            $('#contract').html(contract_sum);


        })

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
            var time_slot = $('.duration').val();
            var data = {target_user_id: user_id, status_id: status_id, type_id: type_id, time_slot: time_slot};
            
            loadGridWitoutAjax('GET', base_url + "/tenant/user/lead/status/report", data, {}, columns, '.team_status', 'result',false);

            ajaxCall('GET', base_url + "/tenant/user/lead/status/report", data).then(function (res) {

                var knock_sum = '';
            var appointment_sum = '';
            var profit_sum = '';
            var contract_sum = '';
            knock_sum += res.data.lead_count;
            appointment_sum += res.data.appointment_count;
            profit_sum += res.data.commission_profit_count;
            contract_sum += res.data.commission_contract_count;

            $('#knock_sum').html(knock_sum);
            $('#appointment_sum').html(appointment_sum);
            $('#profit').html(profit_sum);
            $('#contract').html(contract_sum);


            })

        })

            })
</script>
@include('tenant.include.footer')
<!--footer-->