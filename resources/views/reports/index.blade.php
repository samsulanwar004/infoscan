@extends('app', ['mini_sidebar' => true])

@section('content')
    @include('partials.content_header', ['pageTitle' => 'Reports', 'pageDescription' => '', 'breadcrumbs' => ['Reports' => false]])
    <?php $configurations = config('common.reports.fields'); ?>
    <!-- Main content -->
    <section class="content">
        <div class="progress" style="display: none">
          <div class="progress-bar progress-bar-striped active" role="progressbar"
            aria-valuenow="10" aria-valuemin="0" aria-valuemax="100" style="width:0%">
            <span id="persent"></span>
          </div>
        </div>
        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border form-inline" style="height: 45px;">

                <div class="box-tools pull-right ">
                    <a class="btn btn-default btn-modal" href="javascript:void(0)"><i class="fa fa-btn fa-filter"></i> Filter</a>
                    <!-- Single button -->
                    <div class="btn-group">
                        <button type="button" class="btn btn-default dropdown-toggle"
                            data-toggle="dropdown"
                            aria-haspopup="true"
                            aria-expanded="false">
                            Action <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a href="#" id="get-excel"><i class="fa fa-btn fa-file-excel-o"></i>Excel</a></li>
                            <!-- <li><a href="#">Something else here</a></li> -->
                        </ul>
                    </div>
                </div>
            </div>
            <div class="box-body">
                <div class="col-md-12 scroll-table-outer">
                    <table class="table reportTable" id="reportTable">
                        <thead>
                            <tr>
                            @if ($configs == null)
                                @foreach($configurations as $field => $label)
                                        <td style="min-width: 100px;" class="{{ $field }}">{{ $label['label'] }}</td>
                                @endforeach
                            @else
                                @foreach($configurations as $field => $label)
                                        <td style="min-width: 100px;@if(!in_array($loop->index, $configs))display: none;@endif" class="{{ $field }}">{{ $label['label'] }}</td>
                                @endforeach
                            @endif
                            </tr>
                        </thead>

                        <tbody>
                        @if ($configs == null)
                            @foreach($results as $result)
                            <tr>
                                @foreach($configurations as $field => $label)
                                    @if($field == 'outlet_address')
                                        <td>
                                            <div style="overflow:auto;height: 100px;">{{ $result->{$field} }}</div>
                                        </td>
                                    @else
                                        <td>
                                            @if($field == 'age')
                                            @php
                                                $newDate = new \Carbon\Carbon($result->{$field});
                                                $newAge = intval( $newDate->diffInYears() );
                                            @endphp
                                                {{ $newAge }}
                                            @else
                                                {{ $result->{$field} }}
                                            @endif
                                        </td>
                                    @endif
                                @endforeach
                            </tr>
                            @endforeach
                        @else
                            @foreach($results as $result)
                            <tr>
                                @foreach($configurations as $field => $label)
                                <td @if(!in_array($loop->index, $configs))style="display: none;"@endif>{{ $result->{$field} }}</td>
                                @endforeach
                            </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
                @if($results)
                {{ $results->links() }}
                @endif
        </div>

        @include('reports.chart')

    </section>
    <!-- /.content -->

    <div class="modal fade" tabindex="-1" role="dialog" id="modal-filter">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Filter</h4>
                </div>
                <div class="modal-body" style="padding-bottom: 1px;">
                {{-- <form action="{{ admin_route_url('reports') }}" method="POST">
                 {{ csrf_field() }}
                 {{ method_field('POST') }} --}}
                    <div class="row" style="margin-bottom: 20px;">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date_range" class="control-label">Select Data Range:</label>
                                <!-- <input type="text" class="form-control datepicker" id="date_range"> -->
                                <div class="input-group">
                                    <div class="input-group-addon"> <i class="fa fa-calendar-o"></i> </div>
                                    <input type="text" name="date_create" class="form-control datepicker" id="date_range">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="submit-button" class="control-label">&nbsp;</label>
                                <button id="submit-button" type="submit" class="btn btn-block btn-info"><i class="fa fa-btn fa-filter"></i> Get Filter Data</button>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-bottom: 20px;">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="type" class="control-label">Chart Type</label>
                                <select class="form-control" id="chart-type" name="chart_type" single style="width: 100%;">
                                    <option value="">Select Chart Type</option>
                                    <option value="line">Line</option>
                                    <option value="bar">Bar</option>
                                    <option value="horizontalBar">Horizontal Bar</option>
                                    <option value="radar">Radar</option>
                                    <option value="pie">Pie</option>
                                    <option value="doughnut">Doughnut</option>
                                    <option value="polarArea">Polar Area</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="chart" class="control-label">Chart X:</label>
                                    <select name="chart_x" id="chart-x" class="form-control" single style="width: 100%;">
                                        <option value="">Select Chart X</option>
                                        @foreach($configurations as $field => $label)
                                            <option value="{{ $field }}">{{ $label['label'] }}</option>
                                        @endforeach
                                    </select>
                            </div>
{{--                             <div class="form-group">
                                <label for="chart" class="control-label">Chart Y:</label>
                                    <select name="chart_y" id="chart-y" class="form-control" single style="width: 100%;">
                                        <option value="">Select Chart Y</option>
                                        @foreach($configurations as $field => $label)
                                            <option value="{{ $field }}">{{ $label['label'] }}</option>
                                        @endforeach
                                    </select>
                            </div> --}}
                            <div class="form-group">
                                <label for="dataset" class="control-label">Dataset:</label>
                                    <select name="dataset" id="dataset" class="form-control" single style="width: 100%;">
                                        <option value="">Select Dataset</option>
                                        @foreach($configurations as $field => $label)
                                            <option value="{{ $field }}">{{ $label['label'] }}</option>
                                        @endforeach
                                    </select>
                            </div>
                        </div>
                    </div>
                    <!-- <hr> -->
                    <!-- <div style="overflow-y: scroll; max-height: 350px;"> -->
                    <div class="checkbox-list">
                        @foreach($configurations as $field => $label)
                        @if ($configs == null)
                        <div class="row bg-soft">
                            <div class="col-md-6 d4-border-top" style="min-height: 45px; padding-top: 15px;">
                                <div class="checkbox">
                                    <label><input name="config[{{ $loop->index }}]" checked type="checkbox" class="column-list" checkboxIndex="{{ $loop->index }}">{{ $label['label'] }}</label>
                                </div>
                            </div>

                            <div class="col-md-6 d4-border-top" style="padding-top: 15px;">
                                <div class="form-group checkbox-input-{{ $loop->index }}">
                        @else
                        <div class="row @if(in_array($loop->index, $configs)) bg-soft @endif">
                            <div class="col-md-6 d4-border-top" style="min-height: 45px; padding-top: 15px;">
                                <div class="checkbox">
                                    <label><input name="config[{{ $loop->index }}]" @if(in_array($loop->index, $configs)) checked @endif type="checkbox" class="column-list" checkboxIndex="{{ $loop->index }}">{{ $label['label'] }}</label>
                                </div>
                            </div>

                            <div class="col-md-6 d4-border-top" style="padding-top: 15px;">
                                <div class="form-group checkbox-input-{{ $loop->index }}" @if(!in_array($loop->index, $configs))style="display: none;"@endif>
                        @endif
                                    @php
                                        $elementType = $label['type'];
                                        $options = [
                                            'class' => sprintf("%s %s", 'input-sm form-control', $elementType)
                                        ];
                                        $value = [];
                                        if('range' === $elementType) {
                                            $options['data-min'] = $label['data']['min'];
                                            $options['data-max'] = $label['data']['max'];

                                            if($field == 'age') {
                                                $options['data-min'] = $age->first;
                                                $options['data-max'] = $age->last;
                                            } else if ($field == 'person_in_house') {
                                                $options['data-min'] = $pih->first;
                                                $options['data-max'] = $pih->last;
                                            } else if ($field == 'quantity') {
                                                $options['data-min'] = $qty->first;
                                                $options['data-max'] = $qty->last;
                                            } else if ($field == 'total_price_quantity') {
                                                $options['data-min'] = $total->first;
                                                $options['data-max'] = $total->last;
                                            } else if ($field == 'grand_total_price') {
                                                $options['data-min'] = $grandTotal->first;
                                                $options['data-max'] = $grandTotal->last;
                                            }
                                        }

                                        if('multiple' === $elementType) {
                                            $options['style'] = 'width: 100%;';
                                            $options['multiple'] = 'multiple';
                                        }

                                        if('single' === $elementType) {
                                            $options['style'] = 'width: 100%;';
                                        }

                                        if('dateRange' === $elementType) {
                                            $options['date-min'] = $label['data']['min'];
                                            $options['date-max'] = $label['data']['max'];
                                            $options['date-format'] = $label['data']['format'];
                                            $elementType = 'input';
                                        }

                                        if($field == 'user_id') {
                                            $val = [];
                                             foreach($members as $member)
                                             {
                                                $val[] = $member->id;
                                             }
                                            $value =  $val;
                                        }

                                        if($field == 'gender') {
                                            $val = [];
                                             foreach($gender as $gen)
                                             {
                                                $val[] = $gen->gender;
                                             }
                                            $value =  $val;
                                        }

                                        if($field == 'occupation') {
                                            $val = [];
                                             foreach($oc as $o)
                                             {
                                                $val[] = $o->occupation;
                                             }
                                            $first = ['Select Occupation'];
                                            $value =  array_merge($first, $val);
                                        }

                                        if($field == 'last_education') {
                                            $val = [];
                                             foreach($le as $l)
                                             {
                                                $val[] = $l->last_education;
                                             }
                                            $value =  $val;
                                        }

                                        if($field == 'users_city') {
                                            $val = [];
                                             foreach($citys as $city)
                                             {
                                                $val[] = $city->city;
                                             }
                                            $value =  $val;
                                        }

                                        if($field == 'outlet_type') {
                                            $val = [];
                                             foreach($outletType as $ot)
                                             {
                                                $val[] = $ot->outlet_type;
                                             }
                                            $first = ['Select Outlet Type'];
                                            $value =  array_merge($first, $val);
                                        }

                                        if($field == 'outlet_name') {
                                            $val = [];
                                             foreach($outletName as $om)
                                             {
                                                $val[] = $om->outlet_name;
                                             }
                                            $value =  $val;
                                        }

                                        if($field == 'outlet_province') {
                                            $val = [];
                                             foreach($outletProvince as $op)
                                             {
                                                $val[] = $op->outlet_province;
                                             }
                                            $first = ['Select Outlet Province'];
                                            $value =  array_merge($first, $val);
                                        }

                                        if($field == 'outlet_city') {
                                            $val = [];
                                             foreach($outletCity as $oc)
                                             {
                                                $val[] = $oc->outlet_city;
                                             }
                                            $first = ['Select Outlet City'];
                                            $value =  array_merge($first, $val);
                                        }

                                        if($field == 'products') {
                                            $val = [];
                                             foreach($products as $product)
                                             {
                                                $val[] = $product->name;
                                             }
                                            $value =  $val;
                                        }

                                        if($field == 'brand') {
                                            $val = [];
                                             foreach($brands as $brand)
                                             {
                                                $val[] = $brand->brands;
                                             }
                                            $first = ['Select Brand'];
                                            $value =  array_merge($first, $val);
                                        }

                                        if($field == 'province') {
                                            $val = [];
                                             foreach($provinces as $province)
                                             {
                                                $val[] = $province->name;
                                             }
                                            $value =  $val;
                                        }

                                        if($field == 'sec') {
                                            $val = [];
                                             foreach($sec as $socio)
                                             {
                                                $val[] = $socio->monthly_expense_code;
                                             }
                                            $first = ['Select Sec'];
                                            $value =  array_merge($first, $val);
                                        }

                                    @endphp
                                    {!! \RebelField::type($elementType, $field, $value, [], $options) !!}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <input type="hidden" name="create_report" value="create">
                <!-- </form> -->
                </div>
                <div class="modal-footer">
                    <a href="#" class="btn-link" data-dismiss="modal">Close</a>
                    <!-- <button type="button" class="btn btn-primary">Submit</button> -->
                </div>
            </div>
        </div>
    </div>

