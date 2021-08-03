@include('tenant.include.header')
@include('tenant.include.sidebar')

<link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

    <div class="right_col" role="main">
        <div class="row" id="content-heading">
            <!--content-heading here-->
            <div class="col-md-11">
                <h1 class="cust-head">Training Script Management</h1>
            </div>
            <div class="col-md-1 text-right">
                <a href="{{ URL::to('/tenant/training/create') }}" class="btn btn-info add-bt">Add</a>
            </div>
        </div>
        <hr class="border">
        <!--content-heading-end-->

        <div class="row" id="pg-content">
            <!--content-table here-->
            <table id="example" class="table table-striped jambo_table" id="scroll" class="display" style="width:100% !important">
                <thead>
                    <tr class="headings">

                        <td class="text-left" id="restrict">Training Title <span class="sort sort_asc" style="cursor:pointer;" title="asc" data-column = "training_title"><i class="fas fa-sort-up" style="position:relative;left:10px;"></i></span> <span class="sort sort_desc" title="desc" style="margin-left:-2px;cursor:pointer;position:relative;top:4px;" data-column = "training_title" title="desc"><i class="fas fa-sort-down"></i></span></td>
                        <td class="text-left">Training Script </td>
                    </tr>
                </thead>
                 <tbody class="pag">
            </tbody>
            </table>
        </div>
        <!--content-table-end-->
    </div>
<script src="{{asset('assets/js/tenant-js/training_mgmt.js')}}"></script>
<script type="text/javascript">
$(document).ready(function(){
    var columns = ['title','description'];    

    ajaxDatatable('#example',base_url + "/tenant/user/training/list",10,columns);

    $(document).on('click','.sort',function()
        {
        
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
        loadGridWitoutAjax('GET',base_url + "/tenant/user/training/list",data,{},columns,'tbody', '',true,false,true,'',indexing=false);
        
    
        })

    
})
</script>

@include('tenant.include.footer')
