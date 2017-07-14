@extends('app')

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Dashboard
            <!-- <small>it all starts here</small> -->
        </h1>
        <!-- <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
            <li><a href="#">Examples</a></li>
            <li class="active">Blank page</li>
            </ol> -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="row">
            <div class="col-md-6">
                <report-chart
                    chart-title="Active Users"
                    resource-url="{{ route('chart.active-users') }}"
                    legends="New Users, Snaps, Receipts, General Trade, Hand Written"
                ></report-chart>
            </div>
            <div class="col-md-6">
                <report-chart
                    chart-title="Snaps Status"
                    resource-url="{{ route('chart.snaps-status') }}"
                    legends="Approve, Rejects, Pending"
                ></report-chart>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <report-chart
                    chart-title="Rejection Reason"
                    resource-url="{{ route('chart.snaps-rejection-reason') }}"
                    legends="{{ implode($reasons, ', ') }}"
                ></report-chart>
            </div>
            <div class="col-md-6">
                <report-table resource-url="{{ route('chart.top-ten') }}"></report-table>
            </div>
        </div>

    </section>
    <!-- /.content -->
@endsection
@section('header_scripts')
    <script src="{{ elixirCDN('js/app.js') }}"></script>
@endsection

@section('footer_scripts')
    <script src="{{ elixirCDN('js/Chart.bundle.min.js') }}"></script>
    <script src="{{ elixirCDN('js/dashboard.js') }}"></script>
@endsection
