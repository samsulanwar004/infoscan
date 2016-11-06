@extends('app')

@section('content')
    @include('partials.content_header', ['pageTitle' => 'Roles', 'pageDescription' => 'List of roles', 'breadcrumbs' => ['Roles' => false]])

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">
                </h3>

                <div class="box-tools pull-right">
                    @cando('Role.Create')
                    <a href="/users/roles/create" class="btn btn-box-tool" data-toggle="tooltip"
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
                        <th>Role name</th>
                        <th>Permission</th>
                        <th width="250"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($roles as $role)
                        <tr>
                            <td class="vertical-middle">
                                <i class="fa fa-check-circle {{ $role->is_active == 1 ? 'text-green' : 'text-default' }}"></i>
                            </td>
                            <td class="vertical-middle">
                                {{ $role->role_name }} <br>
                            </td>
                            <td class="vertical-middle">
                                <a href="{{ route('roles.show', ['id' => $role->id]) }}"
                                   data-toggle="modal"
                                   data-target="#"
                                >
                                    <i class="fa fa-key fa-btn"></i> Click to see permissions list
                                </a>
                            </td>
                            <td class="text-right vertival-middle">
                                <div class="btn-group">
                                    @cando('Role.Update')
                                    <a href="{{ route('roles.edit', ['id' => $role->id]) }}" class="btn btn-info">
                                        <i class="fa fa-pencil"> </i>
                                    </a>
                                    @endcando

                                    @cando('Role.Delete')
                                    <a class="btn btn-danger"
                                       href="{{ route('roles.destroy', ['id' => $role->id]) }}"
                                       data-toggle="modal"
                                       data-target="#"
                                       title="Delete this data"
                                       for-delete="true"
                                       data-message="Are you sure you want to delete this roles ?"
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
