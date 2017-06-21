@extends('app')

@section('content')
    @include('partials.content_header', [
        'pageTitle' => 'Referrals',
        'pageDescription' => 'Edit Referral',
        'breadcrumbs' => [
            'Referrals' => admin_route_url('referral.index'),
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
                    <a href="{{ admin_route_url('referral.index') }}" class="btn btn-box-tool"
                       data-toggle="tooltip" title="Back">
                        <i class="fa fa-times"></i></a>
                </div>
            </div>
            <div class="box-body" id="form-body">
                <form role="form" action="{{ admin_route_url('referral.update', ['id' => $referral->id]) }}"
                      method="POST" enctype="multipart/form-data" accept-charset="utf-8" onsubmit="myLoading()">
                    {{ csrf_field() }}
                    {{ method_field('PUT') }}
                    <div class="box-body">
                        <div class="form-group">
                            <label for="referral_point">Referral Point</label>
                            <input type="text" class="form-control" id="referral-point" name="referral_point" placeholder="Enter Referral Point"
                                   value="{{ old('referral_point', $referral->referral_point) }}" required autofocus>
                        </div>

                        <div class="form-group">
                            <label for="referrer_point">Referrer Point</label>
                            <input type="text" class="form-control" id="referrer-point" name="referrer_point" placeholder="Enter Referrer Point"
                                   value="{{ old('referrer_point', $referral->referrer_point) }}" required autofocus>
                        </div>

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" id="description" class="form-control" cols="30"
                                      rows="10" placeholder="Enter Description">{{ old('description', $referral->description) }}</textarea>
                        </div>

                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer text-right">
                        <button type="submit" class="btn btn-primary" id="submit">
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
@endsection
