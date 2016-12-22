@extends('app')

@section('content')
    @include('partials.content_header', [
        'pageTitle' => 'Users',
        'pageDescription' => 'Edit user',
        'breadcrumbs' => [
            'Users' => admin_route_url('users.index'),
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
                    <a href="{{ admin_route_url('users.index') }}" class="btn btn-box-tool"
                       data-toggle="tooltip" title="Back">
                        <i class="fa fa-times"></i></a>
                </div>
            </div>
            <div class="box-body">
                <form role="form" action="{{ admin_route_url('users.update', ['id' => $user->id]) }}" method="POST"
                      enctype="multipart/form-data" class="form" accept-charset="utf-8">
                    {{ csrf_field() }}
                    {{ method_field('PUT') }}
                    <div class="box-body">
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" class="form-control" id="name" name="name" placeholder="Enter name"
                                   value="{{ old('name', $user->name) }}" required autofocus>
                        </div>
                        <div class="form-group">
                            <label for="email">Email address</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter email"
                                   value="{{ old('email', $user->email) }}" required>
                        </div>

                        <div class="form-group">
                            <label for="role">Select Role</label>
                            <select name="role" id="role" class="form-control">
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->role_name }}</option>
                                @endforeach
                            </select>
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
                                <input name="is_active" {{ (bool)$user->is_active ? 'checked' : '' }} type="checkbox">
                                Is Active ?
                            </label>
                        </div>
                    </div>
                    <!-- /.box-body -->

                    <div class="box-footer text-right">
                        <button type="submit" class="btn btn-primary" id="save">
                            <i class="fa fa-save fa-btn"></i> Save User
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
    $("#password, #confirm_password").keyup(function() {
        var pass1 = document.getElementById("password").value;
        var pass2 = document.getElementById("confirm_password").value;

        if (pass1 != pass2) {
            document.getElementById("password").style.borderColor = "#E34234";
            document.getElementById("confirm_password").style.borderColor = "#E34234";
            document.getElementById("save").disabled = true;
        } else {
            document.getElementById("password").style.borderColor = "#00ff00";
            document.getElementById("confirm_password").style.borderColor = "#00ff00";
            document.getElementById("save").disabled = false;
        }
    });
</script>
@endsection