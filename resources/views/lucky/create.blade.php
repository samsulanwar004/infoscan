@extends('app')

@section('content')
    @include('partials.content_header', ['pageTitle' => 'Lucky Draws', 'pageDescription' => 'Create a new lucky draw', 'breadcrumbs' => ['Lucky draws' => '/lucky', 'Create' => false]])

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">
                </h3>

                <div class="box-tools pull-right">
                    <a href="/lucky" class="btn btn-box-tool" data-toggle="tooltip" title="Back"> <i
                            class="fa fa-times"></i></a>
                </div>
            </div>
            <div class="box-body">
                <form role="form" action="{{ route('lucky.store') }}" method="POST" 
                enctype="multipart/form-data" class="form" accept-charset="utf-8" onsubmit="myLoading()">
                    {{ csrf_field() }}
                    <div class="box-body">
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" class="form-control" id="title" name="title" placeholder="Enter title"
                                   value="{{ old('title') }}" required autofocus>
                        </div>

                        <div class="form-group">
                            <label for="start_at">Start Date</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input type="text" class="form-control datepicker" id="start_at" name="start_at" value="{{ old('start_at') }}">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control" cols="30"
                                      rows="10" placeholder="Enter Description">{{ old('description') }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="point">Point</label>
                            <input type="number" name="point" class="form-control" value="{{ old('point') }}" placeholder="Enter Point">
                        </div>

                        <div class="form-group">
                            <label for="image">Image</label>
                            <input type="file" class="form-control" id="image" name="image">
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
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save fa-btn"></i> Save Lucky Draw
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
    <script>
        $(document).ready(function () {
            $('.datepicker').daterangepicker({
                timePicker: true,
                timePicker24Hour: true,
                minDate: "<?php echo \Carbon\Carbon::today()->toDateString(); ?>",
                maxDate: -0,
                locale: {
                    format: 'YYYY-MM-DD HH:mm:ss'
                }
            });
        });

        function myLoading() {
            $('#loading').addClass('overlay');
            document.getElementById("loading").innerHTML = '<i class="fa fa-spinner fa-spin" style="font-size:50px;"></i>';
        }
    </script>

@stop