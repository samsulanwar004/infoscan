@extends('app')

@section('content')
    @include('partials.content_header', [
        'pageTitle' => 'Crowdsource Account',
        'pageDescription' => 'Detail of Crowdsource account',
        'breadcrumbs' => ['Crowdsource account' => admin_route_url('crowdsource.index'), 'Crowdsource activity' => false]
    ])

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border form-inline" style="overflow: hidden; height: 45px;">
                <div class="box-tools-new">
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <meta name="csrf-token" content="{{ csrf_token() }}">
                        <input type="text" class="form-control datepicker" name="filter_date" id="filter-date" @if(isset($date)) value="{{$date}}" @endif>                  
                    </div>
                    <span id="total">Total Action : {{ $data['totalApprove'] + $data['totalReject']}} Total Add : {{ $data['totalAddTag'] }} Total Edit : {{ $data['totalEditTag'] }}</span>
                </div>
                <div class="box-tools pull-right">
                <span id="total-assign">Total Assign : {{ $assign }}</span>
                    <a href="/crowdsource" class="btn btn-box-tool" data-toggle="tooltip" title="Back"> <i
                            class="fa fa-times"></i></a>
                </div>
            </div>
            <div class="box-body table-activity">
                @include('crowdsource.table_activity')
            </div>
        </div>
        <!-- /.box -->
    <!-- /.content -->
@endsection

@section('footer_scripts')
<style type="text/css">
    .box-tools-new {
        position: absolute;
        top: 5px;
    }
</style>
<script>
    $(document).ready(function () {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        })

        function submit(start, end) {
            var id = '{{ $id }}';
            var start_at = start.format('YYYY-MM-DD');
            var end_at = end.format('YYYY-MM-DD');

            window.location.href = id+'?start_at='+start_at+'&end_at='+end_at;
        }

        $('.datepicker').daterangepicker({
            ranges: {
               'Today': [moment(), moment()],
               'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
               'Last 7 Days': [moment().subtract(6, 'days'), moment()],
               'Last 30 Days': [moment().subtract(29, 'days'), moment()],
               'This Month': [moment().startOf('month'), moment().endOf('month')],
               'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        }, submit);

    });

</script>

@stop