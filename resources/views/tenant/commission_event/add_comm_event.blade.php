@include('tenant.include.header')
@include('tenant.include.sidebar')
<div class="right_col" role="main">
    <div class="row" id="content-heading">
        <!--content-heading here-->
        <div class="col-md-12">
            <h1 class="cust-head">Add Commission Event</h1>
        </div>
    </div>

    <hr class="border">

    <!--content-heading-end-->
    <div class="row" id="pg-form">
        @include('tenant.error')
        <form method="post">
            <input type="hidden" class="submit_url" value="{{ URL::to('tenant/commission/event/create') }}" />
            <input type="hidden" class="redirect_url" value="{{ URL::to('tenant/commission_event') }}">
            {{ csrf_field() }}
            <div class="col-md-4 col-md-offset-3">
                <label>Title</label>
                <input type="text" placeholder="Enter Type" name="title" class="input">
            </div>


            <div class="col-md-2">
                <button  class="btn b2 margintop ajax-button">Save</button>
            </div>
        </form>
    </div>
</div>
@include('tenant.include.footer')

<!--footer-->

