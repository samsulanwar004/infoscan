@extends('app')

@section('content')
    @include('partials.content_header', [
        'pageTitle' => 'Exchange Rates',
        'pageDescription' => 'Create a new Exchange rate',
        'breadcrumbs' => [
            'Exchange rates' => admin_route_url('exchange.index'),
            'Create' => false]
        ]
    )

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">
                </h3>

                <div class="box-tools pull-right">
                    <a href="{{ admin_route_url('exchange.index') }}" class="btn btn-box-tool" data-toggle="tooltip"
                       title="Back"> <i
                                class="fa fa-times"></i></a>
                </div>
            </div>
            <div class="box-body">
                <form role="form" action="{{ admin_route_url('exchange.store') }}" method="POST"
                      enctype="multipart/form-data" class="form" accept-charset="utf-8">
                    {{ csrf_field() }}
                    <div class="box-body" id="form-body">
                        <div class="form-group has-feedback">
                            <label for="cash">Cash</label>
                            <input type="number" class="form-control" step="0.01" name="cash" id="cash"
                                   value="{{ old('cash') }}"
                                   placeholder="Enter cash" required>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer text-right">
                        <button type="submit" class="btn btn-primary" id="submit">
                            <i class="fa fa-save fa-btn"></i> Save Exchange
                        </button>
                    </div>
                </form>
            </div>
            <div id="loading"></div>
        </div>
        <!-- /.box -->

    </section>
    <!-- /.content -->
@endsection

@section('footer_scripts')

@endsection