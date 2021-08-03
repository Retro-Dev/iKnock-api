@include('tenant.include.header')
@include('tenant.include.sidebar')
<div class="right_col" role="main">
    <div class="row" id="content-heading">
        <!--content-heading here-->
            <div class="col-md-12">
                <h1 class="cust-head">Add Template</h1>
            </div>
    </div>

    <hr class="border">

    <!--content-heading-end-->
    <div class="row" id="pg-form">
    <div class="col-md-2"></div>
    <div class="col-md-8">
        @include('tenant.error')
        <form method="post">
                    <input type="hidden" class="submit_url" value="{{ URL::to('tenant/template/create') }}" />
                    <input type="hidden" class="redirect_url" value="{{ URL::to('tenant/template') }}">
                    <input type="hidden" class="temp_id" value="">
                    {{ csrf_field() }}
               
                    <label>Template Name</label>
                    <input type="text" placeholder="Enter Template Name" name="title" class="input">
               
        
          
                    <button  class="btn b2 margintop ajax-button">Save</button>
            
        </form>
    </div>
    </div>
</div>

    @include('tenant.include.footer')
        <!--footer-->

