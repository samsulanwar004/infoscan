@extends('app', ['mini_sidebar' => true])

@section('content')
    @include('partials.content_header', [
        'pageTitle' => 'Snaps',
        'pageDescription' => 'Detail of Snap',
        'breadcrumbs' => ['Snap' => admin_route_url('snaps.index'), 'detail' => false]
    ])

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border form-inline" style="overflow: hidden; height: 0px; padding: 0px; margin-bottom: 0px;">
                <!-- <i class="fa fa-camera fa-btn"></i> Snap Code: {-- strtoupper($snap->request_code) --} -->
            </div>
            <div class="box-body">
                <div class="row">

                    <div class="col-md-8">
                        <p class="no-shadow">

                        </p>
                        <div class="timeline-item ">
                            <ul class="timeline timeline-inverse">
                                <li class="no-margin-right">
                                    <i class="fa fa-camera bg-purple"></i>

                                    <div class="timeline-item no-margin-right">
                                        <span class="time"><i class="fa fa-clock-o"></i> {{ $snap->created_at->diffForHumans() }}</span>
                                        <h3 class="timeline-header">
                                            <a href="{{ admin_route_url('members.show', ['id' => $snap->member->id]) }}">{{ $snap->member->name }}</a> uploaded new photos
                                        </h3>
                                        <div class="timeline-body">
                                            @foreach($snap->files as $file)
                                                @if ($file->mode_type == 'audio')
                                                    <img src="{{ URL::to('img/snaps/window-player.png') }}" alt="{{ $file->file_code }}" class="margin img-thumbnail img-responsive img-tag"  id="{{$file->id}}" style="width:150px;height:150px;">
                                                @else
                                                    <img src="{{ config('filesystems.s3url') . $file->file_path }}" alt="{{ $file->file_code }}" class="margin img-thumbnail img-responsive img-tag"  id="{{$file->id}}" style="width:150px;height:150px;">
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </li>
                                <li class="no-margin-right">
                                  <i class="fa fa-list bg-blue"></i>

                                  <div class="timeline-item no-margin-right">
                                    <span class="time"><i class="fa fa-clock-o"></i> {{ $snap->created_at->diffForHumans() }}</span>

                                    <h3 class="timeline-header"><a href="{{ admin_route_url('members.show', ['id' => $snap->member->id]) }}">{{ $snap->member->name }}</a> detail product</h3>

                                    <div class="timeline-body snaps-detail">
                                      @include('snaps.show_detail', ['snap' => $snap])
                                    </div>
                                  </div>
                                </li>
                            </ul>

                        </div>

                    </div>

                        <div class="col-md-4">
                            <div class="snaps-button">
                                <form id="snapUpdate" action="{{ admin_route_url('snaps.update', ['id' => $snap->id]) }}"  method="POST">
                                    {{ csrf_field() }}
                                    {{ method_field('PUT') }}
                                    <div class="form-group receipt_id">
                                        <label for="receipt_id">Receipt ID</label>
                                        <input type="text" class="form-control" id="receipt_id" name="receipt_id" placeholder="Enter Receipt ID" value="{{ $snap->receipt_id }}">
                                    </div>
                                    <div class="form-group outlet_name">
                                        <label for="outlet_name">Outlet Name</label>
                                        <input type="text" class="form-control" id="outlet_name" name="outlet_name" placeholder="Enter outlet name" value="{{ $snap->outlet_name }}">
                                    </div>
                                    <div class="form-group location">
                                        <label for="location">Location</label>
                                        <input type="text" class="form-control" id="location" name="location" placeholder="Enter location" value="{{ $snap->location }}">
                                    </div>
                                    <div class="form-group purchase_time">
                                        <label for="purchase_time">Purchase Time</label>
                                        <div class="input-group date">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            @if ($snap->purchase_time == true)
                                                <input type="text" class="form-control datepicker" id="purchase_time" name="purchase_time" value="{{ $snap->purchase_time }}" placeholder="Enter Purchase Time">
                                            @else
                                                <input type="text" class="form-control datepicker" id="purchase_time" name="purchase_time" value="{{ date('Y-m-d') }}" placeholder="Enter Purchase Time">
                                            @endif
                                        </div>
                                    </div>
                                    <div class="form-group longitude latitude" style="display: none;">
                                        <label for="geography_location">Geographic Coordinates</label>
                                            <div class="form-inline">
                                                <input type="text" class="form-control" id="longitude" name="longitude" placeholder="Enter Longitude" value="{{ $snap->longitude }}">
                                                <input type="text" class="form-control" id="latitude" name="latitude" placeholder="Enter Latitude" value="{{ $snap->latitude }}">
                                            </div>
                                    </div>
                                    <div class="form-group" id="map"></div>
                                    <div class="form-group">
                                        <a href="{{ admin_route_url('snaps.edit', ['id' => $snap->id]) }}" class="btn btn-success btn-lg"
                                            data-toggle="modal"
                                            data-target="#"
                                            modal-size="modal-lg"
                                            title="Edit">
                                            <i class="fa fa-check-circle-o fa-btn"></i>Approve This Content</a>
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fa fa-save fa-btn"></i> Update Snap
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                </div>
            </div>
        </div>
        <!-- /.box -->
        <a href="#"  style="display: none;"
        data-toggle="modal"
        data-target="#"
        modal-size="modal-lg"
        id="modal-edit"
        title="Edit">
        Edit Select Image</a>
    </section>
    <!-- /.content -->
@endsection

@section('footer_scripts')
<link rel="stylesheet" href="{{ elixir('css/taggd.css') }}">

<script>
  function initMap() {
    var longitude = parseFloat('{{ $snap->longitude }}');
    var latitude = parseFloat('{{ $snap->latitude }}');

    var geography_location = {lat: latitude, lng: longitude};
    var map = new google.maps.Map(document.getElementById('map'), {
      zoom: 20,
      center: geography_location
    });
    var marker = new google.maps.Marker({
      position: geography_location,
      map: map
    });
  }
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDL2kUSI8aZRZY2M1x3ios-LmwOoVYZ_9o&callback=initMap" async defer></script>
<script src="{{ elixir('js/taggd.js') }}"></script>
<script src="{{ elixir('js/elevate.js') }}"></script>
<script type="text/javascript">

    $(document).ready(function() {
        $('#snapUpdate').on('submit', function (e) {
            e.preventDefault();
            REBEL.onSubmit($(this), function (responseData) {
                REBEL.removeAllMessageAlert();
                if (responseData.status == "ok") {
                    REBEL.smallNotifTemplate(responseData.message, '.body', 'success');
                }
            }, true);

            setTimeout(function() {
                REBEL.removeAllMessageAlert();
            }, 3000);
        });

        $('.datepicker').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
        });

        $(".img-tag").on('click', function(img) {
            //console.log(img.toElement.id);
            var link = $('#modal-edit');
            var nameLink = img.toElement.id+'/edit-snap-file';
            link.attr('href', nameLink);

            link.trigger('click');

        });
    });
</script>
<style type="text/css">
    img.img-tag {
        cursor: pointer;
    }

    #map {
        border:1px solid rgba(8, 8, 8, 0.22);
        max-width: 100%;
        height: 160px;
    }

</style>
@stop
