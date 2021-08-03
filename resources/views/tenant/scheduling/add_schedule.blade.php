<?php //echo "<pre>"; print_r($data); exit(); ?>
@include('tenant.include.header')
    
  
@include('tenant.include.sidebar')

<div class="right_col" role="main">
   <div class="row" id="content-heading">
      <!--content-heading here-->
      <div class="col-md-12">
         <h1 class="cust-head">Add Scheduling</h1>
      </div>
   </div>
   <hr class="border">
   <div class="row" id="pg-form">
      <div class="col-md-2"></div>
      <div class="col-md-8 ">
         @include('tenant.error')
         <form method="post" enctype="multipart/form-data">
            <input type="hidden" class="redirect_url" value="{{ URL::to('tenant/scheduling') }}">
            <input type="hidden" class="submit_url" value="{{ URL::to('tenant/scheduling/store') }}" />
            {{ csrf_field() }}
            <div class="form-group">
               <label>Start Date</label>
               <!-- <input name="start_date" type="text" class="input form_datetime" autocomplete="off"> -->
               <div class="form-group">
                  <input type='text' class="form-control datetimepicker_class" id='datetimepicker1' name="start_date"/>
               </div>
               
            </div>
            <div class="form-group">
               <label>End Date</label>
               <!--  <input name="end_date" type="text" class="input form_datetime" autocomplete="off"> -->
               <div class="form-group">
                  <input type='text' class="form-control datetimepicker_class" id='datetimepicker2' name="end_date"/>
               </div>
            </div>
            <div class="form-group">
               <label>Slot Type</label><br>
               <input type="radio" id="user" name="slot_type" value="users" checked>
               <label>User</label><br>
               <input type="radio" id="lead" name="slot_type" value="leads">
               <label>Leads</label><br>
            </div>
            <div class="form-group agent_list">
               <label>Select User</label>
               <select class="form-control selectpicker"
                  data-live-search="true" data-actions-box="true" title="Select User"
                  name="target_user_id[]" value="" multiple>
                  @foreach($data['agent'] as $agent )
                  <option value="{{ $agent->id }}">{{$agent->first_name}}</option>
                  @endforeach
               </select>
               <div><br></div>
            </div>
            <div class="form-group lead_list">
               <label>Select Lead</label>
               <select class="form-control selectpicker"
                  data-live-search="true" data-actions-box="true" title="Select lead"
                  name="lead_id[]" value="" multiple>
                  @foreach($data['leads'] as $lead )
                  <option value="{{ $lead->id }}">{{$lead->title}}</option>
                  @endforeach
               </select>
               <div><br></div>
            </div>
            <div class="form-group">
               <label>Notes</label>
               <textarea name="note" rows="2" cols="25"  class="form-control input"></textarea>
            </div>
            <!-- <input type="submit" class="btn b2 submit" style="margin-top:20px;"> -->
            <button class="btn margintop ajax-button b1">Save</button>
         </form>
      </div>
   </div>
</div>
<script>
   $(function () {
       // Basic instantiation:
       // $('#demo').colorpicker();
       $('#demo').colorpicker({
           format: 'hex'
       });
   
   //     $(".form_datetime").datetimepicker({
   //         format: "mm-dd-yyyy hh:ii",
   //         autoclose: true,
   //         todayBtn: true
   //     });
   
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
<script>
   $(document).ready(function(){
       $(".lead_list").hide();
   
     
   $("input[type='radio']").change(function(){
   if($(this).val()=="leads")
   {
     $(".lead_list").show();
     $(".agent_list").hide(); 
   }
   else if($(this).val()=="users")
   {
     $(".agent_list").show();
     $(".lead_list").hide();
   }
   
   });
   
   })
</script>
<!--footer-->
@include('tenant.include.footer')
<!--footer>