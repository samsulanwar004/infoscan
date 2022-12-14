@extends('app')

@section('content')
    @include('partials.content_header', ['pageTitle' => 'Users', 'pageDescription' => 'List of users', 'breadcrumbs' => ['Users' => false]])

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">
                </h3>

                <div class="box-tools pull-right">
                    @cando('User.Create')
                    <a href="{{ admin_route_url('users.create') }}" class="btn btn-box-tool" data-toggle="tooltip"
                       title="Create New">
                        <i class="fa fa-plus-circle fa-btn"></i> Create New</a>
                    @endcando
                </div>
            </div>
            <div class="box-body">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th width="50">#</th>
                        <th width="150">Role</th>
                        <th>Username & Email</th>
                        <th>Activity</th>
                        <th width="250"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td class="vertical-middle">
                                <i class="fa fa-check-circle {{ $user->is_active == 1 ? 'text-green' : 'text-default' }}"></i>
                            </td>
                            <td class="vertical-middle">
                                {{ (!$user->roles->first()) ? '' : $user->roles->first()->role_name }}
                            </td>
                            <td>
                                {{ $user->name }} <br>
                                <small>{{ $user->email }}</small>
                            </td>
                            <td class="vertical-middle">
                                <a href="{{ admin_route_url('users.activities', ['id' => $user->id]) }}" 
                                    data-toggle="modal"
                                    data-target="#">
                                    <i class="fa fa-list-alt fa-btn"></i> See
                                    activity
                                </a>
                            </td>
                            <td class="text-right vertical-middle">
                                <div class="btn-group">
                                    @cando('User.Update')
                                    <a href="{{ admin_route_url('users.edit', ['id' => $user->id]) }}" class="btn btn-info">
                                        <i class="fa fa-pencil"> </i>
                                    </a>
                                    @endcando

                                    @cando('User.Delete')
                                    <a class="btn btn-danger"
                                       href="{{ admin_route_url('users.destroy', ['id' => $user->id]) }}"
                                       data-toggle="modal"
                                       data-target="#"
                                       title="Delete this data"
                                       for-delete="true"
                                       data-message="Are you sure you want to delete this user ?"
                                    > <i class="fa fa-trash"></i> </a>
                                    @endcando
                                </div>
                            </td>
                        </tr>
                    @empty
                        <td colspan="4"> There is no record for users data!</td>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.box -->

    </section>
    <!-- /.content -->
@endsection
