@include('tenant.include.header')
@include('tenant.include.sidebar')
<div class="right_col" role="main">
    <div class="row" id="content-heading">
        <!--content-heading here-->
        <div class="col-md-12">
            <h1 class="cust-head">Add Field</h1>
        </div>
    </div>

    <hr class="border">

    <!--content-heading-end-->
    <div class="row" id="pg-form">
        @include('tenant.error')
        <div class="col-md-2"></div>
        <div class="col-md-8">
        <form method="post">
            <input type="hidden" class="submit_url" value="{{ URL::to('tenant/template/field/create') }}" />
            <input type="hidden" class="redirect_url" value="">
            <input type="hidden" placeholder="Add Field" name="template_id" val="" class="input id">
            {{ csrf_field() }}
            <div class="col-sm-6 col-md-6 col-xs-12">
                <label>Custom Fields for Mapping</label>
                <input type="text" placeholder="Add Field" name="query" class="input">
               
            </div>

            <div class="col-sm-6 col-md-6 col-xs-12">
                <label>File Column in CSV File</label>
                <input type="text" placeholder="Excel column" name="index" class="input">
            </div>


            <div class="col-md-12">
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
        let redirect_url = '{{ URL::to("tenant/template/edit") }}'
        $('.redirect_url').val(redirect_url + '/' + id );
        $('.id').val(id);
       
        //$('.submit_url').val("{{ URL::to('/tenant/template/field/delete') }}" + "/" + id);
})
</script>

@include('tenant.include.footer')

<!--footer-->

