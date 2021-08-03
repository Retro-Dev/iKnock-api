@include('tenant.include.header')
@include('tenant.include.sidebar')
<div class="right_col" role="main">
    <div class="row" id="content-heading">
        <!--content-heading here-->
        <div class="col-md-10">
            <h1 class="cust-head">User Management</h1>
        </div>
        <div class="col-md-2 text-right">
           
        </div>
        <div class="col-md-2 text-right">
            <a href="{{ URL::to('/tenant/agent/create') }}" class="btn btn-info add-bt">Add</a>
        </div>
    </div>
    <hr class="border">
    <!--content-heading-end-->
    <div class="row" id="pg-content">
                    @include('tenant.error')
                    {{ csrf_field() }}
        <!--content-table here-->
        <table class="table table-striped jambo_table" id="scroll" id="user_mgmt">
        <input type="hidden" name="col_title" class="title" value=""/>
        <input type="hidden" name="col_type" class="column_name" value=""/>
            <thead>
                <tr class="headings">
                    <td class="text-left">S.no</td>
                    <td class="text-left">Code <span class="sort sort_asc" style="cursor:pointer;" title="asc" data-column = "code"><i class="fas fa-sort-up" style="position:relative;left:10px;"></i></span> <span class="sort sort_desc" title="desc" style="margin-left:-2px;cursor:pointer;position:relative;top:4px;" data-column = "code" title="desc"><i class="fas fa-sort-down"></i></span></td>
                    <td class="text-left">Name <span class="sort sort_asc" style="cursor:pointer;" title="asc" data-column = "name"><i class="fas fa-sort-up" style="position:relative;left:10px;"></i></span> <span class="sort sort_desc" title="desc" style="margin-left:-2px;cursor:pointer;position:relative;top:4px;" data-column = "name" title="desc"><i class="fas fa-sort-down"></i></span></td>
                    <td class="text-left">Email <span class="sort sort_asc" style="cursor:pointer;" title="asc" data-column = "email"><i class="fas fa-sort-up" style="position:relative;left:10px;"></i></span> <span class="sort sort_desc" title="desc" style="margin-left:-2px;cursor:pointer;position:relative;top:4px;" data-column = "email" title="desc"><i class="fas fa-sort-down"></i></span></td>
                    <td class="text-left">Contact Number <span class="sort sort_asc" style="cursor:pointer;" title="asc" data-column = "contact_number"><i class="fas fa-sort-up" style="position:relative;left:10px;"></i></span> <span class="sort sort_desc" title="desc" style="margin-left:-2px;cursor:pointer;position:relative;top:4px;" data-column = "contact_number" title="desc"><i class="fas fa-sort-down"></i></span></td>
                    <td class="text-left">Joining Date <span class="sort sort_asc" style="cursor:pointer;" title="asc" data-column = "joining_date"><i class="fas fa-sort-up" style="position:relative;left:10px;"></i></span> <span class="sort sort_desc" title="desc" style="margin-left:-2px;cursor:pointer;position:relative;top:4px;" data-column = "joining_date" title="desc"><i class="fas fa-sort-down"></i></span></td>
                    <td class="text-left">Status <span class="sort sort_asc" style="cursor:pointer;" title="asc" data-column = "status"><i class="fas fa-sort-up" style="position:relative;left:10px;"></i></span> <span class="sort sort_desc" title="desc" style="margin-left:-2px;cursor:pointer;position:relative;top:4px;" data-column = "status" title="desc"><i class="fas fa-sort-down"></i></span></td>
                    <td class="text-left">User Type <span class="sort sort_asc" style="cursor:pointer;" title="asc" data-column = "user_type"><i class="fas fa-sort-up" style="position:relative;left:10px;"></i></span> <span class="sort sort_desc" title="desc" style="margin-left:-2px;cursor:pointer;position:relative;top:4px;" data-column = "user_type" title="desc"><i class="fas fa-sort-down"></i></span></td>
                    <td class="text-center">Password Reset Link</td>
      
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    <!--content-table-end-->
</div>
<script src="{{asset('assets/js/tenant-js/user_mgmt.js')}}"></script>
<script type="text/javascript">
$(document).ready(function(){
    var columns = ['code','name','email','mobile_no','date_of_join','user_status','user_type','user_id'];
    loadGridWitoutAjax('GET',base_url + "/tenant/user/list?is_all=1",{},{},columns);

    $(document).on('click','.sort',function(){
        
        var title = $(this).attr('title');
        var column_name = $(this).data('column');
        $('.title').val(title);
        $('.column_name').val(column_name);
        
        // if(title == 'asc'){
        //      $(this).hide(); 
        //      $('.sort[title="desc"]').show(); 
        // }else
        // {
        //    $(this).hide(); 
        //    $('.sort[title="asc"]').show();      
        // }
        data = {order_by:column_name,order_type:title}
        loadGridWitoutAjax('GET',base_url + "/tenant/user/list",data,{},columns);
        
    })
})
</script>
@include('tenant.include.footer')
