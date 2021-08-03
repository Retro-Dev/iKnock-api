@include('tenant.include.header')
@include('tenant.include.sidebar')
<div class="right_col" role="main">
    <div class="row" id="content-heading">
        <!--content-heading here-->
        <div class="col-md-10">
            <h1 class="cust-head">Add Lead</h1>
        </div>
        <div class="col-md-2">
            <a href="{{ URL::to('/tenant/lead/wizard') }}">
                <button class="b1">Import Wizard</button>
            </a>

        </div>

        <hr class="border">

        <!--content-heading-end-->
        <div class="row" id="pg-form">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                @include('tenant.error')
                <form id="frm_step-2">
                    <input type="hidden" class="submit_url" value="{{ URL::to('tenant/lead/create') }}"/>
                    <input type="hidden" class="redirect_url" value="{{ URL::to('tenant/lead') }}">
                    {{ csrf_field() }}

                    <!-- <div class="row nomargin">
                        <div class="row nomargin">
                            <div class="col-sm-12 col-md-12 col-xs-12 margintop">
                                <div class="row nomargin form-group">
                                    <label>Lead Type</label>
                                    @if(count($data->resource['type']))

                                        <select class="form-control selectbox select-template selectpicker input"
                                                data-live-search="true" name="type_id" id="template_id">
                                            @foreach ($data->resource['type'] as $lead)
                                                <option data-tokens="{{ $lead->title }}"
                                                        value="{{ $lead->id }}">{{ $lead->title }} </option>
                                            @endforeach
                                        </select>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div> -->

                    <div class="row nomargin">
                        <div class="row nomargin">
                            <div class="col-sm-6 col-md-6 col-xs-12">
                                <div class="form-group">
                                    <label>{{config('constants.LEAD_TITLE_DISPLAY')}}</label>

                                    <input type="text" value="" name="title"  class="input" required="required">

                                </div>
                            </div>

                            <div class="col-sm-6 col-md-6 col-xs-12">
                                <div class="form-group">
                                    <label>Address</label>
                                    <input type="text" value="" name="address"  class="input" required="required">
                                </div>
                            </div>

                            <!-- <div class="col-sm-6 col-md-6 col-xs-6">
                                    <div class="form-group">
                                        <label>First Name</label>

                                        <input type="text" value="" name="first_name"  class="input" required="required">

                                    </div>
                                </div> -->

                        </div>

                        <div class="row nomargin">
                            <div class="row nomargin">
                                
                                <!-- <div class="col-sm-6 col-md-6 col-xs-6">
                                    <div class="form-group">
                                        <label>Last Name</label>

                                        <input type="text" value="" name="last_name"  class="input" required="required">

                                    </div>
                                </div> -->

                              

                            </div>


                            <div class="row nomargin">
                                <div class="col-sm-6 col-md-6 col-xs-12">
                                    <div class="form-group">
                                        <label>City</label>

                                        <input type="text" value="" name="city"  class="input" required="required">

                                    </div>
                                </div>

                                <div class="col-sm-6 col-md-6 col-xs-12">
                                    <div class="form-group">
                                        <label>County</label>
                                        <input type="text" value="" name="county"  class="input" required="required">
                                    </div>
                                </div>


                            </div>

                    <div class="row nomargin">
                                <div class="col-sm-6 col-md-6 col-xs-12">
                                    <div class="form-group">
                                        <label>State</label>

                                        <input type="text" value="" name="state"  class="input" required="required">

                                    </div>
                                </div>

                                <!-- {{--<div class="col-sm-6 col-md-6 col-xs-12">
                                    <div class="form-group">
                                        <label>Zip code</label>
                                        <input type="text" value="" name="zip_code"  class="input" required="required">
                                    </div>
                                </div>--}} -->

                                <div class="col-sm-6 col-md-6 col-xs-12">
                                    <div class="row nomargin">
                                        <label>Lead Status</label>
                                        @if(count($data->resource['status']))

                                            <select class="form-control selectbox selectpicker input" data-live-search="true" name="status_id" value="status_id">
                                            <option  value="" selected disabled>Select Status</option>
                                                @foreach ($data->resource['status'] as $status)
                                                    <option data-tokens="{{ $status->title }}" value="{{ $status->id }}">{{ $status->title }} </option>
                                                @endforeach
                                            </select>
                                        @endif
                                    </div>
                                 </div>

                                 
                        </div>

                     
                        <div class="row nomargin">
                            <div class="col-sm-6 col-md-6 col-xs-12">
                                <div class="form-group">
                                    <label>Zip Code</label>

                                    <input type="text" value="" name="zip_code"  class="input" required="required">

                                </div>
                            </div>
                            <div class="col-sm-6 col-md-6 col-xs-12">
                                <div class="row nomargin form-group">
                                    <label>Lead Type</label>

                                    
                                        <select class="form-control selectbox selectpicker input" data-live-search="true" name="type_id" value="type_id">

                                        <option  value="" selected disabled>Select Lead Type</option>
                                            @if(count($data->resource['type']))
                                            @foreach ($data->resource['type'] as $lead)
                                                <option data-tokens="{{ $lead->title }}" value="{{ $lead->id }}">{{ $lead->title }} </option>
                                            @endforeach
                                            @endif
                                        </select>

                                </div>
                            </div>


                        </div>

                        <div class="row nomargin">
                                <div class="col-sm-6 col-md-6 col-xs-12">
                                    <div class="form-group">
                                        <label>Foreclosure Date</label>

                                        <input type="text" value="" name="foreclosure_date"  class="input" >

                                    </div>
                                </div>
                                <div class="col-sm-6 col-md-6 col-xs-12">
                                    <div class="form-group">
                                        <label>Admin Notes</label>

                                        <input type="text" value="" name="admin_notes"  class="input" >

                                    </div>
                                </div>
                                






                            </div>
                  @foreach ($data->resource['custom_fields'] as $field)
                        <div class="col-sm-6 col-md-6 col-xs-12">
                            <div class="form-group margintop">
                                <label>{{ $field->key }}</label>
                                <input type="text" value="" name="custom_field[{{ $field->id }}]" class="input">
                            </div>
                        </div>
                    @endforeach

                    <div id="fix_field"></div>
                    <div class="row nomargin margintop">
                        <div class="col-md-12 col-xs-12">
                            <label>Image</label>
                            <input type="file" class="input" multiple="multiple" name="image_url[]">
                        </div>
                        <div class="col-md-12">
                            <!-- <input type="submit" value="Save" class="b2 margintop ajax-button save-button"> -->
                            <button class="btn margintop ajax-button b1">Save</button>
                            <input type="hidden" name="template_id" id="template_id" class="template_id" value=""/>
                            <input type="hidden" class="input " name="template" id="template"/>
                        </div>
                    </div>
            </form>
        </div>

    </div>
</div>
<div class="col-md-2"></div>
</div>
</div>   <!--footer-->
<script>

    //getTemplateFields();

    function repl_str(data) {
        if (data.length > 0)
        {
           return  data.replace(/#|_/g,' ');
        }else {
            return data;
        }
    }
  
    $(document).ready(function () {

        // $('select').trigger('change');
        // getTemplateFields();

       

        //Delete Function

        $(document).on('click', '.import', function (e) {
            var id = $(this).data("id");
            var get_template_id = $("[name='template_id']").val();
            console.log('id', id);
            console.log('template_id', get_template_id);
            var choice = confirm('Do you really want to delete this field?');
            if (choice === true) {

                let deleteRecord = "{{ URL::to('/tenant/template/field/delete') }}" + "/" + id;
                var data = {id: id, template_id: get_template_id};
                ajaxCall('POST', deleteRecord, data, {});
                $('#hide' + id).hide();
            }
            return false;
        });

    })


</script>
@include('tenant.include.footer')

<!--footer-->