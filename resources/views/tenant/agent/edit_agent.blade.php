@include('tenant.include.header')
<style type="text/css">

    .cross {
        display: none;
    }

    .view_image {
        text-align: center;
    }

    #pg-form {
        margin-top: 0px;
    }
</style>
@include('tenant.include.sidebar')
<div class="right_col" role="main">
    <div class="row" id="content-heading">
        <!--content-heading here-->
        <div class="col-md-8">
            <h1 class="cust-head">Update User Profile</h1>
        </div>
        <div class="col-md-4 text-right">
            <button class="btn  b2 delete">Delete</button>

        </div>
    </div>
    <hr class="border">
    <!--content-heading-end-->
    <div class="" id="pg-form">


        <form method="post" enctype="multipart/form-data">
            <div class="" id="pg-form">
                <input type="hidden" name="target_id" class="id" value=""/>
                <input type="hidden" name="delete_media" class="delete_media" value="">
                <input type="hidden" class="submit_url" value="{{ URL::to('tenant/agent/update') }}"/>
                <input type="hidden" class="redirect_url" value="{{ URL::to('tenant/agent') }}">

                <div class="col-md-2"></div>
                <div class="col-md-8">
                    @include('tenant.error')
                    {{ csrf_field() }}

                    <div class="col-sm-12 col-xs-12 text-center">
                        <div class="form-group ">
                            <div class="margintop view_image"></div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-6 col-xs-12">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" id="name" class="input" name="name">
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-6 col-xs-12">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="input" name="email" value="">
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-6 col-xs-12">
                        <div class="form-group">
                            <label>Joining Date</label>
                            <input type="date" class="input" name="date_of_join">
                        </div>
                    </div>
                    <div class="col-sm-6 col-md-6 col-xs-12">
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="number" class="input number" name="mobile_no" id="input-number">
                        </div>
                    </div>
                    <div class="col-sm-12 col-xs-12">
                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control status selectpicker"
                                   title="Select Status"
                                    name="user_status_id" >
                                <option value="1">Active</option>
                                <option value="0" >In Active</option>


                            </select>
                        </div>
                    </div>
                    <div class="col-sm-12 col-xs-12" style="margin-top:15px;">
                        <div class="form-group">
                            <label>User Type</label>
                            <select class="form-control selectpicker"
                                    name="user_group_id" >
                                <option value="2">Mobile User</option>
                                <option value="3" >Sub Admin</option>


                            </select>
                        </div>
                    </div>

                    <div class="col-sm-12 col-xs-12 mt-20">
                        <div class="form-group">
                            <label>Choose image</label>
                            <input type="file" id="image_url" class="input-field" name="image_url">

                        </div>

                    </div>


                    <div class="col-md-12">
                        <button class="btn btn-info b2 ajax-button">Save</button>
                    </div>
                </div>
        </form>
    </div>
</div>   <!--footer-->
<script type="text/javascript">
    $(document).ready(function () {
        let current_url = window.location.href;
        current_url = current_url.split('/');

        let id = current_url.slice(-1)[0];
        $('.id').val(id);
        var columns = ['name', 'email', 'date_of_join', 'mobile_no', 'image_url', 'user_status_id','user_group_id'];
        getEditRecord('POST', base_url + "/tenant/user/profile?target_id=" + id, {}, {}, columns, 'agent'); // UPDATE FUNCTION

//Delete Function

        $('.delete').on('click', function () {
            var choice = confirm('Do you really want to delete this record?');
            if (choice === true) {

                let deleteRecord = "{{ URL::to('tenant/agent/delete') }}" + "/" + id;

                ajaxCall('POST', deleteRecord, {id}, {});
                $(".delete").prop('disabled', true);

                var redirect_url = $('.redirect_url').val();
                redirect_url = typeof redirect_url == 'undefined' ? window.location.href : redirect_url;
                setTimeout(function () {
                    window.location.href = redirect_url;
                }, 1000)

            }
            return false;
        });
    })


</script>
@include('tenant.include.footer')

<!--footer-->