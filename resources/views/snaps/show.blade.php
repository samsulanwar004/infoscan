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
            <div class="box-header with-border">
                <h3 class="box-title">
                </h3>

                <div class="box-tools-new pull-right">
                    <?php
                      $back = isset($_SERVER['HTTP_REFERER']) ? htmlspecialchars($_SERVER['HTTP_REFERER']) : '/snaps';
                    ?>
                    <meta name="csrf-token" content="{{ csrf_token() }}">
                    <a href="{{ $back }}" class="btn btn-box-tool"><i
                            class="fa fa-times"></i></a>
                </div>
            </div>
            <div class="box-body">
                <div class="row">
                    <div id="loading" style="display: none;">
                      <button type="button" class="btn btn-default btn-lrg">
                        <i class="fa fa-spin fa-refresh"></i>&nbsp; Please wait...
                      </button>
                    </div>
                    <div class="col-md-4" id="side-left">
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
                                            @if($snap->mode_type == 'input' || $snap->mode_type == 'tags' || $snap->mode_type == 'no_mode') 
                                                @if($files->lastPage() > 1)  
                                                    <div class="new-pagination">
                                                        {{ $files->links() }}
                                                    </div>                                                   
                                                @endif
                                                <div class="new-mode">
                                                    <button id="mode-tag" class="btn btn-primary btn-sm"><i class="fa fa-tag" aria-hidden="true"></i></button>
                                                    <button id="mode-zoom" class="btn btn-primary btn-sm"><i class="fa fa-search" aria-hidden="true"></i></button>
                                                    <button id="mode-crop" class="btn btn-primary btn-sm"><i class="fa fa-crop" aria-hidden="true"></i></button>
                                                </div>
                                                <div id="imgtag" class="show-tag">
                                                    <img src="{{ config('filesystems.s3url') . $files->first()->file_path }}" alt="{{ $files->first()->file_code }}" class="margin img-thumbnail img-responsive img-zoom"  id="tag-image">
                                                    <div id="tagbox">
                                                    </div>                    
                                                </div>
                                                <div class="show-zoom" style="display: none;">
                                                    <div id="window" class="magnify img-thumbnail" data-magnified-zone=".mg_zone">
                                                        <div class="magnify_glass">
                                                            <div class="mg_ring"></div>
                                                            <div class="pm_btn plus"><h3>+</h3></div>
                                                            <div class="pm_btn minus"><h3>-</h3></div>
                                                            <div class="mg_zone"></div>
                                                        </div>
                                                        <div class="element_to_magnify">
                                                            <img src="{{ config('filesystems.s3url') . $files->first()->file_path }}" alt="{{ $files->first()->file_code }}" class="img-responsive img-zoom" id="{{$files->first()->id}}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="show-crop img-thumbnail margin" style="display: none;">
                                                    <img src="{{ config('filesystems.s3url') . $files->first()->file_path }}" alt="{{ $files->first()->file_code }}" class="  img-responsive img-crop" id="{{$files->first()->id}}">
                                                </div>
                                                <button id="click-zoom" style="display: none;"></button>
                                            @elseif ($snap->mode_type == 'image')
                                                @if($files->lastPage() > 1)  
                                                    <div class="new-pagination">
                                                        {{ $files->links() }}
                                                    </div>                                                   
                                                @endif
                                                <div class="new-mode">                                                   
                                                    <button id="mode-tag" class="btn btn-primary btn-sm"><i class="fa fa-picture-o" aria-hidden="true"></i></button>
                                                    <button id="mode-zoom" class="btn btn-primary btn-sm"><i class="fa fa-search" aria-hidden="true"></i></button>
                                                </div>
                                                <div class="show-tag">
                                                    <img src="{{ config('filesystems.s3url') . $files->first()->file_path }}" alt="{{ $files->first()->file_code }}" class="margin img-thumbnail img-responsive">                 
                                                </div>
                                                <div class="show-zoom" style="display: none;">
                                                    <div id="window" class="magnify img-thumbnail" data-magnified-zone=".mg_zone">
                                                        <div class="magnify_glass">
                                                            <div class="mg_ring"></div>
                                                            <div class="pm_btn plus"><h3>+</h3></div>
                                                            <div class="pm_btn minus"><h3>-</h3></div>
                                                            <div class="mg_zone"></div>
                                                        </div>
                                                        <div class="element_to_magnify">
                                                            <img src="{{ config('filesystems.s3url') . $files->first()->file_path }}" alt="{{ $files->first()->file_code }}" class="img-responsive img-zoom" id="{{$files->first()->id}}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <button id="click-zoom" style="display: none;"></button>
                                            @elseif($snap->mode_type == 'audios')
                                                @if($files->lastPage() > 1)  
                                                    <div class="new-pagination">
                                                        {{ $files->links() }}
                                                    </div>                                                   
                                                @endif
                                                <div class="new-mode">                                                   
                                                    <button id="mode-tag" class="btn btn-primary btn-sm"><i class="fa fa-headphones" aria-hidden="true"></i></button>
                                                    <button id="mode-zoom" class="btn btn-primary btn-sm"><i class="fa fa-search" aria-hidden="true"></i></button>
                                                    <button id="mode-crop" class="btn btn-primary btn-sm"><i class="fa fa-crop" aria-hidden="true"></i></button>                                                    
                                                </div>
                                                <div class="img-thumbnail show-tag" style="padding-right: 10px;">
                                                    <img src="{{ config('filesystems.s3url') . $files->first()->file_path }}" alt="{{ $files->first()->file_code }}" class="margin img-responsive" id="{{ $files->first()->id }}">
                                                    @if($audios)
                                                    <audio controls class="img-responsive" style="padding-top: 30px;">
                                                      <source src="{{ config('filesystems.s3url') . $audios->file_path }}" type="audio/mpeg">
                                                        Your browser does not support the audio element.
                                                    </audio>
                                                    @endif
                                                </div>
                                                <div class="show-zoom" style="display: none;">
                                                    <div id="window" class="magnify img-thumbnail" data-magnified-zone=".mg_zone">
                                                        <div class="magnify_glass">
                                                            <div class="mg_ring"></div>
                                                            <div class="pm_btn plus"><h3>+</h3></div>
                                                            <div class="pm_btn minus"><h3>-</h3></div>
                                                            <div class="mg_zone"></div>
                                                        </div>
                                                        <div class="element_to_magnify">
                                                            <img src="{{ config('filesystems.s3url') . $files->first()->file_path }}" alt="{{ $files->first()->file_code }}" class="img-responsive img-zoom" id="{{$files->first()->id}}">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="show-crop img-thumbnail margin" style="display: none;">
                                                    <img src="{{ config('filesystems.s3url') . $files->first()->file_path }}" alt="{{ $files->first()->file_code }}" class="  img-responsive img-crop" id="{{$files->first()->id}}">
                                                </div>
                                                <button id="click-zoom" style="display: none;"></button>
                                            @endif                                                                     
                                        </div>
                                    </div>
                                </li>
                            </ul>

                        </div>

                    </div>

                    <div class="col-md-6">
                        <p class="no-shadow">

                        </p>                       
                        <div class="timeline-item ">
                            <ul class="timeline timeline-inverse">
                                <li class="no-margin-right">
                                  <i class="fa fa-list bg-blue"></i>

                                  <div class="timeline-item no-margin-right">
                                    <span class="time"><i class="fa fa-clock-o"></i> {{ $snap->created_at->diffForHumans() }}</span>

                                    <h3 class="timeline-header">Detail product</h3>

                                    <div class="timeline-body snaps-detail">
                                      @include('snaps.show_detail', ['snap' => $snap])
                                    </div>
                                  </div>
                                </li>
                            </ul>
                            @if ($snap->mode_type == 'image')
                            <ul class="timeline timeline-inverse">
                                <li class="no-margin-right">
                                    <i class="fa fa-list bg-blue"></i>
                                    <div class="timeline-item no-margin-right">
                                        <span class="time"><i class="fa fa-clock-o"></i> {{ $snap->created_at->diffForHumans() }}</span>

                                        <h3 class="timeline-header">Google OCR</h3>

                                        <textarea class="form-control" style="resize:none;width: 100%;height: 500px;margin-top: 10px;" cols="50" readonly="readonly">
                                            {{ $files->first()->recognition_text }}
                                        </textarea>
                                    </div>
                                </li>
                            </ul>
                            @endif
                        </div>                       
                        
                    </div>
                    <form id="snapUpdate" action="{{ admin_route_url('snaps.update', ['id' => $snap->id]) }}"  method="POST">
                    {{ csrf_field() }}
                    {{ method_field('PUT') }}
                    <div class="col-md-2">
                        <div class="snaps-button">
                                <div class="form-group receipt_id">
                                    <label for="receipt_id">Receipt ID</label>
                                    <input type="text" class="form-control input-sm tab-side" id="receipt_id" name="receipt_id" placeholder="Enter Receipt ID" value="{{ $snap->receipt_id }}" data-id="1">
                                </div>                                    
                                <div class="form-group location">
                                    <label for="location">Location</label>
                                    <input type="text" class="form-control input-sm tab-side" id="location" name="location" placeholder="Enter location" value="{{ $snap->location }}" tabIndex="2">
                                </div>
                                <div class="form-group purchase_time">
                                    <label for="purchase_time">Purchase Date & Time</label>
                                    <div class='input-group date' id='datetimepicker1'>
                                        <span class="input-group-addon">
                                            <span class="fa fa-calendar"></span>
                                        </span>
                                        @if($snap->purchase_time == true)
                                            <input type='text' class="form-control input-sm tab-side" name="purchase_time" value="{{ $snap->purchase_time }}" tabIndex="3"/>
                                        @else
                                            <input type="text" class="form-control input-sm tab-side" name="purchase_time" value="{{ date('Y-m-d H:i')}}" tabIndex="3">
                                        @endif                                  
                                    </div>
                                </div>
                                <div class="form-group outlet_name">
                                    <label for="outlet_name">Outlet Name</label>
                                    <input type="text" class="form-control input-sm tab-side" id="outlet_name" name="outlet_name" placeholder="Enter outlet name" value="{{ $snap->outlet_name }}" tabIndex="4">
                                </div>
                                <div class="form-group outlet_type">
                                    <label for="outlet_type">Outlet Type</label>
                                    <input type="text" class="form-control input-sm tab-side" list="outlet-type" id="outlet_type" name="outlet_type" placeholder="Enter outlet type" value="{{ $snap->outlet_type }}" tabIndex="5">
                                </div>
                                <div class="form-group outlet_city">
                                    <label for="outlet_city">Outlet City</label>
                                    <input type="text" class="form-control input-sm tab-side" id="outlet_city" name="outlet_city" placeholder="Enter outlet type" value="{{ $snap->outlet_city }}" tabIndex="6">
                                </div>
                                <div class="form-group outlet_province">
                                    <label for="outlet_province">Outlet Province</label>
                                    <input type="text" class="form-control input-sm tab-side" id="outlet_province" name="outlet_province" placeholder="Enter outlet province" value="{{ $snap->outlet_province }}" tabIndex="7">
                                </div>
                                <div class="form-group outlet_zip_code">
                                    <label for="outlet_zip_code">Outlet Zipcode</label>
                                    <input type="number" class="form-control input-sm tab-side" id="outlet_zip_code" name="outlet_zip_code" placeholder="Enter outlet zipcode" value="{{ $snap->outlet_zip_code }}" tabIndex="8">
                                </div>
                                <div class="form-group outlet_rt_rw">
                                    <label for="outlet_rt_rw">Outlet RT/RW</label>
                                    <input type="text" class="form-control input-sm tab-side" id="outlet_rt_rw" name="outlet_rt_rw" placeholder="Enter rt00/rw00" value="{{ $snap->outlet_rt_rw }}" tabIndex="9">
                                </div>
                                <div class="form-group total_value">
                                    <label for="total_value">Total Value</label>
                                    <input type="number" class="form-control input-sm tab-side" id="total_value" placeholder="Enter Total Value" value="{{ clean_numeric($snap->total_value,'%',false,'.') }}" readonly="readonly" tabIndex="10">
                                </div>                                    
                                <div class="form-group payment_method">
                                    <label for="payment_method">Payment Method</label>
                                    <input type="text" class="form-control input-sm tab-side" list="payment-method" id="payment_method" name="payment_method" placeholder="Enter Payment Method" value="{{ $snap->payment_method }}" tabIndex="11">
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
                                    <button type="submit" class="btn btn-primary btn-sm btn-block tab-side" id="submit">
                                        <i class="fa fa-save fa-btn"></i> Update Snap
                                    </button>
                                </div>
                                <div class="form-group">
                                    <a href="{{ admin_route_url('snaps.edit', ['id' => $snap->id]) }}" class="btn btn-success btn-sm btn-block"
                                        data-toggle="modal"
                                        data-target="#"
                                        modal-size="modal-lg"
                                        title="Approve" id="confirm">
                                        <i class="fa fa-check-circle-o fa-btn"></i>Approve This Content</a>
                                </div>
                            </form>
                            <datalist id="outlet-type">
                              <option value="Minimarket">
                              <option value="Supermarket">
                              <option value="Hypermarket">
                              <option value="Drug Store">
                            </datalist>
                            
                            <datalist id="payment-method">
                                @foreach($paymentMethods as $pm)
                                  <option value="{{ $pm }}">
                                @endforeach
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
<link rel="stylesheet" href="{{ elixirCDN('css/zoom.css') }}">
<style type="text/css">
    img.img-tag {
        cursor: pointer;
    }

    /*#map {
        border:1px solid rgba(8, 8, 8, 0.22);
        max-width: 100%;
        height: 160px;
    }*/

    .new-pagination {
        margin-bottom: -20px;
        margin-top: -25px;
        margin-left: 10px;
    }

    .new-mode {
        margin-left: 10px;
    }

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

    .zoomContainer
    { 
        z-index: 9999;
    }

    .zoomWindow
    { 
        z-index: 9999;
    }

    #imgtag
    {
        position: relative;
        min-width: 300px;
        min-height: 300px;
        float: none;
/*        border: 3px solid #FFF;*/
        cursor: crosshair;
        text-align: center;
    }
    .tagview
    {
        border: 1px solid #F10303;
        width: 100px;
        height: 80px;
        position: absolute;
    /*display:none;*/
        opacity: 0;
        color: #FFFFFF;
        text-align: center;
    }
    .square
    {
        display: block;
        height: 79px;
    }
    .person
    {
        height: 80px;
        background-color: rgba(0, 0, 0, 0.6);
        border-top: 1px solid #F10303;
    }
    #tagit
    {
        position: absolute;
        z-index: 1;
        top: 0;
        left: 0;
        width: 200px;
        border: 1px solid #D7C7C7;
        background-color: rgba(0, 0, 0, 0.6);
        padding: 7px;
    }
    #tagit div.text
    {
        margin-bottom: 5px;
    }
    #tagit input
    {
        margin-bottom: 5px;
    }
    #tagit input[type=button]
    {
        margin-right: 5px;
    }

    a.taggd__button
    {
        cursor: pointer;
    }

    #tag-image {
        margin-left: 10px;
        margin-bottom: 10px;
    }

    .img-zoom {
        margin-left: 10px;
        margin-bottom: 10px;
        margin-top: 10px;
    }

    #loading {
        top: 50%;
        left: 50%;
        border: 1px solid #ccc;
        background-color: #f3f3f3;
        position:fixed;
        z-index: 1;
    }

    #crop-result {
        border: 5px solid #ccc;
        padding: 5px;
        height: 124px;
        width: 100%;
    }

    #crop-img {
        height: 100px;
        width: 100px;
        margin: 2px;
        cursor: move;
    }

    #img-cropping {
        width: 25px;
        height: 26px;
        margin: 2px;
    }

    #side-left {
        margin-right: -35px;
    }

    #kotak-drop {
        float: left;
        width: 30px;
        height: 30px;
        background-color: white;
        margin-right: -35px;
    }

    .img-item-loading {
        margin-left: 2px;
        margin-top: 3px;
        position: absolute;
        color: white;
    }

    .box-tools-new {
        position: absolute;
        top: 5px;
    }

