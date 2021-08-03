<?php //print_r($data);die; ?>
@include('tenant.include.header')
@include('tenant.include.sidebar')

<div class="right_col" role="main">
    <div class="row" id="content-heading">
        <!--content-heading here-->
        <div class="col-md-9">
            <h1 class="cust-head">Commission Management</h1>
        </div>

        <div class="col-md-1 text-right">
        <a href="javascript:void(0)" class="btn add-bt comm-toggle w-100"><i class="fa fa-filter"></i></a>
        </div>
        <div class="col-md-1">
            <a href="{{ URL::to('/tenant/user/commission/export') }}" class="btn add-bt" id="export-btn">Export</a>
        </div>
        <div class="col-md-1">
            <a href="{{ URL::to('/tenant/commission/create') }}" class="btn add-bt">Add</a>
        </div>
    </div>

    <div class="filter-show mt-20">
<form id="myForm">
    <input type="hidden" class="start" name="start_date"/>
    <input type="hidden" class="end" name="end_date"/>
    <input type="hidden" class="agent_ids" name="agent_ids"/>
    <input type="hidden" class="commission_events" name="commission_events"/>
    <input type="hidden" class="title" name="order_type"/>
    <input type="hidden" class="column_name" name="order_by"/>
    <div class="col-md-4">
            <div class="form-group">
            <label>Select User</label>
                <select class="form-control selectpicker agents" data-live-search="true"  data-actions-box="true" multiple>
                    @foreach ($data->resource['agent'] as $agent)
                    <option  value="{{ $agent->id }}">{{ $agent->first_name }} </option>
                    @endforeach
                </select>
            </div>
        </div>
        <!-- Col End -->

        <div class="col-md-4 form-group">
        <label>Select Commission Event</label>
            <select class="form-control selectbox selectpicker commissions" data-live-search="true"  data-actions-box="true" multiple>
                <!-- <option disabled="disabled" selected="selected">Select Commission Event</option> -->
                @foreach ($data->resource['commission_event'] as $event)
                <option value="{{ $event->title }}" data-name="{{$lead->title}}">{{ $event->title }} </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <div class="form-group">
            <label>Select Date Range</label>
                <input type="text" id="e2"  class="input date_range" value="select date">
            </div>
        </div>
        <!-- Col End -->
</form>
    </div>
    <!--content-heading-end-->
    <hr class="border">

    <div class="row" id="pg-content">
        <!--content-table here-->

        <table id="example" class="table table-striped jambo_table" id="scroll" class="display" style="width:100% !important">
            <thead>
                <tr class="headings">
                    <td class="text-left">User Name <span class="sort sort_asc" style="cursor:pointer;" title="asc" data-column="user_name"><i class="fas fa-sort-up" style="position:relative;left:10px;"></i></span> <span class="sort sort_desc" title="desc" style="margin-left:-2px;cursor:pointer;position:relative;top:4px;" data-column="user_name" title="desc"><i class="fas fa-sort-down"></i></span></td>
                    <td class="text-left">Lead <span class="sort sort_asc" style="cursor:pointer;" title="asc" data-column="lead"><i class="fas fa-sort-up" style="position:relative;left:10px;"></i></span> <span class="sort sort_desc" title="desc" style="margin-left:-2px;cursor:pointer;position:relative;top:4px;" data-column="lead" title="desc"><i class="fas fa-sort-down"></i></span></td>
                    <td class="text-left">Commission Event <span class="sort sort_asc" style="cursor:pointer;" title="asc" data-column="commission_event"><i class="fas fa-sort-up" style="position:relative;left:10px;"></i></span> <span class="sort sort_desc" title="desc" style="margin-left:-2px;cursor:pointer;position:relative;top:4px;" data-column="commission_event" title="desc"><i class="fas fa-sort-down"></i></span></td>
                    <td class="text-left">Commission <span class="sort sort_asc" style="cursor:pointer;" title="asc" data-column="commission"><i class="fas fa-sort-up" style="position:relative;left:10px;"></i></span> <span class="sort sort_desc" title="desc" style="margin-left:-2px;cursor:pointer;position:relative;top:4px;" data-column="commission" title="desc"><i class="fas fa-sort-down"></i></span></td>
                    <td class="text-left">Month <span class="sort sort_asc" style="cursor:pointer;" title="asc" data-column="month"><i class="fas fa-sort-up" style="position:relative;left:10px;"></i></span> <span class="sort sort_desc" title="desc" style="margin-left:-2px;cursor:pointer;position:relative;top:4px;" data-column="month" title="desc"><i class="fas fa-sort-down"></i></span></td>

                </tr>
            </thead>
            <tbody class="pag">
            </tbody>

        </table>

    </div>
    <!--content-table-end-->
</div>

<script src="{{asset('assets/js/tenant-js/commission_mgmt.js')}}"></script>

@include('tenant.include.footer')
