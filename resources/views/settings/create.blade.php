@extends('app')

@section('content')
    @include('partials.content_header', ['pageTitle' => 'Settings', 'pageDescription' => 'Create a new settings', 'breadcrumbs' => ['Settings' => '/settings', 'Create' => false]])

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">
                </h3>

                <div class="box-tools pull-right">
                    <a href="/settings" class="btn btn-box-tool" data-toggle="tooltip" title="Back"> <i
                            class="fa fa-times"></i></a>
                </div>
            </div>
            <div class="box-body">
                <form role="form" action="{{ route('settings.store') }}" method="POST" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="box-body">
                        <div class="form-group">
                            <label for="setting_name">Setting Name</label>
                            <select name="setting_name" class="form-control">
                                <option>Snap Reason</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="setting_type">Setting Type</label>
                            <select name="setting_type" class="form-control">
                                <option>Toggle</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="setting_value">Setting Value</label>
                            <input type="text" name="setting_value" class="form-control">
                        </div>

                        <div class="checkbox">
                            <label>
                                <input name="is_visible" checked="checked" type="checkbox">
                                Is Visible ?
                            </label>
                        </div>

                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save fa-btn"></i> Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <!-- /.box -->

    </section>
    <!-- /.content -->
@endsection
