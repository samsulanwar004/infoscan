@extends('app')

@section('content')
    @include('partials.content_header', ['pageTitle' => 'Promotions', 'pageDescription' => 'Create a new promo', 'breadcrumbs' => ['Promotions' => '/promotions', 'Create' => false]])

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">
                </h3>

                <div class="box-tools pull-right">
                    <a href="/users" class="btn btn-box-tool" data-toggle="tooltip" title="Back"> <i
                            class="fa fa-times"></i></a>
                </div>
            </div>
            <div class="box-body">
                <form role="form" action="{{ route('promotions.store') }}" method="POST">
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
                                <input type="text" class="form-control datepicker" id="start_at" name="start_at">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control" cols="30"
                                      rows="10">{{ old('title') }}</textarea>
                        </div>

                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save fa-btn"></i> Save Promotion
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <!-- /.box -->

    </section>
    <!-- /.content -->
@endsection

@section('footer_scripts')
    <script>
        $(document).ready(function() {
            $('.datepicker').datepicker({
                autoclose: true
            });
        });
    </script>
@endsection