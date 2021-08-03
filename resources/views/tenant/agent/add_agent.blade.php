@include('tenant.include.header')
@include('tenant.include.sidebar')
<style>
    .selectbox{
        height:51px !important;
    }
</style>
<div class="right_col" role="main">
    <div class="row" id="content-heading">
        <!--content-heading here-->
        <div class="col-md-12">
            <h1 class="cust-head">Add User</h1>
        </div>
    </div>
    <hr class="border">
    <!--content-heading-end-->
    <div class="row" id="pg-form">
        <div class="col-md-2"></div>
        <form method="post" enctype="multipart/form-data" autocomplete="off">
        <div class="col-md-8">
           @include('tenant.error')
            <input type="hidden" class="submit_url" value="{{ URL::to('tenant/agent/create') }}" />
             <input type="hidden" class="redirect_url" value="{{ URL::to('tenant/agent') }}">
            {{ csrf_field() }}
            <div class="col-sm-6 col-md-6 col-xs-12">
                <label>Name</label>
                <input type="text"class="input-field input" name="name" >
                
            </div>
            <div class="col-sm-6 col-md-6 col-xs-12">
                <label>Email</label>
                <input type="email" class="input-field input" name="email">
            </div>
            <div class="col-sm-6 col-md-6 col-xs-12">
                <!--<label>Password</label>-->
                <input type="hidden"  class="input-field input" name="password">
            </div>
            <div class="col-sm-6 col-md-6 col-xs-12">
                <!--<label>Confirm Password</label>-->
                <input type="hidden"  class="input-field input" name="confirm_password">
            </div>
            <div class="col-sm-6 col-md-6 col-xs-12 form-group">
                <label>Joining Date</label>
                <input type="date"  class="input" name="date_of_join">
            </div>
            <div class="col-sm-6 col-md-6 col-xs-12">
                <label>Phone Number</label>
                <input type="number" class="input" name="mobile_no" min="0" id="input-number">
            </div>
            <div class="col-md-12 form-group">
                                    <label>User Type</label>
                            <select class="form-control selectbox selectpicker" data-live-search="true" name="user_group_id" value="">
                                <option disabled="disabled" selected="selected">Select User Type</option>
                                     <option  value="2">Mobile User </option>
                                     <option  value="3">Sub Admin </option>
                                
                           </select>

                                </div>
            <div class="col-sm-12 col-xs-12">
                <label>Choose image</label>
                <input type="file" id="image_url" class="input-field" name="image_url">
            </div>


            <div class="col-lg-12">
                <button  class="btn b2 submit ajax-button">Submit</button>
            </div>
        </div>
    </form>
        <div class="col-md-2"></div>
    </div>
</div>
<script type="text/javascript">
    $("input[type='image']").click(function() {
    $("input[id='my_file']").click();
});
</script>
@include('tenant.include.footer')