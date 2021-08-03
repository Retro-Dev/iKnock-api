@include('tenant.include.header')
@include('tenant.include.sidebar')
<div class="right_col" role="main">
    <div class="row" id="content-heading">
        <!--content-heading here-->
        <div class="col-md-12">
            <h1 class="cust-head">Edit Field</h1>
        </div>
    </div>

    <hr class="border">

    <!--content-heading-end-->
    <div class="row" id="pg-form">
        @include('tenant.error')
        <div class="col-md-2"></div>
        <div class="col-md-8">
        <form method="post">
            <input type="hidden" class="submit_url" value="{{ URL::to('tenant/lead/default/field/update') }}" />
            <input type="hidden" class="redirect_url" value="">
            <input type="hidden" name="field_id" class="id" value=""/>
            {{ csrf_field() }}
            <div class="col-sm-8 col-md-8 col-xs-12">
                <label>Field Name</label>
                <input type="text" placeholder="Edit Field" name="field" class="input" value="{{$data[0]->key}}">
               
            </div>

            <div class="col-md-2">
                <button  class="btn b2 margintop ajax-button">Save</button>
            </div>
        </form>
</div>
<div class="col-md-2"></div>
    </div>
</div>
<script>
$(document).ready(function(){
    let current_url = window.location.href;
        current_url = current_url.split('/');
        let id = current_url.slice(-1)[0];
        let redirect_url = '{{ URL::to("tenant/lead-default-order") }}'
        $('.redirect_url').val(redirect_url);
        $('.id').val(id);
})
</script>

@include('tenant.include.footer')

<!--footer-->

