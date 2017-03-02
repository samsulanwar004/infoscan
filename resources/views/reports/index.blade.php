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
                                @foreach($configurations as $field => $label)
                                <td style="min-width: 100px;" class="{{ $field }}">{{ $label['label'] }}</td>
                                @endforeach
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($results as $result)
                            <tr>
                                @foreach($configurations as $field => $label)
                                <td>{{ $result->{$field} }}</td>
                                @endforeach
                            </tr>
                            @endforeach
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
                    <div class="row" style="margin-bottom: 20px;">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="date_range" class="control-label">Select Data Range:</label>
                                <!-- <input type="text" class="form-control datepicker" id="date_range"> -->
                                <div class="input-group">
                                    <div class="input-group-addon"> <i class="fa fa-calendar-o"></i> </div>
                                    <input type="text" class="form-control datepicker" id="date_range">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="submit-button" class="control-label">&nbsp;</label>
                                <button id="submit-button" type="button" class="btn btn-block btn-info"><i class="fa fa-btn fa-filter"></i> Get Filter Data</button>
                            </div>
                        </div>
                    </div>
                    <!-- <hr> -->
                    <!-- <div style="overflow-y: scroll; max-height: 350px;"> -->
                    <div class="checkbox-list">
                        @foreach($configurations as $field => $label)
                        <div class="row bg-soft">
                            <div class="col-md-6 d4-border-top" style="min-height: 45px; padding-top: 15px;">
                                <div class="checkbox">
                                    <label><input checked type="checkbox" class="column-list" checkboxIndex="{{ $loop->index }}">{{ $label['label'] }}</label>
                                </div>
                            </div>

                            <div class="col-md-6 d4-border-top" style="padding-top: 15px;">
                                <div class="form-group checkbox-input-{{ $loop->index }}">
                                    @php
                                        $elementType = $label['type'];
                                        $options = [
                                            'class' => sprintf("%s %s", 'input-sm form-control', $elementType)
                                        ];
                                        if('range' === $elementType) {
                                            $options['data-min'] = $label['data']['min'];
                                            $options['data-max'] = $label['data']['max'];
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
                                    @endphp
                                    {!! \RebelField::type($elementType, $field, [], [], $options) !!}
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

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

        $('.datepicker').daterangepicker({
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

        //Colorpicker
        $('#background-color').colorpicker();
        $('#border-color').colorpicker();

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