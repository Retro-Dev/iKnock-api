@include('tenant.include.header')
@include('tenant.include.sidebar')
<div class="right_col" role="main">
    <div class="row" id="content-heading">
        <!--content-heading here-->
        <div class="col-md-10">
        <h1 class="cust-head">Leads Map</h1>
        </div>
        <div class="col-md-2 text-right">
                  <a href="{{ URL::to('/tenant/lead') }}" class="btn add-bt b1">Leads List View</a>
              </div>
    </div>
    <hr class="border">
    <!--content-heading-end-->
    <div class="row" id="pg-content">
    <div id="map" style="width: 100%; height: 700px;"></div>
    </div>
    <!--content-table-end-->
</div>
<script src="https://maps.google.com/maps/api/js?key={{config('constants.GOOGLE_API_KEY')}}" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function() {
    
getLocation();

function getLocation() {
    if (navigator.geolocation) {
        var loc = navigator.geolocation.getCurrentPosition(showPosition);
    }
}

function showPosition(position) {
    var pos_latitude = position.coords.latitude;
    var pos_longitude = position.coords.longitude;

    var params = '{!! !empty($_GET) ? json_encode($_GET) : '' !!}';
    
    params = typeof params != 'undefined' && typeof params != 'null' &&  params != '' ? (JSON.parse(params)) : {latitude:pos_latitude,longitude:pos_longitude } ;
    
    console.log("params",params);
    
    params.latitude = pos_latitude;
    params.longitude = pos_longitude;

    getMapLocations('GET', base_url + "/tenant/lead/list", params);

};


function getMapLocations(method, url, data = {}) {
    ajaxCall(method, url, data).then(function(res) {
        if (res.code == 200) {
            var record = res.data;
            var locations = [];
            if (record.length > 0) {
                for (var i = 0; i < record.length; i++) {

                    var title = record[i].title;
                    var lat = record[i].coordinate.latitude;
                    var lng = record[i].coordinate.longitude;
                    var id = record[i].id;
                    var arr = new Array(title, lat, lng,id);
                    locations.push(arr);

                }

                showLocation(locations,data);

            } 
            showLocation(locations,data);          
        }
    })

}

function showLocation(locations = [], data = {}) {
       
    var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 10,
        center: new google.maps.LatLng(data.latitude, data.longitude),
        mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    var infowindow = new google.maps.InfoWindow();

    var marker, i;

    for (i = 0; i < locations.length; i++) {
        marker = new google.maps.Marker({
            position: new google.maps.LatLng(locations[i][1], locations[i][2]),
            map: map,
        });

        google.maps.event.addListener(marker, 'mouseover', (function(marker, i) {
            return function() {
                var loc = locations[i][0];
                var id = locations[i][3];
                let url ="{{ URL::to('/tenant/lead/edit/') }}";
                url = url+'/'+id;
                infowindow.setContent("<a class='id' href=' "+url+" ' target='_blank'>"+loc+"</a>");
                infowindow.open(map, marker);
            }
        })(marker, i));

        
    }

    google.maps.event.addListener(map, "click", function(e) {

        //lat and lng is available in e object
        var lat = e.latLng.lat();
        var lng = e.latLng.lng();
        console.log("lat", lat);
        console.log("Lng", lng);
        var data = {
            latitude: lat,
            longitude: lng
        };
        getMapLocations('GET', base_url + "/tenant/lead/list", data);

    });
}

})
</script>
@include('tenant.include.footer')
