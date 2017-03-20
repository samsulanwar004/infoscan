@extends('app', ['mini_sidebar' => true])

@section('content')
    @include('partials.content_header', ['pageTitle' => 'Reports', 'pageDescription' => '', 'breadcrumbs' => ['Reports' => false]])
    <?php $configurations = config('common.reports.fields'); ?>   
    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border form-inline" style="height: 45px;">

                <div class="box-tools pull-right ">
                    <button type="button" class="btn btn-default" data-toggle="modal" data-target="#modal-chart"><i class="fa fa-line-chart"></i> Chart
                            </button>
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
                            <li><a href="#"><i class="fa fa-btn fa-file-pdf-o"></i>PDF</a></li>
                            <li><a href="#"><i class="fa fa-btn fa-file-excel-o"></i>Excel</a></li>
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
                                <td>{{ $result->{$field} }}</td>
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
        </div>  

        <div id="chart"></div>    

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
                <form action="{{ admin_route_url('reports') }}" method="POST">
                 {{ csrf_field() }}
                 {{ method_field('POST') }}
                 <input type="hidden" name="userid" id="user-id">
                 <input type="hidden" name="provinces" id="provinces">
                 <input type="hidden" name="lasteducation" id="last-education">
                 <input type="hidden" name="product" id="product">
                 <input type="hidden" name="userscity" id="users-city">
                 <input type="hidden" name="genders" id="genders">
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
                                                $val[] = $member->user_id;
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
                                                $val[] = $city->users_city;
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
                                            $first = ['Select Outlet Name'];
                                            $value =  array_merge($first, $val);   
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
                                                $val[] = $product->products;
                                             }
                                            $value =  $val;    
                                        }

                                        if($field == 'brand') {
                                            $val = [];
                                             foreach($brands as $brand)
                                             {
                                                $val[] = $brand->brand;
                                             }
                                            $first = ['Select Brand'];
                                            $value =  array_merge($first, $val);   
                                        }

                                        if($field == 'province') {
                                            $val = [];
                                             foreach($provinces as $province)
                                             {
                                                $val[] = $province->province;
                                             }
                                            $value =  $val;    
                                        }

                                        if($field == 'sec') {
                                            $val = [];
                                             foreach($sec as $socio)
                                             {
                                                $val[] = $socio->sec;
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
                </form>
                </div>
                <div class="modal-footer">
                    <a href="#" class="btn-link" data-dismiss="modal">Close</a>
                    <!-- <button type="button" class="btn btn-primary">Submit</button> -->
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" tabindex="-1" role="dialog" id="modal-chart">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Create Chart</h4>
          </div>
          <div class="modal-body">
            <form action="/reports/chart" method="POST" id="submit-chart">
                {{ csrf_field() }}
                {{ method_field('POST') }}
                <div class="form-group">
                    <label for="type">Type Chart</label>
                    <select class="form-control" id="type" name="type">
                        <option value="line">Line</option>
                        <option value="bar">Bar</option>
                        <option value="horizontalBar">Horizontal Bar</option>
                        <option value="radar">Radar</option>
                        <option value="pie">Pie</option>
                        <option value="doughnut">Doughnut</option>
                        <option value="polarArea">Polar Area</option>
                    </select>
                </div>   
                <div class="form-group">
                    <label for="label">Month</label>
                    <input type="text" name="month_labels" id="range-month">
                </div>  
                <div class="form-group">
                    <label for="dataset">Dataset</label>
                    <select name="dataset" id="dataset" class="form-control" multiple="multiple" required="required" style="width: 100%;">
                        <option>Sari Roti</option>
                        <option>Equil</option>
                        <option>Sprite</option>
                        <option>Mizone</option>
                        <option>Aqua</option>
                    </select>
                </div> 
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Save changes</button>
            </form>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
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

        $('#submit-chart').on('submit', function() {
            $.ajax({
              url: this.action,
              type: "post",
              data: {
                '_token':$('input[name=_token]').val(),
                '_method':$('input[name=_method]').val(),  
                'type':$('select[name=type]').val(),                  
                'dataset':$('select[name=dataset]').val(),                  
                'month_labels':$('input[name=month_labels]').val(),                               
            },
                success: function(msg){
                    REBEL.smallNotifTemplate('Success Create Chart', 'body', 'success');
                    $('#modal-chart').modal('hide');
                    $('#chart').html(msg);
                    setTimeout(function () {
                        REBEL.removeAllMessageAlert();                         
                    }, 3000)
                },
                error: function (msg) {
                    REBEL.smallNotifTemplate(msg.message, '.modal-content', 'error');
                    setTimeout(function () {
                        REBEL.removeAllMessageAlert();
                    }, 3000)
                }
            }); 

            return false;
        });

        $("#range-month").ionRangeSlider({
            type: 'double',
            min: 1,
            max: 12,
        });

        $('#dataset').select2();

        $('#submit-button').on('click', function () {

            var dateCreate = $('input[name=date_create]').val();
            var memberCode = $('select[name=user_id]').val();
            var age = $('select[name=age]').val();
            var province = $('select[name=province]').val();
            var gender = $('select[name=gender]').val();
            var occupation = $('select[name=occupation]').val();
            var pih = $('select[name=person_in_house]').val();
            var le = $('select[name=last_education]').val();
            var userCity = $('select[name=users_city]').val();
            var sec = $('select[name=sec]').val();
            var usership = $('select[name=usership]').val();
            var receiptNumber = $('select[name=receipt_number]').val();
            var outletType = $('select[name=outlet_type]').val();
            var outletName = $('select[name=outlet_name]').val();
            var outletProvince = $('select[name=outlet_province]').val();
            var outletCity = $('select[name=outlet_city]').val();
            var outletAddress = $('select[name=outlet_address]').val();
            var products = $('select[name=products]').val();
            var brand = $('select[name=brand]').val();
            var quantity = $('select[name=quantity]').val();
            var tpq = $('select[name=total_price_quantity]').val();
            var gtp = $('select[name=grand_total_price]').val();
            var purchaseDate = $('input[name=purchase_date]').val();
            var sentTime = $('input[name=sent_time]').val();

            dateCreate = (dateCreate == null) ? '' : insertParam('date_create', dateCreate);
            memberCode = (memberCode == null) ? '' : insertParam('user_id', memberCode);
            province = (province == null) ? '' : insertParam('province', province);
            gender = (gender == null) ? '' : insertParam('gender', gender);
            occupation = (occupation == null) ? '' : insertParam('occupation', occupation);
            age = (age == null) ? '' : insertParam('age', age);
            products = (products == null) ? '' : insertParam('products', products);
            le = (le == null) ? '' : insertParam('last_education', le);
            pih = (pih == null) ? '' : insertParam('person_in_house', pih);
            userCity = (userCity == null) ? '' : insertParam('users_city', userCity);
            sec = (sec == 0) ? '' : insertParam('sec', sec);
            usership = (usership == 0) ? '' : insertParam('usership', usership);
            receiptNumber = (receiptNumber == null) ? '' : insertParam('receipt_number', receiptNumber);
            outletType = (outletType == null) ? '' : insertParam('outlet_type', outletType);
            outletName = (outletName == null) ? '' : insertParam('outlet_name', outletName);
            outletProvince = (outletProvince == null) ? '' : insertParam('outlet_province', outletProvince);
            outletCity = (outletCity == null) ? '' : insertParam('outlet_city', outletCity);
            outletAddress = (outletAddress == null) ? '' : insertParam('outlet_address', outletAddress);
            brand = (brand == null) ? '' : insertParam('brand', brand);
            quantity = (quantity == null) ? '' : insertParam('quantity', quantity);
            tpq = (tpq == null) ? '' : insertParam('total_price_quantity', tpq);
            gtp = (gtp == null) ? '' : insertParam('grand_total_price', gtp);
            purchaseDate = (purchaseDate == null) ? '' : insertParam('purchase_date', purchaseDate);
            sentTime = (sentTime == null) ? '' : insertParam('sent_time', sentTime);
            // window.location.href = '/reports?'+dateCreate+memberCode+province+gender+occupation+age+products+le+pih+userCity+sec+usership+receiptNumber+outletType+outletName+outletProvince+outletCity+outletAddress+brand+quantity+tpq+gtp+purchaseDate+sentTime;

        });

        $('select[name=user_id]').on('change', function () {
            $('#user-id').val($('select[name=user_id]').val());
        });

        $('select[name=province]').on('change', function () {
            $('#provinces').val($('select[name=province]').val());
        });

        $('select[name=last_education]').on('change', function () {
            $('#last-education').val($('select[name=last_education]').val());
        });

        $('select[name=products]').on('change', function () {
            $('#product').val($('select[name=products]').val());
        });

        $('select[name=users_city]').on('change', function () {
            $('#users-city').val($('select[name=users_city]').val());
        });

        $('select[name=gender]').on('change', function () {
            $('#genders').val($('select[name=gender]').val());
        });

    });

    function insertParam(key, value)
    {
        key = encodeURI(key); value = encodeURI(value);

        var kvp = document.location.search.substr(1).split('&');

        var i=kvp.length; var x; while(i--) 
        {
            x = kvp[i].split('=');

            if (x[0]==key)
            {
                x[1] = value;
                kvp[i] = x.join('=');
                break;
            }
        }

        if(i<0) {kvp[kvp.length] = [key,value].join('=');}

        //this will reload the page, it's likely better to store this until finished
        return kvp.join('&'); 
    }

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
</script>
@stop