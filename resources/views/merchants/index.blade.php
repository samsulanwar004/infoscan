@extends('app')

@section('content')
    @include('partials.content_header', ['pageTitle' => 'Merchants', 'pageDescription' => 'List of merchants', 'breadcrumbs' => ['Merchants' => false]])

    <!-- Main content -->
    <section class="content">

        <!-- Default box -->
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">
                </h3>

                <div class="box-tools pull-right">
                    @cando('User.Create')
                    <a href="{{ admin_route_url('merchants.create') }}" class="btn btn-box-tool" data-toggle="tooltip"
                       title="Create New">
                        <i class="fa fa-plus-circle fa-btn"></i> Create New</a>
                    @endcando
                </div>
            </div>
            <div class="box-body">
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th>Merchant Code</th>
                        <th>Company</th>
                        <th>Email</th>
                        <th width="250"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($merchants as $merchant)
                        <tr>
                            <td>
                                {{ $merchant->merchant_code }}
                            </td>
                            <td>
                                {{ $merchant->company_name }} <br>
                                <small>{{ $merchant->address }}</small>
                            </td>
                            <td>
                                {{ $merchant->company_email }}
                            </td>
                            <td class="text-right vertical-middle">
                                <div class="btn-group">
                                    @cando('User.Update')
                                    <a href="{{ admin_route_url('merchants.edit', ['id' => $merchant->id]) }}"
                                       class="btn btn-info">
                                        <i class="fa fa-pencil"> </i>
                                    </a>
                                    @endcando

                                    @cando('User.Delete')
                                    <a class="btn btn-danger"
                                       href="{{ admin_route_url('merchants.destroy', ['id' => $merchant->id]) }}"
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
                        <td colspan="4"> There is no record for merchants data!</td>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <!-- /.box -->

    </section>
    <!-- /.content -->
@endsection
