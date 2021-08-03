@include('tenant.include.header')
@include('tenant.include.sidebar')

<link href='{{ URL::to('fullcalendar/packages/core/main.css') }}' rel='stylesheet'/>
<link href='{{ URL::to('fullcalendar/packages/daygrid/main.css')}}' rel='stylesheet'/>
<link href='{{ URL::to('fullcalendar/packages/timegrid/main.css')}}' rel='stylesheet'/>
<link href='{{ URL::to('fullcalendar/packages/list/main.css')}}' rel='stylesheet'/>
<script src='{{ URL::to('fullcalendar/packages/core/main.js') }} '></script>
<script src='{{ URL::to('fullcalendar/packages/interaction/main.js') }} '></script>
<script src='{{ URL::to('fullcalendar/packages/daygrid/main.js') }} '></script>
<script src='{{ URL::to('fullcalendar/packages/timegrid/main.js') }} '></script>
<script src='{{ URL::to('fullcalendar/packages/list/main.js') }} '></script>


<div class="right_col" role="main">
    <div class="row" id="content-heading">
        <!--content-heading here-->
        <div class="col-md-10">
            <h1 class="cust-head">Scheduling</h1>
        </div>
        <div class="col-md-2 text-right">
            <a href="{{ URL::to('/tenant/scheduling/create') }}" class="btn btn-info add-bt">Add</a>

        </div>
    </div>
    <hr class="border">
    <!--content-heading-end-->
    <div class="row">
        <div class="col-lg-3 ">
            <select class="form-control selectpicker lead_users" data-live-search="true" data-actions-box="true"
                    name="user_ids" value="" multiple>
                @foreach ($data['agent'] as $agent)

                    <option data-tokens="{{ $agent->title }}"
                            value="{{ $agent->id }}"
                    @if (in_array($agent->id, $data['leadUserIds']))
                            selected
                            @endif
                    >
                        {{ $agent->first_name }} </option>
                @endforeach
            </select>
        </div>
        <div class="col-lg-3 ">
            <button class="btn btn-primary filter-btn"> Filter</button>
        </div>
    </div>
    <div class="row" id="pg-content">
        <!--content-table here-->
        <input type="hidden" id="userIds">
        <div id='calendar'>

        </div>

    </div>
    <!--content-table-end-->
</div>
<script>
    var getUrlParams = function (url) {
        var params = {};
        (url + '?').split('?')[1].split('&').forEach(function (pair) {
            pair = (pair + '=').split('=').map(decodeURIComponent);
            if (pair[0].length) {
                params[pair[0]] = pair[1];
            }
        });
        return params;
    };

    $(document).ready(function () {
        var url = window.location.href;

        var  param = getUrlParams(url);
        console.log(param);
        var userIds = [];
        $('.filter-btn').on('click', function () {
            userIds = userIds.join(',');
            var  param = getUrlParams(url);
            var url = "{{ URL::to('tenant/scheduling?userIds=') }}"+userIds;
            window.location  = url;
        });


        var leadAUrl ;
        if(param.userIds){
            leadAUrl = "{{ URL::to('tenant/user/lead/appointment/list?userIds=') }}"+param.userIds;
        }else{
            leadAUrl = "{{ URL::to('tenant/user/lead/appointment/list') }}";
        }

        var calendarEl = $('#calendar')[0];
        var calendar = new FullCalendar.Calendar(calendarEl, {
            // calendarEl.fullCalendar({
            plugins: ['interaction', 'dayGrid', 'timeGrid', 'list', 'bootstrap'],
           
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth'
            },
            // height: 400,
            aspectRatio: 1.5,
            displayEventEnd: {
                month: false,
                basicWeek: true,
                "default": true
            },
            // defaultDate: '2019-04-12',
            navLinks: true, // can click day/week names to navigate views
            businessHours: false, // display business hours
            editable: false,
            events: {
                url: leadAUrl,
                dataType: 'json',
                extraParams: {
                    // our hypothetical feed requires UNIX timestamps
                    // startDate: info.startStr,
                    // endDate: info.endStr,
                },

                
                failure: function () {
                    alert('there was an error while fetching events!');
                },

                success: function (response, xhr) {
                    var data = response.data;
                    var events = [];
                    $(data).each(function (key, item) {
                        var title = item.address;
                        if(item.is_out_bound == 1 && item.appointment_result != null){
                            title = item.appointment_result;
                        }
                        else if(item.address == null){
                            title = '';
                        }
                        events.push({
                            title: title,
                            id: item.appointment_id,
                            //start: item.appointment_date.replace(' ','T')+':00',
                            start: moment(new Date(item.appointment_date)).format('YYYY-MM-DDTHH:mm:00'),
                            //start: '2019-08-06T13:00:00',
                            //end: item.appointment_end_date,
                            end: moment(new Date(item.appointment_end_date)).format('YYYY-MM-DDTHH:mm:00'),

                            is_out_bound: item.is_out_bound,
                            lead_id: item.id,
                        });

                    });
                    // console.log(events);
                    return events;
                }
            },

            eventClick: function(info) {
                var appointmentUrl = "{{ URL::to('tenant/scheduling') }}"+"/"+info.event.id;
                var leadDetailUrl = "{{ URL::to('tenant/lead/edit') }}"+"/"+info.event.extendedProps.lead_id;
                
                // alert(info.event.extendedProps.is_out_bound);
                // alert(info.event.title);

                if(info.event.extendedProps.is_out_bound == 1 ){
                    window.open(appointmentUrl);
                }else{
                    window.open(leadDetailUrl);
                }
                // change the border color just for fun
                info.el.style.borderColor = 'red';
            },

        });
        calendar.render();




        $('.lead_users').on('change', function () {
            userIds = $(this).val();
        });
    });

    function getUserIDs() {
        console.log("Here");
    }

</script>
{{--<script src="{{asset('assets/js/tenant-js/lead_status.js')}}"></script>--}}
{{--<script type="text/javascript">--}}
{{--$(document).ready(function(){--}}
{{--var columns = ['title','code','color_code'];--}}
{{--loadGridWitoutAjax('GET',base_url + "/tenant/status/list",{},{},columns);--}}
{{--})--}}
{{--</script>--}}
@include('tenant.include.footer')
