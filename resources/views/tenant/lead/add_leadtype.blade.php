@include('tenant.include.header')
@include('tenant.include.sidebar')
<div class="right_col" role="main">
    <div class="row" id="content-heading">
        <!--content-heading here-->
            <div class="col-md-12">
                <h1 class="cust-head">Add Lead Type</h1>
            </div>
    </div>

    <hr class="border">

    <!--content-heading-end-->
    <div class="row" id="pg-form">
        @include('tenant.error')
                <form method="post">
                            <input type="hidden" class="submit_url" value="{{ URL::to('tenant/type/create') }}" />
                            <input type="hidden" class="redirect_url" value="{{ URL::to('tenant/lead/lead_type') }}">
                            <input type="hidden" class="temp_id" value="">
                            {{ csrf_field() }}
                        <div class="col-md-5">
                            <label>Type</label>
                            <input type="text" placeholder="Enter Type" name="title" class="input">
                        </div>
                    <div class="col-md-5">
                        <label>System Generated Code</label>
                        <input type="text" placeholder="Enter Code"   name="code" class="input">
                    </div>

                    <div class="col-md-2">
                            <button  class="btn b2 margintop ajax-button">Save</button>
                    </div>
             </form>
    </div>
</div>

<script>
$(document).ready(function(){
    $('input[name="title"]').focusout(function(){
        var str = $(this).val();
        var char1 = str.charAt(0);
        for (var i = 0; i <1; i++)
       var char2 = str.charAt(Math.floor(Math.random() * str.length));
        var resl = char1+char2;
    $('input[name="code"]').val(resl);
    })
    

})
</script>
    @include('tenant.include.footer')
        <!--footer-->

