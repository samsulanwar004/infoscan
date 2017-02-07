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

                    <div class="col-md-4">
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
                                                    <img src="{{ URL::to('img/snaps/window-player.png') }}" alt="{{ $file->file_code }}" class="margin img-thumbnail img-responsive img-tag"  id="{{$file->id}}">
                                                @else
                                                    <img src="{{ config('filesystems.s3url') . $file->file_path }}" alt="{{ $file->file_code }}" class="margin img-thumbnail img-responsive img-tag"  id="{{$file->id}}">
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </li>
                            </ul>

                        </div>

                    </div>

                    <div class="col-md-6">
                        <p class="no-shadow">

                        </p>
                        <form id="snapUpdate" action="{{ admin_route_url('snaps.update', ['id' => $snap->id]) }}"  method="POST">
                        {{ csrf_field() }}
                        {{ method_field('PUT') }}
                        <div class="timeline-item ">
                            <ul class="timeline timeline-inverse">
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

                    <div class="col-md-2">
                        <div class="snaps-button">
                                <div class="form-group receipt_id">
                                    <label for="receipt_id">Receipt ID</label>
                                    <input type="text" class="form-control input-sm" id="receipt_id" name="receipt_id" placeholder="Enter Receipt ID" value="{{ $snap->receipt_id }}">
                                </div>                                    
                                <div class="form-group location">
                                    <label for="location">Location</label>
                                    <input type="text" class="form-control input-sm" id="location" name="location" placeholder="Enter location" value="{{ $snap->location }}">
                                </div>
                                <div class="form-group purchase_time">
                                    <label for="purchase_time">Purchase Date & Time</label>
                                    <div class='input-group date' id='datetimepicker1'>
                                        <span class="input-group-addon">
                                            <span class="fa fa-calendar"></span>
                                        </span>
                                        @if($snap->purchase_time == true)
                                            <input type='text' class="form-control input-sm" name="purchase_time" value="{{ $snap->purchase_time }}" />
                                        @else
                                            <input type="text" class="form-control input-sm" name="purchase_time" value="{{ date('Y-m-d H:i')}}">
                                        @endif                                  
                                    </div>
                                </div>
                                <div class="form-group outlet_name">
                                    <label for="outlet_name">Outlet Name</label>
                                    <input type="text" class="form-control input-sm" id="outlet_name" name="outlet_name" placeholder="Enter outlet name" value="{{ $snap->outlet_name }}">
                                </div>
                                <div class="form-group outlet_type">
                                    <label for="outlet_type">Outlet Type</label>
                                    <input type="text" class="form-control input-sm" list="outlet-type" id="outlet_type" name="outlet_type" placeholder="Enter outlet type" value="{{ $snap->outlet_type }}">
                                </div>
                                <div class="form-group outlet_city">
                                    <label for="outlet_city">Outlet City</label>
                                    <input type="text" class="form-control input-sm" id="outlet_city" name="outlet_city" placeholder="Enter outlet type" value="{{ $snap->outlet_city }}">
                                </div>
                                <div class="form-group outlet_province">
                                    <label for="outlet_province">Outlet Province</label>
                                    <input type="text" class="form-control input-sm" id="outlet_province" name="outlet_province" placeholder="Enter outlet province" value="{{ $snap->outlet_province }}">
                                </div>
                                <div class="form-group outlet_zip_code">
                                    <label for="outlet_zip_code">Outlet Zipcode</label>
                                    <input type="number" class="form-control input-sm" id="outlet_zip_code" name="outlet_zip_code" placeholder="Enter outlet zipcode" value="{{ $snap->outlet_zip_code }}">
                                </div>
                                <div class="form-group total_value">
                                    <label for="total_value">Total Value</label>
                                    <input type="number" class="form-control input-sm" id="total_value" placeholder="Enter Total Value" value="{{ clean_numeric($snap->total_value,'%',false,'.') }}" readonly="readonly">
                                </div>                                    
                                <div class="form-group payment_method">
                                    <label for="payment_method">Payment Method</label>
                                    <select name="payment_method" class="form-control input-sm">
                                        @if($snap->payment_method == true)
                                            <option value="{{ $snap->payment_method }}" selected="selected">{{ $snap->payment_method }}</option>
                                        @endif
                                        @foreach($paymentMethods as $pm)
                                            <option value="{{ $pm }}">{{ $pm }}</option>
                                        @endforeach
                                    </select>
                                </div>
<!--                                     <div class="form-group longitude latitude" style="display: none;">
                                    <label for="geography_location">Geographic Coordinates</label>
                                        <div class="form-inline">
                                            <input type="text" class="form-control input-sm" id="longitude" name="longitude" placeholder="Enter Longitude" value="{{ $snap->longitude }}">
                                            <input type="text" class="form-control input-sm" id="latitude" name="latitude" placeholder="Enter Latitude" value="{{ $snap->latitude }}">
                                        </div>
                                </div> -->
