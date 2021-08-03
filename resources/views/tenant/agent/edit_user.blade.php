<?php //print_r($data);exit(); ?>
@include('tenant.include.header')
<style type="text/css">
  
  .cross{display: none;}
  .view_image{text-align: center;}
  #pg-form {
    margin-top: 0px;
}
</style>
@include('tenant.include.sidebar')
<div class="right_col" role="main">
    <div class="row" id="content-heading">
        <!--content-heading here-->
        <div class="col-md-12">
            <h1 class="cust-head">Account Details</h1>
        </div>
      
    </div>
    <hr class="border">
    <form method="post" enctype="multipart/form-data">
    <!--content-heading-end-->
    <div class="row" id="pg-form">        
        

   
        <div class="row" id="pg-form">
            <input type="hidden" class="submit_url" value="{{ URL::to('tenant/user/update') }}" />
            <input type="hidden" class="redirect_url" value="{{ URL::to('tenant/dashboard') }}">

            <div class="col-md-2"></div>
            <div class="col-md-8">
                @include('tenant.error')
                {{ csrf_field() }}

                 <div class="col-md-12 ">
                    <div class="form-group ">
                       <div class="row margintop view_image" ></div>
                    </div>
                </div>
                <div class="col-md-6 ">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" id="name" class="input" name="name" value="{!! $data['first_name']. ' ' . $data['last_name'] !!}">
                    </div>
                    </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Password</label>
                        <input type="password" class="input" name="password" value="">
                    </div>
                </div>

            

                 <!-- <div class="col-md-6">
                    <div class="form-group">
                        <label>Printer Number</label>
                        <input type="number" class="input" name="printer_number" value="">
                    </div>
                </div> --> 
                <div class="col-md-12">
                      <button class="btn btn-info b2 ajax-button">Save</button>
                </div>
   
    </div>

 

   
 </form>
    </div>   <!--footer-->

<!-- <script type="text/javascript">
$(document).ready(function(){
    
    var columns = ['name','password','printer_email_address'];    
    //getEditRecord('POST',base_url + "/tenant/user/profile",{},{},columns); // UPDATE FUNCTION

})


</script> -->
@include('tenant.include.footer')

    <!--footer-->