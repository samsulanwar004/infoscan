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
                    @cando('MerchantUser.Create')
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
                        <th>#</th>
                        <th>Username & Email</th>
                        <th>Activity</th>
 
                        <th width="250"></th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        @forelse($mu as $merchantuser)
                            <td>
                                <i class="fa fa-check-circle {{ $merchantuser->is_active == 1 ? 'text-green' : 'text-default' }}"></i>
                            </td>
                            <td>
                                {{ $merchantuser->name }}<br>
                                <small>{{ $merchantuser->email }}</small>
                            </td>
                            <td>
                                <a href="#"><i class="fa fa-list-alt fa-btn"></i> See activity</a>
                            </td>                            
                            <td class="text-right vertical-middle">
                                <div class="btn-group">
                                    @cando('MerchantUser.Update')
                                    <a href="{{ admin_route_url('merchantusers.edit', ['id' => $merchantuser->user_id]) }}"
                                       class="btn btn-info">
                                        <i class="fa fa-pencil"> </i>
                                    </a>
                                    @endcando

                                    @cando('MerchantUser.Delete')
                                    <a class="btn btn-danger"
                                       href="{{ admin_route_url('merchantusers.destroy', ['id' => $merchantuser->user_id]) }}"
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
