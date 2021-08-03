@include('tenant.include.header')
<style>
     .cust-nav>li>a{
        line-height: 0px !important;
    }
    </style>
@include('tenant.include.sidebar')
<div class="right_col" role="main">
    <div class="row" id="content-heading">
        <!--content-heading here-->
        <div class="col-md-8">
            <h1 class="cust-head">Edit Script</h1>
        </div>

    <div class="col-md-4 text-right">
                      <button class="btn btn-info b2 delete">Delete</button>
                      
                </div>

    </div>
    <hr class="border">
    <!--content-heading-end-->
    <div class="row" id="pg-form">
        <div class="col-md-3"></div>
        <div class="col-md-6">
             @include('tenant.error')
            <form>
                 <input type="hidden" name="id" class="id" value=""/>
                <input type="hidden" class="submit_url" value="" />
                <input type="hidden" class="redirect_url" value="{{ URL::to('tenant/training') }}">
                 <input type="hidden" name="delete_media" class="delete_media" value="">
                 {{ csrf_field() }}
        <div class="form-group row">
                <label>Title</label>
                <input type="text"  class="input" id="staticEmail" placeholder="Enter Title" value="" name="title">
        </div>
        <div class="form-group row">
                <label>Description</label>
                <textarea class="form-control hint" rows="5" name="description"></textarea>

        </div>
       <div class="row form-group">
                <label>Upload Image</label>
                <article class="input">
                    <label for="files">
                    <input id="files" type="file" class="" multiple name="image_url[]" ></label>
<!--                    <button type="button" id="b1" class="btn btn-default">Clear</button>-->
<!--                    <output id="result" />-->
                </article>
        </div>

          <div class="row">

                <label>Upload PDF</label>


                <article class="input">
                    <label for="files">
                    <input id="files" multiple name="image_url[]" type="file" class="" >
                </label>

                </article>

        </div>
        <div class="row margintop view_image" >
                

        </div>

                <div class="form-group row">
                <button class="btn btn-info b2 margintop ajax-button">Save</button>
        </div>
</form>
        </div>
        <div class="col-md-3"></div>
    </div>
</div>


<script type="text/javascript">
$(document).ready(function(){
    let current_url = window.location.href;
        current_url = current_url.split('/');
    let id  = current_url.slice(-1)[0]; 
    $('.id').val(id);
    $('.submit_url').val("{{ URL::to('tenant/user/training') }}" + "/" + id); //Update API
    
    var columns = ['title','description','media'];    
    getEditRecord('GET',base_url + "/tenant/user/training/"+id,{},{},columns); //Get Record
    
    

    $('.delete').on('click', function() {
    var choice = confirm('Do you really want to delete this record?');
    if(choice === true) {
       
      let deleteRecord =   "{{ URL::to('tenant/user/training/delete') }}" + "/" + id;
      
      ajaxCall('POST',deleteRecord,{id},{});
       var redirect_url = $('.redirect_url').val();
       $(".delete").prop('disabled', true);
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



