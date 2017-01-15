@extends('app')
@section('content')
    @include('partials.content_header', ['pageTitle' => 'Map Snaps Report', 'pageDescription' => 'List of Map Snaps Report Table', 'breadcrumbs' => ['Report' => admin_route_url('reports.index')]])
    <body onload="initialize()">
    <section class="content">
        <div class="box">
            <div id="map"></div>
        </div>
    </section>
@endsection
<style>
    #map {
        height: 100%;
    }
</style>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDmrSyeWfGLZ051XBJBZ3fomy989yCBtNM"></script>
<script>
    /*(function() {
        window.onload = function() {
            var map;
            var locations = [
            <?php 
                foreach($reports as $location) {
                    $city = $location->users_city;
                    $longitude = $location->longitude;
                    $latitude = $location->latitude;
            ?>
                                ['<?php echo $city; ?>', <?php echo $longitude; ?>, <?php echo $latitude; ?>],
            <?php
                }
            ?>
                            ];
            var options = {
                zoom: 12,
                center: new google.maps.LatLng('-6.259872', '106.781134'),
                mapTypeId: google.maps.MapTypeId.ROADMAP
            };
            var map = new google.maps.Map(document.getElementById('map'), options);
            var infowindow = new google.maps.InfoWindow();
            var marker, i;
            for (i=0; i<locations.length; i++) {  
                marker = new google.maps.Marker({
                    position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                    map: map,
                    icon: ''
                });
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
    })();*/
    var historicalOverlay;
    var map;
    var newark;
    var markers = [];
    var iB = [];
    var Groundy = [];
    
    var ibLoc1 = [[-6.0770344607117,-6.2621460540182,106.74830059373,106.56334047928,"129.png"],[-6.2621460540182,-6.4472576473247,106.74830059373,106.56334047928,"130.png"],[-6.4472576473247,-6.6323692406312,106.74830059373,106.56334047928,"131.png"],[-6.6323692406312,-6.6692921341204,106.74830059373,106.56334047928,"132.png"],[-6.0770344607117,-6.2621460540182,106.93326070818,106.74830059373,"133.png"],[-6.2621460540182,-6.4472576473247,106.93326070818,106.74830059373,"134.png"],[-6.4472576473247,-6.6323692406312,106.93326070818,106.74830059373,"135.png"],[-6.6323692406312,-6.6692921341204,106.93326070818,106.74830059373,"136.png"],[-6.0770344607117,-6.2621460540182,107.11822082264,106.93326070818,"137.png"],[-6.2621460540182,-6.4472576473247,107.11822082264,106.93326070818,"138.png"],[-6.4472576473247,-6.6323692406312,107.11822082264,106.93326070818,"139.png"],[-6.6323692406312,-6.6692921341204,107.11822082264,106.93326070818,"140.png"],[-6.0770344607117,-6.2621460540182,107.19087727385,107.11822082264,"141.png"],[-6.2621460540182,-6.4472576473247,107.19087727385,107.11822082264,"142.png"],[-6.4472576473247,-6.6323692406312,107.19087727385,107.11822082264,"143.png"],[-6.6323692406312,-6.6692921341204,107.19087727385,107.11822082264,"144.png"],[3.7719578469342,3.4745115064489,98.932570832498,98.418076534012,"145.png"]]
    
    function setActual(){
        resetGround();
        Groundy = [];
        for(var x = 0; x < ibLoc1.length; x++){
            iB[x] = new google.maps.LatLngBounds(
                    new google.maps.LatLng(ibLoc1[x][1], ibLoc1[x][3]),
                    new google.maps.LatLng(ibLoc1[x][0], ibLoc1[x][2])
            );
            historicalOverlay = new google.maps.GroundOverlay( '../userdata/kml/' + ibLoc1[x][4], iB[x], {opacity:0.25});
            historicalOverlay.setMap(map);
            Groundy.push(historicalOverlay);
        }
    }
    //cities db
    var mapCenters = [[-6.22967,106.8367],[3.58663,98.67333889]]

    function mapSetCenter(cityId){
        x = mapCenters[cityId][0];
        y = mapCenters[cityId][1];
        map.setCenter(new google.maps.LatLng(x,y));

    }
    
    function resetGround(){
        if(Groundy.length > 0){
            for(i=0; i<Groundy.length; i++){
                Groundy[i].setMap(null);
            }
        }
    }

    function initialize() {
        initX = mapCenters[0][0];
        initY = mapCenters[0][1];
        newark = new google.maps.LatLng(initX, initY);
        var mapOptions = {
            zoom: 12,
            center: newark,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        };
        map = new google.maps.Map(document.getElementById('map'), mapOptions);
        setActual();

        // Try HTML5 geolocation
        if(navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                var pos = new google.maps.LatLng(position.coords.latitude,
                                                 position.coords.longitude);

                var infowindow = new google.maps.InfoWindow({
                  map: map,
                  position: pos,
                  content: 'Lokasi anda berada disini.'
                });

                map.setCenter(pos);
            }, function() {
                handleNoGeolocation(true);
            });
        } else {
            // Browser doesn't support Geolocation
            handleNoGeolocation(false);
        }
        // Create the search box and link it to the UI element.
        var input = document.getElementById('target');
        var searchBox = new google.maps.places.SearchBox(input);
        

        // [START region_getplaces]
        // Listen for the event fired when the user selects an item from the
        // pick list. Retrieve the matching places for that item.
        google.maps.event.addListener(searchBox, 'places_changed', function() {
            var places = searchBox.getPlaces();

            for (var i = 0, marker; marker = markers[i]; i++) {
                marker.setMap(null);
            }

            // For each place, get the icon, place name, and location.
            markers = [];
            var bounds = new google.maps.LatLngBounds();
            for (var i = 0, place; place = places[i]; i++) {
                var image = {
                    url: place.icon,
                    size: new google.maps.Size(71, 71),
                    origin: new google.maps.Point(0, 0),
                    anchor: new google.maps.Point(17, 34),
                    scaledSize: new google.maps.Size(25, 25)
                };

                // Create a marker for each place.
                var marker = new google.maps.Marker({
                    map: map,
                    icon: image,
                    title: place.name,
                    position: place.geometry.location
                });

                markers.push(marker);

                bounds.extend(place.geometry.location);
            }

            map.fitBounds(bounds);
        });
        
        // [END region_getplaces]

        // Bias the SearchBox results towards places that are within the bounds of the
        // current map's viewport.
        google.maps.event.addListener(map, 'bounds_changed', function() {
            var bounds = map.getBounds();
            searchBox.setBounds(bounds);
        });
        
       
    }

    function handleNoGeolocation(errorFlag) {
        if (errorFlag) {
            var content = 'Error: The Geolocation service failed.';
        } else {
            var content = 'Error: Your browser doesn\'t support geolocation.';
        }

        var options = {
            map: map,
            position: newark,
            content: content
        };

        var infowindow = new google.maps.InfoWindow(options);
        map.setCenter(options.position);
    }

    google.maps.event.addDomListener(window, 'load', initialize);
</script>
