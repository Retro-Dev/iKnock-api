@include('tenant.include.header')
@include('tenant.include.sidebar')
<div class="right_col" role="main">
    <div class="row" id="content-heading">
        <!--content-heading here-->
        <div class="col-md-8">
            <h1 class="cust-head">Edit Commission </h1>
        </div>

        <div class="col-md-4 text-right">
                      <button class="btn btn-info b2 delete">Delete</button>
                      
                </div>

    </div>

    <hr class="border">

    <div class="row" id="pg-form">
        @include('tenant.error')
            <form>
                <input type="hidden" name="id" class="id" value=""/>
                <input type="hidden" name="target_id" class="target_id" value=""/>
                <input type="hidden" name="lead_id" class="lead_id" value=""/>
                <input type="hidden" class="submit_url" value="" />
                <input type="hidden" class="redirect_url" value="{{ URL::to('tenant/commission') }}">
                {{ csrf_field() }}
                    <div class="col-md-6 form-group">
                        <label>User Name</label>
                            
                            <input type="text" name="user_name" class="input" disabled="disabled">
                    </div>
                    <div class="col-md-6 form-group">
                        <label>Lead</label>
                            
                            <input type="text" name="lead_title" class="input" disabled="disabled">
                    </div>

                    <div class="col-md-6 form-group">
                                    <label>Commission Event</label>
                                        @if(count($data->resource['commission_event']))
    
                            <select class="form-control selectbox selectpicker" data-live-search="true" name="commission_event" value="">
                                <!-- <option disabled="disabled" selected="selected">Select Commission Event</option> -->
                                @foreach ($data->resource['commission_event'] as $event)
                                     <option data-tokens="{{ $event->id }}" value="{{ $event->title }}" data-name="{{$lead->title}}">{{ $event->title }} </option>
                                @endforeach
                           </select>
                    
                                @endif
                                </div>

                    <div class="col-md-6 form-group">
                        <label>Commission</label>
                        <input type="number" name="commission" class="input">
                    </div>

                    <div class="col-md-6">
                        <label>Date</label>
                        
                        <input type="date" class="input" name="target_month">
                    </div>

                    <div class="col-md-6 form-group">
                        <label>Comments</label>
                        <textarea class="form-control" rows="2" name="comments"></textarea>
                    </div>

                   <div class="form-group col-md-12">
                <button class="btn btn-info b2 margintop ajax-button">Save</button>
        </div>

            </form>
    </div>

</div>
<script>
    $(function() {
        $('.date-picker').datepicker( {
            changeMonth: true,
            changeYear: true,
            showButtonPanel: true,
            dateFormat: 'MM yy',
            onClose: function(dateText, inst) {
                $(this).datepicker('setDate', new Date(inst.selectedYear, inst.selectedMonth, 1));
            }
        });
    });
</script>

<script type="text/javascript">
$(document).ready(function(){
    let current_url = window.location.href;
        current_url = current_url.split('/');
    let id  = current_url.slice(-1)[0]; 
    $('.id').val(id);


    $('.submit_url').val("{{ URL::to('tenant/user/commission') }}" + "/" + id); //Update API
    
    var columns = ['comm_target_id','lead_id','user_name','lead_title','new_commission_event','commission','target_month','comments'];    
    getEditRecord('GET',base_url + "/tenant/user/commission/"+id,{},{},columns); //Get Record
    
    

    $('.delete').on('click', function() {
    var choice = confirm('Do you really want to delete this record?');
    if(choice === true) {
       
      let deleteRecord =   "{{ URL::to('tenant/user/commission/delete') }}" + "/" + id;
      
      ajaxCall('POST',deleteRecord,{id},{});
      $(".delete").prop('disabled', true);
       var redirect_url = $('.redirect_url').val();
           redirect_url = typeof redirect_url == 'undefined' ? window.location.href : redirect_url;
           setTimeout(function(){
                       window.location.href = redirect_url;
                    },1000)

    }
    return false;
});
})
</script>
              @include('tenant.include.footer')

        <!--footer-->
