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
        <form>
             @include('tenant.error')
            <input type="hidden" class="submit_url" value="{{ URL::to('tenant/query/create') }}" />
            <input type="hidden" class="redirect_url" value="{{ URL::to('tenant/field') }}">
            {{ csrf_field() }}
                <div class="col-md-4">
                    <label>Field Name</label>
                    <input type="text" placeholder="" name="query" class="input">
                </div>

                <div class="col-md-5">
                    <label>Field Type</label>
                    <select class="form-control selectbox select" name="type">
                        <option value="summary">Lead Summary</option>
                        <option value="appointment"> Appointment Schedule</option>
                    </select>
                </div>

               <div class="col-md-3">
                <button  class="btn btn-info b2 margintop ajax-button">Save</button>
            </div>
        </form>
    </div>
</div>
<!--footer-->
    @include('tenant.include.footer')
<!--footer-->

