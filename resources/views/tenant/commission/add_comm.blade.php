
@include('tenant.include.header')
@include('tenant.include.sidebar')
<style>
.input{
    padding:14px;
}
</style>

<div class="right_col" role="main">
                <div class="row" id="content-heading">
                    <!--content-heading here-->
                       <div class="col-md-12">
                           <h1 class="cust-head">Add Commission </h1>
                       </div>
                </div>
      <hr class="border">

                <!--content-heading-end-->
            <div class="row" id="pg-form">
                 @include('tenant.error')
                <form method="post">
                    <input type="hidden" class="submit_url" value="{{ URL::to('tenant/user/commission/create') }}" />
                    <input type="hidden" class="redirect_url" value="{{ URL::to('tenant/commission') }}">
                            {{ csrf_field() }}
                            <div class="col-md-6 form-group">
                                <label>User Name</label>
                            <select class="form-control selectbox selectpicker" data-live-search="true" name="target_id">
                                <option disabled="disabled" selected="selected">Select User</option>
                                @foreach ($data->resource['agent'] as $agent)
                                     <option data-tokens="{{ $agent->first_name }}" value="{{ $agent->id }}">{{ $agent->first_name }} </option>
                                @endforeach
                           </select>
                            </div>


                                <div class="col-md-6 form-group">
                                    <label>Lead</label>
                            <select class="form-control selectbox lead_names selectpicker" data-live-search="true" name="lead_id" value="lead_id">
                                <option disabled="disabled" selected="selected">Select lead</option>
                                @foreach ($data->resource['lead'] as $lead)
                                @if($lead->formatted_address == '')
                                     <option data-tokens="{{ $lead->id }}" value="{{ $lead->id }}" data-name="{{$lead->title}}">{{ $lead->title }} </option>
                                     @else
                                     <option data-tokens="{{ $lead->id }}" value="{{ $lead->id }}" data-name="{{$lead->title}}">{{ $lead->formatted_address }} </option>
                                     @endif
                                @endforeach
                           </select>
                                </div>


                                 <div class="col-md-6 form-group pb-15">
                                    <label>Commission Event</label>
                            <select class="form-control selectbox selectpicker" data-live-search="true" name="commission_event" value="">
                                <option disabled="disabled" selected="selected">Select Commission Event</option>
                                @foreach ($data->resource['commission_event'] as $event)
                                     <option data-tokens="{{ $event->id }}" value="{{ $event->title }}" data-name="{{$lead->title}}">{{ $event->title }} </option>
                                @endforeach
                           </select>

                                </div>

                     <div class="col-md-6 form-group pb-15">
                    <label>Commission</label>
                    <input name="commission" class="input" type="number" id="input-number">
                    </div>
                    <div class="col-md-6 form-group">
                    <label>Date</label>
                    <!-- <input name="month" class="date-picker input"> -->
                    <input type="date" id="month_date" name="month" class="input">
                    </div>

                    <div class="col-md-6 form-group">
                    <label>Comments</label>
                    <textarea class="form-control" rows="2" name="comments" ></textarea>
                    </div>
                    

                     <div class="col-md-6 form-group form_lead" style="display: none;">
                    <label>Lead Name</label>
                    <input name="lead_name" class="input form_lead lead_name"  type="text">
                    </div>

                     <div class="col-md-12">
                            <button  class="btn b2 margintop ajax-button">Save</button>
                    </div>
                </form>
        </div>
            <!--footer-->
 </div>

                <script>
                    $(function() {

                        current_date_obj = new Date();
                        current_month = current_date_obj.getMonth() + 1;
                        if(current_month < 10)
                            current_month = '0' + current_month;

                        $('#month_date').val(current_date_obj.getFullYear() + '-' + current_month);
                        $('.date-picker').datepicker( {
                            changeMonth: true,
                            changeYear: true,
                            showButtonPanel: true,
                            dateFormat: 'MM yy',
                            //defaultDate:new Date(),
                            onClose: function(dateText, inst) {
                                $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
                            }
                        });
                    });
    
                    $('.lead_names').on('change',function(){
                        var option = $('option:selected', this).data('name');
                        
                            $('.lead_name').val(option);
                            
                            $('.form_lead').show();


                    })



                </script>
            @include('tenant.include.footer')

                <!--footer-->

