<?php //echo "<pre>"; print_r($data); exit(); ?>
@include('tenant.include.header')
@include('tenant.include.sidebar')

<div class="right_col" role="main">
   <div class="row" id="content-heading">
      <!--content-heading here-->
      <div class="col-md-8">
         <h1 class="cust-head">Scheduling</h1>
      </div>
      <div class="col-md-4 text-right">
         <button class="btn  b2 delete">Delete</button>
      </div>
   </div>
   <hr class="border">
   <div class="" id="pg-form">
      <div class="col-md-2"></div>
      <div class="col-md-8">
         @include('tenant.error')
         <form method="post" enctype="multipart/form-data">
            <input type="hidden" class="redirect_url" value="{{ URL::to('tenant/scheduling') }}">
            <input type="hidden" class="submit_url" value="{{ URL::to('tenant/scheduling') }}
               " />
            <input type="hidden" name="id" class="id" value=""/>
            {{ csrf_field() }}
            <div class="form-group">
               <label>Start Date</label>
               <!--  <input name="start_date" type="text" value="{{$data['detail']->appointment_date}}" class="input form_datetime" > -->
               <div class="form-group">
                   <input type='text' value="{{$data['detail']->appointment_date}}" class="form-control datetimepicker_class" id='datetimepicker1' name="start_date"/>
               </div>
            </div>
            <div class="form-group">
               <label>End Date</label>
               <!-- <input name="end_date" type="text" value="{{$data['detail']->appointment_end_date}}" class="input form_datetime" > -->
               <div class="form-group">
                
                  <input type='text' value="{{$data['detail']->appointment_end_date}}" class="form-control datetimepicker_class" id='datetimepicker2' name="end_date"/>
               </div>
            </div>
            
            <div class="form-group">
               <label>Slot Type</label><br>
               <input type="radio" id="user" name="slot_type" value="users" {{ ($data['slot_type'] == 'user') ? "checked" : "" }}>
               <label>User</label><br>
               <input type="radio" id="lead" name="slot_type" value="leads"{{ ($data['slot_type'] == 'leads') ? "checked" : "" }}>
               <label>Leads</label><br>
            </div>
            
            <div class="form-group agent_list">
               <label>Select User</label>
               @if(count($data['agent']))
               <select class="form-control selectpicker" data-live-search="true"
                  name="target_user_id" value="" >
               @foreach ($data['agent'] as $agent)
               <option data-tokens="{{ $agent->title }}"
               @if ($agent->id  == $data['detail']->user_id)
               selected="selected"
               @endif
               value="{{ $agent->id }}">{{ $agent->first_name }} </option>
               @endforeach
               </select>
               @else
               <div class="form-group">
                  <input type="text" name="" class="form-control" value="No user Found"
                     disabled="disabled">
               </div>
               @endif
            </div>
            
            <div class="form-group lead_list">
                <div><br/></div>
               <label>Select Lead</label>
               @if(count($data['leads']))
               <select class="form-control selectpicker" data-live-search="true"
                  name="lead_id" value="" >
               @foreach ($data['leads'] as $lead)
               <option data-tokens="{{ $agent->title }}"
               @if ($lead->id  == $data['detail']->lead_id)
               selected="selected"
               @endif
               value="{{ $lead->id }}">{{ $lead->title }} </option>
               @endforeach
               </select>
               @else
               <div class="form-group">
                  <input type="text" name="" class="form-control" value="No user Found"
                     disabled="disabled">
               </div>
               @endif
            </div>
            
            <div class="form-group mt-40">
               <label>Note</label>
               <input name="note" type="text" value="{{$data['detail']->result}}" class="input" >
            </div>
            <div class="form-group">
               <button class="btn btn-info b2 ajax-button">Save</button>
            </div>
         </form>
      </div>
   </div>
</div>
<script>
   $(document).ready(function () {
      let current_url = window.location.href;
      current_url = current_url.split('/');
      
      if($('#lead').is(':checked')) { $(".lead_list").show();
     $(".agent_list").hide();
     $('#user').attr('disabled','disabled');
     }
     
      if($('#user').is(':checked')) { $(".lead_list").hide();
     $(".agent_list").show();
     $('#lead').attr('disabled','disabled');
     }
     
        $("input[type='radio']").change(function(){
   if($(this).val()=="leads")
   {
     $(".lead_list").show();
     $(".agent_list").hide();
     $(".agent_list").removeAttr('checked');
     $(".lead_list").attr('checked');
   }
   else if($(this).val()=="users")
   {
     $(".agent_list").show();
     $(".lead_list").hide();
   }
   
   });
   
      let id = current_url.slice(-1)[0];
      $('.id').val(id);
      var columns = ['start_date', 'end_date', 'user_name', 'note'];
     // getEditRecord('POST', base_url + "/tenant/scheduling/" + id, {}, {}, columns, 'agent'); // UPDATE FUNCTION
   
   //Delete Function
   
      $('.delete').on('click', function () {
          var choice = confirm('Do you really want to delete this record?');
          if (choice === true) {
   
              let deleteRecord = "{{ URL::to('tenant/scheduling/delete') }}" + "/" + id;
   
              ajaxCall('POST', deleteRecord, {id}, {});
              $(".delete").prop('disabled', true);
   
              var redirect_url = $('.redirect_url').val();
              redirect_url = typeof redirect_url == 'undefined' ? window.location.href : redirect_url;
              setTimeout(function () {
                  window.location.href = redirect_url;
              }, 1000)
   
          }
          return false;
      });
   })
   
   $(function () {
      // Basic instantiation:
      // $('#demo').colorpicker();
      $('#demo').colorpicker({
          format: 'hex'
      });
   
   
   
   });
</script>
<script type="text/javascript">
   $(function () {
       $('#datetimepicker1').datetimepicker({
            format: "MM-DD-YYYY hh:mm a",
            showClose:true
       });
   });
   
     $(function () {
       $('#datetimepicker2').datetimepicker({
           format: "MM-DD-YYYY hh:mm a",
           showClose:true
       });
   });
</script>
<!--footer-->
@include('tenant.include.footer')
<!--footer>