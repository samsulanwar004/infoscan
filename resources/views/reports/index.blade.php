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

    </section>
    <!-- /.content -->

    <div class="modal fade" tabindex="-1" role="dialog">
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
                                    <?php
                                        $options = [
                                            'class' => sprintf("%s %s", 'input-sm form-control', $label['type'])
                                        ];
                                        if('range' === $label['type']) {
                                            $options['data-min'] = $label['data']['min'];
                                            $options['data-max'] = $label['data']['max'];
                                        }

                                        if('multiple' === $label['type']) {
                                            $options['style'] = 'width: 100%;';
                                            $options['multiple'] = 'multiple';
                                        }

                                        if('single' === $label['type']) {
                                            $options['style'] = 'width: 100%;';
                                        }
                                    ?>
                                    {!! \RebelField::type($label['type'], $field, [], [], $options) !!}
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
@endsection

@section('footer_scripts')
<link rel="stylesheet" href="{{ elixir('css/report-vendor.css') }}" />
<script src="{{ elixir('js/report-vendor.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        whenLoaded();
        $('.range').each(function(i, obj) {
            buildRangeSlider($(obj));
        });

        $('.multiple, .single').select2();

        $('.btn-modal').on('click', function(e) {
            e.preventDefault();
            $('.modal').modal('show');
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
        var selector = $(".range"),
            $min = selector.attr('data-min'),
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
</script>
@stop