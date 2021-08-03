@include('tenant.include.header')
@include('tenant.include.sidebar')
<div class="right_col" role="main">
    <div class="row" id="content-heading">
        <!--content-heading here-->
        <div class="col-md-12">
            <h1 class="cust-head">Add Lead Status</h1>
        </div>
    </div>

    <hr class="border">

    <div class="row" id="pg-form">
                    @include('tenant.error')
            <form method="post" enctype="multipart/form-data">
                                <input type="hidden" class="submit_url" value="{{ URL::to('tenant/status/create') }}" />
                                <input type="hidden" class="redirect_url" value="{{ URL::to('tenant/lead/lead_status') }}">
                                {{ csrf_field() }}
                        <div class="col-md-6">
                            <label>Lead Status</label>
                            <input type="text" placeholder="Enter Type" class="input" name="title">
                        </div>

                        <div class="col-md-2">
                            <label>Lead Code</label>
                            <input type="text" placeholder="Enter Code" class="input" name="code">
                        </div>

                        <div class="col-md-2">
                            <label>Colors</label>
                            <input id="demo" type="text" class="input" value="#abcabc" name="color_code">
                        </div>

                    <div class="col-md-2">
                            <!-- <input type="submit" value="Save" class="b2 margintop ajax-button"> -->
                            <button class="btn margintop ajax-button b1">Save</button>
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
@include('tenant.include.footer')
        <!--footer>