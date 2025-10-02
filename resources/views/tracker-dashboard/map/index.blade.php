<x-tracker-layout :assets="$assets ?? []">
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="card card-block card-stretch">
                <div class="card-body p-0">
                    <div class="d-flex justify-content-between align-items-center p-3">
                        <h5 class="font-weight-bold">{{ $pageTitle }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div id="map" style="height: 600px;"></div>
                    <div id="maplegend" class="d-none">

                        <div>
                            <img src="{{ asset('images/online.png') }}" /> {{ __('message.online') }}
                        </div>
                        <div>
                            <img src="{{ asset('images/ontrip.png') }}" /> {{ __('message.in_service') }}
                        </div>
                        <div>
                            <img src="{{ asset('images/offline.png') }}" /> {{ __('message.offline') }}
                        </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@section('bottom_script')
    <script>
        $(function(){
            let map;
            var marker = undefined;
            var locations = [];
            var taxiicon = ""
            var markers = {};
            $(document).ready( function() {
                // send every second
                setInterval( async function() {
                    await driverList();
                }, 2000);
            });
            function initialize() {
                var myLatlng = new google.maps.LatLng(20.947940, 72.955786);
                var myOptions = {
                    zoom: 1.5,
                    center: myLatlng,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                }
                map = new google.maps.Map(document.getElementById('map'), myOptions);
                const legend = document.getElementById("maplegend");

                map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(legend)
            }

            function changeMarkerPositions(locations)
            {
                // clearMarkers();
                const infowindow = new google.maps.InfoWindow();
                if(locations.length > 0 )
                {
                    for(let i = 0 ; i < locations.length ; i++) {
                        if(locations[i].id in markers ){
                            const latlng = markers[locations[i].id].getPosition();
                            if (latlng.lat().toString() !== locations[i].latitude || latlng.lng().toString() !== locations[i].longitude) {
                                markers[locations[i].id].setMap(null);
                                markers[locations[i].id].setPosition(new google.maps.LatLng(locations[i].latitude, locations[i].longitude));
                                // delete markers[locations[i].id];
                            }
                        }
                        if (!(locations[i].id in markers) ) {
                            if( locations[i].is_online === 1 && locations[i].is_available === 0) {
                                taxicon = "{{ asset('images/ontrip.png') }}";
                            } else if( locations[i].is_online == 1 ) {
                                taxicon = "{{ asset('images/online.png') }}";
                            } else {
                                taxicon = "{{ asset('images/offline.png') }}";
                            }
                            marker = new google.maps.Marker({
                                position:  new google.maps.LatLng(locations[i].latitude, locations[i].longitude) ,
                                map: map,
                                icon: taxicon,
                                title: locations[i].display_name,
                                driver_id: locations[i].id
                            });
                            marker.metadata= { id : locations[i].id };

                            google.maps.event.addListener(marker, 'click', (function (marker, i) {
                                return function () {
                                    driver = driverDetail(marker.driver_id);
                                    service_name = driver.driver_service != null ? driver.driver_service.name : '-';
                                    last_location_update_at = driver.last_location_update_at != null ? driver.last_location_update_at : '-';
                                    driver_view = "{{ route('driver.show', '' ) }}/"+marker.driver_id;
                                    contentString = '<div class="map_driver_detail"><ul class="list-unstyled mb-0">'+
                                        '<li><i class="fa fa-address-card" aria-hidden="true"></i>: '+driver.display_name+'</li>'+
                                        '<li><i class="fa fa-phone" aria-hidden="true"></i>: '+driver.contact_number+'</li>'+
                                        '<li><i class="fa fa-taxi" aria-hidden="true"></i>: '+service_name+'</li>'+
                                        '<li><i class="fa fa-clock" aria-hidden="true"></i>: '+last_location_update_at+'</li>'+
                                        '<li><a href="'+driver_view+'"><i class="fa fa-eye" aria-hidden="true"></i> {{ __("message.view_form_title",[ "form" => __("message.driver") ]) }}</a></li>'+
                                        '</ul></div>';
                                        if (driver?.driver_ride_request_detail != null) {
                                            // add start_address and end_address
                                            contentString += '<div class="map_driver_detail"><ul class="list-unstyled mb-0">'+
                                                '<li><i style="color: red" class="fa fa-map-signs" aria-hidden="true"></i>: '+driver.driver_ride_request_detail?.start_address+'</li>'+
                                                '<li><i style="color: green" class="fa fa-flag-checkered" aria-hidden="true"></i>: '+driver.driver_ride_request_detail?.end_address+'</li>'+
                                                '</ul></div>';
                                        }
                                    infowindow.setContent(contentString);
                                    // infowindow.setContent(locations[i].display_name);
                                    infowindow.open(map, marker);
                                }
                            })(marker, i));
                            markers[locations[i].id] = marker;
                        }
                    }
                }
            }

            function clearMarkers() {
                for (var key in markers) {
                    if (markers.hasOwnProperty(key)) {
                        markers[key].setMap(null);
                    }
                }
                markers = {};
            }

            function driverDetail(driver_id) {
                url = "{{ route('driverdetail',[ 'id' =>'']) }}"+driver_id;
                var driver_data;
                $.ajax({
                    type: 'get',
                    url: url,
                    async: false,
                    success: function(res) {
                        console.log(res.data);
                        driver_data = res.data;
                    }
                });
                return driver_data;
            }
            function driverList() {
                var url = "{{ route('driver_list.map') }}";
                $.ajax({
                    type: 'get',
                    url: url,
                    success: function(res) {
                        if(res.data.length > 0) {
                            changeMarkerPositions(res.data)
                        }
                    }
                });
            }

            if(window.google || window.google.maps) {
                initialize();
                $('#maplegend').removeClass('d-none')
                // console.log('1.initial');
            }
        });
    </script>
@endsection
</x-tracker-layout>
