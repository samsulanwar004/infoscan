@extends('app')

@section('content')
    @include('partials.content_header', ['pageTitle' => 'Product Categories', 'pageDescription' => 'Create a new product category', 'breadcrumbs' => ['Product Categories' => '/product-categories', 'Create' => false]])

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">
                </h3>

                <div class="box-tools pull-right">
                    <a href="/product-categories" class="btn btn-box-tool" data-toggle="tooltip" title="Back"> <i
                                class="fa fa-times"></i></a>
                </div>
            </div>
            <div class="box-body">
                <form role="form" action="{{ route('product-categories.store') }}" method="POST" onsubmit="myLoading()"
                      enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="box-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter Name"
                                   value="{{ old('name') }}" required autofocus>
                        </div>

                        <div class="form-group">
                            <label for="hex_color">Hex Color</label>
                            <input type="text" class="form-control" id="hex_color" name="hex_color"
                                   placeholder="Select Color" value="{{ old('hex_color') }}" readonly>
                        </div>

                        <div class="form-group">
                            <label for="icon">Icon</label>
                            <input type="file" class="form-control" id="icon" name="icon">
                        </div>

                        <div class="form-group">
                            <label for="background">Background</label>
                            <input type="file" class="form-control" id="background" name="background">
                        </div>

                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer text-right">
                        <button type="submit" class="btn btn-primary">
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
    <script type="text/javascript">
        function myLoading() {
            $('#loading').addClass('overlay');
            document.getElementById("loading").innerHTML = '<i class="fa fa-spinner fa-spin" style="font-size:50px;"></i>';
        }

        $(function () {
            $('#hex_color').colorpicker();
        });
    </script>

@stop