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
            <input type="hidden" class="submit_url" value="{{ URL::to('tenant/template/field/update') }}" />
            <input type="hidden" class="redirect_url" value="">
            <input type="hidden" name="template_id"  value="{{$data[0]->template_id}}" class="input template_id">
            <input type="hidden" name="field"  value="{{$data[0]->field}}" class="input id">
            {{ csrf_field() }}
            <div class="col-sm-6 col-md-6 col-xs-12">
                <label>Custom Fields for Mapping</label>
                @if($data[0]->is_fixed == 1)
                    <input type="hidden" name="query"  value="{{$data[0]->field}}" class="input id">
                    <input type="text" placeholder="Add Field" name="query1" class="input" value="{{$data[0]->key}}">
                @else
                <input type="text" placeholder="Add Field" name="query" class="input" value="{{$data[0]->key}}">
                @endif

            </div>

            <div class="col-sm-6 col-md-6 col-xs-12">
                <label>File Column in CSV File</label>
                <input type="text" placeholder="Excel column" name="index" class="input" value="{{$data[0]->index_map}}" >
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
        let template_id = $('.template_id').val();
        let redirect_url = '{{ URL::to("tenant/template/edit") }}'
        $('.redirect_url').val(redirect_url + '/' + template_id );
        $('.id').val(id);
})
</script>

@include('tenant.include.footer')

<!--footer-->