</style>
<link rel="stylesheet" href="{{ elixirCDN('css/datetimepicker.css') }}" />
<link rel="stylesheet" href="{{ elixirCDN('css/crop.css') }}" />
<script src="{{ elixirCDN('js/datetimepicker.js') }}"></script>
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
<script src="{{ elixirCDN('js/zoom.js') }}"></script>
<script src="{{ elixirCDN('js/crop.js') }}" crossorigin="anonymous"></script>
<script type="text/javascript">

    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        $('#snapUpdate').on('submit', function (e) {
            e.preventDefault();
            REBEL.onSubmit($(this), function (responseData) {
                REBEL.removeAllMessageAlert();
                if (responseData.status == "ok") {
                    REBEL.smallNotifTemplate(responseData.message, '.body', 'success'); 
                    var totalValue = responseData.data;
                    $('#total_value').val(responseData.data);                  
                }
            }, false);

            setTimeout(function() {
                REBEL.removeAllMessageAlert();
            }, 3000);
        });

        $('#tagUpdate').on('submit', function (e) {
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
            var time = Math.round(Date.now() / 100);

            if(countOfTextbox >= 20) {
                $(this).attr('disabled', 'disabled');
                return;
            }

            $('tbody#inputs-show').append('<tr id="input-show'+countOfTextbox+'">'+
                '<td><a class="btn btn-box-tool" onclick="deleteTagShow('+countOfTextbox+')"><i class="fa fa-remove"></i></a></td>'+
                '<td><i class="fa fa-spinner fa-spin fa-2x img-item-loading" id="load-'+time+'" aria-hidden="true" style="display: none;"></i><div id="kotak-drop" class="'+time+'" ondrop="drop(event)" ondragover="allowDrop(event)"></div><input type="hidden" name="newtag[crop_path][]" id="crop-'+time+'"></td>'+
                '<td width="300"><input type="text" name="newtag[name][]" class="form-control input-sm tag-name-show" placeholder="Product Name" required="required"></td>'+
                '<td width="200"><input type="text" name="newtag[weight][]" class="form-control input-sm" placeholder="Weight"></td>'+
                '<td width="300"><input type="text" name="newtag[brands][]" class="form-control input-sm" placeholder="Brands"></td>'+
                '<td width="300"><input type="text" name="newtag[sku][]" class="form-control input-sm" placeholder="SKU"></td>'+
                // '<td width="300"><input type="text" list="variants" name="newtag[variants][]" class="form-control input-sm" placeholder="Variants"></td>'+                
                '<td width="100"><input type="number" name="newtag[qty][]" class="form-control input-sm" placeholder="QTY" required="required"></td>'+
                '<td width="200" class="text-right"><input type="number" name="newtag[total][]" class="form-control input-sm" placeholder="Total Price" required="required">'+
                '</td></tr>');
        });

        $('a#remove-show').on('click', function(e) {
            e.preventDefault();
            if(confirm('Are you sure want to delete this item ?')) {
                $(e.target).closest('#input-show').remove();
            }
        });

        $('.tab-side').keydown(function(e) {
            return tab(this, e);
        });

        $('#receipt_id').focus(function(e) {
            this.setAttribute('tabIndex', this.getAttribute( "data-id" ));
        });

        $('#receipt_id').blur(function(e) {
            this.removeAttribute('tabIndex');
        });

        $('#click-zoom').on('click', function() {
            var scaleNum = 1.5;
            $(".magnify").jfMagnify();
            $('.plus').click(function(){
                scaleNum += .5;
                if (scaleNum >=3) {
                    scaleNum = 3;
                };
                $(".magnify").data("jfMagnify").scaleMe(scaleNum);
            });
            $('.minus').click(function(){
                scaleNum -= .5;
                if (scaleNum <=1.5) {
                    scaleNum = 1.5;
                };
                $(".magnify").data("jfMagnify").scaleMe(scaleNum);
            });
        });

        var counter = 0;
        var mouseX = 0;
        var mouseY = 0;
        var fileId = {!! $files->first()->id !!};

        $("#imgtag img").click(function(e) { // make sure the image is click
            var offset = $(this).offset(); // get the div to append the tagging list
            mouseX = (e.pageX - offset.left); // x and y axis
            mouseY = (e.pageY - offset.top);

            $('#tagit').remove(); // remove any tagit div first           
            $('div#imgtag').append('<div id="tagit">'+
                '<input type="text" name="name" class="form-control input-sm" placeholder="Product Name" id="name">'+
                '<input type="text" name="weight" class="form-control input-sm" placeholder="Weight" id="weight">'+
                '<input type="number" name="qty" class="form-control input-sm" placeholder="QTY" id="qty">'+
                '<input type="number" class="form-control input-sm" placeholder="Total Price" id="total" name="total">'+
                '<input type="hidden" name="x" id="x" value="'+mouseX+'">'+
                '<input type="hidden" id="y" name="y" value="'+mouseY+'">'+
                '<input type="button" name="btnsave" value="Save" id="btnsave"/>'+
                '<input type="button" name="btncancel" value="Cancel" id="btncancel" />'+
                '</div>');
            var imgtag = document.getElementById('imgtag');
            var tagit = document.getElementById('tagit');
            var tengah = imgtag.clientHeight/2;

            if (mouseY > tengah) {                
                $('#tagit').css({ top:mouseY-tagit.clientHeight, left:mouseX });          
            } else {
                $('#tagit').css({ top:mouseY, left:mouseX });
            }
            $('#name').focus();

        });

        $(document).on('click', '#tagit #btnsave', function(e) {
            e.stopPropagation();

            var time = Math.round(Date.now() / 100);

            if ($('.tag-input[time=' + time + ']').length > 0) {
                return false;
            }
            
            e.preventDefault();
            var countOfTextbox = $('.tag-name').length;

            if(countOfTextbox >= 20) {
                REBEL.smallNotifTemplate('Form is full', '.modal-content', 'error');
                REBEL.scrollToTop('#modalContent');
                setTimeout(function(){
                    REBEL.removeAllMessageAlert();
                }, 3000);
                return false;
            }

            var name = $('#name').val();
            var weight = $('#weight').val();
            var qty = $('#qty').val();
            var total = $('#total').val();
            mouseX = $('#x').val();
            mouseY = $('#y').val();

            if (name == false || weight == false || qty == false || total == false)
            {
                REBEL.smallNotifTemplate('Not null', '.body', 'error');
                REBEL.scrollToTop('#modalContent');
                setTimeout(function(){
                    REBEL.removeAllMessageAlert();
                }, 3000);
                return false;
            }

            var image = document.getElementById('tag-image');            
            mouseX = mouseX / image.clientWidth;
            mouseY = mouseY / image.clientHeight;

            taggingSave(name, weight, qty, total, mouseX, mouseY, fileId, time);

            var className = countOfTextbox+'-new-tag';
            viewtagsave(name, mouseX, mouseY, className);
            // $('tbody#inputs-show').append('<tr class="tag-input" time=' + time + ' id="input'+countOfTextbox+'">'+
            //     '<td><a class="btn btn-box-tool" onclick="deleteTag('+countOfTextbox+')"><i class="fa fa-remove"></i></a></td>'+
            //     '<td><div id="kotak-drop" class="'+time+'" ondrop="drop(event)" ondragover="allowDrop(event)"><input type="hidden" name="newtag[crop_path][]" id="crop-'+time+'"></div></td>'+
            //     '<td width="300"><input type="text" name="newtag[name][]" class="form-control input-sm tag-name '+countOfTextbox+'new" id="'+countOfTextbox+'|'+mouseX+'|'+mouseY+'" onclick="editTag(this)" onkeyup="editNewTag(this)" value="'+name+'"></td>'+
            //     '<td width="200"><input type="text" name="newtag[brands][]" class="form-control input-sm" placeholder="Brands"></td>'+
            //     '<td width="300"><input type="text" list="variants" name="newtag[variants][]" class="form-control input-sm" placeholder="Variants"></td>'+
            //     '<td width="200"><input type="text" name="newtag[weight][]" class="form-control input-sm" value="'+weight+'" placeholder="Weight"></td>'+
            //     '<td width="100"><input type="number" name="newtag[qty][]" class="form-control input-sm" value="'+qty+'"></td>'+
            //     '<td width="200"><input type="number" name="newtag[total][]" class="form-control input-sm" value="'+total+'">'+
            //     '<input type="hidden" name="newtag[x][]" value="'+mouseX+'"><input type="hidden" name="newtag[y][]" value="'+mouseY+'"></td></tr>');

            $('tbody#inputs-show').append('<tr class="tag-input" time=' + time + ' id="input'+countOfTextbox+'">'+
                '<td><a class="btn btn-box-tool" onclick="deleteTag('+countOfTextbox+')"><i class="fa fa-remove"></i></a></td>'+
                '<td><i class="fa fa-spinner fa-spin fa-2x img-item-loading" id="load-'+time+'" aria-hidden="true" style="display: none;"></i><div id="kotak-drop" class="'+time+'" ondrop="drop(event)" ondragover="allowDrop(event)"></div><input type="hidden" name="tag[crop_path][]" id="crop-'+time+'"></td>'+
                '<td width="300"><input type="text" name="tag[name][]" class="form-control input-sm tag-name '+countOfTextbox+'new" id="'+countOfTextbox+'|'+mouseX+'|'+mouseY+'" onclick="editTag(this)" onkeyup="editNewTag(this)" value="'+name+'"></td>'+
                '<td width="200"><input type="text" name="tag[weight][]" class="form-control input-sm" value="'+weight+'" placeholder="Weight"></td>'+
                '<td width="200"><input type="text" name="tag[brands][]" class="form-control input-sm" placeholder="Brands"></td>'+
                '<td width="200"><input type="text" name="tag[sku][]" class="form-control input-sm" placeholder="SKU"></td>'+
                // '<td width="300"><input type="text" list="variants" name="tag[variants][]" class="form-control input-sm" placeholder="Variants"></td>'+                
                '<td width="100"><input type="number" name="tag[qty][]" class="form-control input-sm" value="'+qty+'"></td>'+
                '<td width="200"><input type="number" name="tag[total][]" class="form-control input-sm" value="'+total+'">'+
                '<input type="hidden" name="tag[id][]" id="tag-id-'+time+'"></td></tr>');


            $('#tagit').fadeOut();

        });

        // Cancel the tag box.
        $(document).on('click', '#tagit #btncancel', function() {
          $('#tagit').fadeOut();
        });

        // mouseover the tagboxes that is already there but opacity is 0.
        $('#tagbox').on('mouseover', '.tagview', function() {
            var pos = $( this ).position();
            $(this).css({ opacity: 1.0 }); // div appears when opacity is set to 1.
        }).on('mouseout', '.tagview', function() {
            $(this).css({ opacity: 0.0 }); // hide the div by setting opacity to 0.
        });

        // load the tags for the image when page loads.
        var img = $('#imgtag').find('img');
        var id = $(img).attr('alt');
        var type = '{!! $snap->mode_type !!}';
        if (type == 'input' || type == 'tags' || type == 'no_mode') {
            viewtag(id);
        }        
        function viewtag(id)
        {
          // get the tag list with action remove and tag boxes and place it on the image.
            var image = document.getElementById('tag-image');
            var data =[];
            var options = {};
            var taggd;
            var datas = {!! json_encode($files->first()->tag) !!};
            //$.getJSON( id+"/tagging" , function( datas ) {
                $.each( datas, function( key, value ) {
                    if (value.img_x != '' && value.img_y != '') {
                        data.push(
                            Taggd.Tag.createFromObject({
                                position: { x: value.img_x, y: value.img_y },
                                text: value.name,
                                buttonAttributes: {
                                    id: value.id+"-tag",
                                },
                            })
                        );
                    }
                });
                taggd = new Taggd(image, options, data);
            //}, "json");
        }

        function viewtagsave(name, mouseX, mouseY, className)
        {
            var image = document.getElementById('tag-image');
            var data =[];
            var options = {};
            var taggd;

            var data = [
                Taggd.Tag.createFromObject({
                    position: { x: mouseX, y: mouseY },
                    text: name,
                    buttonAttributes: {
                        id: className,
                    },
                }),
            ];

            taggd = new Taggd(image, options, data);
        }

        $('.tag-name').on('click', function(e) {
            var image = document.getElementById('tag-image');
            var idArray = e.toElement.id.split('|');
            var id = idArray[0];
            var img_x = idArray[1];
            var img_y = idArray[2];
            $("#"+id+"popup").css({
                'display' : 'none',
            });
            var options = {};
            if (img_x != '' || img_y != '') {
                var data = [
                    Taggd.Tag.createFromObject({
                        position: { x: img_x, y: img_y },
                        text: this.value,
                        popupAttributes: {
                            id: id+"popup",
                        },
                    }),
                ];
            }

            var taggd = new Taggd(image, options, data);

            $("#"+id+"popup").css({
                'display' : '',
            });

            $('.'+id+'old').on('keyup', function() {
                $("#"+id+"popup").html($("."+id+'old').val());
            });
        });

        function taggingSave(name, weight, qty, price, x, y, fileId, time) {
            $.get( "{{ admin_route_url('tagging.save') }}", { 
                name : name,
                weight : weight,
                quantity : qty,
                total_price : price,                
                img_x : x,                
                img_y : y, 
                file_id : fileId,               
            })
                .success(function( data ) {
                $('#tag-id-'+time).val(data.message);
            });
        } 

        $('#mode-zoom').on('click', function() {
            $('.show-tag').hide();
            $('.show-crop').hide();
            $('.show-zoom').show();
            $('#click-zoom').click();
        });

        $('#mode-tag').on('click', function() {
            $('.show-tag').show();
            $('.show-zoom').hide();
            $('.show-crop').hide();
        });

        $('#mode-crop').on('click', function() {
            $('.show-tag').hide();
            $('.show-zoom').hide();
            $('.show-crop').show();
        });

        // start cropping image
        var $image = $('.img-crop');
        var $button = $('#crop-button');
        var $result = $('#crop-result');
        var croppable = false;

        $image.cropper({
            movable: false,
            zoomable: false,
            viewMode: 1,
            autoCrop: false,
            ready: function () {
              croppable = true;
            },
            cropstart: function() {
                $('#btn-drag').remove();
            },
            cropend: function() {
                var croppedCanvas;
                var roundedCanvas;

                if (!croppable) {
                  return;
                }

                // Crop
                croppedCanvas = $image.cropper('getCroppedCanvas');

                // Round
                roundedCanvas = getRoundedCanvas(croppedCanvas);

                var _img = '<img id="crop-img" class="img-dragging" src="' + roundedCanvas.toDataURL() + '" draggable="true" ondragstart="drag(event)" ondrop="drop(event)" ondragover="allowDrop(event)">';
                if($('.img-dragging').length > 0) {
                    $('.img-dragging').attr('src', roundedCanvas.toDataURL());
                } else {
                    $('.cropper-crop-box').append(_img);
                }
                
            }
        });

    });

    function allowDrop(ev) {
        ev.preventDefault();
    }

    var dragElement = null;
    function drag(ev) {
        dragElement = this;
        //ev.originalEvent.dataTransfer.effectAllowed = 'move';
        ev.dataTransfer.setData("text", ev.target.id);
    }

    function removeNode(node)
    {

    }

    function drop(ev) { 

        ev.preventDefault();
        var data = ev.dataTransfer.getData("text");
        var node = document.getElementById(data);
        var nodeCopy = document.getElementById(data);
       
        var img = '';
        var _className = '';
        if(ev.target.nodeName == 'IMG') {
            var nodeParent = node.parentNode;
            var dropParent = ev.target.parentNode;
            try{
               _className = dropParent.className;
                dropParent.innerHTML = '';
                node.parentNode.removeChild(nodeCopy);// = '';
                img = dropParent.appendChild(nodeCopy);
            }catch(e){
                img = nodeParent.appendChild(node);
                _className = nodeParent.className;
            }           
        } else {
            img = ev.target.appendChild(node);
            _className = ev.target.className;
        }

        
        img.style.width += "25px";
        img.style.height += "25px";

        $('.img-dragging').removeClass('img-dragging').addClass('img-dropped');
        
        var blob = dataURItoBlob(img.src);
        var fd = new FormData();
        fd.append('image', blob);
        $('#load-'+_className).show();
        $('#submit').attr('disabled', 'disabled');
        $.ajax({
            type: 'POST',
            url: "{{ admin_route_url('crop.upload') }}",
            data: fd,
            processData: false,
            contentType: false
        }).success(function(data) {
            $('#submit').removeAttr('disabled');
            $('#crop-'+_className).val(data.message);
            $('#load-'+_className).hide();
        });
    }

    function getRoundedCanvas(sourceCanvas) {
      var canvas = document.createElement('canvas');
      var context = canvas.getContext('2d');
      var width = sourceCanvas.width;
      var height = sourceCanvas.height;

      canvas.width = width;
      canvas.height = height;
      context.drawImage(sourceCanvas, 0, 0, width, height);

      return canvas;
    }

    function dataURItoBlob(dataURI) {
        var binary = atob(dataURI.split(',')[1]);
        var array = [];
        for(var i = 0; i < binary.length; i++) {
            array.push(binary.charCodeAt(i));
        }
        return new Blob([new Uint8Array(array)], {type: 'image/jpeg'});
    }

    function deleteTagShow(e)
    {
        if(confirm('Are you sure want to delete this item ?')) {
            $('#input-show'+e).remove();
        }
    }

    function tab(field, event) {
        if (event.which == 13 /* IE9/Firefox/Chrome/Opera/Safari */ || event.keyCode == 13 /* IE8 and earlier */ ) {
            for (i = 0; i < field.form.elements.length; i++) {
                if (field.form.elements[i].tabIndex == field.tabIndex + 1) {
                    field.form.elements[i].focus();
                    if (field.form.elements[i].type == "text") {
                        field.form.elements[i].select();                           
                        break;
                    } else if (field.form.elements[i].type == "submit") {
                        field.form.elements[i].click();
                        $('#confirm').trigger('click');
                        break;
                    }
                }
            }
            return false;
        }
        return true;
    }

    $('#snapUpdate').on('keydown', 'input, select, textarea', function(e) {
        var self = $(this)
          , form = self.parents('form:eq(0)')
          , focusable
          , next
          ;          
        if (e.keyCode == 13) {
            focusable = form.find('input,select,button,textarea').filter(':visible');
            next = focusable.eq(focusable.index(this)+1);

            if (next.length) {
                next.focus();
                next.select();
            } else {
                $('#receipt_id').focus();
                $('#receipt_id').select();
            }
            return false;
        }
    });

    function deleteTag(e)
    {
        if(confirm('Are you sure want to delete this item ?')) {
            $('#input'+e).remove();
            $("#"+e+"-new-tag").remove();
        }
    }

    function editTag(e)
    {
        var image = document.getElementById('tag-image');
        var idArray = e.id.split('|');
        var id = idArray[0];
        var img_x = idArray[1];
        var img_y = idArray[2];
        $("#"+id+"newpopup").css({
            'display' : 'none',
        });
        var options = {};
        var data = [
            Taggd.Tag.createFromObject({
                position: { x: img_x, y: img_y },
                text: e.value,
                popupAttributes: {
                    id: id+"newpopup",
                },
            }),
        ];

        var taggd = new Taggd(image, options, data);

        $("#"+id+"newpopup").css({
            'display' : '',
        });
    }

    function editNewTag(e)
    {
        var idArray = e.id.split('|');
        var id = idArray[0];
        $("#"+id+"newpopup").html($("."+id+'new').val());
    }


</script>
@stop
