@include('tenant.include.header')
@include('tenant.include.sidebar')
<div class="right_col" role="main">
    <div class="row" id="content-heading">
        <!--content-heading here-->
        <div class="col-md-10">
            <h1 class="cust-head">Template Management</h1>
        </div>
        
        <div class="col-md-2 text-right">
            <a href="{{ URL::to('/tenant/template/add') }}" class="btn add-bt">Add</a>
            
        </div>
        <!-- <div class="col-md-2 text-right">
           
            <a href="{{ URL::to('/tenant/lead/lead_type') }}" class="btn b1">Manage Templates</a>
        </div> -->
        
    </div>
    <hr class="border">
    <!--content-heading-end-->
    <div class="row" id="pg-content">
        <!--content-table here-->
        <table class="table table-striped jambo_table" id="scroll" id="">
            <thead>
                <tr class="headings">
                    <td class="text-left" style="width: 15%;">S.no</td>
                    <td class="text-left">Template Name <span class="sort sort_asc" style="cursor:pointer;" title="asc" data-column = "template_name"><i class="fas fa-sort-up" style="position:relative;left:10px;"></i></span> <span class="sort sort_desc" title="desc" style="margin-left:-2px;cursor:pointer;position:relative;top:4px;" data-column = "template_name" title="desc"><i class="fas fa-sort-down"></i></span></td>
                    <td class="text-center">Action</td>
                    
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
    <!--content-table-end-->
</div>
<script type="text/javascript">
$(document).ready(function(){
    var columns = ['title','template-action'];
    loadGridWitoutAjax('GET',base_url + "/tenant/lead/template/list",{},{},columns);

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
        loadGridWitoutAjax('GET',base_url + "/tenant/lead/template/list",data,{},columns);
        
    })

})

    //Delete Function

    $(document).on('click', '.delete', function (e) {
            var id = $(this).attr("id");
            var get_template_id = $("[name='template_id']").val();
            var choice = confirm('Do you really want to delete this field?');
            if (choice === true) {
                let deleteRecord = "{{ URL::to('/tenant/lead/template/delete') }}" + "/" + id;
                var data = {id: id, template_id: get_template_id};
                ajaxCall('POST', deleteRecord, data, {});
                var redirect_url = '';
                redirect_url = typeof redirect_url == '' ? window.location.href : redirect_url;
                setTimeout(function () {
                    window.location.href = redirect_url;
                }, 1000)
            }
            return false;
        });
</script>
@include('tenant.include.footer')
