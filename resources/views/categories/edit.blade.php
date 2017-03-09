@extends('app')

@section('content')
    @include('partials.content_header', [
        'pageTitle' => 'Product Categories',
        'pageDescription' => 'Edit Product Categories',
        'breadcrumbs' => [
            'Product Categories' => admin_route_url('product-categories.index'),
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
                    <a href="{{ admin_route_url('product-categories.index') }}" class="btn btn-box-tool"
                       data-toggle="tooltip" title="Back">
                        <i class="fa fa-times"></i></a>
                </div>
            </div>
            <div class="box-body" id="form-body">
                <form role="form" action="{{ admin_route_url('product-categories.update', ['id' => $category->id]) }}"
                      method="POST" enctype="multipart/form-data" accept-charset="utf-8" onsubmit="myLoading()">
                    {{ csrf_field() }}
                    {{ method_field('PUT') }}
                    <div class="box-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name"
                                   value="{{ old('name', $category->name) }}" required autofocus>
                        </div>

                        <div class="form-group">
                            <label for="hex_color">Hex Color</label>
                            <input type="text" class="form-control" id="hex_color" name="hex_color"
                                   placeholder="Select Color" value="{{ old('hex_color', $category->hex_color) }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="icon">Icon</label><br>
                            @if($category->icon)
                                <img src="{{ $category->icon }}">
                            @endif
                            <input type="file" class="form-control" id="icon" name="icon">
                        </div>

                        <div class="form-group">
                            <label for="background">Background</label><br>
                            @if($category->background)
                                <img src="{{ $category->background }}">
                            @endif
                            <input type="file" class="form-control" id="background" name="background">
                        </div>

                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer text-right">
                        <button type="submit" class="btn btn-primary" id="submit">
                            <i class="fa fa-save fa-btn"></i> Save Category
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
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.1/css/bootstrap-colorpicker.css">
    <script type="text/javascript"
            src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-colorpicker/2.5.1/js/bootstrap-colorpicker.js"></script>
    <script>
        function myLoading() {
            $('#loading').addClass('overlay');
            document.getElementById("loading").innerHTML = '<i class="fa fa-spinner fa-spin" style="font-size:50px;"></i>';
        }

        $(function () {
            $('#hex_color').colorpicker();
        });
    </script>
@endsection
