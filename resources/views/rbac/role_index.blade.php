@extends('app')

@section('content')
    @include('partials.content_header', ['pageTitle' => 'Roles', 'pageDescription' => 'List of roles', 'breadcrumbs' => ['Roles' => false]])

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">
                    <a href="/users/roles/create" class="btn btn-info btn-sm"> <i class="fa fa-plus fa-btn"></i> add new</a>
                </h3>

                <!--<div class="box-tools pull-right">
                    <button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip"
                            title="Collapse">
                        <i class="fa fa-minus"></i></button>
                    <button type="button" class="btn btn-box-tool" data-widget="remove" data-toggle="tooltip"
                            title="Remove">
                        <i class="fa fa-times"></i></button>
                </div>-->
            </div>
            <div class="box-body">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>Role name</th>
                        <th>Total of Permission</th>
                        <th width="250"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($roles as $role)
                        <tr>
                            <td class="vertical-middle">
                                <i class="fa fa-check-circle {{ $role->is_active == 1 ? 'text-green' : 'text-default' }}"></i>
                            </td>
                            <td>
                                {{ $role->role_name }} <br>
                            </td>
                            <td class="vertical-middle"><a href="#">
                                    <i class="fa fa-key fa-btn"></i> </a>
                            </td>
                            <td class="text-right vertival-middle">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-primary">
                                        <i class="fa fa-pencil"> </i>
                                    </button>
                                    <button type="button" class="btn btn-danger">
                                        <i class="fa fa-trash"> </i>
                                    </button>
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