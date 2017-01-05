@extends('app')
@section('content')
    @include('partials.content_header', ['pageTitle' => 'Map Snaps Report', 'pageDescription' => 'List of Map Snaps Report Table', 'breadcrumbs' => ['Report' => false]])
    <body onload="initialize()">
    <section class="content">
        <div class="box">
            <form role="form" action="#" method="post" enctype="multipart/form-data" class="form" accept-charset="utf-8">
                {{ csrf_field() }}
                <div class="box-header with-border">
                    <h3 class="box-title"></h3>
                    <div class="btn-group pull-right">
                        <button type="button" class="btn btn-default" title="Back" style="height: 48px;"> 
                            <a id="back" class="back" title="Back" href="{{ admin_route_url('reports.index') }}">
                                <i class="fa fa-hand-o-left"></i>
                            </a>
                        </button>
                    </div>
                    <div class="btn-group pull-left">
                        <button type="button" class="btn btn-default" title="Location" style="height: 48px;"> 
                            <a id="globe" class="globe" title="Location" href="#">
                                <i class="fa fa-globe"></i>
                            </a>
                        </button>
                        <button class="btn btn-default" type="button" aria-expended="false" style="height: 48px;">
                            <select name="location" id="location" class="form-control">
                                <option value="">Select Location</option>
                                @foreach($reports as $location)
                                    <option value="{{ $location->users_city }}">{{ $location->users_city }}</option>
                                @endforeach
                            </select>
                        </button>
                        <button class="btn btn-default" type="text" aria-expended="false" style="height: 48px;">
                            <a id="search" class="search" title="Submit Snaps Date" href="#">
                                <i class="fa fa-search"></i>
                            </a>
                        </button>                    
                    </div>
                </div>
                <div id="map"></div>
            </form>
        </div>
    </section>
@endsection
<style>
    #map {
        height: 100%;
    }
</style>
<script>
    /*var map;
    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 4,
            center: {lat: -28, lng: 137}
        });
        map.data.loadGeoJson('https://storage.googleapis.com/mapsdevsite/json/google.json');
        map.data.setStyle({
            fillColor: 'green',
            strokeWeight: 1
        });
    }*/
    (function() {
        window.onload = function() {
            var map;
            <?php 
                foreach($reports as $location) {
                    $city = $location->users_city;
                    $longitude = $location->longitude;
                    $latitude = $location->latitude;
                }
            ?>
            var locations = [
                                //[<?php echo $city.', '.$longitude.', '.$latitude; ?>]
                            ];
            //Parameter Google maps
            var options = {
                zoom: 12, //level zoom
                //posisi tengah peta
                center: new google.maps.LatLng(-6.259872, 106.781134),
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };
            // Buat peta di 
            var map = new google.maps.Map(document.getElementById('map'), options);
            // Tambahkan Marker 
            var infowindow = new google.maps.InfoWindow();
            var marker, i;
            /* kode untuk menampilkan banyak marker */
            for (i=0; i<locations.length; i++) {  
                marker = new google.maps.Marker({
                    position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                    map: map,
                    icon: ''
                });
                /* menambahkan event clik untuk menampikan infowindows dengan isi sesuai dengan marker yang di klik */
                google.maps.event.addListener(marker, 'click', (function(marker, i) {
                    return function() { 
                        var id= locations[i][0];
                        console.log(id);
                        $.ajax({
                            url : "#",
                            data : "id=" +id ,                    
                            success : function(data) {
                                infowindow.setContent(data);
                                infowindow.open(map, marker);
                            }
                        });     
                    }
                })(marker, i));
            }
        };
    })();
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDmrSyeWfGLZ051XBJBZ3fomy989yCBtNM"></script>
<!--<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDmrSyeWfGLZ051XBJBZ3fomy989yCBtNM&callback=initMap">
</script>-->