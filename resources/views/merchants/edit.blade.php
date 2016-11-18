@extends('app')

@section('content')
    @include('partials.content_header', [
        'pageTitle' => 'Merchants',
        'pageDescription' => 'Edit merchants',
        'breadcrumbs' => [
            'Merchants' => admin_route_url('merchants.index'),
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
                    <a href="{{ admin_route_url('merchants.index') }}" class="btn btn-box-tool"
                       data-toggle="tooltip" title="Back">
                        <i class="fa fa-times"></i></a>
                </div>
            </div>
            <div class="box-body">
                <form role="form" action="{{ admin_route_url('merchants.update', ['id' => $merchant->id]) }}"
                      method="POST" enctype="multipart/form-data" accept-charset="utf-8">
                    {{ csrf_field() }}
                    {{ method_field('PUT') }}
                    <div class="box-body">

                        <div class="form-group has-feedback{{ $errors->has('company_name') ? ' has-error' : '' }}">
                            <label for="company_name">Company Name</label>
                            <input type="text" class="form-control" id="company_name" name="company_name"
                                   placeholder="Enter company name"
                                   value="{{ old('company_name', $merchant->company_name) }}" required>
                        </div>

                        <div class="form-group has-feedback{{ $errors->has('company_logo') ? ' has-error' : '' }}">
                            <label for="company_logo">Company Logo</label>
                            <img width="200" height="200" src="{{ '/storage/merchants/'.$merchant->company_logo }}">
                            <input type="file" class="form-control" id="company_logo" name="company_logo">
                        </div>

                        <div class="form-group has-feedback{{ $errors->has('address') ? ' has-error' : '' }}">
                            <label for="address">Address</label>
                            <textarea class="form-control" name="address" id="address" placeholder="Enter address"
                                      value=""
                                      required>{{ old('address', $merchant->address) }}</textarea>
                        </div>
                        <div class="form-group has-feedback{{ $errors->has('company_email') ? ' has-error' : '' }}">
                            <label for="company_email">Email</label>
                            <input type="email" class="form-control" name="company_email" id="company_email"
                                   value="{{ old('company_email', $merchant->company_email) }}"
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
