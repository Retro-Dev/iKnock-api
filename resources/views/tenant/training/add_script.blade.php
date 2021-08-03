@include('tenant.include.header')
@include('tenant.include.sidebar')
<div class="right_col" role="main">
    <div class="row" id="content-heading">
        <!--content-heading here-->
        <div class="col-md-12">
        <h1 class="cust-head">Add Script</h1>
        </div>

    </div>
    <hr class="border">
    <!--content-heading-end-->
    <div class="row" id="pg-form">
        <div class="col-md-3"></div>
        <div class="col-md-6">
        @include('tenant.error')
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" class="submit_url"  value="{{ URL::to('tenant/user/training/create') }}" />
             <input type="hidden" class="redirect_url" value="{{ URL::to('tenant/training') }}">
            
                    {{ csrf_field() }}
        <div class="form-group row">
                <label>Title</label>
                <input type="text" name="title" required="required"  class="input" id="staticEmail" placeholder="Enter Title">
        </div>
        <div class="form-group row">
                <label>Description</label>
                <textarea class="form-control hint" rows="5" required="required" name="description"></textarea>
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
<!--                    <button type="button" id="b1" class="btn btn-default">Clear</button>-->
<!--                    <output id="result" />-->
                </article>

        </div>

            <div class="col-md-12 row">
                <button  class="btn b2 margintop ajax-button" style="margin-left: -8px;">Save</button>
            </div>


</form>
    </div>
        <div class="col-md-3"></div>

    </div>
</div>

@include('tenant.include.footer')

<!--footer-->



