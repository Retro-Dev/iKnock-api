@include('tenant.include.header')
@include('tenant.include.sidebar')
<div class="right_col" role="main">
    <div class="row" id="content-heading">
        <!--content-heading here-->
        <div class="col-md-8">
            <h1 class="cust-head">Edit Lead Type</h1>
        </div>


        <div class="col-md-4 text-right">
            <button class="btn btn-info b2 delete">Delete</button>

        </div>

    </div>

    <hr class="border">
    <!--content-heading-end-->
    <div class="row" id="pg-form">
       @include('tenant.error')
        <form>
            <input type="hidden" name="id" class="id" value=""/>
                <input type="hidden" class="submit_url" value="" />
                <input type="hidden" class="redirect_url" value="{{ URL::to('tenant/commission_event') }}">
            {{ csrf_field() }}

            <div class="col-md-4 col-md-offset-3">
                <label>Title</label>
                <input type="text" placeholder="" name="title"  class="input">
            </div>


            <div class="col-md-2 margintop">
                <button class="btn b2 ajax-button">Save</button>
            </div>

        </form>
    </div>   <!--footer-->
</div>

<script type="text/javascript">
$(document).ready(function(){
    let current_url = window.location.href;
        current_url = current_url.split('/');
    let id  = current_url.slice(-1)[0]; 
    $('.id').val(id);
    $('.submit_url').val("{{ URL::to('tenant/commission/event/edit') }}" + "/" + id);
    
    var columns = ['title']; 
    getEditRecord('GET',base_url + "/tenant/commission/event/detail/"+id,{},{},columns);
    
    

    $('.delete').on('click', function() {
    var choice = confirm('Do you really want to delete this record?');
    if(choice === true) {
       
      let deleteRecord =   "{{ URL::to('tenant/commission/event/delete') }}" + "/" + id;
      
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