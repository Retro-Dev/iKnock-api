@include('tenant.include.header')
@include('tenant.include.sidebar')
<div class="right_col" role="main">
    <div class="row" id="content-heading">
        <!--content-heading here-->
        <div class="col-md-8">
        <h1 class="cust-head">Edit Lead Status</h1>
        </div>

          <div class="col-md-4 text-right">
                      <button class="btn b2 delete">Delete</button>
                      
                </div>
    </div>

    <hr class="border">

    <div class="row" id="pg-form">
       
        @include('tenant.error')
               
       
            <form>
                 <input type="hidden" name="id" class="id" value=""/>
                <input type="hidden" class="submit_url" value="" />
                <input type="hidden" class="redirect_url" value="{{ URL::to('tenant/lead/lead_status') }}">
                 {{ csrf_field() }}
                <div class="col-md-6">
                    <label>Lead Status</label>
                    <input type="text" name="title" placeholder="" class="input">
                </div>

                <div class="col-md-2">
                    <label>Lead Code</label>
                    <input  type="text" name="code" class="input" value="">
                </div>

                <div class="col-md-2">
                    <label>Colors</label>
                    <input id="demo" type="text" name="color_code" class="input" value="">
                </div>

                
                <div class="col-md-4" style="margin-top: 15px;">
                      <button class="btn  b2 ajax-button">Save</button>
                </div>
            </form>
            
    </div>
</div>
    <script>
        $(function () {
            // Basic instantiation:
            // $('#demo').colorpicker();
            $('#demo').colorpicker({
                format: 'hex'
            });


        });

    </script>
    <!--footer-->
    <script type="text/javascript">
$(document).ready(function(){
    let current_url = window.location.href;
        current_url = current_url.split('/');
    let id  = current_url.slice(-1)[0]; 
    $('.id').val(id);
    $('.submit_url').val("{{ URL::to('tenant/status/edit') }}" + "/" + id);
    
    var columns = ['title','code','color_code'];    
    getEditRecord('GET',base_url + "/tenant/status/detail/"+id,{},{},columns);
    
    

    $('.delete').on('click', function() {
    var choice = confirm('Do you really want to delete this record?');
    if(choice === true) {
       
      let deleteRecord =   "{{ URL::to('tenant/status/delete') }}" + "/" + id;
      
      ajaxCall('POST',deleteRecord,{id},{}).then(function(res)
        {
            if(res.code == 200)
            {
                $(".delete").prop('disabled', true);
       var redirect_url = $('.redirect_url').val();
           redirect_url = typeof redirect_url == 'undefined' ? window.location.href : redirect_url;
           setTimeout(function(){
                       window.location.href = redirect_url;
                    },1000)
            }

        })
      
    }
    return false;
});
})
</script>
@include('tenant.include.footer')
    <!--footer>