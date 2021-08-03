<style>
tbody> tr{
    cursor: row-resize !important;
    font-size: 13px;
}
</style>
@include('tenant.include.header')
@include('tenant.include.sidebar')
<div class="right_col" role="main">
    <div class="row" id="content-heading">
        <!--content-heading here-->
        <div class="col-md-10">

            <h1 class="cust-head">{{ucwords($data['title'])}}</h1>
        </div>
        <!-- <div class="col-md-2 text-right">
            <a href="" class="btn btn-info add-bt id">Add</a>
        </div> -->
        
    </div>
    <hr class="border">
    <!--content-heading-end-->
    <div class="row" id="pg-content">
    <form method="post">
                    @include('tenant.error')
                    {{ csrf_field() }}
        <!--content-table here-->
        <table class="table table-striped jambo_table example3" id="scroll" id="user_mgmt">
        <input type="hidden" name="template_id" class="template_id" value=""/>
        <input type="hidden" name="col_title" class="title" value=""/>
        <input type="hidden" id="current_page_index" name="current_page_index" value="1">
                <input type="hidden" name="col_type" class="column_name" value=""/>
            <thead>
                <tr class="headings">
                    <td class="text-left">S.no</td>
                    <td class="text-left">Field Name </td>
                    {{--<td class="text-center">Action</td>--}}
                </tr>
            </thead>
            <tbody>

            </tbody>
        </table>
</form>
    </div>
    <!--content-table-end-->
</div>
@include('tenant.include.footer')
<script type="text/javascript">
    $(document).ready(function () {

        
        var columns = ['key'];

        loadGridWitoutAjax('GET',base_url + "/tenant/lead/default/fields",{},{},columns,'tbody','',false);
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
        loadGridWitoutAjax('GET',base_url + "/tenant/lead/template/"+id,data,{},columns);
        
    })
    
    sortingTable('.example3 tbody','lead_detail',base_url + "/tenant/query/update/sorting");
    function sortingTable(element = 'tbody',field_type = "",url)
    {
        $(element).on( "sortbeforestop", function( event, ui ) 
        {
            var type = field_type;
            //var current_page = $('#current_page_index').val();
            var sortedIDs = $(element).sortable( "toArray" );
            
            if(Array.isArray(sortedIDs)) 
            {
                sortedIDs = sortedIDs.join();
            }
                data = {type:type,ids:sortedIDs};
            ajaxCall('POST',url,data)
        });
    }

    // //Delete Function

    $(document).on('click','.delete', function (e) {
            var id = jQuery(this).attr("id");
            var choice = confirm('Do you really want to delete this field?');
            if (choice === true) {

                let deleteRecord = "{{ URL::to('/tenant/lead/default/field/delete') }}" + "/" + id;
                var data = {id:id};
                ajaxCall('POST', deleteRecord, data, {});
                $(".delete").prop('disabled', true);
                location.reload();

            }
            return false;
        });
        
    })

</script>
<script type="text/javascript">
  $('tbody').sortable();
</script>


<!--footer-->