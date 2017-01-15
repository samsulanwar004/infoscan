@extends('app', ['mini_sidebar' => true])

@section('content')
    @include('partials.content_header', ['pageTitle' => 'Reports', 'pageDescription' => '', 'breadcrumbs' => ['Reports' => false]])

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border form-inline" style="height: 45px;">

                <div class="box-tools pull-right ">
                    <!-- Filter Snap type:
                    <select class="form-control snap-type">
                        <option value="all">All</option>
                    </select>

                    Filter Mode type:
                    <select class="form-control snap-mode">
                        <option value="all">All</option>
                    </select> -->
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
                                @foreach(config('common.reports.fields') as $field => $label)
                                <td style="min-width: 100px;" class="{{ $field }}">{{ $label }}</td>
                                @endforeach
                            </tr>
                        </thead>

                        <tbody>
                            @foreach($results as $result)
                            <tr>
                                @foreach(config('common.reports.fields') as $field => $label)
                                <td>{{ $result->{$field} }}</td>
                                @endforeach
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- /.box -->

    </section>
    <!-- /.content -->

    <div class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Filter</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
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
                                <button id="submit-button" type="button" class="btn btn-block btn-success"><i class="fa fa-btn fa-filter"></i> Get Filter Data</button>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div style="overflow-y: scroll; max-height: 250px;">
                        @foreach(config('common.reports.fields') as $field => $label)
                        <div class="row">
                            <div class="col-md-6" style="min-height: 45px;">
                                <div class="checkbox">
                                    <label><input checked type="checkbox" class="column-list" checkboxIndex="{{ $loop->index }}">{{ $label }}</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group checkbox-input-{{ $loop->index }}">
                                    <select class="form-control input-sm">
                                        <option value="0">Select Options</option>
                                    </select>
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
<script src="{{ elixir('js/cookie.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        whenLoaded();

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
            } else {
                showHideColumn('reportTable', checkboxIndex, false);
                checkboxInput.hide();
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
</script>
@stop
