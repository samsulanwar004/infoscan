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
            <div class="col-md-12">
              <!-- Custom Tabs -->
              <div class="nav-tabs-custom">
                <ul class="nav nav-tabs">
                  <li class="active">
                    <a href="#tab_1" data-toggle="tab" aria-expanded="true">Active Users</a>
                  </li>
                  <li class="">
                    <a href="#tab_2" data-toggle="tab" aria-expanded="false">Snaps Status</a></li>
                  <li><a href="#tab_3" data-toggle="tab">Rejection Reason</a></li>
                  <li><a href="#tab_4" data-toggle="tab">Top 10's</a></li>

                </ul>
                <div class="tab-content">
                  <div class="tab-pane active" id="tab_1">
                    <report-chart
                        chart-title="Active Users"
                        resource-url="{{ route('chart.active-users') }}"
                        legends='["New Users", "Snaps", "Receipts", "General Trade", "Hand Written"]'
                        {{-- export-url="{{ route('chart.active-users.export', ['timeRage' => 'daily', 'export' => 'xls']) }}" --}}
                    ></report-chart>
                  </div>
                  <!-- /.tab-pane -->
                  <div class="tab-pane" id="tab_2">
                    <report-chart
                        chart-title="Snaps Status"
                        resource-url="{{ route('chart.snaps-status') }}"
                        legends='["Approve", "Rejects", "Pending"]'
                        {{-- export-url="{{ route('chart.active-users.export', ['timeRage' => 'daily', 'export' => 'xls']) }}" --}}
                    ></report-chart>
                  </div>
                  <!-- /.tab-pane -->
                  <div class="tab-pane" id="tab_3">
                    <report-chart
                        chart-title="Rejection Reason"
                        resource-url="{{ route('chart.snaps-rejection-reason') }}"
                        legends="{{ json_encode($reasons) }}"
                        {{-- export-url="{{ route('chart.active-users.export', ['timeRage' => 'daily', 'export' => 'xls']) }}" --}}
                    ></report-chart>
                  </div>
                  <!-- /.tab-pane -->
                  <div class="tab-pane" id="tab_4">
                        <report-table resource-url="{{ route('chart.top-ten') }}"></report-table>
                  </div>
                </div>
                <!-- /.tab-content -->
              </div>
              <!-- nav-tabs-custom -->
            </div>
            <!-- /.col -->

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
