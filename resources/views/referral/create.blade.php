@extends('app')

@section('content')
    @include('partials.content_header', ['pageTitle' => 'Referrals', 'pageDescription' => 'Create a new referral point', 'breadcrumbs' => ['Referrals' => '/referral', 'Create' => false]])

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">
                </h3>

                <div class="box-tools pull-right">
                    <a href="/referral" class="btn btn-box-tool" data-toggle="tooltip" title="Back"> <i
                            class="fa fa-times"></i></a>
                </div>
            </div>
            <div class="box-body">
                <form role="form" action="{{ route('referral.store') }}" method="POST" onsubmit="myLoading()" enctype="multipart/form-data">
                    {{ csrf_field() }}
                    <div class="box-body">
                        <div class="form-group">
                            <label for="referral_point">Referral Point</label>
                            <input type="number" class="form-control" id="referral-point" name="referral_point" placeholder="Enter Referral Point"
                                   value="{{ old('referral_point') }}" required autofocus>
                        </div>

                        <div class="form-group">
                            <label for="referrer_point">Referrer Point</label>
                            <input type="number" class="form-control" id="referrer-point" name="referrer_point" placeholder="Enter Referrer Point"
                                   value="{{ old('referrer_point') }}" required autofocus>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control" cols="30"
                                      rows="10" placeholder="Enter Description">{{ old('description') }}</textarea>
                        </div>

                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save fa-btn"></i> Save Referral
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
        function myLoading() {
            $('#loading').addClass('overlay');
            document.getElementById("loading").innerHTML = '<i class="fa fa-spinner fa-spin" style="font-size:50px;"></i>';
        }
    </script>

@stop