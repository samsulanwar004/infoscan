@extends('app')

@section('content')
    @include('partials.content_header', [
        'pageTitle' => 'Snaps Assigned',
        'pageDescription' => 'List of all Snaps assigned',
        'breadcrumbs' => ['Crowdsource account' => admin_route_url('crowdsource.index'), 'Snaps assigned' => false]
    ])

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
            <div class="box-header with-border form-inline" style="overflow: hidden; height: 45px;">
                <div class="box-tools-new">
                    <div class="input-group date">
                        <div class="input-group-addon">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control datepicker" name="filter_date" id="filter-date"
                            @if(isset($date)) value="{{$date}}" @endif
                        >
                    </div>
                </div>
                <div class="box-tools pull-right">

                    Total : {{ $snaps->total() }}

                    <select class="form-control filter-status">
                        <option value="all" @if($status == 'all') selected @endif>Select Status</option>
                        <option value="approve" @if($status == 'approve') selected @endif>Approved</option>
                        <option value="reject" @if($status == 'reject') selected @endif>Rejected</option>
                        <option value="pending" @if($status == 'pending') selected @endif>Pending</option>
                    </select>

                    <select class="form-control snap-type">
                        <option value="all">Select Snap type</option>
                        @foreach($snapCategorys as $id => $name)
                            <option value="{{$id}}" id="{{$id}}" @if($type == $id) selected @endif>{{$name}}</option>
                        @endforeach
                    </select>

                    <select class="form-control snap-mode">
                        <option value="all">Select Mode type</option>
                        @foreach($snapCategoryModes as $id => $name)
                            <option value="{{$id}}" id="{{$id}}" @if($mode == $id) selected @endif>{{$name}}</option>
                        @endforeach
                    </select>

                    <button class="btn btn-primary" id="show">Show</button>

                </div>
            </div>
            <div class="box-body">
                @include('snaps.table_snaps')
            </div>
        </div>
        <!-- /.box -->

    </section>
    <!-- /.content -->
@endsection

@section('footer_scripts')
<style type="text/css">
    .box-tools-new {
        position: absolute;
        top: 5px;
    }
</style>
<script type="text/javascript">
$(document).ready(function () {

    $('.datepicker').daterangepicker({
        ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    });

    $('#show').on('click', function() {
        var mode = $(".snap-mode").val();
        var type = $(".snap-type").val();
        var status = $(".filter-status").val();
        var date = $(".datepicker").val();
        var dateArr = date.split(' - ');
        var startArr = dateArr[0].split("/");
        var endArr = dateArr[1].split("/");

        var start_at = startArr[2]+"-"+startArr[0]+"-"+startArr[1];
        var end_at = endArr[2]+"-"+endArr[0]+"-"+endArr[1];
        var id = '{{ $userId }}';

        window.location.href = '/crowdsource/'+id+'?date_start='+start_at+'&date_end='+end_at+'&status='+status+'&type='+type+'&mode='+mode;
    });

});

</script>
@stop