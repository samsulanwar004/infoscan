@extends('app')

@section('content')
    @include('partials.content_header', [
        'pageTitle' => 'Snaps',
        'pageDescription' => 'List of all Snaps',
        'breadcrumbs' => ['Snaps' => false]
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
                        <input type="text" class="form-control datepicker" name="filter_date" id="filter-date">                  
                    </div>
                </div>
                <div class="box-tools pull-right">                 

                    <select class="form-control filter-status">
                        <option value="all">Pilih Status</option>
                        <option value="approve">Approve</option>
                        <option value="reject">Reject</option>
                        <option value="pending">Pending</option>
                    </select>

                    <select class="form-control snap-type">
                        <option value="all">Pilih Snap type</option>
                        @foreach($snapCategorys as $id => $name)
                            <option value="{{$id}}" id="{{$id}}">{{$name}}</option>
                        @endforeach
                    </select>

                    <select class="form-control snap-mode">
                        <option value="all">Pilih Mode type</option>
                        @foreach($snapCategoryModes as $id => $name)
                            <option value="{{$id}}" id="{{$id}}">{{$name}}</option>
                        @endforeach
                    </select>

                    <div class="input-group">
                      <input type="text" id="search-box" class="form-control" placeholder="Search...">
                          <span class="input-group-btn">
                            <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                            </button>
                          </span>
                    </div>

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
    $(".filter-status").on("change", function() {
        var status = $(this).val();
        window.location.href = '/snaps?status='+status;
    });

    $(".snap-type").on("change", function() {

        var type = $(this).val();
        var mode = $(".snap-mode").val();
        window.location.href = '/snaps?type='+type;
    });

    $(".snap-mode").on("change", function() {

        var mode = $(this).val();
        var type = $(".snap-type").val();
        window.location.href = '/snaps?mode='+mode;
    });

    $("#search-btn").on('click', function() {
        var search = $('#search-box').val();
        console.log(search);
    });

    var start = moment().subtract(29, 'days');
    var end = moment();

    function submit(start, end) {
        var start_at = start.format('YYYY-MM-DD');
        var end_at = end.format('YYYY-MM-DD');

        window.location.href = '/snaps?date_start='+start_at+'&date_end='+end_at;
    }

    $('.datepicker').daterangepicker({
        startDate: start,
        endDate: end,
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