@endsection

@section('footer_scripts')
<link rel="stylesheet" href="{{ elixirCDN('css/report-vendor.css') }}" />
<script src="{{ elixirCDN('js/report-vendor.js') }}"></script>
<script src="{{ elixirCDN('js/Chart.bundle.min.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $.fn.modal.Constructor.prototype.enforceFocus = function() {};
        whenLoaded();
        $('.range').each(function(i, obj) {
            buildRangeSlider($(obj));
        });

        $('.dateRange').each(function(i, obj) {
            buildDateRange($(obj));
        });

        $('.multiple, .single').select2();

        $('.btn-modal').on('click', function(e) {
            e.preventDefault();
            $('#modal-filter').modal('show');
        })

        var start = moment().subtract(29, 'days');
        var end = moment();

        $('.datepicker').daterangepicker({
            startDate: start,
            endDate: end,
            timePicker: false,
            timePicker24Hour: false,
            minDate: -0,
            maxDate: "<?php echo \Carbon\Carbon::today()->toDateString(); ?>",
            locale: {
                format: 'YYYY-MM-DD'
            }
        });

        $('.column-list').on('change', function() {
            var checkboxIndex = $(this).attr('checkboxIndex');
            var checkboxInput = $('.checkbox-input-' + checkboxIndex);

            if($(this).is(':checked')) {
                showHideColumn('reportTable', checkboxIndex, true);
                checkboxInput.show();
                $(this).parents('.row').addClass('bg-soft');
            } else {
                showHideColumn('reportTable', checkboxIndex, false);
                checkboxInput.hide();
                $(this).parents('.row').removeClass('bg-soft');
            }
        });

        $("#range-month").ionRangeSlider({
            type: 'double',
            min: 1,
            max: 12,
        });

        $('#dataset').select2();
        $('#chart-type').select2();
        $('#chart-x').select2();
        $('#chart-y').select2();

        $('#submit-button').on('click', function () {

            var createReport = $('input[name=create_report]').val();
            var dateCreate = $('input[name=date_create]').val();
            var memberCode = $('select[name=user_id]').val();
            var age = $('input[name=age]').val();
            var province = $('select[name=province]').val();
            var gender = $('select[name=gender]').val();
            var occupation = $('select[name=occupation]').val();
            var pih = $('input[name=person_in_house]').val();
            var le = $('select[name=last_education]').val();
            var userCity = $('select[name=users_city]').val();
            var sec = $('select[name=sec]').val();
            // var usership = $('select[name=usership]').val();
            var receiptNumber = $('input[name=receipt_number]').val();
            var outletType = $('select[name=outlet_type]').val();
            var outletName = $('select[name=outlet_name]').val();
            var outletProvince = $('select[name=outlet_province]').val();
            var outletCity = $('select[name=outlet_city]').val();
            var outletAddress = $('input[name=outlet_address]').val();
            var products = $('select[name=products]').val();
            var brand = $('select[name=brand]').val();
            var quantity = $('input[name=quantity]').val();
            var tpq = $('input[name=total_price_quantity]').val();
            var gtp = $('input[name=grand_total_price]').val();
            var purchaseDate = $('input[name=purchase_date]').val();
            var sentTime = $('input[name=sent_time]').val();

            var chartType = $('select[name=chart_type]').val();
            var chartX = $('select[name=chart_x]').val();
            var dataset = $('select[name=dataset]').val();

            createReport = (createReport == null) ? '' : 'create_report='+createReport;
            dateCreate = (dateCreate == null) ? '' : '&date_create='+dateCreate;
            chartType = (chartType == null) ? '' : '&chart_type='+chartType;
            chartX = (chartX == null) ? '' : '&chart_x='+chartX;
            dataset = (dataset == null) ? '' : '&dataset='+dataset;
            memberCode = (memberCode == null) ? '' : '&user_id='+memberCode;
            province = (province == null) ? '' : '&province='+province;
            gender = (gender == null) ? '' : '&gender='+gender;
            occupation = (occupation == null) ? '' : '&occupation='+occupation;
            age = (age == null) ? '' : '&age='+age;
            products = (products == null) ? '' : '&products='+products;
            le = (le == null) ? '' : '&last_education='+le;
            pih = (pih == null) ? '' : '&person_in_house='+pih;
            userCity = (userCity == null) ? '' : '&users_city='+userCity;
            sec = (sec == 0) ? '' : '&sec='+sec;
            // usership = (usership == 0) ? '' : '&usership='+usership;
            receiptNumber = (receiptNumber == null) ? '' : '&receipt_number='+receiptNumber;
            outletType = (outletType == null) ? '' : '&outlet_type='+outletType;
            outletName = (outletName == null) ? '' : '&outlet_name='+outletName;
            outletProvince = (outletProvince == null) ? '' : '&outlet_province='+outletProvince;
            outletCity = (outletCity == null) ? '' : '&outlet_city='+outletCity;
            outletAddress = (outletAddress == null) ? '' : '&outlet_address='+outletAddress;
            brand = (brand == null) ? '' : '&brand='+brand;
            quantity = (quantity == null) ? '' : '&quantity='+quantity;
            tpq = (tpq == null) ? '' : '&total_price_quantity='+tpq;
            gtp = (gtp == null) ? '' : '&grand_total_price='+gtp;
            purchaseDate = (purchaseDate == null) ? '' : '&purchase_date='+purchaseDate;
            sentTime = (sentTime == null) ? '' : '&sent_time='+sentTime;

            window.location.href = '/reports?'+createReport+dateCreate+chartType+chartX+dataset+memberCode+province+gender+occupation+age+products+le+pih+userCity+sec+receiptNumber+outletType+outletName+outletProvince+outletCity+outletAddress+brand+quantity+tpq+gtp+purchaseDate+sentTime+'&tail=0';
        });

    });

    function whenLoaded() {
        //console.log(Cookies.get('reports'));
        var $cookies = Cookies.get('reportFilters');

        if(!$cookies) {
            Cookies.set('reportFilters', 'gagah berani bersemi sepanjang hari!', { path: '/reports' });
            return;
        } else {
            console.log($cookies);
        }
    }

    function buildRangeSlider(selector) {
        //var selector = $(".range"),
        var $min = selector.attr('data-min'),
            $max = selector.attr('data-max'),
            slider;

        var create = function () {
            selector.ionRangeSlider({
                type: "double",
                hide_min_max: true,
                keyboard: true,
                min: $min,
                max: $max,
                //from: 1000,
                grid: true
            });

            slider = selector.data("ionRangeSlider");
        };

        return create();
    }

    function buildDateRange(selector) {
        var $min = selector.attr('date-min'),
            $max = selector.attr('date-max'),
            $format = selector.attr('date-format');

        $(selector).daterangepicker({
            startDate: moment().subtract(29, 'days'),
            endDate: moment(),
            timePicker: false,
            drops: "up",
            timePicker24Hour: false,
            showDropdowns: true,
            autoApply: true,
            alwaysShowCalendars: true,
            minDate: $min,
            maxDate: $max,
            cancelClass: "btn-danger",
            locale: {
                format: 'YYYY-MM-DD'
            }
        });
    }

    (function() {
        var ctx = document.getElementById("myChart").getContext("2d");
        ctx.canvas.height = 150;
        var data = {!! json_encode($charts) !!};
        var type = '{!! $chartType !!}';
        if (type == 'line' || type == 'bar' || type == 'radar' || type == 'horizontalBar') {
            var options = {
                scales:{
                    yAxes:[{
                        ticks:{
                            beginAtZero:true,
                        },
                        gridLines:{
                            display:false
                        }
                    }]
                },
                title:{
                    display:true,
                    text:type.toUpperCase()+' CHART'
                }
            };
        } else if (type == 'pie' || type == 'doughnut' || type == 'polarArea') {
            if (type == 'pie' || type == 'doughnut') {
                var options = {
                    animation: {
                        duration: 500,
                        easing: "easeOutQuart",
                        onComplete: function () {
                          var ctx = this.chart.ctx;
                          ctx.font = Chart.helpers.fontString(Chart.defaults.global.defaultFontFamily, 'normal', Chart.defaults.global.defaultFontFamily);
                          ctx.textAlign = 'center';
                          ctx.textBaseline = 'bottom';

                          this.data.datasets.forEach(function (dataset) {

                            for (var i = 0; i < dataset.data.length; i++) {
                              var model = dataset._meta[Object.keys(dataset._meta)[0]].data[i]._model,
                                  total = dataset._meta[Object.keys(dataset._meta)[0]].total,
                                  mid_radius = model.innerRadius + (model.outerRadius - model.innerRadius)/2,
                                  start_angle = model.startAngle,
                                  end_angle = model.endAngle,
                                  mid_angle = start_angle + (end_angle - start_angle)/2;

                              var x = mid_radius * Math.cos(mid_angle);
                              var y = mid_radius * Math.sin(mid_angle);

                              ctx.fillStyle = '#fff';
                              if (i == 3){ // Darker text color for lighter background
                                ctx.fillStyle = '#444';
                              }
                              var percent = String(Math.round(dataset.data[i]/total*100)) + "%";
                              ctx.fillText(dataset.data[i], model.x + x, model.y + y);
                              // Display percent in another line, line break doesn't work for fillText
                              ctx.fillText(percent, model.x + x, model.y + y + 15);
                            }
                            });
                        }
                    },
                    title : {
                        display : true,
                        text : type.toUpperCase()+' CHART'
                    },
                };
            } else {
                var options = {
                    animation : {
                        animateScale : true
                    },
                    title : {
                        display : true,
                        text : type.toUpperCase()+' CHART'
                    },
                };
            }
        }

        var chartInstance = new Chart(ctx, {
            type: type,
            data: data,
            options: options,
        });
    })();

    $('#download').on('click', function(){
        var canvas = document.getElementById("myChart");
        var img    = canvas.toDataURL("image/png");
        var link = $('#link-img');

        link.attr('href', img);
    });

    $('#get-excel').on('click', function() {
        var _link = window.location.href;
        var _linkArray = _link.split("?");
        var _isiLink = _linkArray[1].split("&");
        var data = {};
        $.each( _isiLink, function( key, value ) {
          var _field = value.split("=");
          data[_field[0]] = _field[1];
        });
        var type_request = 'new';
        var page = 1;
        var filename = null;

        requestCsv(data, type_request, page, filename);

        $('.progress').show();
    });

    function requestCsv(data, type_request, page, filename, no = null) {
        $.get( "{{ admin_route_url('reports.export') }}", {
            data : data,
            type_request : type_request,
            filename : filename,
            page : page,
            no : no,
        }).success(function( response ) {
            var type_request = response.message.type_request;
            if (type_request == 'next') {
                var type_request = response.message.type_request;
                var filename = response.message.filename;
                var page = response.message.page;
                var no = response.message.no;
                var last = response.message.last;
                requestCsv(data, type_request, page, filename, no);

                var progress = Math.round(page/last*100);
                $('.progress-bar').css("width", function(){
                    return progress+'%';
                });
                $('#persent').text(progress+'% Please wait...');
            } else {
                $('.progress-bar').css("width", function(){
                    return 100+'%';
                });
                $('#persent').text('100% Please wait...');
                setTimeout(function(){
                    $('.progress').hide();
                }, 3000);
                window.location.href = 'download/snaps/?download='+response.message.filename;
            }
        });
    }
</script>
@stop