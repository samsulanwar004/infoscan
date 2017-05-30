@extends('app')

@section('content')
    @include('partials.content_header', [
        'pageTitle' => 'Exchange Rate',
        'pageDescription' => 'Edit City rate',
        'breadcrumbs' => [
            'Exchange rates' => admin_route_url('exchange.index'),
            'Edit' => false]
        ]
    )

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <!-- <a href="/users" class="btn btn-link btn-sm">
                        <i class="fa fa-plus fa-arrow-circle-left"></i> back to user list</a> -->
                </h3>

                <div class="box-tools pull-right">
                    <a href="{{ admin_route_url('exchange.index') }}" class="btn btn-box-tool"
                       data-toggle="tooltip" title="Back">
                        <i class="fa fa-times"></i></a>
                </div>
            </div>
            <div class="box-body" id="form-body">
                <form role="form" action="{{ admin_route_url('city.rate.update', ['id' => $rate->id]) }}"
                      method="POST" accept-charset="utf-8">
                    {{ csrf_field() }}
                    {{ method_field('PUT') }}
                    <div class="box-body" id="form-body">
                        <div class="form-group has-feedback">
                            <label for="city">City</label>
                            <select class="form-control" name="city_name" id="select-city">
                                @foreach($provincies as $province)
                                    <optgroup label="{{ $province->name }}">
                                        @foreach($province->regencies as $regency)
                                            <option value="{{ $regency->name }}" @if($rate->city_name == $regency->name) selected @endif>{{ $regency->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group has-feedback">
                            <label for="cash">Cash</label>
                            <input type="number" class="form-control" name="cash" step="0.01" id="cash"
                                   value="{{ old('cash', $rate->cash_per_unit) }}"
                                   placeholder="Enter cash" required>
                        </div>
                        <div class="checkbox">
                            <label>
                                <input name="is_active" checked="checked" type="checkbox">
                                Is Active ?
                            </label>
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
<link rel="stylesheet" href="{{ elixirCDN('css/report-vendor.css') }}" />
<script src="{{ elixirCDN('js/report-vendor.js') }}"></script>
<script type="text/javascript">
    $("#select-city").select2({
        placeholder: "Select a City",
    });
</script>
@endsection
