@include('tenant.include.header')
@include('tenant.include.sidebar')
<div class="right_col" role="main">
    <div class="row" id="content-heading">
        <!--content-heading here-->
        <div class="col-md-12">
            <h1 class="cust-head">Add Printer Email</h1>
        </div>
    </div>
    <hr class="border">
    <!--content-heading-end-->
    <div class="row" id="pg-form">
        <div class="col-md-2"></div>
        <form method="post" enctype="multipart/form-data" autocomplete="off">
        <div class="col-md-8">
           @include('tenant.error')
           <input type="hidden" class="redirect_url" value="{{ URL::to('/tenant/printer_email') }}">
            <input type="hidden" class="submit_url" value="{{ URL::to('tenant/printer/email/update') }}" />
            <input type="hidden" class="printer_emails" value="" />
            {{ csrf_field() }}
            <div class="col-sm-12 col-md-12 col-xs-12">
                <label>Printer Email</label>
                <input type="text"class="input-field input printer_emails" name="printer_email_address" >
                
            </div>
            
            <div class="col-lg-12">
                <button  class="btn b2 submit ajax-button">Save</button>
            </div>
        </div>
    </form>
        <div class="col-md-2"></div>
    </div>
</div>
<script type="text/javascript">
$(document).ready(function(){
    $(document).on('click','.add-email',function(){
        var values = $('input[name="printer_email_address"]').val();
        $('.printer_emails').val(values);
       
        // var data = {printer_email_address:values};
        //        var data =  ajaxCall('POST', base_url + "/tenant/printer/email/update", data, {});
        //             var redirect_url = $('.redirect_url').val();
        //    redirect_url = typeof redirect_url == 'undefined' ? window.location.href : redirect_url;
        //    setTimeout(function(){
        //                window.location.href = redirect_url;
        //             },1000)

    })

})
</script>
@include('tenant.include.footer')