@extends('app')

@section('content')
    @include('partials.content_header', ['pageTitle' => 'Merchant Users', 'pageDescription' => 'List of merchant users', 'breadcrumbs' => ['Merchant Users' => false]])

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">
                </h3>

                <div class="box-tools pull-right">
                    @cando('User.Create')
                    <a href="{{ admin_route_url('merchantusers.create') }}" class="btn btn-box-tool"
                       data-toggle="tooltip"
                       title="Create New">
                        <i class="fa fa-plus-circle fa-btn"></i> Create New</a>
                    @endcando
                </div>
            </div>
            <div class="box-body">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>User</th>
                        <th>Merchant</th>
                        <th>Active</th>
                        <th width="250"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        @forelse($merchantUsers as $merchantuser)
                            <td>
                                {{ $merchantuser->user->name }}<br>
                                <small>{{ $merchantuser->user->email }}</small>
                            </td>
                            <td>
                                {{ $merchantuser->merchant->company_name }}
                            </td>
                            <td>
                                <i class="fa fa-check-circle {{ $merchantuser->user->is_active == 1 ? 'text-green' : 'text-default' }}"></i>
                            </td>
                            <td class="text-right vertical-middle">
                                <div class="btn-group">
                                    @cando('User.Update')
                                    <a href="{{ admin_route_url('merchantusers.edit', ['id' => $merchantuser->user->id]) }}"
                                       class="btn btn-info">
                                        <i class="fa fa-pencil"> </i>
                                    </a>
                                    @endcando

                                    @cando('User.Delete')
                                    <a class="btn btn-danger"
                                       href="{{ admin_route_url('merchantusers.destroy', ['id' => $merchantuser->user->id]) }}"
                                       data-toggle="modal"
                                       data-target="#"
                                       title="Delete this data"
                                       for-delete="true"
                                       data-message="Are you sure you want to delete this merchant ?"
                                    > <i class="fa fa-trash"></i> </a>
                                    @endcando
                                </div>
                            </td>
                        </tr>
                        @empty
                            <td colspan="4"> There is no record for merchant users data!</td>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </section>

@endsection