<!--                                     @if($snap->longitude != 0.00000000 && $snap->latitude != 0.00000000 )
                                    <div class="form-group" id="map"></div>
                                @endif -->                                
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary btn-sm btn-block">
                                        <i class="fa fa-save fa-btn"></i> Update Snap
                                    </button>
                                </div>
                                <div class="form-group">
                                    <a href="{{ admin_route_url('snaps.edit', ['id' => $snap->id]) }}" class="btn btn-success btn-sm btn-block"
                                        data-toggle="modal"
                                        data-target="#"
                                        modal-size="modal-lg"
                                        title="Approve">
                                        <i class="fa fa-check-circle-o fa-btn"></i>Approve This Content</a>
                                </div>
                            </form>
                            <datalist id="outlet-type">
                              <option value="Minimarket">
                              <option value="Supermarket">
                              <option value="Hypermarket">
                              <option value="Drug Store">
                            </datalist>
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
<link rel="stylesheet" href="{{ elixirCDN('css/taggd.css') }}">
<style type="text/css">
    img.img-tag {
        cursor: pointer;
    }

    /*#map {
        border:1px solid rgba(8, 8, 8, 0.22);
        max-width: 100%;
        height: 160px;
    }*/

    div.table-custom table {
        border-collapse: separate;
        border-spacing: 0 5px;
    }

    div.table-custom thead th {
        background-color: #EA5941;
        color: white;
    }

    div.table-custom tbody td {
        background-color: rgba(14, 14, 14, 0.1);
    }

    div.table-custom tr td:first-child,
    div.table-custom tr th:first-child {
        border-top-left-radius: 6px;
        border-bottom-left-radius: 6px;
    }

    div.table-custom tr td:last-child,
    div.table-custom tr th:last-child {
        border-top-right-radius: 6px;
        border-bottom-right-radius: 6px;
    }

    input[type=number]::-webkit-inner-spin-button, 
    input[type=number]::-webkit-outer-spin-button 
    { 
      -webkit-appearance: none; 
      margin: 0; 
    }

    div.timeline-body img {
        width: 95%;
        background-size: 100% 100%;
    }

</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.45/css/bootstrap-datetimepicker.min.css" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.45/js/bootstrap-datetimepicker.min.js"></script>
<script src="http://localhost/coba/id.js"></script>
<!-- <script>
  function initMap() {
    var longitude = parseFloat('{{ $snap->longitude }}');
    var latitude = parseFloat('{{ $snap->latitude }}');

    if (longitude != 0.0000000 && latitude != 0.0000000) {
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
  }
</script>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDL2kUSI8aZRZY2M1x3ios-LmwOoVYZ_9o&callback=initMap" async defer></script> -->
<script src="{{ elixirCDN('js/taggd.js') }}"></script>
<script src="{{ elixirCDN('js/elevate.js') }}"></script>
<script type="text/javascript">

    $(document).ready(function() {

        $('#snapUpdate').on('submit', function (e) {
            e.preventDefault();
            REBEL.onSubmit($(this), function (responseData) {
                REBEL.removeAllMessageAlert();
                if (responseData.status == "ok") {
                    REBEL.smallNotifTemplate(responseData.message, '.body', 'success');                    
                }
            }, false);

            setTimeout(function() {
                REBEL.removeAllMessageAlert();
            }, 3000);
        });

        $('#datetimepicker1').datetimepicker({
            icons: {
                time: "fa fa-clock-o",
                date: "fa fa-calendar",
                up: "fa fa-arrow-up",
                down: "fa fa-arrow-down",
                next: "fa fa-arrow-right",
                previous: "fa fa-arrow-left",
            },
            locale: 'id',
            format: 'YYYY-MM-DD H:mm',

        });

        $(".img-tag").on('click', function(img) {
            //console.log(img.toElement.id);
            var link = $('#modal-edit');
            var nameLink = img.toElement.id+'/edit-snap-file';
            link.attr('href', nameLink);

            link.trigger('click');

        });

        $('input[type=number]').on('focus', function(e) {
          $(this).on('mousewheel.disableScroll', function(e) {
            e.preventDefault()
          })
        });
        $('input[type=number]').on('blur', function(e) {
          $(this).off('mousewheel.disableScroll')
        });

        $('a#add-show').on('click', function(e) {
            e.preventDefault();
            var countOfTextbox = $('.tag-name-show').length;

            if(countOfTextbox >= 20) {
                $(this).attr('disabled', 'disabled');
                return;
            }

            $('tbody#inputs-show').append('<tr id="input-show'+countOfTextbox+'"><td><a class="btn btn-box-tool" onclick="deleteTagShow('+countOfTextbox+')"><i class="fa fa-remove"></i></a></td><td width="300"><input type="text" name="newtag[name][]" class="form-control input-sm tag-name" placeholder="Product Name" required="required"></td><td width="300"><input type="text" name="newtag[brands][]" class="form-control input-sm" placeholder="Brands"></td><td width="300"><input type="text" list="variants" name="newtag[variants][]" class="form-control input-sm" placeholder="Variants"></td><td width="100"><input type="number" name="newtag[qty][]" class="form-control input-sm" placeholder="QTY" required="required"></td><td width="200" class="text-right"><input type="number" name="newtag[total][]" class="form-control input-sm" placeholder="Total Price" required="required"><input type="hidden" name="newtag[fileId][]" value="{{ $file->id }}"></td></tr>');
        });

        $('a#remove-show').on('click', function(e) {
            e.preventDefault();
            if(confirm('Are you sure want to delete this item ?')) {
                $(e.target).closest('#input-show').remove();
            }
        });

    });

    function deleteTagShow(e)
    {
        if(confirm('Are you sure want to delete this item ?')) {
            $('#input-show'+e).remove();
        }
    }
</script>
@stop
