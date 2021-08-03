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
        <div class="col-md-11">
            <h1 class="cust-head">Field Management</h1>
        </div>
                <div class="col-md-1">
                    <a href="{{ URL::to('/tenant/field/create') }}" class="btn add-bt">Add</a>

                </div>
    </div>
    <hr class="border">
    <!--content-heading-end-->

    <div class="row" id="pg-content">
       <div class="row nomargin" style="margin:10px 0px;">
            <ul class="nav nav-tabs tab" name="type">
                <li class="active"><a data-toggle="tab" href="#home">Lead Summary</a></li>
                <!-- <li><a data-toggle="tab" href="#menu2">Lead Detail</a></li> -->
                <li><a data-toggle="tab" href="#menu3">Appointment Schedule</a></li>
            </ul>
       </div>
            <div class="tab-content">
                <div id="home" class="tab-pane fade in active">
                    <table id="example" class="example1 table table-striped jambo_table field_manage" id="scroll" class="display" style="width:100% !important">
                        <thead>
                        <tr class="headings">
                            <th class="text-left">Field Name </th>
                            <th class="text-left">Field Type</th>
                        </tr>
                        </thead>

                        <tbody>
                        
                        </tbody>
                    </table>
                </div>



                 <!-- <div id="menu2" class="tab-pane fade">
                    <table id="example" class="example3 table table-striped jambo_table field_manage" id="scroll" class="display" style="width:100% !important">
                        <thead>
                        <tr class="headings">
                            
                            <th class="text-left">Field Name</th>
                            <th class="text-left">Field Type</th>
                        </tr>
                        </thead>

                        <tbody>
                       
                        </tbody>
                    </table>
                </div> -->

                <div id="menu3" class="tab-pane fade">
                    <table id="example" class="example2 table table-striped jambo_table field_manage" id="scroll" class="display" style="width:100% !important">
                        <thead>
                        <tr class="headings">

                            <th class="text-left">Field Name</th>
                            <th class="text-left">Field Type</th>
                        </tr>
                        </thead>

                        <tbody>

                        </tbody>
                    </table>
                    <input type="hidden" id="current_page_index" name="current_page_index" value="1">
                </div>
            </div>

    </div>
    <!--content-table-end-->
</div>
@include('tenant.include.footer')
<script src="{{asset('assets/js/tenant-js/field_mgmt.js')}}"></script>

<script type="text/javascript">
$(document).ready(function(){

    // var columns = ['type'];    
    // loadGridWitoutAjax('GET',base_url + "/tenant/query/list",{},{},columns);

    var columns = ['query','type'];
    ajaxDatatable('.example1',base_url + "/tenant/query/list?type=summary",10,columns,'type');
    //ajaxDatatable('.example3',base_url + "/tenant/query/list?type=lead_detail",10,columns,'type');
    ajaxDatatable('.example2',base_url + "/tenant/query/list?type=appointment",10,columns,'type');

    sortingTable('.example1 tbody','summary',base_url + "/tenant/query/update/sorting");
    
    sortingTable('.example2 tbody','appointment',base_url + "/tenant/query/update/sorting");
    
    //sortingTable('.example3 tbody','lead_detail',base_url + "/tenant/query/update/sorting");


  

})

function sortingTable(element = 'tbody',field_type = "",url){
    
    $(element).on( "sortbeforestop", function( event, ui ) {
    var type = field_type;
    var current_page = $('#current_page_index').val();
    var sortedIDs = $(element).sortable( "toArray" );
    if(Array.isArray(sortedIDs)) {
                sortedIDs = sortedIDs.join();
            }
        data = {type:type,ids:sortedIDs,current_page:current_page};
    ajaxCall('POST',url,data)
} );
}

</script>
<script type="text/javascript">
  $('tbody').sortable();
</script>
