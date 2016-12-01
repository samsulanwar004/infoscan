@extends('app')

@section('content')
    @include('partials.content_header', [
        'pageTitle' => 'Socio Economic Status',
        'pageDescription' => 'Edit SES',
        'breadcrumbs' => [
            'SES' => admin_route_url('ses.index'),
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
                    <a href="{{ admin_route_url('ses.index') }}" class="btn btn-box-tool"
                       data-toggle="tooltip" title="Back">
                        <i class="fa fa-times"></i></a>
                </div>
            </div>
            <div class="box-body" id="form-body">
                <form role="form" action="{{ admin_route_url('ses.update', ['id' => $ses->id]) }}"
                      method="POST" accept-charset="utf-8">
                    {{ csrf_field() }}
                    {{ method_field('PUT') }}
                    <div class="box-body" id="form-body">
                        <div class="form-group has-feedback {{ $errors->has('range_start') ? 'has-error' : false }}">
                            <label for="code">Code</label>
                            <input type="text" class="form-control" name="code" id="code"
                                   placeholder="Enter code" value="{{ old('code', $ses->code) }}" required>
                        </div>
                        <div class="form-group has-feedback {{ $errors->has('range_start') ? 'has-error' : false }}">
                            <label for="range_start">Start Range</label>
                            <input type="number" class="form-control" name="range_start" id="range_start"
                                   value="{{ old('range_start', $ses->range_start) }}"
                                   placeholder="Enter start range" required>
                        </div>
                        <div class="form-group has-feedback {{ $errors->has('range_end') ? 'has-error' : false }}">
                            <label for="range_end">End range:</label>
                            <input type="number" class="form-control" name="range_end" id="range_end"
                                   value="{{ old('range_end', $ses->range_end) }}"
                                   placeholder="Enter end range" required>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer text-right">
                        <button type="submit" class="btn btn-primary" id="submit">
                            <i class="fa fa-save fa-btn"></i> Save SES
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
