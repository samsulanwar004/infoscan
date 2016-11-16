@extends('app')

@section('content')
    @include('partials.content_header', [
        'pageTitle' => 'Merchant',
        'pageDescription' => 'Create a new merchant',
        'breadcrumbs' => [
            'Merchants' => admin_route_url('merchants.index'),
            'Create' => false]
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
                    <a href="{{ admin_route_url('merchants.index') }}" class="btn btn-box-tool" data-toggle="tooltip"
                       title="Back"> <i
                                class="fa fa-times"></i></a>
                </div>
            </div>
            <div class="box-body">
                <form role="form" action="{{ admin_route_url('merchants.store') }}" method="POST"
                      enctype="multipart/form-data" class="form" accept-charset="utf-8">
                    {{ csrf_field() }}
                    <div class="box-body">
                        <div class="form-group">
                            <label for="merchant_code">Merchant Code</label>
                            <input type="text" class="form-control" id="merchant_code" name="merchant_code"
                                   placeholder="Enter merchant code" required autofocus>
                        </div>
                        <div class="form-group">
                            <label for="company_name">Company Name</label>
                            <input type="text" class="form-control" id="company_name" name="company_name"
                                   placeholder="Enter company name" required>
                        </div>
                        <div class="form-group">
                            <label for="company_name">Company Logo</label>
                            <input type="file" class="form-control" id="company_logo" name="company_name"
                                   placeholder="Enter company name">
                        </div>
                        <div class="form-group">
                            <label for="address">Address</label>
                            <textarea class="form-control" name="address" id="address" placeholder="Enter address"
                                      required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="company_email">Email</label>
                            <input type="email" class="form-control" name="company_email" id="company_email"
                                   placeholder="Enter email" required>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save fa-btn"></i> Save Merchant
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <!-- /.box -->

    </section>
    <!-- /.content -->
@endsection

