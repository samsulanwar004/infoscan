@extends('app')

@section('content')
    @include('partials.content_header', ['pageTitle' => 'Users', 'pageDescription' => 'Create a new user', 'breadcrumbs' => ['Users' => '/users', 'Create' => false]])

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
                    <a href="/users" class="btn btn-box-tool"
                       data-toggle="tooltip" title="Back">
                        <i class="fa fa-times"></i></a>
                </div>
            </div>
            <div class="box-body">
                <form role="form" action="{{ route('users.store') }}" method="POST">
                    {{ csrf_field() }}
                    <div class="box-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter name"
                                   required autofocus>
                        </div>
                        <div class="form-group">
                            <label for="email">Email address</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter email"
                                   required>
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" class="form-control" id="password" name="password"
                                   placeholder="Password" required>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                                   placeholder="Password" required>
                        </div>

                        <div class="checkbox">
                            <label>
                                <input name="is_active" type="checkbox"> Is Active ?
                            </label>
                        </div>
                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save fa-btn"></i> Save User
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <!-- /.box -->

    </section>
    <!-- /.content -->
@endsection