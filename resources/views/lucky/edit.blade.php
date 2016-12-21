@extends('app')

@section('content')
    @include('partials.content_header', [
        'pageTitle' => 'Lucky Draws',
        'pageDescription' => 'Edit lucky draws',
        'breadcrumbs' => [
            'Lucky draws' => admin_route_url('lucky.index'),
            'Edit' => false]
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
                    <a href="{{ admin_route_url('lucky.index') }}" class="btn btn-box-tool"
                       data-toggle="tooltip" title="Back">
                        <i class="fa fa-times"></i></a>
                </div>
            </div>
            <div class="box-body" id="form-body">
                <form role="form" action="{{ admin_route_url('lucky.update', ['id' => $lucky->id]) }}"
                      method="POST" enctype="multipart/form-data" accept-charset="utf-8" onsubmit="myLoading()">
                    {{ csrf_field() }}
                    {{ method_field('PUT') }}
                    <div class="box-body">
                        <div class="form-group">
                            <label for="title">Title</label>
                            <input type="text" class="form-control" id="title" name="title" placeholder="Enter title"
                                   value="{{ old('title', $lucky->title) }}" required autofocus>
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
                                      rows="10" placeholder="Enter Description">{{ old('description', $lucky->description) }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="point">Point</label>
                            <input type="number" name="point" class="form-control" value="{{ old('point', $lucky->point) }}" placeholder="Enter Point">
                        </div>

                        <div class="form-group">
                            <label for="image">Image</label><br>
                            @if($lucky->image)
                                <img width="200" height="200" src="{{ '/storage/luckydraws/'.$lucky->image }}">
                            @endif
                            <input type="file" class="form-control" id="image" name="image">
                        </div>

                        <div class="checkbox">
                            <label>
                                <input name="is_multiple" {{ (bool)$lucky->is_multiple ? 'checked' : '' }} type="checkbox">
                                Is Multiple ?
                            </label>
                        </div>

                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer text-right">
                        <button type="submit" class="btn btn-primary" id="submit">
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
            locale: {
                format: 'YYYY-MM-DD HH:mm:ss'
            },
            startDate: '{{ $lucky->start_at }}',
            endDate: '{{ $lucky->end_at }}'
        });
    });

    function myLoading() {
        $('#loading').addClass('overlay');
        document.getElementById("loading").innerHTML = '<i class="fa fa-spinner fa-spin" style="font-size:50px; position: fixed;"></i>';
    }
</script>
@endsection
