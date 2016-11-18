@extends('app')

@section('content')
    @include('partials.content_header', [
        'pageTitle' => 'Merchant users',
        'pageDescription' => 'Create a new merchant user',
        'breadcrumbs' => [
            'Merchant users' => admin_route_url('merchantusers.index'),
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
                    <a href="{{ admin_route_url('merchantusers.index') }}" class="btn btn-box-tool"
                       data-toggle="tooltip"
                       title="Back"> <i
                                class="fa fa-times"></i></a>
                </div>
            </div>
            <div class="box-body">
                <form role="form"
                      action="{{ admin_route_url('merchantusers.update', ['id' => $merchantUsers->user->id]) }}"
                      method="POST" enctype="multipart/form-data" class="form" accept-charset="utf-8">
                    {{ csrf_field() }}
                    {{ method_field('PUT') }}
                    <div class="box-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter name"
                                   value="{{ old('name', $merchantUsers->user->name) }}" required autofocus>
                        </div>
                        <div class="form-group">
                            <label for="email">Email address</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter email"
                                   value="{{ old('email', $merchantUsers->user->email) }}" required>
                        </div>

                        <div class="form-group">
                            <label for="merchant">Merchant</label>
                            <input type="text" class="form-control" id="merchant" name="merchant"
                                   value="{{ $merchantUsers->merchant->company_name }}" disabled="disabled">
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password"
                                   value="{{ old('password', '******') }}" placeholder="Password" required>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                                   value="{{ old('confirm_password', '******') }}" placeholder="Password" required>
                        </div>

                        <div class="checkbox">
                            <label>
                                <input name="is_active" {{ (bool)$merchantUsers->user->is_active ? 'checked' : '' }} type="checkbox">
                                Is Active ?
                            </label>
                        </div>
                    </div>
                    <!-- /.box-body -->
                    <div class="box-footer text-right">
                        <button type="submit" class="btn btn-primary" id="submit">
                            <i class="fa fa-save fa-btn"></i> Save All
                        </button>
                    </div>
                </form>
            </div>

        </div>
        <!-- /.box -->

    </section>
    <!-- /.content -->
@endsection